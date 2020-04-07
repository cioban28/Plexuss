// User.js - Reducer

import find from 'lodash/find'

var is_prem = document.getElementById('_StudentApp_Component').dataset.premium;

var init = {
	premium_user_type: is_prem,
};
const _ = {
    find: find
}
export default (state = init, action) => {

	switch( action.type ){

		case '_USER:INIT':
		case '_USER:PENDING':
		case '_USER:UPDATE_INFO':
		case '_USER:IMPOSTER_PENDING':
			return {...state, ...action.payload};

        case '_USER:UPDATE_EMAIL_LIST':
            return toggleSelectedInviteEmail(state, action.payload.entry);

		case '_USER:INIT_IMPOSTER':
			return {...action.payload};

        case '_USER:UPDATE_ELIGIBLE_COLLEGES':
        case '_USER:HAS_FACEBOOK_SHARED':
            return handleNumberOfEligibleColleges({...state, ...action.payload});

		default:
			return state;
	}
	
}

const handleNumberOfEligibleColleges = (state) => {
    const newState = {...state};

    let num_of_colleges = 1;

    // Only allow premium 10 colleges if they are also international (not United States)
    if (newState['premium_user_level_1'] === 1 && newState['country_id'] != 1) {
        num_of_colleges = 10;
    } else {
        num_of_colleges = 2;
    }

    newState['num_of_eligible_applied_colleges'] = num_of_colleges;

    return newState;
}

const toggleSelectedInviteEmail = (state, entry) => {
    const newState = { ...state };

    newState.selectedContactList = newState.selectedContactList || [];

    if (!_.find(newState.selectedContactList, { invite_email: entry.invite_email })) {
        // Insert entry
        newState.selectedContactList.push(entry);
    } else {
        // Remove entry
        newState.selectedContactList = newState.selectedContactList.filter((selection) => selection.invite_email !== entry.invite_email);
    }

    return newState;
}