// adminApi.js

import axios from 'axios';
import logger from 'redux-logger';

var actions = {
	profileData: {
		type: 'GET_USERS',
        users: response.data
	}
};

export function getUsers() {
    console.log(' in axios api file');
    // return axios.get('http://localhost:3000/users').then(function(response) {

    //     store.dispatch(profileData);

    //     return response;

    // }).catch(function(err) {
    //     console.error(err);
    // });
}