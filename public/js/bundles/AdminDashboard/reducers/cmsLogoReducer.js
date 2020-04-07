// cmsLogoReducer.js

import _ from 'lodash'

var init = {};

export default (state = init, action) => {

	switch( action.type ){

		case 'CMS_PENDING':
		case 'SAVE_LOGO_DONE':
		case 'UPLOAD_LOGO':
			return Object.assign({}, state, {...action.payload});

		case 'GET_COLLEGE_DATA_DONE':
			return Object.assign({}, state, _.pick(action.payload, 'pending'));

		default:
			return state;
	}
	
}