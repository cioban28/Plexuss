// asyncActions.js

export const get(dispatch) => {

	dispatch({
		type: 'SETUP_COMPLETED_PENDING', 
		payload: true 
	}); 

	axios.get('/admin/setupCompleted')
		 .then((response) => {
		 	console.log('done with get');
		 	// dispatch({
		 	// 	type: 'SETUP_COMPLETED_DONE', 
		 	// 	payload: true
		 	// });
		 })
		 .catch((err) => {
		 	console.log(err);
		 	// console.log('is an error');
		 	// dispatch({type: 'SETUP_COMPLETED_ERR', payload: true}); //turn of loader and show error msg
		 });
}
