// manageUsersActions.js

import axios from 'axios';

//update user from manage users page
export const updateUser = (user, fpUpdated) => {
	return function(dispatch){
		dispatch({
			type: 'UPDATE_USER_PENDING', 
			payload: { pending: true }
		}); 

		axios.post('/admin/updateUsersForManageUsers', {...user})
			 .then((response) => {
			 	dispatch({
			 		type: 'UPDATE_USER_DONE', 
			 		payload: {
			 			updated_user: user,
			 			pending: false,
			 			fp_updated: fpUpdated,
			 			err: response.data === 'success' ? false : response.data,
			 		} 
			 	});
			 })
			 .catch((err) => {
			 	console.log(err);
			 	console.log('is an error');
			 	dispatch({
			 		type: 'UPDATE_USER_ERR', payload: {update_err: true, pending: false}
			 	});
			 });
	}
}

//get all users part of this organization to populate manage users page
export const getUsers = (user) => {
	return function(dispatch){
		dispatch({
			type: 'GET_USERS_PENDING', 
			payload: { pending: true }
		}); 

		axios.get('/admin/getUsers')
			 .then((response) => {
			 	dispatch({
			 		type: 'GET_USERS_DONE', 
			 		payload: {
			 			list: response.data, 
			 			pending: false, 
			 			initDone: true
			 		}
			 	});
			 })
			 .catch((err) => {
			 	console.log(err);
			 	console.log('is an error');
			 	dispatch({
			 		type: 'GET_USERS_ERR', 
			 		payload: {pending: false}
			 	});
			 });
	}
}

export const setAlternateProfile = (user) => {
	user.isAlternate = true;
	return {
		type: 'SET_ALTERNATE_PROFILE',
		payload: {temporaryAlternateProfile: user} 
	};
}

export const addUser = (newUser) => {
	return (dispatch) => {
		dispatch({
			type: 'UPDATE_USER_PENDING', 
			payload: {pending: true}
		});

		axios.post('/admin/ajax/addUserFromManageUser', newUser)
		.then((response) => {
			var data = response.data[0];

			dispatch({
		 		type: 'ADD_USER_TO_ORG_DONE', 
		 		payload: {
		 			pending: false, 
		 			newUserAdded: true,
		 			newUserAddedMsg: data.email + ' has been successfully added and will receive an email with a link to access their portals.',
		 		}
		 	});
		})
		.catch(() => {
			dispatch({
		 		type: 'UPDATE_USER_ERR', 
		 		payload: {
		 			pending: false, 
		 			err_msg: 'There was an error adding this user.'
		 		}
		 	});
		})
	}
}

export const updateNewUsersPortalList = (portal) => {
	return {
		type: 'UPDATE_NEW_USERS_PORTAL_LIST',
		payload: portal 
	}
}

export const resetNewUser = () => {
	return {
		type: 'RESET_NEW_USER',
		payload: {
			newUsersPortals: [],
			newUserAdded: false,
		}
	}
}


