import _ from 'lodash'

export default (state = {}, action) => {

    switch( action.type ){
        case '_USER:UPDATE_INFO':
            return { ...state, ...action.payload };
        case '_USER:UPDATE_EMAIL_LIST':
            return toggleSelectedInviteEmail(state, action.payload.entry);

        default:
            return state;
    }
    
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