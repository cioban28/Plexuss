// cmsRankingsReducer.js
import _ from 'lodash'

var init = {
	pins: [],
	activePin: {},
};

export default function(state = init, action) {
    switch(action.type) {

        case 'RANK_PENDING':
        case 'GET_PINS_DONE':
        case 'EDIT_PIN':
        case 'CLEAR_FORM':
        	return Object.assign({}, state, {...action.payload}); 

        case 'SAVE_PIN':
        	var newState = Object.assign({}, state, {pending: action.payload.pending});

        	//if pin id is set, that mean a new pin was just save, so add to list
        	if( action.payload.pin_id ){
        		//add returned pin id to active pin before adding to list - id is used during edit
	        	newState.activePin.id = action.payload.pin_id;
	        	//save active to pins list and push to start of array, then empty it
				newState.pins.unshift( newState.activePin );
        	}else{
        		//else existing pin was edited so update list
        		newState.pins = newState.pins.map((pin) => {
        			if( pin.id === newState.activePin.id ) return Object.assign({}, pin, {...newState.activePin});
        			return pin;
        		});
        	}

	        newState.activePin = {};

        	return newState;

        case 'REMOVE_PIN':
        	var newState = Object.assign({}, state, _.omit(action.payload, 'pin'));

        	newState.pins = _.reject(newState.pins, action.payload.pin);
        	return newState;

        case 'UPDATE_VALUE':
        	var newState = Object.assign({}, state);

        	newState.activePin = Object.assign({}, newState.activePin, {...action.payload});
        	return newState;

        case 'GET_COLLEGE_DATA_DONE':
            return Object.assign({}, state, _.pick(action.payload, 'pending'));

        default:
            return state;
            
    }
}