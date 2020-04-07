import React, {Component} from 'react';

import Edit_section from './profile_edit_section';
import Loader from './../../../utilities/loader';
import Avatar from './../../../utilities/roundPortrait';
import { isEmpty } from 'lodash';

import UploadPicturePreviewModal from './UploadPicturePreviewModal';

import ClickOutside from './../../../utilities/clickOutside';
import Profile_edit_privacy_settings from './profile_edit_privacy_settings'

const CUSTOM_AVATAR_STYLES = {
    width: '151px',
    height: '151px',
    position: 'absolute',
    zIndex: 5,
    top: '-0.65em',
};

export default class Profile_edit_details1 extends Component{
	
	constructor(props){
		super(props);

		let {fname, lname, user_type } = this.props._profile;

		this.state = {
			fname: fname,
			lname: lname,
			user_type: user_type,
			fnameV: true,
			lnameV: true,
			submittable: true,

			openPicturePreviewModal: false,
			openEditPicturePreviewModal: false,
		}

		this._valid = this._valid.bind(this);
		this._save = this._save.bind(this);
		this._cancel = this._cancel.bind(this);
		this.handlePreviewModeRef = this.handlePreviewModeRef.bind(this);

		this.previewModeUploadInput;
	}

	handlePreviewModeRef(ref) {
		if(this.previewModeUploadInput !== ref) {
			this.previewModeUploadInput = ref;
			// if(this.props.autoOpenPicture === true) {
			// 	let click = new MouseEvent('click',{view: window, bubbles: true, cancelable: true});
			// 	setTimeout(() => {
			// 		var canceled = !this.previewModeUploadInput.dispatchEvent(click);
			// 	}, 700);
			// }
		}
	}

