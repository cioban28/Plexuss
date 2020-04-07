// Profile.js - actions

import $ from 'jquery'
import axios from 'axios'
import moment from 'moment'
import _ from 'lodash'

//keep track of fields changed -- for analytics
export const changedFields = (fields) => {

	let humanReadible = fields.replace(/[_]/g, " ");

	return {
		type: '_PROFILE:CHANGED_FIELDS',
		payload: humanReadible
	}	
}

//keep track of fields changed -- for analytics
export const clearChangedFields = () => {
	return (dispatch) => {
		dispatch({
			type: '_PORFILE:CLEAR_CHANGED_FIELDS',
			payload: {changedFields: []}
		})
	}
}

export const updateProfile = (data = {}) => {

	return {
		type: '_PROFILE:UPDATE_DATA',
		payload: data
	}	
}

export const resetSaved = () => {
	return {
		type: '_PROFILE:RESET_SAVED',
		payload: {
			coming_from: '',
			save_error: null,
			save_success: null,
			save_pending: false,
			unqualified_modal: null,
			confirmation_sent_error:false,
			confirmation_sent_success: false,
			verify_confirmation_code_success: false,
		}
	}
}

export const getGPAGradingScales = (country_id) => {
	return (dispatch) => {
		dispatch({
			type: '_PROFILE:INIT_GRADING_SCALES',
			payload: { 
				grading_scales_pending: true,
				country_grading_scales: null,
				gpa_applicant_scale: null,
			},
		});

		axios({
			url: '/ajax/getGPAGradingScales/' + country_id,
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
		})
		.then((response) => {
			dispatch({
				type: '_PROFILE:INIT_GRADING_SCALES',
				payload: {
					country_grading_scales: response.data,
					grading_scales_pending: false,
				}
			});
		});
	}
}

export const convertToUnitedStatesGPA = (gch_id, old_value, conversion_type) => {
	return (dispatch) => {
		dispatch({
			type: '_PROFILE:INIT_GRADING_SCALES',
			payload: { 
				grading_conversion_pending: true,
			},
		});

		axios({
			url: '/ajax/convertToUnitedStatesGPA/' + gch_id + '/' + old_value + '/' + conversion_type,
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
		})
		.then((response) => {
			// Force US GPA to have a absolute minimum at 0.1
			const new_gpa = response.data > 0.1 ? response.data : 0.1;

			// Verify that the user's value is the same as what is being converted before dispatching.
			// This is because the front end is converting on the fly (on change) when a new valid value is entered
			dispatch({
				type: '_PROFILE:VALIDATE_PAGE',
				payload: {
					gpa: new_gpa.toFixed(2),
					grading_conversion_pending: false
				}
			});
		})
		.catch((response) => {
			dispatch({
				type: '_PROFILE:INIT_GRADING_SCALES',
				payload: {
					grading_conversion_pending: false
				}
			});
		});
	}
}

export const getCountries = () => {
	return (dispatch) => {
		
		axios.get('/ajax/getCountriesWithNameId')
			 .then((response) => {
			 	dispatch({
					type: '_PROFILE:INIT_COUNTRIES',
					payload: {
						init_countries_done: true,
						countries_list: response.data,
						init_countries_pending: false,
					}
				});
			 })
			 .catch((err) => {
			 	console.log('error countries: ', err);
			 	dispatch({
			 		type: '_PROFILE:ERR',
			 		payload: {init_countries_pending: false},
			 	});
			 });
	}
}

//////////////////////////////////////////////////////////////////////
// save application sections to database
export const saveApplication = (data, step) => {

	return (dispatch) => {
		dispatch({
	 		type: '_PROFILE:SAVE_PENDING',
	 		payload: {save_pending: true},
	 	});


		//will send form changes to Amplitude -- form changes stored in state
		data["page"] = step
		axios({
			  method: 'post',
			  url: '/ajax/saveCollegeApplication',
			  data: data,
			  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		  	})
			.then((res) => {
			 	var ret = res.data;
			 	var new_data = ret.new_data || {};
			 	var pay = {
					save_pending: false,
					profile_percent: ret.profile_percent,
					save_success: ret.status === 'success',
					save_err_msg: ret.error_msg || '',
					app_last_updated: moment().format('M/D/YYYY @ h:ma'),
					changedFields: [],
					...new_data
				};

			 	dispatch({
					type: '_PROFILE:APPLICATION_SAVED',
					payload: pay 
				});

			})
			.catch((err) => {
			 	console.log('error: ', err);
			 	dispatch({
			 		type: '_PROFILE:APPLICATION_SAVE_ERR',
			 		payload: {
			 			save_error: true,
			 			save_pending: false,
			 		},
			 	});
			 });
	}
}
