// reducers.js

export const profileReducer = (state = {}, action) => {
    console.log('state in profileReducer: ',state);
    console.log('action: ',action);

    switch( action.type ) {

        case 'CHANGE_NAME':
            return 'name';
        
        case 'GET_USERS':
            var newState = Object.assign({}, state, {num: action.num});
            newState.users = action.users;
            return newState;

        case 'INCREMENT':
            console.log('reducer state: ', state);
            console.log('reducer payload: ', action.payload);
            action.payload.num++;
        	return Object.assign({}, state, {num: action.payload.num});

        default:
            return 0;
            
    };
};

export const managePortalReducer = (state = {}, action) => {
    console.log('state in portalReducer: ',state);
    console.log('action: ',action);

    switch( action.type ){

        case 'INCREMENT':
            action.payload.num++;
            return Object.assign({}, state, {num: action.payload.num});

        default: 
            return 0;
    };
};
