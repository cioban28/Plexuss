// /College_Application/Submit.js

import React from 'react'
import { connect } from 'react-redux'
import { Link, browserHistory } from 'react-router'
import DocumentTitle from 'react-document-title'
import moment from 'moment'
import MyCollege from './MyCollege'
import TextField from './TextField'
import SaveButton from './SaveButton'
import CheckboxField from './CheckboxField'
import ReactSpinner from 'react-spinjs-fix'
import { TOC, SIGN } from './constants'
import { updateProfile, saveApplication, resetSaved, clearChangedFields } from './../../actions/Profile'
import { getPrioritySchools } from './../../actions/Intl_Students'

var PAGE_DONE = '';

class Submit extends React.Component {
	constructor(props) {
		super(props)
		this.state = {
			today: ''
		}
		this._buildCollegeLogos = this._buildCollegeLogos.bind(this)
		this._submitApplication = this._submitApplication.bind(this)
		this._addRemoveColleges = this._addRemoveColleges.bind(this)
	}

	_addRemoveColleges(){
		let { dispatch, route } = this.props;

		dispatch( updateProfile({coming_from: route.id}) );
		browserHistory.push('/college-application/colleges');
	}

	_submitApplication(e){
		e.preventDefault();
		
		let { dispatch, _profile } = this.props;

		if( _profile[PAGE_DONE] ){
			let form = _.omitBy( _profile, (v, k) => k.includes('list') );
			dispatch( saveApplication(form, 'submit', _profile.oneApp_step) );
		}
	}

	_buildCollegeLogos(college, index){
		let { _profile } = this.props,
			completed_colleges = _profile.completed_colleges,
			finished = ( completed_colleges && !!_.find(completed_colleges, ['school_name', college.school_name]) ) 
				? 'done' : '';

		return (
			<MyCollege key={index} i={index} college={college} classes={'submit-college-logo ' + finished} {...this.props} />
		);
	}

	componentWillMount(){
		let { dispatch, route, _profile } = this.props;

		PAGE_DONE = route.id+'_form_done';
		this.state.today = moment().format('MM/DD/YYYY');

		dispatch( updateProfile({page: route.id}) );
		dispatch( clearChangedFields());
	}

	componentWillReceiveProps(np){
		let { dispatch, route, _profile } = this.props;

		// after saving, reset saved and go to next route
		if( np._profile.save_success !== _profile.save_success && np._profile.save_success ){
			dispatch( resetSaved() );
			browserHistory.push('/college-application/'+route.next + window.location.search);	
		}
	}	

	render(){
		let { _profile, route } = this.props,
			{ today } = this.state;
		
		return (
			<DocumentTitle title={"Plexuss | College Application | "+route.name}>
				<div className="application-container submit-container">

					<form onSubmit={ this._submitApplication }>

						<div className="page-head">{route.name}</div>
						
						{ !_profile.init_priority_schools_done && <div className="spin-container"><ReactSpinner color="#24b26b" /></div> }

						<div>Congratulations! You have completed all the requirements!</div>
						<div>You are now ready to submit your Recruitment Application to the following school(s):</div>
						
						<br />

						<div className="my-colleges-list">
							{ _.get(_profile, 'applyTo_schools.length', 0) > 0 && _profile.applyTo_schools.map(this._buildCollegeLogos) }
						</div>
						<div className="addmore"><u onClick={ this._addRemoveColleges }>Add/remove colleges</u></div>

						<div className="mid-head">Signature</div>
						<div>Please affirm the following before you submit your application.</div>
						<br />
						<CheckboxField field={ TOC } {...this.props} />

						<div className="sign">
							<TextField field={ SIGN } {...this.props} />
							<div className="sign-date">
								<div>Date</div>
								<div>{ today || '' }</div>
							</div>
						</div>

						<SaveButton 
							label={'Submit'}
							_profile={_profile}
							error_msg={'Please ensure all sections are completed'}
							page_done={PAGE_DONE} />
					</form>

				</div>
			</DocumentTitle>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		_user: state._user,
		_profile: state._profile,
	};
};

export default connect(mapStateToProps)(Submit);