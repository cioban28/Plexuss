// cmsRankingsActions.js

import _ from 'lodash'
import axios from 'axios'
import $ from 'jquery'

//save ranking pin
export const savePin = (form) => {
	return (dispatch) => {
		dispatch({
			type: 'RANK_PENDING', 
			payload: {pending: true} 
		}); 

		$.ajax({
			url: '/admin/ajax/saveRankingPin',
			type: 'POST',
			data: form, 
			enctype: 'multipart/form-data',
			contentType: false,
        	processData: false,
        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        	success: (data) => {
        		dispatch({
			 		type: 'SAVE_PIN', 
			 		payload: {
			 			pin_id: _.isNumber(+data) ? +data : null,
			 			pending: false,
			 		} 
			 	});
        	},
        	error: (err) => {
        		console.log('error: ', err);
        		dispatch({type: 'RANK_PENDING', payload: {pending: false}});
        	}
		});
	}	
}

// get ranking pins
export const getPins = () => {
	return (dispatch) => {
		dispatch({
			type: 'RANK_PENDING', 
			payload: {pending: true}
		});

		axios.get('/admin/ajax/getSavedRankingPins')
			 .then((response) => {
			 	dispatch({
			 		type: 'GET_PINS_DONE', 
			 		payload: {
			 			pending: false,
			 			pins: response.data && _.isArray(response.data) ? response.data : [],
			 			slug: _.isArray(response.data) && response.data.length > 0 ? response.data[0].slug : null,
			 		} 
			 	});
			 })
			 .catch((err) => {
			 	console.log(err);
			 	dispatch({type: 'RANK_PENDING', payload: {pending: false}}); //turn of loader and show error msg
			 });
	} 
}

//edit pin - making selected pin active
export const editPin = (pin) => {
	return {
		type: 'EDIT_PIN',
		payload: { 
			activePin: pin,
		}
	}
}

//remove pin
export const removePin = (pin) => {
	return (dispatch) => {
		dispatch({
			type: 'RANK_PENDING', 
			payload: {pending: true}
		});

		axios.post('/admin/ajax/removeRankingPin', pin)
			 .then((response) => {
			 	dispatch({
					type: 'REMOVE_PIN',
					payload: { 
						pin,
						activePin: {},
						pending: false,
					}
				});
			 })
			 .catch((err) => {
			 	console.log(err);
			 	dispatch({type: 'RANK_PENDING', payload: {pending: false}});
			 });
	}
}

//clears form
export const clearForm = () => {
	return {
		type: 'CLEAR_FORM',
		payload: { 
			activePin: {},
		}
	}
}

//update value on change
export const updateValue = (fieldAndValue) => {
	return {
		type: 'UPDATE_VALUE',
		payload: fieldAndValue
	}
}