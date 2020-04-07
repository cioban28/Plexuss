const initialState ={
    conversations:[
        {
            id: 1,
            friendName: 'Slothy McSlothernson1',
            userImage:'/social/images/AlveProfilePic.png',
            friendImage:'/social/images/AlveProfilePic.png',
            messages:[
                {
                    type: 'inComming',
                    text: 'Test 101',
                    date: Date.now(),
                },
                {
                    type: 'inComming',
                    text: 'Test 101',
                    date: Date.now(),
                },
                {
                    type: 'outComming',
                    text: 'Test 101',
                    date: Date.now(),
                },
                {
                    type: 'inComming',
                    text: 'Test 101',
                    date: Date.now(),
                }
            ]
        },
        {
            id: 2,
            friendName: 'Slothy McSlothernson2',
            userImage:'/social/images/AlveProfilePic.png',
            friendImage:'/social/images/AlveProfilePic.png',
            messages:[
                {
                    type: 'inComming',
                    text: 'Test 101',
                    date: Date.now(),
                },
                {
                    type: 'inComming',
                    text: 'Test 101',
                    date: Date.now(),
                },
                {
                    type: 'outComming',
                    text: 'Test 101',
                    date: Date.now(),
                },
                {
                    type: 'inComming',
                    text: 'Test 101',
                    date: Date.now(),
                }
            ]
        },
        {
            id: 3,
            friendName: 'Slothy McSlothernson3',
            userImage:'/social/images/AlveProfilePic.png',
            friendImage:'/social/images/AlveProfilePic.png',
            messages:[
                {
                    type: 'inComming',
                    text: 'Test 101',
                    date: Date.now(),
                },
                {
                    type: 'inComming',
                    text: 'Test 101',
                    date: Date.now(),
                },
                {
                    type: 'outComming',
                    text: 'Test 101',
                    date: Date.now(),
                },
                {
                    type: 'inComming',
                    text: 'Test 101',
                    date: Date.now(),
                }
            ]
        },
        {
            id: 4,
            friendName: 'Slothy McSlothernson4',
            userImage:'/social/images/AlveProfilePic.png',
            friendImage:'/social/images/AlveProfilePic.png',
            messages:[
                {
                    type: 'inComming',
                    text: 'Test 101',
                    date: Date.now(),
                },
                {
                    type: 'inComming',
                    text: 'Test 101',
                    date: Date.now(),
                },
                {
                    type: 'outComming',
                    text: 'Test 101',
                    date: Date.now(),
                },
                {
                    type: 'inComming',
                    text: 'Test 101',
                    date: Date.now(),
                }
            ]
        },
        {
            id: 5,
            friendName: 'Slothy McSlothernson5',
            userImage:'/social/images/AlveProfilePic.png',
            friendImage:'/social/images/AlveProfilePic.png',
            messages:[
                {
                    type: 'inComming',
                    text: 'Test 101',
                    date: Date.now(),
                },
                {
                    type: 'inComming',
                    text: 'Test 101',
                    date: Date.now(),
                },
                {
                    type: 'outComming',
                    text: 'Test 101',
                    date: Date.now(),
                },
                {
                    type: 'inComming',
                    text: 'Test 101',
                    date: Date.now(),
                }
            ]
        },

    ]
}
const conversations = (state = initialState, action) => {
    switch(action.type){
        case "GET_MESSAGES":
            return {...state};           
        case "ADD_MESSAGES":
            let convoIndex = state.conversations.findIndex(conversation => conversation.id === action.payload.convoId);
            let conversations = Object.assign([], state.conversations)
            conversations[convoIndex].messages.push(action.payload.message);
            return { ...state, conversations: [...conversations] }
        default:
            return {...state};
    }
}

export default conversations;