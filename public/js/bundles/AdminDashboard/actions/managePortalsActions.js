// managePortalsActions.js

import _ from 'lodash'
import axios from 'axios' //lib used to make async calls (no more jQuery!)

export function updatePortal(val, type, hashedid){
	return function(dispatch){

		dispatch({
			type: type.toUpperCase() + '_PORTAL_PENDING', 
			payload: {saving: true}
		}); 

		axios.post('/admin/ajax/createEditPortal', {name: val, type: type, hashedid: hashedid})
			 .then((response) => {
			 	var data = {saving: false, name: val};

			 	//if response is array, then it's a list of 
			 	if( type === 'create' ) data = Object.assign({}, data, {...response.data});

			 	dispatch({
			 		type: type.toUpperCase() + '_PORTAL_DONE', 
			 		payload: data
			 	});
			 })
			 .catch((err) => {
			 	console.log(err);
			 	console.log('is an error');
			 	dispatch({type: type.toUpperCase() + '_PORTAL_ERR', payload: true}); //turn of loader and show error msg
			 });
	}
};


//Make async call here in action creators
export function getPortals(){
	//returning a thunk here, instead of an action
	return function(dispatch){

		dispatch({
			type: 'GET_PORTALS_PENDING', 
			payload: {
				fetching: true, //to turn on loader
				portals: false
			}
		}); 

		axios.get('/admin/getPortals')
			 .then((response) => {
			 	dispatch({
			 		type: 'GET_PORTALS_DONE', 
			 		payload: {
			 			fetching: false, //to turn off loader
			 			initDone: true,
			 			active_portals: response.data.active_portals,
			 			deactivated_portals: response.data.deactivated_portals,
			 			organization_portals: response.data.organization_portals,
			 			current_portal: response.data.default_organization_portal,
			 			default_portal: response.data.default_portal,
			 		}
			 	});
			 })
			 .catch((err) => {
			 	console.log(err);
			 	console.log('is an error');
			 	dispatch({type: 'GET_PORTALS_ERR', payload: true}); //turn of loader and show error msg
			 });
	}
}

// add user to portal
export function addUser(portal, email, users_access){
	return function(dispatch){

		dispatch({
			type: 'ADD_USER_PENDING', 
			payload: true
		}); 

		axios.post('/admin/ajax/addRemoveUsers', {type: 'add', hashedid: portal, email: email, users_access: users_access})
			 .then((response) => {
			 	dispatch({
			 		type: 'ADD_USER_DONE', 
			 		payload: {
			 			saving: false, //to turn off loader
			 			portal: portal,
			 			email: email
			 		}
			 	});
			 })
			 .catch((err) => {
			 	console.log(err);
			 	console.log('is an error');
			 	dispatch({type: 'ADD_USER_ERR', payload: true}); //turn of loader and show error msg
			 });
	}
};

// remove user from portal
export function removeUser(portal, user){
	return function(dispatch) {
		dispatch({
			type: 'REMOVE_USER_PENDING',
			payload: true
		});

		axios.post('/admin/ajax/addRemoveUsers', {type: 'delete', hashedid: portal, hasheduserid: user})
			 .then((response) => {
			 	dispatch({
			 		type: 'REMOVE_USER_DONE',
				 	payload: {
				 		saving: false,
				 		portal: portal,
				 		user: user
				 	}
			 	});
			 })
			 .catch((err) => {
			 	console.log(err);
			 	console.log("is an error");
			 	dispatch({
			 		type: 'REMOVE_USER_ERR',
			 		payload: true
			 	});
			 });
	}
};

export const showSetupModal = (val) => {
	return {
		type: 'TOGGLE_SETUP_MODAL',
		payload: {show: val || false}
	};
};

// set portal
export const setPortal = (portal) => {
	return function(dispatch) {
		dispatch({
			type: 'SET_PORTAL_PENDING',
			payload: {
				saving: true,
			}
		});

		axios.post('/admin/ajax/setOrgnizationPortal', {hashedId: portal.hashedid})
			 .then((response) => {
			 	var data = {saving: false}

			 	// if general portal, pass dispatch different object
			 	if( response.data === 'General' ){
			 		data.portalEntered = true;
			 		data.currentPortal = response.data;
			 	}else{
			 		data.portalEntered = !!response.data.id;
			 		data.currentPortal = response.data;
			 	}

			 	dispatch({
			 		type: 'SET_PORTAL_DONE',
				 	payload: data
			 	});
			 })
			 .catch((err) => {
			 	console.log(err);
			 	console.log("is an error");
			 	dispatch({
			 		type: 'SET_PORTAL_ERR',
			 		payload: {
			 			saving: false
			 		} 
			 	});
			 });
	}
}

export const resetPortalEntered = () => {
	return {
		type: 'RESET_PORTAL_ENTERED',
		payload: {portalEntered: false}
	}
}

export const getPortalsThatHaveMajors = () => {
	return (dispatch) => {
		dispatch({
	 		type: 'PORTAL_W_MAJOR_PENDING',
	 		payload: {portal_w_majors_pending: true},
	 	});

		axios.get('/admin/tools/international/getPortalsForInternationalTab')
			 .then((response) => {
			 	dispatch({
					type: 'INIT_PORTALS_WITH_MAJORS',
					payload: {
						portal_w_majors_pending: false,
						portal_w_majors_init_done: true,
						portals_that_have_majors: response.data,
					},
				});
			 })
			 .catch((err) => {
			 	console.log('error: ', err);
			 	dispatch({
			 		type: 'PORTAL_W_MAJOR_PENDING',
			 		payload: {portal_w_majors_pending: false},
			 	});
			 });
	}
}