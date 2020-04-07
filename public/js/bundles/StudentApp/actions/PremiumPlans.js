

export function getPlansData(){
	return function(dispatch){

		dispatch({
			type: 'GET_PLANS_PENDING', 
			payload: {fetching: true}
		}); 

		axios.get('/admin/getPlansData')
			 .then((response) => {
			 	dispatch({
			 		type: 'GET_PLANS_DONE', 
			 		payload: {...response.data, fetching: false}
			 	});
			 })
			 .catch((err) => {
			 	console.log(err);
			 	console.log('is an error');
			 	dispatch({
			 		type: 'GET_PLAN_ERR', 
			 		payload: {
			 			fetching: false,
			 			errmsg: 'There was an error fetching the data'
			 		} 
			 	}); //turn of loader and show error msg
			 });
	}
};