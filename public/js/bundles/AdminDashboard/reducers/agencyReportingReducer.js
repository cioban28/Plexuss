var init = {};

export default (state = init, action) => {

	switch( action.type ){

		case 'AGENCY_REPORTING:UPDATE':
			return { ...state, ...action.payload };
		
		default:
			return state;
	}
	
}