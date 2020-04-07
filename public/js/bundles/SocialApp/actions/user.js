export function getUserDataSuccess(payload){
    return{
        type: "GET_USER_DATA_SUCCESS",
        payload,
    };
}

export function getUserDataFailure(payload){
    return{
        type: "GET_USER_DATA_FAILURE",
        payload,
    };
}

export function addInOnlineUsersArr(payload){
    return{
        type: "ADD_IN_ONLINE_USERS",
        payload,
    };
}

export function removeFromOnlineUsersArr(payload){
    return{
        type: "REMOVE_FROM_ONLINE_USERS",
        payload,
    };
}