// overviewActions.js

import axios from 'axios'

export const setNewItem = (item) => {
	var _item = item.bg ? {new_img: item} : {new_vid: item};

	return {
		type: 'SET_NEW_OVERVIEW_ITEM',
		payload: _item
	}
}

export const updateContent = (val, name) => {
	return {
		type: 'UPDATE_OVERVIEW_CONTENT',
		payload: {
			name,
			val
		}
	}
}

export const resetSaved = () => {
	return {
		type: 'RESET_SAVED',
		payload: {
			saved: false,
			img_err: false,
			app_link_saved: false,
			app_requirements_saved: false,
		} 
	}
}

export const updateSimpleProp = (obj) => {
	return {
		type: 'OVERVIEW:UPDATE',
		payload: obj
	}
}

export const getOverviewData = () => {
	return (dispatch) => {
		dispatch({
			type: 'PENDING',
			payload: {init_pending: true},
		});

		axios.get('/admin/tools/overview/getOverviewToolsTab')
			 .then((response) => {
				dispatch({
					type: 'GET_OVERVIEW_DATA_DONE',
					payload: {
						init_pending: false,
						init_done: true,
						...response.data.overview,
					},
				});
			 })
			 .catch((err) => {
			 	console.log('error: ', err);
			 	dispatch({
			 		type: 'PENDING',
			 		payload: {pending: false},
			 	});
			 });
	}
}

export const saveOverview = (form, route, item) => {
	return (dispatch) => {
		dispatch({
			type: 'PENDING',
			payload: {pending: true},
		});

		$.ajax({
			url: route,
			type: 'POST',
			data: form,
			enctype: 'multipart/form-data',
			contentType: false,
        	processData: false, 
        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: (response) => {
				var valid_img = response !== 'wrong dimensions';

				var pay = {
					saved: valid_img,
					img_err: !valid_img,
					pending: false,
				};

				// update item with id from response
				if( response.id ){
					item.id = response.id;
					pay.which_list = item.video_id ? 'videos' : 'images';
					pay.which_item = item.video_id ? 'new_vid' : 'new_img';
				}

				// response should return full aws url of new image, so add to item
				if( response.url ) item.url = response.url;

				// add updated item to payload
				pay.item = item;

				dispatch({
					type: 'SAVE_OVERVIEW_ITEM',
					payload: pay,
				});
			},
			error: (err) => {
				console.log('err: ', err);
			}
		});
	}
}

export const removeItem = (item) => {
	return (dispatch) => {
		dispatch({
			type: 'PENDING',
			payload: {pending: true},
		});

		axios.post('/admin/tools/overview/removeOverviewImageVideo', {id: item.id})
			 .then((response) => {
			 	dispatch({
					type: 'REMOVE_OVERVIEW_ITEM',
					payload: {
						item: item,
						saved: true,
						pending: false,
						which_list: item.video_id ? 'videos' : 'images',
					},
				});
			 })
			 .catch((err) => {
			 	console.log('error: ', err);
			 	dispatch({
			 		type: 'PENDING',
			 		payload: {pending: false},
			 	});
			 });
	}
}


/************** below are actions for application link page - using overviewActions.js b/c 
				this file doesn't have too much and application link will only have three actions *************/

export const editAppLink = (url) => {
	return {
		type: 'EDIT_APP_LINK',
		payload: { application_link: url }
	};
}

export const saveAppLink = (application_link) => {
	return (dispatch) => {
		dispatch({
			type: 'APP_LINK_PENDING',
			payload: {app_link_pending: true},
		});

		axios.post('/admin/tools/overview/saveApplicationLink', { application_link })
			 .then((response) => {
			 	dispatch({
					type: 'SAVE_APP_LINK',
					payload: {
						app_link_saved: true,
						app_link_pending: false,
					},
				});
			 })
			 .catch((err) => {
			 	console.log('error: ', err);
			 	dispatch({
			 		type: 'APP_LINK_PENDING',
			 		payload: {app_link_pending: false},
			 	});
			 });
	}
}

export const getAppLink = () => {
	return (dispatch) => {
		dispatch({
			type: 'APP_LINK_PENDING',
			payload: {app_link_pending: true},
		});

		axios.get('/admin/tools/overview/getApplicationLink')
			 .then((response) => {
			 	dispatch({
					type: 'GET_APP_LINK',
					payload: {
						app_link_pending: false,
						app_link_init_done: true,
						application_link: response.data.application_url,
						college_page_url: response.data.college_page_url,
					},
				});
			 })
			 .catch((err) => {
			 	console.log('error: ', err);
			 	dispatch({
			 		type: 'APP_LINK_PENDING',
			 		payload: {app_link_pending: false},
			 	});
			 });
	}
}

export const saveAppRequirements = (form) => {
	return (dispatch) => {
		dispatch({
			type: 'OVERVIEW:SAVE_PENDING',
			payload: {save_app_requirements_pending: true},
		});

		axios.post('/admin/tools/application/saveCollegeApplicationAllowedSection', form)
			 .then((response) => {
			 	dispatch({
					type: 'OVERVIEW:SAVED_APP_REQ',
					payload: {
						app_requirements_saved: true,
						save_app_requirements_pending: false,
					},
				});
			 })
			 .catch((err) => {
			 	console.log('error: ', err);
			 	dispatch({
			 		type: 'OVERVIEW:SAVE_PENDING',
			 		payload: {save_app_requirements_pending: false},
			 	});
			 });
	}
}

export const getAppRequirements = (form) => {
	return (dispatch) => {
		dispatch({
			type: 'OVERVIEW:SAVE_PENDING',
			payload: {init_app_requirements_pending: true},
		});

		axios.get('/admin/tools/application/getCollegeApplicaitonAllowedSection')
			 .then((response) => {
			 	dispatch({
					type: 'OVERVIEW:GET_APP_REQ',
					payload: {
						...response.data,
						app_requirements_init_done: true,
						init_app_requirements_pending: true,
					},
				});
			 })
			 .catch((err) => {
			 	console.log('error: ', err);
			 	dispatch({
			 		type: 'OVERVIEW:SAVE_PENDING',
			 		payload: {init_app_requirements_pending: false},
			 	});
			 });
	}
}