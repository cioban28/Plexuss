// profileActions.js

import axios from 'axios'
import $ from 'jquery'

export function edit(val, fieldName){
	switch(fieldName){
		case 'fname': return editFname(val);
		case 'lname': return editLname(val);
		case 'title': return editTitle(val);
		case 'working_since': return editWorkingSince(val);
		case 'department': return editDepartment(val);
		case 'added_department': return addDepartment(val);
		case 'email': return editEmail(val);
		case 'role': return editRole(val);
		case 'portal': return editPortal(val);
		case 'permissions': return editPermissions(val);
		case 'blurb': return editBlurb(val);
		case 'pic': return editProfilePic(val);
		case 'avatar': return editAvatar(val);
		case 'avatarChosen': return avatarChosen(val);
		case 'toggleProfileAvatarModal': return toggleProfileAvatarModal(val);
		case 'addDeptInputVisible': return addDeptInputVisible(val);
		case 'alternateProfile': return updateAlternateProfile(val);
		default: return new Error('no fieldname passed');
	}
}

export const updateAlternateProfile = (obj) => {
	return {
		type: 'EDIT_ALTERNATE_PROFILE',
		payload: obj
	}
}

export const removeAlternateProfile = () => {
	return {
		type: 'REMOVE_ALTERNATE_PROFILE',
		payload: {temporaryAlternateProfile: null}
	}
}

export const commonActionCreator = (ACTION_NAME, val) => {
	return {
		type: ACTION_NAME,
		payload: val
	}
}

export function editFname(val){
	return {
		type: 'EDIT_FNAME',
		payload: {fname: val}
	};
};

export function editLname(val){
	return {
		type: 'EDIT_LNAME',
		payload: {lname: val}
	};
};

export function editTitle(val){
	return {
		type: 'EDIT_TITLE',
		payload: {title: val}
	};
};

export function editWorkingSince(val){
	return {
		type: 'EDIT_WORKING_SINCE',
		payload: {working_since: val}
	};
};

export function editDepartment(val){
	return {
		type: 'EDIT_DEPARTMENT',
		payload: {department: val}
	};
};

export function addDepartment(val){
	return {
		type: 'ADD_DEPARTMENT',
		payload: val
	};
};

export function addDeptInputVisible(val){
	return {
		type: 'ADD_DEPT_INPUT_VISIBLE',
		payload: val
	};
};

export function editEmail(val){
	return {
		type: 'EDIT_EMAIL',
		payload: {email: val}
	};
};

export function editRole(val){
	return {
		type: 'EDIT_ROLE',
		payload: {role: val}
	};
};

export function editPortals(val){
	return {
		type: 'EDIT_PORTALS',
		payload: {portals: val}
	};
}

export function editPermissions(val){
	return {
		type: 'EDIT_PERMISSIONS',
		payload: {permissions: val}
	};
}

export function editBlurb(val){
	return {
		type: 'EDIT_BLURB',
		payload: {blurb: val}
	};
}

export function editProfilePic(file){
	return {
		type: 'EDIT_PROFILE_PIC',
		payload: {
			picFile: file.picFile,
			picObjectURL: URL.createObjectURL(file.picFile),
			useAvatar: false,
			profilePicModalOpen: false,
			avatarModalOpen: false,
			forAlternateProfile: file.alternate,
		} 
	}
}

export const editAvatar = (val) => {
	return {
		type: 'EDIT_AVATAR',
		payload: {
			avatar_url: val.avatar_url,
			forAlternateProfile: val.alternate
		}
	};
}

export const avatarChosen = (val) => {
	return {
		type: 'AVATAR_CHOSEN',
		payload: {
			useAvatar: val.useAvatar,
			profilePicModalOpen: false,
			avatarModalOpen: false,
			forAlternateProfile: val.alternate
		}
	}
}

export const toggleProfileAvatarModal = (val) => {
	return {
		type: 'TOGGLE_PROFILE_AVATAR_MODAL',
		payload: {
			profilePicModalOpen: val.pro,
			avatarModalOpen: val.avatar,
			forAlternateProfile: val.alternate
		} 
	}
} 

