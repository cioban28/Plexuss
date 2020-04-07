// asyncActions.js
import axios from 'axios'

export asyncGET = (dispatch, route, actionType, pending, success, error) => {
	return (dispatch) => {
		dispatch(pending);

		axios.get(route)
			 .then((response) => {
			 	success.payload = response.data;
				dispatch(success);
			 })
			 .catch((err) => {
				dispatch(error);
			 });
	}
}

export asyncPOST = (dispatch, route, data, actionType, pending, success, error) => {
	return (dispatch) => {
		dispatch(pending);

		axios.post(route, data)
			 .then((response) => {
			 	success.payload = response.data;
				dispatch(success);
			 })
			 .catch((err) => {
				dispatch(error);
			 });
	}
}