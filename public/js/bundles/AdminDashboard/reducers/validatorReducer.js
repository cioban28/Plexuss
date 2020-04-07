// validatorReducer.js
var initValidator = {};

export default function(state = initValidator, action) {
    var newState = null;

    switch(action.type) {

        case 'EDIT_EMAIL':
            return Object.assign({}, state, {email_valid:action.payload.email_valid, msg: action.payload.msg});

        case 'PROFILE_PERMISSIONS_FORM_VALID': //sets profilePermissionsFormValid to true/false
        case 'PROFILE_FORM_VALID': // sets profileFormValid to true/false
            return Object.assign({}, state, {...action.payload});

        default:
            return state;
            
    }
}