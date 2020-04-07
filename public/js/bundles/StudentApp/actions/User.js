// User.js - actions

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

export const getStudentData = () => {
	return (dispatch) => {
		dispatch({
	 		type: '_USER:PENDING',
	 		payload: {
	 			init_pending: false,
	 			init_student_data_pending: true,
	 		},
	 	});

		axios.get('/ajax/getStudentData/')
			 .then((response) => {
			 	dispatch({
					type: '_USER:INIT',
					payload: {
						init_done: true,
						...response.data,
						init_pending: false,
						init_student_data_pending: false,
					}
				});
			 })
			 .catch((err) => {
			 	// console.log('error: ', err);
			 	dispatch({
			 		type: '_USER:PENDING',
			 		payload: {
			 			init_pending: false,
						init_student_data_pending: false,
			 		},
			 	});
			 });
	}
}

export const getTCPAData = () => {
    return (dispatch) => {
        dispatch({
            type: '_USER:PENDING',
            payload: {
                init_pending: true,
                init_tcpa_data_pending: true,
            },
        });

        axios.get('/get_started/getDataFor/tcpa')
             .then((response) => {
                dispatch({
                    type: '_USER:INIT',
                    payload: {
                        init_done: true,
                        ...response.data,
                        init_pending: false,
                        init_tcpa_data_pending: false,
                    }
                });
             })
             .catch((err) => {
                // console.log('error: ', err);
                dispatch({
                    type: '_USER:PENDING',
                    payload: {
                        init_pending: false,
                        init_tcpa_data_pending: false,
                    },
                });
             });
    }
}
export const saveTCPAData = (data) => {
    return (dispatch) => {
        dispatch({
            type: '_USER:PENDING',
            payload: {
                save_tcpa_data_pending: true,
            },
        });

        axios({
            url: '/get_started/save',
            method: 'POST',
            data: data,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        })
        .then((res) => {
            dispatch({
                type: '_USER:PENDING',
                payload: {
                    save_tcpa_data_pending: false,
                },
            });
            window.location.href = '/home';
        })
        .catch((err) => {});
    }
}


export const saveSignupFacebookShare = (user_id, utm_term) => {
    return (dispatch) => {
        axios({
            url: '/ajax/saveSignupFacebookShare',
            method: 'POST',
            data: { user_id, utm_term },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        })
        .then((res) => {
            if (res.data == 'success') {
                dispatch({
                    type: '_USER:HAS_FACEBOOK_SHARED',
                    payload: {
                        has_facebook_shared: true,
                    }
                });

                amplitude.getInstance().logEvent('share sign up link post', { Method: 'facebook share', Location: 'oneapp colleges' });
            }
        })
        .catch((err) => {});
    }
}

export const updateEligibleColleges = (payload) => ({
    type: '_USER:UPDATE_ELIGIBLE_COLLEGES',
    payload
});

// this method is used for plexuss members only!
// used to get _user data for a specific user/student to set overwrite the _user obj with
// and storing the current user's (plexuss user) data in _superUser temporarily
export const getImposterData = (impersonateAs_id, super_user) => {
	return (dispatch) => {
		dispatch({
	 		type: '_USER:IMPOSTER_PENDING',
	 		payload: {init_imposter_pending: true},
	 	});

		axios.get('/ajax/getViewDataController/'+impersonateAs_id)
			 .then((response) => {
			 	dispatch({
					type: '_USER:INIT_IMPOSTER',
					payload: {
						...response.data,
						is_imposter: true,
						init_imposter_done: true,
						init_imposter_pending: false,
						impersonateAs_id,
					},
					super_user,
				});
			 })
			 .catch((err) => {
			 	// console.log('error: ', err);
			 	dispatch({
			 		type: '_USER:IMPOSTER_ERR',
			 		payload: {init_student_data_pending: false},
			 	});
			 });
	}
}