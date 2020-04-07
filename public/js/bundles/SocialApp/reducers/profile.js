const initialState={
    user: [],
    relationStatus: "",
    friends: [],
}
const profile = (state = initialState, action) => {
    switch(action.type){
        case "GET_PROFILE_DATA_SUCCESS":
            state.relationStatus = (action.payload.relation_status && action.payload.relation_status[0] && action.payload.relation_status[0].relation_status ) ? action.payload.relation_status[0].relation_status : "";
            return {...state, user: {...action.payload} };
        case "GET_PROFILE_DATA_FAILURE":
            return {...state, user: {} };
        case "SEND_FRIEND_REQUEST":
            state.relationStatus = "Pending";
            return { ...state }
        case "GET_FRIENDS_SUCCESS":
            return { ...state, friends: [...action.payload.friends]}
        case "ACCEPT_FRIEND_REQUEST":
            state.relationStatus = "Accepted";
            return { ...state }
        default:
            return {...state};
    }
}
export default profile;