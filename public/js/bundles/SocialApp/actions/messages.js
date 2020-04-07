export function setThread(payload){
    return{
        type: "SET_CURRENT_THREAD",
        payload,
    }
}
export function setThreadData(payload){
    return{
        type: "SET_THREAD_DATA",
        payload,
    }
}

export function setAllThreadMessages(payload){
    return{
        type: "SET_ALL_THREAD_MESSAGES",
        payload,
    }
}

export function appendPreviousMessages(payload){
    return{
        type: "APPEND_PREVIOUS_MSGS",
        payload,
    }
}

export function setThreadInfo(payload){
    return{
        type: "SET_THREAD_INFO",
        payload,
    }
}
export function setMessagesThreads(payload){
    return{
        type: "SET_MESSAGES_THREAD",
        payload,
    }
}
export function sendMessageAction(payload){
    return{
        type: "SEND_MESSAGE",
        payload,
    }
}
export function setUserRequestFlag(payload){
    return{
        type: "SET_THREADS_FLAG",
        payload,
    }
}
export function setThreadCountAction(){
    return{
        type: "SET_THREAD_COUNT",
    }
}
export function addInConversationArray(payload){
    return{
        type: "ADD_IN_CONVERSATION_ARRAY",
        payload,
    }
}
export function setNmFlag(){
    return{
        type: "SET_NMFLAG",
    }
}
export function removeThreadAction(payload){
    return{
        type: "REMOVE_THREAD",
        payload,
    }
}
export function readThreadMsg(payload){
    return{
        type: "READ_MESSAGE",
        payload,
    }
}
export function addNewThread(payload){
    return{
        type: "ADD_NEW_THREAD",
        payload,
    }
}

export function typeMsg(payload){
    return{
        type: "TYPE_MESSAGE",
        payload
    }
}
export function cancelMsg(payload){
    return{
        type: "CANCEL_TYPE_MESSAGE",
        payload
    }
}
export function setLogInUserIdAction(payload){
    return{
        type: "SET_LOGIN_USER_ID",
        payload,
    }
}

export function unsetNextTopicUsr(){
    return{
        type: "UNSET_NEXTTOPICUSR",
    }
}

export function setViewTimeAction(payload){
    return{
        type: "SET_MSG_VIEW_TIME",
        payload,
    }
}

export function addThreadUserAction(payload){
    return{
        type: "ADD_THREAD_USER",
        payload,
    }
}