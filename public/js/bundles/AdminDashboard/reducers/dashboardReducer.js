// dashboardReducer.js

import { VERIFIED, RECRUITMENT, COMMUNICATION, STATS } from './../components/Dashboard/constants'

let initDash = {
    startToSchedule : 0, 
    startGoalSetting: 0,
    activeMeter: 'Monthly',
    statsBlocks: [...STATS],
    verifiedBlocks: [...VERIFIED],
    recruitmentBlocks: [...RECRUITMENT],
    communicationBlocks: [...COMMUNICATION],
};

export default function(state = initDash, action) {

    switch(action.type) {

        case 'SET_GOAL_DONE':
        case 'START_SCHEDULE':
        case 'SET_GOAL_METER':
        case 'GET_DASHBOARD_ERR':
        case 'START_GOAL_SETTING':
        case 'GET_DASHBOARD_DONE':
        case '_DASH:INIT_STATS_ERR':
        case 'GET_DASHBOARD_PENDING':
        case 'RESET_CANCELTOKENLIST':
        case 'UPDATE_DASHBOARD_DATA':
    		return {...state, ...action.payload};

        case 'TOGGLE_INTERESTED_PREMIUM_SERVICE':
        var newState = toggleInterestedPremiumService(state, action.payload.service);
            return newState;

        case 'SET_APPOINTMENT_PENDING':
        case 'SET_APPOINTMENT_DONE':
        case 'SET_APPOINTMENT_ERR':
        case 'SET_GOAL_PENDING':
        case 'SET_GOAL_ERR':
            return {...state, fetching: action.payload};

        case '_DASH:INIT_STATS_PENDING':
            var { cancelToken } = action.payload;

            return {
                ...state,
                ..._.omit(action.payload, 'cancelToken'),
                cancelTokenList: [...(state.cancelTokenList || []), cancelToken],
            }

        case 'DISMISSED_SLIDE':
            var newState = {...state};
            newState.announcements = _.reject(newState.announcements, action.payload);
            return newState;

        case '_DASH:INIT_STATS_DONE':
            var newState = {...state, ..._.omit(action.payload, ['data'])},
                data = action.payload.data;

            if( data.block ){
                if( data.list ){
                    var list = data.list+'Blocks';
                    newState[list] = newState[list].map(l => l.name === data.block ? {...l, ...data.stats} : l);

                }else newState = {...newState, ...data.stats};
            }

            return newState;

        default:
            return state;
            
    }

}

const toggleInterestedPremiumService = (state, service) => {
    let newState = {...state},
        indexOfService = null;

    newState.interestedPremiumServices = newState.interestedPremiumServices || [],

    indexOfService = newState.interestedPremiumServices.indexOf(service);

    if (indexOfService === -1) {
        newState.interestedPremiumServices.push(service);
    } else {
        newState.interestedPremiumServices.splice(indexOfService, 1);
    }

    return newState;
}