export function getProfile(){
	return function(dispatch){
		dispatch({
			type: 'GET_PROFILE_PENDING', 
			payload: true
		});

	 	var representative_type = window.location.href.includes('admin') ? 'admin' : 'agency'; 

		axios.get('/' + representative_type + '/getProfile')
			 .then((response) => {
			 	dispatch({
			 		type: 'GET_PROFILE_DONE', 
			 		payload: response.data
			 	});

			 	return response;
			 })
			 .catch((err) => {
			 	console.log(err);
			 	console.log('is an error');
			 	dispatch({type: 'GET_PROFILE_ERR', payload: true}); //turn of loader and show error msg
			 });
	}
}

export function saveProfile(form){

	//returning a thunk here, instead of an action
	return function(dispatch){

		dispatch({
			type: 'SAVE_PROFILE_PENDING', 
			payload: {pending: true}
		}); 

		$.ajax({
			url: '/admin/saveProfile',
			type: 'POST',
			data: form,
			enctype: 'multipart/form-data',
			contentType: false,
        	processData: false, 
        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        	success: (ret) => {
        		var properlySaved = '';
        		if( ret === 'success' ) properlySaved = true;
        		else properlySaved = !ret.email_exists;

        		dispatch({
			 		type: 'SAVE_PROFILE_DONE', 
			 		payload: {
			 			pending: false,
			 			saved: properlySaved,
			 			err_msg: ret.err_msg
			 		} 
			 	});
        	},
        	error: (err) => {
        		console.log(err);
			 	dispatch({
			 		type: 'SAVE_PROFILE_ERR',
			 		payload: {
			 			pending: true,
			 			saveErr: true
			 		}
			 	}); //turn of loader and show error msg
        	}
		});
	}
};

//fetch profile info
const fetchProfile = () => {
	return axios('/admin/getProfile');
}

//execute fetchProfile and handle return
const initProfileInfo = () => {
	return (dispatch) => {
		return fetchProfile().then(
			res => console.log('this is response in thunk: ', res),
			err => console.log('this is error in thunk: ', err)
		);
	}
}

// run initProfile then synchronously execute second function
export const getProfileAndMore = ( secondAJAXFunction ) => {
	return (dispatch, getState) => {
		return dispatch(
			initProfileInfo()
		).then(() => {
			secondAJAXFunction()
		});
	}
}

// update role and show on frontpage/collegepage
export const updateUserDisplaySettings = (user, fpUpdated) => {
	return function(dispatch){
		dispatch({
			type: 'UPDATE_PROFILE_PENDING', 
			payload: { pending: true, isAlternate: user.isAlternate }
		}); 

		axios.post('/admin/updateUsersForManageUsers', {...user})
			 .then((response) => {
			 	dispatch({
			 		type: 'UPDATE_USER_DISPLAY_SETTINGS', 
			 		payload: {
			 			pending: false,
			 			updated_user: user,
			 			fp_updated: fpUpdated,
			 			err: response.data === 'success' ? false : response.data,
			 		} 
			 	});
			 })
			 .catch((err) => {
			 	console.log(err);
			 	console.log('is an error');
			 	dispatch({
			 		type: 'UPDATE_PROFILE_ERR', 
			 		payload: {
			 			err: true, 
			 			pending: false, 
			 			isAlternate: user.isAlternate,
			 			err_msg: 'There was an error updating your display settings for ' + user.fname + ' ' + user.lname,
			 		}
			 	});
			 });
	}
}

// delete a user - acct preserved, but makes user no longer part of org
export const deleteUser = (user) => {
	return function(dispatch){
		dispatch({
			type: 'UPDATE_PROFILE_PENDING', 
			payload: { pending: true, isAlternate: user.isAlternate }
		}); 

		axios.post('/admin/ajax/addRemoveUsers', {
				type: 'delete',
				hashedid: user.hashedid,
				hasheduserid: user.hasheduserid,
			})
			.then((response) => {
			 	dispatch({
			 		type: 'DELETE_USER_DONE', 
			 		payload: {
			 			deleted: true,
			 			pending: false,
			 			deleted_user: user,
			 		} 
			 	});
			})
			.catch((err) => {
			 	console.log(err);
			 	console.log('is an error');
			 	dispatch({
			 		type: 'UPDATE_PROFILE_ERR', 
			 		payload: {
			 			err: true, 
			 			pending: false, 
			 			isAlternate: user.isAlternate,
			 			err_msg: 'There was an error deleting this user ' + user.fname + ' ' + user.lname,
			 		}
			 	});
			});
	}
}

export const resetSaved = () => {
	return {
		type: 'RESET_SAVED',
		payload: {
			saved: false
		}
	}
}