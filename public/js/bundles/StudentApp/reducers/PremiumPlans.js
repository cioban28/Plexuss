

export default function(state = initDash, action) {
    var newState = null, arrCopy = null, tmp = null, newList = null, deactivated = null;

    switch(action.type) {

    	case 'GET_DASHBOARD_PENDING':
    	case 'GET_DASHBOARD_DONE':
        case 'GET_DASHBOARD_ERR':
    		return Object.assign({}, state, {...action.payload});
        default:
            return state;
            
    }
}