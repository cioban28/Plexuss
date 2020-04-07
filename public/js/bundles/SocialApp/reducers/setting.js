const initialState={
    setting:{
        errorMsg: '',
        passNotMatch: '',
        successMsg: '',
        setting_notification: {
            email: '',
            text: '',
        },
        phone: '+1',
        phone_without_calling_code:'+1',
        savePhone: '',
        savePhoneCount: 0,
        verifiedPhone: 0,
        validateCode: {
            response: '',
            errorMessage: '',
        },
        sendCode: {
            response: '',
            errorMessage: '',
        },
        singleInvite: {
            response: '',
            msg: '',
        },
        plexussMembers: [],
        storedContacts: [],
        hasMoreContacts: true,
        offset: 0,
        sendMultipleInvites:{
            response: '',
        }

    }
}
const setting = (state = initialState, action) => {
    switch(action.type){
        case "CHANGE_PASSWORD_SUCCESS":
            let setting = Object.assign({}, state.setting)
            setting.errorMsg = action.payload.error_msg;
            setting.passNotMatch = action.payload.pass_not_match;
            setting.successMsg = action.payload.success_msg;
            return {...state, setting: {...setting}};
        case "CHANGE_PASSWORD_FAILURE":
            setting = Object.assign({}, state.setting)
            setting.errorMsg = action.payload.error_msg;
            setting.passNotMatch = action.payload.pass_not_match;
            setting.successMsg = action.payload.success_msg;
            return {...state, setting: {...setting}};
        case "GET_SETTING_DATA_SUCCESS":
            setting = Object.assign({}, state.setting)
            setting.account_settings = action.payload.account_settings;
            setting.setting_notification = action.payload.setting_notification;
            setting.phone = action.payload.phone;
            setting.phone_without_calling_code = action.payload.phone_without_calling_code;
            setting.verifiedPhone = action.payload.verified_phone;
            return {...state, setting: {...setting}};
        case "SAVE_USER_ACCOUNT_PRIVACY_SUCCESS":
            setting = Object.assign({}, state.setting)
            setting.account_settings = action.payload.account_settings;
            return {...state, setting: {...setting}};
        case "SAVE_EMAIL_NOTIFICATION_SUCCESS":
            setting = Object.assign({}, state.setting)
            setting.setting_notification = action.payload.setting_notification;
            return {...state, setting: {...setting}};
        case "SAVE_EMAIL_NOTIFICATION_FAILURE":
            setting = Object.assign({}, state.setting)
            setting.setting_notification = action.payload.setting_notification;
            return {...state, setting: {...setting}};
        case "SAVE_PHONE_SUCCESS":
            setting = Object.assign({}, state.setting)
            setting.savePhone = action.payload;
            setting.savePhoneCount +=1;
            return {...state, setting: {...setting}};
        case "SAVE_PHONE_FAILURE":
            setting = Object.assign({}, state.setting)
            setting.savePhone = action.payload;
            return {...state, setting: {...setting}};

        case "SEND_CODE_SUCCESS":
            setting = Object.assign({}, state.setting)
            setting.sendCode.response = action.payload.response;
            return {...state, setting: {...setting}};
        case "SEND_CODE_FAILURE":
            setting = Object.assign({}, state.setting);
            setting.sendCode.response = action.payload.response;
            setting.sendCode.errorMessage = action.payload.error_message;
            return {...state, setting: {...setting}};

        case "VALIDATE_CODE_SUCCESS":
            setting = Object.assign({}, state.setting)
            setting.validateCode.response = action.payload.response;
            setting.verifiedPhone = 1;
            return {...state, setting: {...setting}};
        case "VALIDATE_CODE_FAILURE":
            setting = Object.assign({}, state.setting)
            setting.validateCode.response = action.payload.response;
            setting.validateCode.errorMessage = action.payload.error_message;
            return {...state, setting: {...setting}};

        case "SINGLE_INVITE_SUCCESS":
            let setting1 = Object.assign({}, state.setting)
            setting1.singleInvite.msg = 'Invitation has been sent!';
            setting1.singleInvite.response = action.payload;
            return {...state, setting: {...setting1}};
        case "SINGLE_INVITE_FAILURE":
            let setting2 = Object.assign({}, state.setting)
            setting2.singleInvite.msg = 'Oops. Looks like something went wrong. Please try again.';
            setting2.singleInvite.response = action.payload;
            return {...state, setting: {...setting2}};


        case "STORED_CONTACTS_SUCCESS":
            setting = Object.assign({}, state.setting)
            setting.storedContacts = [...setting.storedContacts , ...action.payload.users_invites];
            setting.plexussMembers = [...setting.plexussMembers, ...action.payload.plexuss_members];
            setting.offset += 1;
            if(action.payload.users_invites.length == 0 && action.payload.plexuss_members.length == 0){
                setting.hasMoreContacts = false;
            }
            return {...state, setting: {...setting}};
        case "STORED_CONTACTS_FAILURE":
            setting = Object.assign({}, state.setting)
            setting.storedContacts = action.payload;
            return {...state, setting: {...setting}};

        case "SEND_MULTIPLE_INVITES_SUCCESS":
            setting = Object.assign({}, state.setting)
            setting.sendMultipleInvites.response = action.payload;
            return {...state, setting: {...setting}};
        case "SEND_MULTIPLE_INVITES_FAILURE":
            setting = Object.assign({}, state.setting)
            setting.sendMultipleInvites.response = action.payload;
            return {...state, setting: {...setting}};

        case "SENT_INVITE_TO_STORED_CONTACT":
            setting = Object.assign({}, state.setting);
            let index = setting.storedContacts.findIndex(contact => contact.invite_email == action.payload);
            if(index != -1){
                setting.storedContacts[index].sent = 1;
            }
            return {...state, setting: {...setting}};
        case "SENT_REQUEST_TO_PLEXUSS_MEMBER":
            setting = Object.assign({}, state.setting);
            index = setting.plexussMembers.findIndex(contact => contact.id == action.payload.id);
            if(index != -1){
                setting.plexussMembers[index].relation_status = {relation_status: "Pending"};
            }
            return {...state, setting: {...setting}};
        default:
            return {...state};
    }
}

export default setting;
