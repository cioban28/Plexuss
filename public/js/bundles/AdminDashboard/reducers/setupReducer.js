// setupReducer.js
let setupCompleted = document.getElementById('AdminDashboard_Component').dataset.setupcompleted;

let setupInit = {
	currentStep: 1,
	pending: false,
	setupErr: false,
	completed: +setupCompleted 
};

export default function( state = setupInit, action ){
	var newState = null;

	switch( action.type ){
		case 'NEXT_STEP': //Same reducer case in ProfileReducer, so will be called during same dispatch
			return Object.assign({}, state, {currentStep: action.payload.step});

		case 'SETUP_COMPLETED_DONE':
			return Object.assign({}, state, {completed: action.payload, pending: !action.payload});

		case 'SETUP_COMPLETED_PENDING':
			return Object.assign({}, state, {pending: action.payload});

		case 'SETUP_COMPLETED_ERR':
			return Object.assign({}, state, {setupErr: action.payload, pending: !action.payload});

		default: return state;
	}
}