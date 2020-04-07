// internationalActions.js

import _ from 'lodash'
import axios from 'axios'

export const editProgram = (program = {}) => {
	var active = _.findKey(program, v => v);
	
	return {
		type: 'EDIT_PROGRAM',
		payload: {
			program,
			activeProgram: active || '',
		}
	}
}

export const setProgramHeader = (program) => {
	return {
		type: 'SET_PROGRAM_HEADER',
		payload: {activeProgram: program}
	}
}

export const editHeaderInfo = (header) => {
	return {
		type: 'EDIT_HEADER_INFO',
		payload: header
	}
}

export const editTestimonial = (video) => {
	return {
		type: 'EDIT_TESTIMONIAL',
		payload: video
	}
}

export const newTestimonial = (newTestimonial = {}) => {
	return {
		type: 'NEW_TESTIMONIAL',
		payload: {newTestimonial} 
	}
}

export const addTestimonial = (newTestimonial = {}) => {
	return {
		type: 'ADD_TESTIMONIAL',
		payload: newTestimonial
	}
}

export const editActiveReq = (req) => {
	return {
		type: 'EDIT_ACTIVE_REQ',
		payload: req
	}
}

export const editAlum = (alum_data = null) => {
	return {
		type: 'EDIT_ALUM',
		payload: alum_data
	}
}

export const addRemoveIntlDept = ( dept = {} ) => {
	return {
		type: 'ADD_REMOVE_INTL_DEP',
		payload: dept
	}
}

export const addRemoveIntlMajor = ( major_with_dept ) => {
	return {
		type: 'ADD_REMOVE_INTL_MAJOR',
		payload: major_with_dept
	}
}

export const editCampusType = ( dept_or_maj ) => {
	return {
		type: 'EDIT_MAJOR|DEPT_CAMPUS_TYPE',
		payload: dept_or_maj
	}
}

export const setMajorOption = ( option ) => {
	return {
		type: 'SET_MAJOR_SELECTION_OPTION',
		payload: {...option}, 
	}
}

export const resetMajors = () => {
	return {
		type: 'RESET_INTL_MAJORS',
		payload: true, 
	}
}

export const resetSaved = () => {
	return {
		type: 'RESET_SAVED',
		payload: {
			saved: false,
			removed: false,
		} 
	}
}

export const editNote = (note) => {
	return {
		type: 'EDIT_NOTES',
		payload: note 
	}
}

export const saveIntlData = (form, DONE_ACTION = 'SAVE_INTL_DONE') => {
	return (dispatch) => {
		dispatch({
			type: 'PENDING',
			payload: {pending: true},
		});

		axios.post('/admin/tools/international/save', form)
			 .then((response) => {
			 	dispatch({
					type: DONE_ACTION,
					payload: {
						saved: true,
						pending: false,
						page: form.page_name,
						response: response.data,
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

export const removeItem = (item, removeRoute, DONE_ACTION = 'SAVE_INTL_DONE') => {
	return (dispatch) => {
		dispatch({
			type: 'PENDING',
			payload: {pending: true},
		});

		axios.post(removeRoute, {id: item.id})
			 .then((response) => {
			 	dispatch({
					type: DONE_ACTION,
					payload: {
						item: item,
						saved: true,
						pending: false,
						response: response.data,
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

export const saveIntlDataWithFile = (form, DONE_ACTION, obj) => {
	return (dispatch) => {
		dispatch({
			type: 'PENDING',
			payload: {pending: true},
		});

		$.ajax({
			url: '/admin/tools/international/save',
			type: 'POST',
			data: form,
			enctype: 'multipart/form-data',
			contentType: false,
        	processData: false, 
        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: (response) => {
				dispatch({
					type: DONE_ACTION,
					payload: {
						pending: false,
						saved: true,
						...obj,
						response: response,
					},
				});
			},
			error: (err) => {
				console.log('err: ', err);
			}
		});
	}
}

export const getAllDepts = () => {
	return (dispatch) => {
		axios.get('/ajax/getAllDepts')
			 .then((response) => {
			 	dispatch({
					type: 'GET_ALL_DEPTS_DONE',
					payload: {
						pending: false,
						all_depts: response.data,
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

export const getAllIntlData = () => {
	return (dispatch) => {
		axios.get('/admin/tools/getCollegeInternationalTab')
			 .then((response) => {
			 	dispatch({
					type: 'GET_ALL_INTL_DATA',
					payload: {
						pending: false,
						init_done: true,
						data: response.data,
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

export const getMajorsFor = ( dept_id ) => {
	return (dispatch) => {
		axios.get('/ajax/getMajorByDepartmentWithNamesAndIds/'+dept_id)
			 .then((response) => {

			 	var pay = {pending: false};
			 	pay['init_done_for_dept_'+dept_id] = true;
			 	pay['all_majors_for_dept_'+dept_id] = response.data;

			 	dispatch({
					type: 'GET_MAJOR_FOR_THIS_DEPT',
					payload: pay,
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

export const initMajorsData = () => {
	return (dispatch) => {
		axios.get('/admin/tools/international/getInternationalMajorDegreeTab')
			 .then((response) => {
			 	dispatch({
					type: 'INIT_INTL_MAJORS_DATA',
					payload: {
						pending: false,
						data: response.data,
						major_data_init_done: true,
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

export const importMajors = (crf_id) => {
	return (dispatch) => {
		dispatch({
			type: 'PORTAL_W_MAJOR_PENDING',
			payload: {import_majors_pending: true},
		});

		axios.post('/admin/tools/international/importMajorsFromTargetting', {crf_id: crf_id})
			 .then((response) => {
			 	dispatch({
					type: 'INIT_INTL_MAJORS_DATA',
					payload: {
						data: response.data.data,
						import_majors_pending: false,
					},
				});
			 })
			 .catch((err) => {
			 	console.log('error: ', err);
			 	dispatch({
			 		type: 'PORTAL_W_MAJOR_PENDING',
			 		payload: {import_majors_pending: false},
			 	});
			 });
	}
}