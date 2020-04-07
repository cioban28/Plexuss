export function getMessages(){
    return{
        type: 'GET_MESSAGES',
        payload: ''
    };
}

export function addMessage(convoId, message){
    return{
        type: 'ADD_MESSAGES',
        payload: {
            convoId: convoId,
            message: message,
        }
    };
}