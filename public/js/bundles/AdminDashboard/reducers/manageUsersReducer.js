// manageUsersReducer.js

import _ from 'lodash'

let initUsers = {
    newUsersPortals: [],
};

export default function(state = initUsers, action) {
    switch(action.type) {

        case 'UPDATE_USER_DONE':
            var newState = null, { updated_user, pending, err, fp_updated } = action.payload;

            if( action.payload.err ){
                newState = Object.assign({}, state, {pending: pending, fp_unset_err_msg: err.err_msg}); //copy w/pending and err
            }else{
                newState = Object.assign({}, state, {pending: pending}); //copy w/pending

                newState.list = newState.list.map((user) => {
                                    //if current user is updating frontpage value, change everyone to 0
                                    if( fp_updated ) user = Object.assign({}, user, {show_on_front_page: 0});

                                    //update only the current user
                                    if( user.id === updated_user.id ) user = Object.assign({}, updated_user);

                                    return user;
                                }); 
            }

            return newState; 

        case 'UPDATE_USER_DISPLAY_SETTINGS':
            // same case as profile reducer - except this get triggered from origignal user profile edit page changing display settings
            // and only update users list if we already have users and if it was the frontpage that was edited - dont care about college page
            var newState = Object.assign({}, state);

            if( newState.list && newState.list.length > 0 && action.payload.fp_updated ){
                newState.list = newState.list.map((user) => Object.assign({}, user, {show_on_front_page: 0}) ); 
            }
            
            return newState;

        case 'DELETE_USER_DONE':
            var newState = Object.assign({}, state), { deleted_user } = action.payload;

            if( newState.list && newState.list.length > 0 && action.payload.fp_updated ){
                newState.list = _.reject(newState.list, {id: deleted_user.id});
            }

            return newState;

        case 'UPDATE_USER_PENDING':
        case 'UPDATE_USER_ERR':
        case 'GET_USERS_PENDING':
        case 'GET_USERS_DONE':
        case 'GET_USERS_ERR':
            return Object.assign({}, state, {...action.payload});

        case 'ADD_USER_TO_ORG_DONE':
        case 'RESET_NEW_USER':
            return Object.assign({}, state, {...action.payload});

        case 'UPDATE_NEW_USERS_PORTAL_LIST':
            var newState = Object.assign({}, state), portalExists = null, portalsCopy = null;

            //copy list
            portalsCopy = newState.newUsersPortals.slice();
            //check if user already selected that portal
            portalExists = _.find(portalsCopy, action.payload);

            //list already has this portal id, remove it, else add it
            if( portalExists ) newState.newUsersPortals = _.reject(portalsCopy, action.payload);
            else newState.newUsersPortals = portalsCopy.concat([action.payload]);

            return newState;

        default:
            return state;
            
    }
}