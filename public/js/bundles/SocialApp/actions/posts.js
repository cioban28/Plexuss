export function publishPost(payload){
    return{
        type: "ADD_POST",
        payload,
    };
}
export function publishSinglePost(payload){
    return{
        type: "GET_SINGLE_POST",
        payload,
    };
}
export function deletePostAction(payload){
    return{
        type: "DELETE_POST",
        payload,
    }
}
export function addCommentSuccess(payload){
    return{
        type: "ADD_COMMENT",
        payload,
    };
}
export function likeSuccess(payload){
    return{
        type: "LIKE",
        payload,
    };
}
export function unlikeSuccess(payload){
    return{
        type: "UNLIKE",
        payload,
    };
}
export function getNetworkingDataSuccess(payload){
    return{
        type: "GET_NETWORKING_DATA",
        payload,
    };
}
export function getNetworkingDataForSicSuccess(payload){
    return{
        type: "GET_SUGGESTION_DATA_FOR_SIC_SUCCESS",
        payload,
    };
}
export function getSuggestionDataSuccess(payload){
    return{
        type: "GET_SUGGESTION_DATA",
        payload,
    };
}
export function sendRequestSuccess(payload){
    return{
        type: "SEND_FRIEND_REQUEST",
        payload,
    };
}
export function getHomePost(payload){
    return{
        type: "GET_HOME_POSTS",
        payload,
    }
}
export function setSocket(payload){
    return{
        type: "SET_SOCKET",
        payload,
    }
}
export function updateSharePostCountAction(payload){
    return{
        type: "UPDATE_SHARE_POST_COUNT",
        payload,
    }
}
export function setHeaderState(){
    return{
        type: "SET_HEADER_STATE",
    }
}
export function deleteCommentAction(payload){
    return{
        type: "DELETE_COMMENT",
        payload,
    }
}
export function editedCommentAction(payload){
    return{
        type: "EDIT_COMMENT",
        payload,
    }
}

export function publishPostStart(){
    return{
        type: "PUBLISH_POST_START",
    }
}

export function publishPostComplete(){
    return{
        type: "PUBLISH_POST_COMPLETE",
    }
}

export function hidePostAction(payload){
    return{
        type: "HIDE_POST",
        payload,
    }
}

export function addInSharedPostAction(payload){
    return{
        type: "ADD_IN_SHARED_POSTS",
        payload,
    }
}

export function getHomePostFailureAction(){
    return{
        type: "GET_HOME_POSTS_FAILURE",
    }
}

export function addFrndStateInArrAction(payload){
    return{
        type: "ADD_IN_FRNDSSTATEARR",
        payload,
    }
}