import axios from 'axios'

export const updateUserInfo = (data = {}) => {
    return {
        type: '_USER:UPDATE_INFO',
        payload: data
    }   
}

export const toggleSelectedInviteEmail = (entry) => {
    return {
        type: '_USER:UPDATE_EMAIL_LIST',
        payload: { entry },
    }
}

export const inviteContacts = (selectedContactList) => {
    return (dispatch) => {
        dispatch({
            type: '_USER:UPDATE_INFO',
            payload: {
                inviteContactsPending: true,
            }
        });

        axios({
            url: '/ajax/sendReferralInvitesByQueue',
            method: 'POST',
            data: { people: selectedContactList },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        })
        .then((response) => {
            if (response.data === 'success') {
                dispatch({
                    type: '_USER:UPDATE_INFO',
                    payload: {
                        inviteContactsPending: false,
                        inviteContactsStatus: 'success',
                    }
                })
            }
        })
        .catch((response) => {
            dispatch({
                type: '_USER:UPDATE_INFO',
                payload: {
                    inviteContactsPending: false,
                    inviteContactsStatus: 'fail',
                }
            })
        })
    }
}

// provider is a string and must be either 'Microsoft', 'Google', or 'Yahoo'
export const getEmailContacts = (provider) => {
    if (!/^Microsoft$|^Google$|^Yahoo$/.test(provider)) return;

    let payload = null;

    return (dispatch) => {
        dispatch({
            type: '_USER:UPDATE_INFO',
            payload: {
                getContactsPending: true,
                getContactsStatus: 'none',
            }
        })

        axios({
            url: `/get${provider}Contacts`,
            method: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        })
        .then((response) => {
            if (response.data.url) {
                payload = {
                    getContactsPending: false,
                    getContactsStatus: 'require-signin',
                    getContactsSignInURL: response.data.url,
                }
            } else if (response.data.contact_list) {
                payload = {
                    getContactsPending: false,
                    getContactsStatus: 'success',
                    contact_list: response.data.contact_list,
                }
            } else {
                payload = {
                    getContactsPending: false,
                    getContactsStatus: 'failed',
                }
            }

            dispatch({
                type: '_USER:UPDATE_INFO',
                payload
            });
        })
        .catch((response) => {
            dispatch({
                type: '_USER:UPDATE_INFO',
                payload: {
                    getContactsPending: false,
                    getContactsStatus: 'failed',
                }
            });
        })
    }
}