// managePortalsReducer.js

import _ from 'underscore';

let bg = document.getElementById('AdminDashboard_Component').dataset.bg;

let Portal = function Portal(deactive, name, users, userType, nameTip, editTip, id){
	this.active = 1;
	this.name = name || '';
	this.users = users || [];
	this.super_admin = userType || 0;
	this.hasNameTooltip = nameTip || false;
	this.hasEditTooltip = editTip || false;
    this.deactivatable = deactive === false ? deactive : true;
    this.hashedid = id || null;
};

let initPortal = {
    initDone: false,
    fetching: false,
    savingErr: false,
    fetch_err: false,
    current_portal: {},
    showSetupModal: false,
    deactivated_portals: [],
    organization_portals: [],
    active_portals: [], //[new Portal(false, 'General', [], 1, true, true)],
    randomColor: [],
    netflixBg: bg || null,
};

let defaultVals = {
	editTip: true,
    nameTip: false,
    deactivatable: true
};

let convertToPortalObj = function( arg ){
    let newArr = [];

    if( arg && _.isArray(arg) ){ //if truth, an array, and has more than 0 elements
        _.each(arg, function(obj){
            newArr.push( Object.assign(new Portal(), obj) );
        });

        return newArr;

    }else if( arg && _.isObject(arg) ){ //if truthy and is an object
        return Object.assign(new Portal(), arg);
    }

    return arg; //else just return arg;
};

let setCurrentPortal = function( newActivePortals, portal ){
    let portalFound = null;

    if( portal ){
        let portalCopy = convertToPortalObj( portal );
        portalFound = _.findWhere( newActivePortals, {name: portalCopy.name} );
    }else{
        portalFound = _.findWhere( newActivePortals, {name: 'General'} );
    }

    return Object.assign({}, {...portalFound});
};

