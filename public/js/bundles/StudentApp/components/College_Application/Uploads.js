// /College_Application/Uploads

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import { browserHistory } from 'react-router'
import DocumentTitle from 'react-document-title'

import SaveButton from './SaveButton'
import SingleUpload from './SingleUpload'
import UploadDocModal from './UploadDocModal'
import CustomModal from './../common/CustomModal'
import _getRequiredRoutes from './../SIC/constants'
import updateReqAppRoutes from './../../actions/Profile'
import { TIPS } from './constants'
import { updateProfile, saveApplicationWithFiles, resetSaved, saveUploadedFiles, clearChangedFields } from './../../actions/Profile'
import ProfileDocuments from './../profile/profile_documents'

var PAGE_DONE = '';

class Uploads extends React.Component {
	constructor(props) {
		super(props)
		this.state = {
			openModal: false,
			tipsModal: false,
			exampleModal: false,
		}
		this._saveAndContinue = this._saveAndContinue.bind(this)
		this._buildFormData = this._buildFormData.bind(this)
		this._onSkip = this._onSkip.bind(this)
	}

	componentWillMount(){
		let { dispatch, route } = this.props;

		PAGE_DONE = route.id+'_form_done';
		dispatch( updateProfile({page: route.id}) );
		dispatch( clearChangedFields());
	}

	componentWillReceiveProps(np){
		let { dispatch, route, _profile } = this.props;

		// after saving, reset saved and go to next route
		if( np._profile.save_success !== _profile.save_success && np._profile.save_success ){
			dispatch( resetSaved() );
			
			if( np._profile.coming_from ) browserHistory.goBack();
			else{
				this.props.location.pathname.includes("social") ? this.props.history.push('/social/one-app/'+route.next + window.location.search) : browserHistory.push('/college-application/'+route.next + window.location.search);
			}
		}
	}		

	_saveAndContinue(e){
		e.preventDefault();
		
		let { dispatch, _profile } = this.props;
		let form = _.omitBy( _profile, (v, k) => k.includes('list') ),
			formData;

		// if page is not valid, dont allow moving on 
		if( !_profile[PAGE_DONE] ) return;

		formData = this._buildFormData(form);

		dispatch( saveApplicationWithFiles(formData, 'uploads', _profile.oneApp_step) );
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

			}else formData.append(key, val);
		});

		return formData;
	}

    _onSkip() {
        const { _profile, route } = this.props;
        const required_route = _.find(_profile.req_app_routes.slice(), {id: route.id});
        
        if(required_route) browserHistory.push('/college-application/'+required_route.next + window.location.search);
    }

	componentWillMount(){
		let { dispatch, route } = this.props;

		PAGE_DONE = route.id+'_form_done';
		dispatch( updateProfile({page: route.id}) );
		dispatch( clearChangedFields());
	}

	componentWillReceiveProps(np){
		let { dispatch, route, _profile } = this.props;

		// after saving, reset saved and go to next route
		if( np._profile.save_success !== _profile.save_success && np._profile.save_success ){
			dispatch( resetSaved() );
			
			if( np._profile.coming_from ) browserHistory.goBack();
			else{
				let required_route = _.find(_profile.req_app_routes.slice(), {id: route.id});
				if(required_route) browserHistory.push('/college-application/'+required_route.next + window.location.search);
			}
		}
	}

	componentDidUpdate(prevProps){
		let { dispatch, route, _profile } = this.props,
			prevTranscripts = prevProps._profile.transcripts,
			form = _.omitBy( _profile, (v, k) => k.includes('list') ),
			formData;
		
		if (prevTranscripts && _profile.transcripts && prevTranscripts.length < _profile.transcripts.length){
			formData = this._buildFormData(form);
			dispatch( saveUploadedFiles(formData) );
		}
	}

	render(){
		let { _profile, route } = this.props,
			{ openModal, tipsModal, exampleModal } = this.state,
			is_duplicate_upload = _profile.is_duplicate_upload != null 
				? _profile.is_duplicate_upload 
				: false;

		return (
			<DocumentTitle title={"Plexuss | College Application | "+route.name}>
				<div className="application-container">

					<form onSubmit={ this._saveAndContinue }>
					<div className="uploads_page_styling"style={{paddingBottom: '12%' }}>
						<ProfileDocuments dispatch={this.props.dispatch} />
					</div>
					</form>	

				</div>
			</DocumentTitle>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		_profile: state._profile,
	};
};

export default connect(mapStateToProps)(Uploads);