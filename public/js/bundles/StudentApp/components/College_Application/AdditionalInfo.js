import React from 'react'
import { connect } from 'react-redux'
import DocumentTitle from 'react-document-title'

import * as Q from './questionConstants'
import SaveButton from './SaveButton'
import { saveUploadedFilesThenApplication, toggleAdditionalInfoColleges, saveApplication, updateProfile, saveAdditionalInfoPage, resetSaved, changeCollegeAdditionalPage, saveUploadedFiles, clearChangedFields } from './../../actions/Profile'
import AdditionalInfoModal from './AdditionalInfoModal'
import { browserHistory } from 'react-router'
import MyCollege from './MyCollege'
import SingleCollegeAdditionalInfo from './SingleCollegeAdditionalInfo'

class AdditionalInfo extends React.Component {
	constructor(props) {
		super(props);

		this.PAGE_DONE = '';
	}

	componentWillMount() {
		let { dispatch, route, _profile } = this.props,
			active_college = _profile.active_college_for_additional_questions,
			colleges = _profile.applyTo_schools;

		this.PAGE_DONE = route.id+'_form_done';
		dispatch( updateProfile({ page: route.id }) );	
		dispatch( clearChangedFields());

		if (!active_college && colleges && colleges.length > 0) {
			this._dispatchInitialCollege(colleges);
		}
	}

	// componentDidMount() {
	// 	let { _profile, dispatch } = this.props;
	// }

	componentWillReceiveProps(newProps) {
		let { _profile, dispatch } = this.props,
			{ _profile: _newProfile } = newProps,
			active_college = _newProfile.active_college_for_additional_questions,
			newColleges = _newProfile.applyTo_schools,
			colleges = _profile.applyTo_schools;

		if (!active_college && !_.isEqual(colleges, newColleges) && newColleges.length > 0) {
			this._dispatchInitialCollege(newColleges);
		}
	}

	_dispatchInitialCollege(colleges) {
		let { _profile, dispatch } = this.props;
		
		// Filter out colleges with custom questions and sort by least questions first
		colleges = colleges
			.filter(college => college.custom_questions)
			.sort((a, b) => Object.keys(a.custom_questions).length - Object.keys(b.custom_questions).length);

		dispatch( updateProfile({ active_college_for_additional_questions: colleges[0] }) );
		this._toggleColleges(colleges[0]);
	}

	_buildFormData(form) {
		let formData = new FormData();

		_.each(form, (val, key) => {
			if( key === 'transcripts' && _.get(val, 'length') > 0 ){
				var only_new_uploads = _.filter(val, (v) => !v.transcript_id);

				// only loop through newly uploaded files - omit already saved uploads
				_.each(only_new_uploads, (up, i) => {
					if( up.upload_type ){
						let name = up.upload_type;
						formData.append(name+'_'+(i+1), up);
					}
				});

			}
		});

		return formData;
	}

	_buildLogoButtons() {
		let { _profile, dispatch } = this.props,
			colleges = _profile.applyTo_schools;
		
		if (!colleges) { return null; }

		colleges = colleges
			.filter(college => college.custom_questions) // Remove colleges that do not have custom_questions
			.sort((a, b) => Object.keys(a.custom_questions).length - Object.keys(b.custom_questions).length) // Sort by ASC order
			.map((college, index) => this._buildLink(college, index)); // Build college links

		return colleges;
	}

