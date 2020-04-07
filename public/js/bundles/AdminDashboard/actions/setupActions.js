// setupActions.js

import axios from 'axios';

export function nextStep(step){
	return {
		type: 'NEXT_STEP',
		payload: {step: step, resetSaved: false}
	};
};

export function setupCompleted() {
	return function(dispatch){

		dispatch({
			type: 'SETUP_COMPLETED_PENDING', 
			payload: true 
		}); 

		axios.get('/admin/setupCompleted')
			 .then((response) => {
			 	dispatch({
			 		type: 'SETUP_COMPLETED_DONE', 
			 		payload: true
			 	});
			 })
			 .catch((err) => {
			 	console.log(err);
			 	console.log('is an error');
			 	dispatch({type: 'SETUP_COMPLETED_ERR', payload: true}); //turn of loader and show error msg
			 });
	}
};