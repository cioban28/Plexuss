// /College_Application/AdditionalInfo.js

import React from 'react'
import { connect } from 'react-redux'
import { browserHistory } from 'react-router'
import DocumentTitle from 'react-document-title'

import TextForm from './TextForm'
import TextField from './TextField'
import RadioForm from './RadioForm'
import SaveButton from './SaveButton'
import UploadForm from './UploadForm'
import RadioField from './RadioField'
import SelectField from './SelectField'
import ReactSpinner from 'react-spinjs'
import CheckboxForm from './CheckboxForm'
import CheckboxField from './CheckboxField'
import DatePickr from './../common/DatePickr'
import PhoneNumberVerifier from './../common/PhoneNumberVerifier'

import * as Q from './questionConstants'
import { updateProfile, saveApplication, resetSaved } from './../../actions/Profile'

var PAGE_DONE = '',
	ALL_ADDTL = [...Q._getAllQuestionsAsArray()];

const AdditionalInfo = React.createClass({
	componentWillMount(){
		let { dispatch, route, _profile } = this.props;

		if( _.get(_profile, 'req_additional_questions.length', 0) > 0 && _profile.religion_list ){
			_profile.req_additional_questions = this._initLists(_profile.req_additional_questions, [..._profile.religion_list]);
		}
		
		PAGE_DONE = route.id+'_form_done';
		dispatch( updateProfile({page: route.id}) );	
	},	

	componentWillReceiveProps(np){
		let { dispatch, route, _profile } = this.props,
			this_questions = _.get(_profile, 'req_additional_questions.length', 0),
			next_questions = _.get(np, '_profile.req_additional_questions.length', 0);

		// init religion for questions that require religion after questions have been set.
		if( (this_questions !== next_questions || _profile.religion_list !== np._profile.religion_list) &&
			(np._profile.religion_list && np._profile.req_additional_questions) ){
				np._profile.req_additional_questions = this._initLists(np._profile.req_additional_questions, [...np._profile.religion_list]);
		}

		// after saving, reset saved and go to next route
		if( np._profile.save_success !== _profile.save_success && np._profile.save_success ){
			dispatch( resetSaved() );
			
			if( np._profile.coming_from ) browserHistory.goBack();
			else{
				let required_route = _.find(_profile.req_app_routes.slice(), {id: route.id});
				if(required_route) browserHistory.push('/college-application/'+required_route.next);
			}
		}
	},	

	_initLists(list, religions){
		return list.map(q => {

			// doing this nested map cause the religious_affiliation is a dependent field of q
			if( q.name === 'addtl__are_you_christian' && q.dependents_no && q.dependents_no.length > 0 ){
				return {
					...q,
					dependents_no: q.dependents_no.map(d => { return {...d, options: religions}; }), // update options w/religions list
				};
			}
			
			return q;
		});
	},	

	_scrollToLatestQuestion(){
		let { _profile } = this.props;
		let target = _profile.latest_question ? $('#'+_profile.latest_question) : $('body');

		$('html, body').animate({scrollTop: target.offset().top}, 1000);
	},

	_saveAndContinue(e){
		e.preventDefault();

		let { dispatch, _profile, route } = this.props,
			resume_found = false;

		if( _profile[PAGE_DONE] ){
			let graduate_question_req = _.find(_profile.req_additional_questions, {name: 'addtl__post_secondary_resume'});

			// the graduate resume question is required and user is seeking a graduate degree, check for resume upload
			if( _.get(_profile, 'degree_id', 0) >= 4 && graduate_question_req ){
				// find resume transcript
				if( _.get(_profile, 'transcripts.length') ){
					resume_found = _.find(_profile.transcripts, {transcript_type: 'resume'});
				}

				// if resume not found, show error message
				if( !resume_found ){
					this.setState({resume_uploaded: !!resume_found});
					return;
				}
			}

			// else submit form
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

				if( has_new_file ) form = this._buildFormData(form);	
			}
			
			dispatch( saveApplication(form) );
			
		}else{
			this._scrollToLatestQuestion(); // scrolls to the next required invalid question

			// if have not attempted saving yet, make it true
			!_profile.save_attempted && dispatch( updateProfile({save_attempted: true}) );
		}
	},

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

			}else formData.append(key, val);
		});

		return formData;
	},

	render(){
		let { _profile, route } = this.props;

		return (
			<DocumentTitle title={"Plexuss | College Application | "+route.name}>
				<div className="application-container">

					<form onSubmit={ this._saveAndContinue }>
						<div className="page-head">{route.name} Required</div>

						{ !_profile.init_priority_schools_done && <div className="spin-container"><ReactSpinner color="#24b26b" /></div> }

						{	_.get(_profile, 'req_additional_questions.length', 0) > 0 &&
							_profile.req_additional_questions.map(a => {
								switch(a.field_type){
									case 'text': return <TextField key={a.name} field={a} {...this.props} />;
									case 'radio': return <RadioForm key={a.name} radio={a} {...this.props} />;
									case 'textform': return <TextForm key={a.name} field={a} {...this.props} />;
									case 'select': return <SelectField key={a.name} field={a} {...this.props} />;
									case 'redirect': return <UploadForm key={a.name} field={a} {...this.props} />;
									case 'checkbox': return <CheckboxForm key={a.name} checkb={a} {...this.props} />;
									case 'phone': return <div key={a.name}>{a.label || ''}<PhoneNumberVerifier alternate={a.alternate_name} /></div>;
									case 'date': return <DatePickr 
															key={a.name}
															_label={a.label}
															_side="_left"
															_action={ updateProfile }
															_state={ _profile }
															_format={'YYYY-MM-DD'}
															_name={a.name} />;
								}
							})
						}

						<div>* indicates required questions</div>

						{ ( !_profile[PAGE_DONE] && _profile.save_attempted ) && 
							<div className="field-err">Cannot move on until all of the above required (*) fields have been filled out.</div> }

						<SaveButton 
							_profile={_profile}
							page_done={PAGE_DONE} />
					</form>

				</div>
			</DocumentTitle>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		_profile: state._profile,
	};
};

export default connect(mapStateToProps)(AdditionalInfo);