// SuperUser.js - Reducer


var init = {};

export default (state = init, action) => {

	switch( action.type ){

		case '_USER:IMPOSTER_ERR':
		case '_USER:IMPOSTER_PENDING':
			return {...state, ...action.payload};

		case '_USER:INIT_IMPOSTER':
			return {...state, ...action.payload, ...action.super_user};

		default:
			return state;
	}
	
}