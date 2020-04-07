import MyCollege from './MyCollege'
import React from 'react'
import CustomModal from './../common/CustomModal'
import { browserHistory } from 'react-router'
import { updateProfile, saveUploadedFilesThenApplication, resetSaved, saveApplication } from './../../actions/Profile'

export default class AdditionalInfoModal extends React.Component {
	constructor(props) {
		super(props);
	}

	componentWillReceiveProps(np) {
		let { dispatch, route, _profile } = this.props;

		// after saving, reset saved and go to next route
		if( np._profile.save_success !== _profile.save_success && np._profile.save_success ){
			dispatch( resetSaved() );
			
			if( np._profile.coming_from ) browserHistory.goBack();
			else{
				let required_route = _.find(_profile.req_app_routes.slice(), {id: route.id});
				if(required_route) browserHistory.push('/college-application/'+required_route.next);
			}
		}
	}

	_saveAndContinue(event){
		event.preventDefault();

		let { dispatch, _profile, route, PAGE_DONE } = this.props,
			resume_found = false;

		if( _profile[PAGE_DONE] ){
			let form = _.omitBy( _profile, (v, k) => k.includes('list') );
			
			if( form.transcripts.length > 0 ){
				let has_new_file = false;

				// check transcripts to see if a new file has been uploaded b/c you'll have to transform form into FormData
				_.each(form.transcripts, t => {
					if( t.upload_type ){ // break out as soon as new file has been found
						has_new_file = !!t.upload_type;
						return false;
					}
				});

				if( has_new_file ) {
					dispatch( saveUploadedFilesThenApplication(this._buildFormData(form), form) );
					return;
				} 
			}

			dispatch( saveApplication(form, 'additional_info', _profile.oneApp_step) );
			
		}
	}

	_buildFormData(form){
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

	_buildLogos() {
		let { _profile } = this.props,
			colleges = null;
		if (!_profile.applyTo_schools) { return null; }
		
		return _profile.applyTo_schools
			.filter(college => college.custom_questions)
			.sort((a, b) => Object.keys(a.custom_questions).length - Object.keys(b.custom_questions).length) // Sort by ASC order
			.map((college, index) => this._buildLink(college, index)); // Build college links
	}

	_buildLink(college, index){
		let { _profile } = this.props,
			name = college.school_name.split(/\s+/).join('_').toLowerCase(),
			isDone = _profile[name + '_additional_questions_valid'] != null 
				? _profile[name + '_additional_questions_valid']
				: false,
			classes = isDone ? 'done' : '';

		return (
			<MyCollege key={ college.college_id } classes={ 'additional-info-modal-college-icon ' + classes } i={ index } college={ college } />
		);
	}

	_countFinished() {
		let { _profile } = this.props,
			name = null,
			colleges = _.filter(_profile.applyTo_schools, college => college.custom_questions);

		return _.reduce(colleges, (total, college) => {

			name = college.school_name.split(/\s+/).join('_').toLowerCase();

			if (_profile[name + '_additional_questions_valid'] != null && _profile[name + '_additional_questions_valid'] == true)
				return total + 1;
			else
				return total;

		}, 0);

	}

	_countTotal() {
		let { _profile } = this.props;
			
		return _.filter(_profile.applyTo_schools, college => college.custom_questions).length;
	}

	render() {
		let { _profile } = this.props,
			finished_count = this._countFinished(),
			total_count = this._countTotal(),
			disabled = _profile.save_pending,
			label = _profile.save_pending ? 'Saving...' : 'Save & Continue';

		let finished_string = '' + finished_count + '/' + total_count,
			colleges_left = total_count - finished_count;

		return (
			<CustomModal>
				<div className="modal additional-info-modal">
					<div className="closeMe" onClick={ disabled ? null : this.props._toggleModal }>&times;</div>
						{ finished_count < total_count &&
							<div>
								<h3>You've only completed additional info for { finished_string } colleges.</h3>
								<div>If you wish to apply to the additional{ ' ' + ( colleges_left == 1 ? '' : colleges_left + ' ' ) }college{ colleges_left == 1 ? '' : 's' }, please finish the required information.</div>
							</div> }
						{ finished_count == total_count &&
							<h3>You've completed all additional info for the following colleges.</h3> }
					<div className="my-colleges-list mt25">
						{ this._buildLogos() }
					</div>
					<div>If you want to continue and apply to these colleges click <b>Save & Continue</b> below, otherwise click <b>I'm not finished</b>.</div>
					<div className="additional-info-modal-buttons mt40">
						<div onClick={ disabled ? null : this.props._toggleModal } className='not-finished-button'>I'm not finished</div>
						<button onClick={ event => this._saveAndContinue(event) } className='next-button' disabled={ disabled }>{ label }</button>
					</div>
				</div>
			</CustomModal>
		);
	}
}