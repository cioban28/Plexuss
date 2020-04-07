// Intl_Students.js - actions

import isEmpty from 'lodash/isEmpty'
import forIn from 'lodash/forIn'
import orderBy from 'lodash/orderBy'
import pull from 'lodash/pull'
import axios from 'axios'
// import store from './../../stores/adminStore'
const _ = {
	isEmpty: isEmpty,
	forIn: forIn,
	orderBy: orderBy,
	pull: pull,
}
export const sortCols = (list) => {
	return {
		type: '_INTL:SORT',
		payload: list
	}
}

export const filterCost = (val) => {
	return {
		type: '_INTL:APPLY_FILTER',
		payload: {
			name: 'cost',
			value: val,
		} 
	}
}

export const convertExRate = (rate_item) => {
	console.log('action: ', rate_item);
	return {
		type: '_INTL:CONVERT_EX_RATE',
		payload: {new_conversion_rate: rate_item}
	}
}

export const filterMajors = (val, filteredMajors) => {
	var list;

	if( filteredMajors ){
		if( val ){
			var found = filteredMajors.findIndex(id => val === id);

			// if found, remove from list, else add to list
			if( found > -1 ) list = _.pull(filteredMajors.slice(), val);
			else list = [...filteredMajors, val];
			
		}else list = null; 

	}else list = [val];

	return {
		type: '_INTL:APPLY_FILTER',
		payload: {
			name: 'majors',
			value: list,
		} 
	}
}

export const filterDegrees = (val) => {
	return {
		type: '_INTL:APPLY_FILTER',
		payload: {
			name: 'degree',
			value: val,
		} 
	}
}

export const getPrioritySchools = (params = '') => {
	return (dispatch, getState) => {
		dispatch({
			type: '_INTL:PENDING',
			payload: {init_pending: true, priority_schools_pending: true},
		});

		var _state = getState();

		// if _superUser is set and has impersonateAs_id, for this ajax call, get Priority schools for impersonateAs_id
		if( !_.isEmpty(_state._superUser) && _state._superUser.impersonateAs_id ){
			params = '?user_id='+_state._superUser.impersonateAs_id;
		}

		axios({
			url: '/college-application/getInternationalStudentsAjax'+params,
			type: 'GET',
			headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')}
		})
		.then((response) => {
			dispatch({
				type: '_INTL:INIT',
				payload: {
					init_pending: false,
					priority_schools_pending: false,
					init_done: true,
					list: response.data.undergrad,
					original_state_of_list: response.data.undergrad,
					unqualified_modal: response.data.modal_num || null,
				},
			});
		})
		.catch((err) => {
			console.log('error: ', err);
			dispatch({
				type: '_INTL:ERR',
				payload: {
					init_pending: false,
					priority_schools_pending: false,
					get_priority_schools_err: true,
				},
			});
		});
	}
}

export const getAllMajors = () => {
	return (dispatch) => {
		axios({
			url: '/ajax/getAllMajors',
			type: 'GET',
			headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')}
		})
		.then((response) => {
			dispatch({
				type: '_INTL:INIT_MAJORS',
				payload: {
					init_majors_done: true,
					all_majors: response.data,
				},
			});
		})
		.catch((err) => {
			console.log('error: ', err);
			dispatch({
				type: '_INTL:PENDING',
				payload: {init_pending: false},
			});
		});
	}
}

export const getExchangeRates = () => {
	return (dispatch) => {
		axios.get('https://api.fixer.io/latest?base=USD')
			 .then((response) => {

			 	var _rates = [{name: 'USD', value: null}];

			 	if( response.data.rates ){
			 		_.forIn(response.data.rates, (val, key) => _rates = [..._rates, {name: key, value: +val}] );
			 	}

			 	dispatch({
					type: '_INTL:SET_EX_RATES',
					payload: {
						init_ex_rates_done: true,
						ex_rates: _.orderBy(_rates.slice(), ['name'], ['asc']),
					},
				});
			 })
			 .catch((err) => {
			 	console.log('error: ', err);
			 	dispatch({
			 		type: '_INTL:PENDING',
			 		payload: {init_pending: false},
			 	});
			 });
	}
}