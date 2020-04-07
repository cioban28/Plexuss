// profileReducer.js
let elem = document.getElementById('AdminDashboard_Component'),
    setupCompleted = elem.dataset.setupcompleted,
    logo = elem.dataset.logo,
    profileInfo = elem.dataset.profile,
    orgsFirstUser = elem.dataset.orgsFirstUser,
    superAdmin = elem.dataset.super,
    is_admin_premium = elem.dataset.is_admin_premium;

let initUser = {
    role: 'Admin',
    portals: 'General',
    permissions: 'All',
    is_admin_premium: +is_admin_premium || 0,
    completed_signup: +setupCompleted || 0,
    school_logo: logo,
    super_admin: +superAdmin || 0,
    orgs_first_user: +orgsFirstUser || 0,
};

export default function(state = initUser, action) {
    var newState = null;

    switch(action.type) {
        
        case 'EDIT_FNAME':
            return Object.assign({}, state, {fname: action.payload.fname});

        case 'EDIT_LNAME':
            return Object.assign({}, state, {lname: action.payload.lname});

        case 'EDIT_TITLE':
            return Object.assign({}, state, {title: action.payload.title});

        case 'EDIT_WORKING_SINCE':
            return Object.assign({}, state, {working_since: action.payload.working_since});

        case 'EDIT_DEPARTMENT':
            return Object.assign({}, state, {department: action.payload.department, added_department: ''});

        case 'ADD_DEPARTMENT':
            return Object.assign({}, state, {added_department: action.payload});

        case 'ADD_DEPT_INPUT_VISIBLE':
            return Object.assign({}, state, {addDeptInputVisible: action.payload});

        case 'EDIT_EMAIL':
            return Object.assign({}, state, {email: action.payload.email});

        case 'EDIT_ROLE':
            return Object.assign({}, state, {role: action.payload.role});

        case 'EDIT_PORTALS':
            return Object.assign({}, state, {portals: action.payload.portals});

        case 'EDIT_PERMISSIONS':
            return Object.assign({}, state, {permissions: action.payload.permissions});

        case 'EDIT_BLURB':
            return Object.assign({}, state, {blurb: action.payload.blurb});

        case 'SAVE_PROFILE_PENDING':
        case 'SAVE_PROFILE_DONE':
        case 'SAVE_PROFILE_ERR':
            if( state.temporaryAlternateProfile ){
                var newState = Object.assign({}, state);
                newState.temporaryAlternateProfile = Object.assign({}, newState.temporaryAlternateProfile, {...action.payload});
                return newState;
            }

            return Object.assign({}, state, {...action.payload});

        case 'NEXT_STEP': //Same reducer case in SetupReducer, so will be called during same dispatch
            return Object.assign({}, state, {saved: action.payload.resetSaved});

        case 'GET_PROFILE_PENDING':
            return Object.assign({}, state, {pending: true});

        case 'GET_PROFILE_DONE':
            return Object.assign({}, state, {pending: false, initProfile: true, ...action.payload});

        case 'GET_PROFILE_ERR':
            return Object.assign({}, state, {pending: false, err: true});

        case 'EDIT_PROFILE_PIC':
        case 'EDIT_AVATAR':
        case 'AVATAR_CHOSEN':
            if( action.payload.forAlternateProfile ){
                let newState = Object.assign({}, state);
                newState.temporaryAlternateProfile = Object.assign({}, newState.temporaryAlternateProfile, {...action.payload});
                return newState;   
            }

            return Object.assign({}, state, {...action.payload});

        case 'TOGGLE_PROFILE_AVATAR_MODAL':
            //if alternateProfile is active, set modal for that profile, else for current user
            if( action.payload.forAlternateProfile ){
                let newState = Object.assign({}, state);
                newState.temporaryAlternateProfile = Object.assign({}, newState.temporaryAlternateProfile, {...action.payload});
                return newState;   
            }

            return Object.assign({}, state, {...action.payload});

        case 'EDIT_ALTERNATE_PROFILE':
            // updates alternate profile
            var newState = Object.assign({}, state);
            newState.temporaryAlternateProfile = Object.assign({}, newState.temporaryAlternateProfile, {...action.payload});
            return newState;

        case 'SET_ALTERNATE_PROFILE': //sets alternate profile obj w/ new profile obj
        case 'REMOVE_ALTERNATE_PROFILE': // sets temporaryAlternateProfile = null
        case 'RESET_SAVED': // reset saved = false
            return Object.assign({}, state, {...action.payload});

        case 'UPDATE_USER_DISPLAY_SETTINGS':
            var { updated_user, pending, err } = action.payload, newState = null, obj = null;;

            // if there's an error, don't save update_user
            if( err ) obj = {pending: pending, display_setting_err: err}
            else obj = {...updated_user, pending: pending, display_setting_err: err}
 
            // if alternate profile
            if( updated_user.isAlternate ){
                newState = Object.assign({}, state);
                newState.temporaryAlternateProfile = Object.assign({}, newState.temporaryAlternateProfile, obj);
            }else{
                // else original profile
                newState = Object.assign({}, state, obj);
            }

            return newState;

        case 'UPDATE_PROFILE_PENDING':
        case 'UPDATE_PROFILE_ERR':
            if( action.payload.isAlternate ){
                newState = Object.assign({}, state);
                newState.temporaryAlternateProfile = Object.assign({}, newState.temporaryAlternateProfile, {...action.payload});
                return newState;
            }

            return Object.assign({}, state, {...action.payload});

        case 'UPDATE_USER_DONE': // same case as ManageUserReducer when updating display settings - except when this case is triggered here, we make frontpage = 0
            let { err, fp_updated, updated_user } = action.payload;

            if( err.has_err ) return Object.assign({}, state, {display_setting_err: err});

            // if frontpage is being updated, and user updated is not me, change my frontpage to 0, else it is me so update my frontpage value
            // else update my collegepage value if frontpage is not being updated AND the updated user is me
            if( fp_updated ){
                if( state.id !== updated_user.id ) return Object.assign({}, state, {show_on_front_page: 0});
                return Object.assign({}, state, {show_on_front_page: updated_user.show_on_front_page});
            }else if( !fp_updated && state.id === updated_user.id ) return Object.assign({}, state, {show_on_college_page: updated_user.show_on_college_page});

            return state;

        case 'DELETE_USER_DONE':
            var newState = Object.assign({}, state), { pending, deleted } = action.payload;
            newState.temporaryAlternateProfile = Object.assign({}, newState.temporaryAlternateProfile, {pending: pending, deleted: deleted});
            return newState;

        default:
            return state;
            
    }
};