	_buildLink(college, index) {
		let { _profile, dispatch } = this.props,
			name = college.school_name.split(/\s+/).join('_').toLowerCase(),
			active_college = _profile.active_college_for_additional_questions,
			save_pending = _profile.save_pending,
			isDone = _profile[name + '_additional_questions_valid'] != null 
				? _profile[name + '_additional_questions_valid']
				: false,

			isActive = ( active_college && active_college.school_name == college.school_name ) 
				? 'active' : '';

		return (
			<a title={ college.school_name } key={ college.college_id } onClick={ save_pending ? () => false : () => this._toggleColleges(college) }>
				{ isDone && <span title={ college.school_name } className='green-check-mark'>&#10003;</span> }
				<MyCollege classes={ 'college-link ' + isActive + ( isDone ? ' done' : '' ) } i={ index } college={ college } />
			</a>
		);
	}

	_toggleColleges(college) {
		let { dispatch } = this.props;
		dispatch( toggleAdditionalInfoColleges({ active_college_for_additional_questions: college }) );
	}

    _onSkip = () => {
        const { _profile, route } = this.props;
        const required_route = _.find(_profile.req_app_routes.slice(), {id: route.id});
        
        if(required_route) browserHistory.push('/college-application/'+required_route.next + window.location.search);
    }

	render() {
		let { _profile, route, dispatch } = this.props,
			active_college = _profile.active_college_for_additional_questions;

		if (active_college) {
			// var used to scope outside if statement	
			var name = active_college.school_name.split(/\s+/).join('_').toLowerCase(),
				additional_questions = _profile[name + '_school_additional_questions'];

			if (additional_questions) {
				var	current_page = additional_questions.current_page + 1,
			    	page_count = additional_questions.questions.length;
		    }
		}
		
		// Only show done button if last page of a single college or if no additional questions exist.
		let show_done_btn = !active_college || current_page == page_count; 

		return (
			<DocumentTitle title={ "Plexuss | College Application | "+route.name }>
				<div className="application-container">

					<form className="additional-info-form">
						<div className="page-head">{ route.name } (optional)</div>
						<div className="additional-notes">{ Q.ADDTL_PRE_MESSAGE }</div>
						<div className="my-colleges-list full mt30">{ this._buildLogoButtons() }</div>
						<SingleCollegeAdditionalInfo route={ route } dispatch={ dispatch } _profile={ _profile } college={ active_college } />
						<div className="buttons">
							{ active_college && <NextButton route={route} _profile={ _profile } dispatch={ dispatch } college={ active_college } /> }
							{ show_done_btn && <DoneButton route={route} PAGE_DONE={ this.PAGE_DONE } dispatch={ dispatch } _profile={ _profile } /> }

                            <div className='section-skip-button' onClick={this._onSkip}>Skip</div>
						</div>
					</form>
				</div>
			</DocumentTitle>
		);
	}

}

// Private classes //
class NextButton extends React.Component {
	constructor(props) {
		super(props);
	}

	_saveAndContinue(event) {
		event.preventDefault();

		let { dispatch, _profile, route, college } = this.props,
			resume_found = false,
			name = this._getFormatedName(),
			additional_questions = _profile[name + '_school_additional_questions'],
			current_page = additional_questions.current_page,
			last_page = ( current_page + 1 ) == additional_questions.questions.length;


		if( additional_questions.page_done ){
			let form = _.omitBy( _profile, (v, k) => k.includes('list') );

			if (last_page) {
				dispatch( saveAdditionalInfoPage(form, college, 'next-college', 'additional_info', _profile.oneApp_step) );
			} else {
				dispatch( saveAdditionalInfoPage(form, college, 'next', 'additional_info', _profile.oneApp_step) );
			}
		}else{
			// if have not attempted saving yet, make it true
			!_profile.save_attempted && dispatch( updateProfile({save_attempted: true}) );
		}
	}

	// Replacing spaces with _ and all letters lowercased.
	_getFormatedName(){
		let { _profile, college } = this.props;

		return college ? college.school_name.split(/\s+/).join('_').toLowerCase() : null;
	}

	render() {
		let { _profile } = this.props,
			college = _profile.active_college_for_additional_questions,
			name = this._getFormatedName(),
			additional_questions = _profile[name + '_school_additional_questions'],
			disabled = _profile.save_pending || ( additional_questions && !additional_questions.page_done ),
			questions = additional_questions ? additional_questions.questions : null,
			current_page = additional_questions ? additional_questions.current_page : null;

		// Check if questions exist
		if (questions == null) { return null; }

		let last_page = ( current_page + 1 ) == questions.length,
			last_college = _profile.last_additional_info_college,
			label = _profile.save_pending ? 'Saving...' : ( last_page ? 'Next College' : 'Continue' );
		
		// If last page and last college, do not render next button
		if ( last_page && last_college ) { return null; }

		return (
			<div>
				<button 
					onClick={ (event) => this._saveAndContinue(event) }
					className="continue-btn next-page-btn"
					disabled={ disabled }>
						{ label }
				</button>
			</div>
		);
	}
}

class DoneButton extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			showModal: false
		}

		this._toggleModal = this._toggleModal.bind(this);
	}

	_toggleModal(event) {
		event.preventDefault();
		this.setState({ showModal: !this.state.showModal });
	}

	render() {
		let { college, _profile, dispatch, PAGE_DONE, route } = this.props,
			label = 'I\'m Done',
			disabled = ( _profile[PAGE_DONE] != null && _profile[PAGE_DONE] == true ) ? false : true,
			showModal = this.state.showModal;

		return (
			<div>
				<button
					onClick={ this._toggleModal }
					className="continue-btn done-btn"
					disabled={ disabled }>
						{ label }
				</button>
				{ showModal && <AdditionalInfoModal route={ route } PAGE_DONE={ PAGE_DONE } _toggleModal={ this._toggleModal } dispatch={ dispatch } _profile={ _profile }/> }
			</div>
		);
	}
}
// End Private classes //

const mapStateToProps = (state, props) => {
	return {
		_profile: state._profile,
	};
};

export default connect(mapStateToProps)(AdditionalInfo);