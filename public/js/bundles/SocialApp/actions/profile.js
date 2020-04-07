export function getProfileDataSuccess(payload){
    return{
        type: "GET_PROFILE_DATA_SUCCESS",
        payload,
    };
}
export function getProfileDataFailure(payload){
    return{
        type: "GET_PROFILE_DATA_FAILURE",
        payload,
    };
}
export function getProfilePostsSuccess(payload){
    return{
        type: "GET_PROFILE_POSTS_SUCCESS",
        payload,
    };
}
export function getFriendsAction(payload){
    return{
        type: "GET_FRIENDS_SUCCESS",
        payload,
    };
}
export function resetProfilePosts(){
    return{
        type: "RESET_PROFILE_POSTS",
    };
}

export function acceptFriendRequest() {
    return{
        type: "ACCEPT_FRIEND_REQUEST",
    };
}