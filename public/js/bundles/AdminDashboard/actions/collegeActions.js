// collegeActions.js

import axios from 'axios'

export const getCollegeData = () => {
	return (dispatch) => {
		dispatch({
	 		type: 'CMS_PENDING',
	 		payload: {pending: true}
	 	});

		axios.get('/admin/ajax/getSchoolData')
			 .then((response) => {
			 	dispatch({
			 		type: 'GET_COLLEGE_DATA_DONE',
			 		payload: {
			 			pending: false,
			 			initDone: true,
			 			...response.data,
			 		}
			 	});
			 })
			 .catch((err) => {
				dispatch({
			 		type: 'PENDING',
			 		payload: {pending: false}
			 	});
			 });
	}
	
}