	_valid(input, type, fieldV){
		let valid = true;
		let obj = {};		
		obj[fieldV] = true;


		switch(type){
			case 'name':
				if( !(/^[a-zA-Z0-9\.\,\s\-\']+$/g.test(input)) || input === ''){
					valid = false;
					obj[fieldV] = false;
				}
				break;
			case 'number':
				if(!/^[0-9]{4}$/g.test(input)){
					valid = false;
					obj[fieldV] = false;
				}
				break;
			default:
				valid = true;
				break;
		}

		this.setState(obj);
		this.setState({submittable: valid});
	}

	_save(callback){
		let {save}  = this.props;
		let {fname, lname, user_type } = this.state;
		let data = {};

		data.fname = fname;
		data.lname = lname;
        data.user_type = user_type;

		save(data, callback);
	}

	_cancel(){
		let { _profile } = this.props;
		if(this.state.fname !== _profile.fname){ this.setState({fname: _profile.fname}); this._valid(_profile.fname, 'name', 'fnameV'); }
		if(this.state.lname !== _profile.lname){ this.setState({lname: _profile.lname}); this._valid(_profile.lname, 'name', 'lnameV'); }
		if(this.state.user_type !== _profile.user_type){ this.setState({user_type: _profile.user_type}); }
	}

    _previewProfilePicture = (event, editMode) => {
        const newState = {};

        if (event.target.files.length === 0) {
            return;
        }
        if (event.target.files[0].type == "image/png") {
        	var blob = event.target.files[0].slice(0, event.target.files[0].size, 'image/png'); 
			newState['pendingFile'] = new File([blob], 'name.png', {type: 'image/png'});
        }
        else if(event.target.files[0].type == "image/jpeg") {
        	var blob = event.target.files[0].slice(0, event.target.files[0].size, 'image/jpeg'); 
			newState['pendingFile'] = new File([blob], 'name.jpeg', {type: 'image/jpeg'});
        }
        else if(event.target.files[0].type == "image/gif") {
        	var blob = event.target.files[0].slice(0, event.target.files[0].size, 'image/gif'); 
			newState['pendingFile'] = new File([blob], 'name.gif', {type: 'image/gif'});
        }
        else
        {
        	newState['pendingFile'] = event.target.files[0];
        }

        if (editMode) {
            newState['openEditPicturePreviewModal'] = true;
        } else {
            newState['openPicturePreviewModal'] = true;
        }

        this.setState(newState);
    }

    _confirmUpload = () => {
        const callback = () => this.setState({ openEditPicturePreviewModal: false, openPicturePreviewModal: false });
        const { uploadProfilePicture } = this.props;
        const { pendingFile } = this.state;
        const formData = new FormData();

        if (!pendingFile) {
            return;
        }

        formData.append('profile-picture', pendingFile);

        uploadProfilePicture(formData, callback);
    }

	render(){
		let {fname, lname, user_type, showCountries, fnameV, lnameV, submittable, 
			 openPicturePreviewModal, openEditPicturePreviewModal, pendingFile} = this.state;
		let {_profile, findSchools} = this.props;

        const userType = isEmpty(user_type) ? 'Student' : (user_type[0].toUpperCase() + user_type.substr(1));

		return(
			<Edit_section autoOpenEdit={this.props.autoOpenEdit} editable={true} saveHandler={this._save} onCancelEditing={this._cancel} section="basic" submittable={submittable}>

					{/* display section */}
					<div className='basic-student-preview-container'>
                        <div className='upload-avatar-button' onClick={() => this.previewModeUploadInput.click()}>
                            <input type='file' onChange={this._previewProfilePicture} value={undefined} ref={(previewModeUploadInput) => this.handlePreviewModeRef(previewModeUploadInput)} accept="image/x-png,image/gif,image/jpeg" />
                            <Avatar customStyle={CUSTOM_AVATAR_STYLES} url={_profile.profile_img_loc} diameter={110} firstLetter={fname.charAt(0).toUpperCase()}>
		                        <div className="upload-avatar-overlay">
									<img src="/social/images/Subtraction 10.svg" />
								</div>
                            </Avatar>
                        </div>

						<div className="sec1-left">
							<div className="edit-name">{_profile.fname  || ''}&nbsp;{ _profile.lname || ''}
								<Profile_edit_privacy_settings section="basic_info" initial={!!_profile.public_profile_settings ? !!_profile.public_profile_settings.basic_info ? _profile.public_profile_settings.basic_info : null : null}/>
							</div>
							<div className="edit-detail">{userType}</div>
						</div>
						<div className="sec1-right">
						</div>

                        <UploadPicturePreviewModal 
                            toggleModal={() => this.setState({ openPicturePreviewModal: !openPicturePreviewModal })}
                            isOpen={openPicturePreviewModal}
                            file={pendingFile}
                            confirmUpload={this._confirmUpload} />
					</div>

					{/* edit section */}
					<div className='basic-student-edit-container'>
						<div className="edit-mode-basic-student-title green-title">Edit User Information</div>
						<div className="mt30"></div>

						<div className="clearfix">
                            <div className='upload-avatar-button' onClick={() => this.editModeUploadInput.click()}>
                                <input type='file' onChange={(event) => this._previewProfilePicture(event, 1)} value={undefined} ref={(editModeUploadInput) => this.editModeUploadInput = editModeUploadInput} accept="image/x-png,image/gif,image/jpeg" />
                                <Avatar customStyle={CUSTOM_AVATAR_STYLES} url={_profile.profile_img_loc} diameter={110}>
	                                <div className="upload-avatar-overlay">
										<img src="/social/images/Subtraction 10.svg" />
									</div>
	                            </Avatar>
                            </div>
							<div className="left left-side-user-info">
								<div className="edit-label" >First Name</div>
								<input className={fnameV ? '': 'error'} name="fname" value={fname} onChange={(e) => {this.setState({fname: e.target.value}); this._valid(e.target.value, 'name', 'fnameV'); }} placeholder="First Name" />

								<div className="edit-label">Last Name</div>
								<input className={lnameV ? '': 'error'} name="lname" value={lname} onChange={(e) => {this.setState({lname: e.target.value}); this._valid(e.target.value, 'name', 'lnameV');  }} placeholder="Last Name" />

                                <UploadPicturePreviewModal 
                                    toggleModal={() => this.setState({ openEditPicturePreviewModal: !openEditPicturePreviewModal })}
                                    isOpen={openEditPicturePreviewModal}
                                    file={pendingFile}
                                    confirmUpload={this._confirmUpload} />
							</div>

							<div className="right right-side-user-info">
                                <div className="edit-label">I am a(n)...</div>
                                <select name="user_type" value={user_type} onChange={(e) => this.setState({user_type: e.target.value})} placeholder="Type of user...">
                                    <option value="student">Student</option>
                                    <option value="alumni">Alumni</option>
                                    <option value="parent">Parent</option>
                                    <option value="counselor">Counselor</option>
                                    <option value="university_rep">University Rep</option>
                                </select>
							</div>
						</div>
					</div>

			</Edit_section>
		);

	}
}