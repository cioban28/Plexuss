export function changePasswordSuccess(payload){
    return{
        type: "CHANGE_PASSWORD_SUCCESS",
        payload,
    };
}

export function changePasswordFailure(payload){
    return{
        type: "CHANGE_PASSWORD_FAILURE",
        payload,
    };
}

export function getSettingDataSuccess(payload){
    return{
        type: "GET_SETTING_DATA_SUCCESS",
        payload,
    };
}

export function saveUserAccountPrivacySuccess(payload){
    return{
        type: "SAVE_USER_ACCOUNT_PRIVACY_SUCCESS",
        payload,
    };
}
export function saveUserAccountPrivacyData(payload){
    return{
        type: "SAVE_USER_ACCOUNT_PRIVACY_DATA",
        payload,
    };
}

export function saveEmailNotificationSuccess(payload){
    return{
        type: "SAVE_EMAIL_NOTIFICATION_SUCCESS",
        payload,
    };
}

export function saveEmailNotificationFailure(payload){
    return{
        type: "SAVE_EMAIL_NOTIFICATION_FAILURE",
        payload,
    };
}

export function savePhoneNumberSuccess(payload){
    return{
        type: "SAVE_PHONE_SUCCESS",
        payload,
    };
}

export function savePhoneNumberFailure(payload){
    return{
        type: "SAVE_PHONE_FAILURE",
        payload,
    };
}


export function sendCodeSuccess(payload){
    return{
        type: "SEND_CODE_SUCCESS",
        payload,
    };
}

export function sendCodeFailure(payload){
    return{
        type: "SEND_CODE_FAILURE",
        payload,
    };
}

export function validateCodeSuccess(payload){
    return{
        type: "VALIDATE_CODE_SUCCESS",
        payload,
    };
}

export function validateCodeFailure(payload){
    return{
        type: "VALIDATE_CODE_FAILURE",
        payload,
    }
}



export function sendSingleInviteSuccess(payload){
    return{
        type: "SINGLE_INVITE_SUCCESS",
        payload,
    };
}

export function sendSingleInviteFailure(payload){
    return{
        type: "SINGLE_INVITE_FAILURE",
        payload,
    }
}


export function getStoredContactsSuccess(payload){
    return{
        type: "STORED_CONTACTS_SUCCESS",
        payload,
    };
}

export function getStoredContactsFailure(payload){
    return{
        type: "STORED_CONTACTS_FAILURE",
        payload,
    }
}


export function sendMultipleInvitesSuccess(payload){
    return{
        type: "SEND_MULTIPLE_INVITES_SUCCESS",
        payload,
    };
}

export function sendMultipleInvitesFailure(payload){
    return{
        type: "SEND_MULTIPLE_INVITES_FAILURE",
        payload,
    }
}

export function sentInvitationAction(payload){
    return{
        type: "SENT_INVITE_TO_STORED_CONTACT",
        payload,
    }
}

export function sentRequsetAction(payload){
    return{
        type: "SENT_REQUEST_TO_PLEXUSS_MEMBER",
        payload,
    }
}