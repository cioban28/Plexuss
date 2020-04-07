// overviewActions.js

import axios from 'axios'

export const setCostValues = (val) => {
	return {
		type: 'COST:SET_FIELD_VALS',
		payload: val
	}
}

export const resetSaved = () => {
	return {
		type: 'COST:RESET_SAVED',
		payload: {saved: false} 
	}
}

export const getTuitionCostData = () => {
	return (dispatch) => {
		dispatch({
			type: 'COST:PENDING',
			payload: {pending: true},
		});

		axios.get('/admin/tools/getInternatioanlTuitionCosts')
			 .then((response) => {
			 	dispatch({
					type: 'COST:INIT_DATA',
					payload: {
						pending: false,
						init_done: true,
						...response.data,
					},
				});
			 })
			 .catch((err) => {
			 	console.log('error: ', err);
			 	dispatch({
			 		type: 'COST:PENDING',
			 		payload: {pending: false},
			 	});
			 });
	}
}

export const saveTuitionCost = (form) => {
	return (dispatch) => {
		dispatch({
			type: 'COST:PENDING',
			payload: {pending: true},
		});

		axios.post('/admin/tools/internationalTuitionCostSave', form)
			 .then((response) => {
			 	dispatch({
					type: 'COST:SAVE',
					payload: {
						saved: true,
						pending: false,
					},
				});
			 })
			 .catch((err) => {
			 	console.log('error: ', err);
			 	dispatch({
			 		type: 'COST:PENDING',
			 		payload: {pending: false},
			 	});
			 });
	}
}

export const saveTuitionCostWithFiles = (form) => {
	return (dispatch) => {
		dispatch({
	 		type: 'COST:PENDING',
	 		payload: {save_pending: true},
	 	});

	 	$.ajax({
			url: '/admin/tools/internationalTuitionCostSave',
			type: 'POST',
			data: form,
			enctype: 'multipart/form-data',
			contentType: false,
        	processData: false,
        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        	success: (data) => {
				dispatch({
					type: 'COST:SAVE',
					payload: {
						saved: true,
						save_pending: false,
					}
				});
        	},
        	error: (err) => {
				dispatch({
			 		type: 'COST:PENDING',
			 		payload: {
			 			save_error: true,
			 			save_pending: false,
			 		},
			 	});
        	}
		});
	}
}