// actionTypes.js

import axios from 'axios';

// -- action creators -> functions that return actions
// logic to manipulate data should go here, not in reducer
export function addName(){
	return {
		type: 'ADD_NAME',
		payload: {name: 'Pogba'}
	};
};

export function incrementMe(data){
	return {
		type: 'INCREMENT',
		payload: {
			num: data
		}
	}
};

//example use of middleware
export function fetchTweets(){
	//returning a thunk here, instead of an action
	return function(dispatch){
		console.log(dispatch);

		dispatch({type: 'FETCH_TWEETS_PENDING', payload: 'pending'});
		axios.get('http://somthing')
			 .then((response) => {
			 	dispatch({type: 'FETCH_TWEETS_FULFILLED', payload: response.data});
			 })
			 .catch((err) => {
			 	dispatch({type: 'FETCH_TWEETS_REJECTED', payload: err});
			 });
	}
}