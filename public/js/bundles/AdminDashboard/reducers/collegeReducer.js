// collegeReducer.js

var init = {};

export default (state = init, action) => {

	switch( action.type ){
		case 'GET_COLLEGE_DATA_DONE':
			return Object.assign({}, state, {...action.payload});

		default:
			return state;
	}
	
}