export default function(state = initPortal, action) {
    var newState = null, arrCopy = null, tmp = null, newList = null, deactivated = null;

    switch(action.type) {

        case 'GET_PORTALS_PENDING':
            return Object.assign({}, state, {fetching: action.payload.fetching});

        case 'GET_PORTALS_DONE':
            newState = Object.assign({}, state, {fetching: action.payload.fetching, initDone: action.payload.initDone}); //create copy of obj

            newState.active_portals = newState.active_portals.concat( convertToPortalObj(action.payload.active_portals) ); //then add rest to active portals
            newState.deactivated_portals = newState.deactivated_portals.concat( convertToPortalObj( action.payload.deactivated_portals ) ); //set deactivated portals
            newState.organization_portals = newState.organization_portals.concat( convertToPortalObj( action.payload.organization_portals ) ); //set organization portals

            newState.current_portal = setCurrentPortal( newState.active_portals, action.payload.current_portal ); //set current portal if any
            return newState;

        case 'GET_PORTALS_ERR':
            return Object.assign({}, state, {fetch_err: action.payload.fetch_err});
        
        case 'CREATE_PORTAL_DONE':
            var initialUsers = [], portal_id = null
        	newState = Object.assign({}, state, {saving: action.payload.saving}); //make copy of state

        	arrCopy = newState.active_portals.slice(); //make copy of portals list arr

            // if initial users prop is set, add too new portal, else add empty array
            if( action.payload.users ) initialUsers = action.payload.users; 
            if( action.payload.hashedportalid ) portal_id = action.payload.hashedportalid;

            //add new portal to active_portals list
        	arrCopy.push( new Portal(defaultVals.deactivatable, action.payload.name, initialUsers, 'admin', defaultVals.nameTip, defaultVals.editTip, portal_id) );

            // update new state
        	newState.active_portals = arrCopy; //save new portal list to new state
            return newState;

        case 'CREATE_PORTAL_PENDING':
            return Object.assign({}, state, {...action.payload});

        case 'CREATE_PORTAL_ERR':
            return Object.assign({}, state, {saving: !action.payload, savingErr: action.payload});

        case 'DEACTIVATE_PORTAL_PENDING':
            return Object.assign({}, state, {saving: action.payload});

        case 'DEACTIVATE_PORTAL_DONE':
            //make copies of state and active portal list
            newState = Object.assign({}, state, {saving: action.payload.saving});
            arrCopy = newState.active_portals.slice();

            //get deactivated portal and remove it from portal list
            deactivated = _.findWhere(arrCopy, {name: action.payload.name});
            newList = _.reject(arrCopy, deactivated);

            //update portals
            deactivated.active = 0;
            newState.active_portals = newList;
            newState.deactivated_portals.push(deactivated);
            return newState;

        case 'DEACTIVATE_PORTAL_ERR':
            return Object.assign({}, state, {saving: !action.payload, savingErr: action.payload});

        case 'REACTIVATE_PORTAL_PENDING':
            return Object.assign({}, state, {saving: action.payload.saving});

        case 'REACTIVATE_PORTAL_DONE':
            newState = Object.assign({}, state, {saving: action.payload.saving});
            arrCopy = newState.deactivated_portals.slice();

            deactivated = _.findWhere(arrCopy, {name: action.payload.name});
            newList = _.reject(arrCopy, deactivated);

            deactivated.active = 1;
            newState.deactivated_portals = newList;
            newState.active_portals.push(deactivated);

            return newState;

        case 'REACTIVATE_PORTAL_ERR':
            return Object.assign({}, state, {saving: !action.payload});

        case 'REMOVE_PORTAL_PENDING':
            return Object.assign({}, state, {saving: action.payload});

        case 'REMOVE_PORTAL_DONE':
            //make copies of state and deactivated portal list
            newState = Object.assign({}, state, {saving: action.payload.saving});
            arrCopy = newState.deactivated_portals.slice();

            //get deactivated portal and remove it from portal list
            deactivated = _.findWhere(arrCopy, {name: action.payload.name});

            //update deactivated portals
            newState.deactivated_portals = _.reject(arrCopy, deactivated);
            return newState;

        case 'REMOVE_PORTAL_ERR':
            return Object.assign({}, state, {saving: !action.payload, savingErr: action.payload});

    	case 'ADD_USER_PENDING':
            return Object.assign({}, state, {saving: action.payload});

        case 'ADD_USER_DONE':
            newState = Object.assign({}, state, {saving: action.payload.saving});

            // -- does it make sense to move this reducer case to the usersReducer?
            // -- will actually have to make axios post to save new user and have it return email, user_id, and super_admin
            newState.active_portals = newState.active_portals.map(function(portal){
                if( portal.hashedid === action.payload.portal ){
                    portal.users.push({email: action.payload.email});
                }
                return portal;
            });

            return newState;

        case 'ADD_USER_ERR':
            return Object.assign({}, state, {saving: !action.payload, savingErr: action.payload});

        case 'REMOVE_USER_PENDING':
            return Object.assign({}, state, {saving: action.payload});

        case 'REMOVE_USER_DONE':
            newState = Object.assign({}, state, {saving: action.payload.saving});

            newState.active_portals = newState.active_portals.map(function(portal){
                if( portal.hashedid === action.payload.portal ){
                    portal.users = _.reject(portal.users, {user_id: action.payload.user});
                }
                return portal;
            });

            return newState;

        case 'TOGGLE_QUESTION_MODAL':
            return Object.assign({}, state, {showQuestionModal: action.payload.show});

        case 'TOGGLE_SETUP_MODAL':
            return Object.assign({}, state, {showSetupModal: action.payload.show}); 

        case 'SET_PORTAL_DONE':
            var { saving, portalEntered, currentPortal } = action.payload, 
                newState = Object.assign({}, state, {saving: saving, portalEntered: portalEntered}),
                arrCopy = null;

            if( newState.active_portals ) arrCopy = newState.active_portals.slice();

            // if currentPortal is truthy and has a name, find it in our current list of active portals and set it as the current_portal
            if( currentPortal && currentPortal.name ) newState.current_portal = setCurrentPortal( arrCopy, currentPortal );
            else if( currentPortal && currentPortal === 'General' ) newState.current_portal = setCurrentPortal( arrCopy );
            
            return newState;

        case 'SET_PORTAL_PENDING':
        case 'SET_PORTAL_ERR':
        case 'RESET_PORTAL_ENTERED':
            return Object.assign({}, state, {...action.payload});

        default:
            return state;
            
    }
}