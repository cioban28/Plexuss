// datesActions.js
import axios from 'axios'

export const setRange = (range) => {
	return {
		type: 'SET_DATE_RANGE',
		payload: range
	}
}

export const resetDateSaved = (range) => {
	return {
		type: 'RESET_DATE_SAVED_OR_ERR',
		payload: {saved: false, err: false}
	}
}

export const getDateFor_pickACollege = (dateObjName) => {
	return (dispatch) => {

		dispatch({
			type: 'GET_DATE_FOR_PICKACOLLEGE_PENDING', 
			payload: {date_set_pending: true} 
		}); 

		axios.get('/getDateForPickACollege')
			 .then((response) => {

			 	// initializing dateObjName with dates that have been saved already
			 	var obj = {};

			 	obj[dateObjName] = {};
			 	obj[dateObjName].start_date = response.data.start_goal;
			 	obj[dateObjName].end_date = response.data.end_goal;

			 	dispatch({
			 		type: 'GET_DATE_FOR_PICKACOLLEGE_DONE', 
			 		payload: {
			 			date_set_pending: false,
			 			...obj
			 		}
			 	});
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: 'GET_DATE_FOR_PICKACOLLEGE_ERR', 
			 		payload: {
			 			date_set_pending: false, 
			 			err: true, 
			 			err_msg: err 
			 		}
			 	}); //turn of loader and show error msg
			 });
	}
}

export const setRangeFor_pickACollege = (dates) => {
	return (dispatch) => {

		dispatch({
			type: 'SAVE_DATE_RANGE_PENDING', 
			payload: {date_set_pending: true} 
		}); 

		axios.post('/saveGoalDates', dates)
			 .then((response) => {
			 	dispatch({
			 		type: 'SAVE_DATE_RANGE_DONE', 
			 		payload: {
			 			date_set_pending: false,
			 			saved: true,
			 		}
			 	});
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: 'SAVE_DATE_RANGE_ERR', 
			 		payload: {
			 			date_set_pending: false, 
			 			err: true, 
			 			err_msg: 'There was a problem saving your date range. Check you dates to make sure they are proper dates.'
			 		}
			 	}); //turn of loader and show error msg
			 });
	}
}