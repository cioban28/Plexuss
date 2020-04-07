const initialState={
    data: {},
    onlineUsers: [],
    networkingDate: {
        suggestedUser: [],
        requests: [],
        friends: [],
        suggestionOffset: 0,
        suggestionForSic: [],
        suggestionOffsetForSic: 0,
        suggestionForSicFlag: true,
        suggestionFlag: true,
    }
}
const user = (state = initialState, action) => {
    switch(action.type){
        case "GET_USER_DATA_SUCCESS":
            const { payload } = action;
            return {...state, data:{...payload}, isLoading: false };
        case "UPDATE_PROFILE_IMG":
            let imgData = Object.assign({}, state.data)

            if (action.payload.status === 'success') {
                imgData['profile_img_loc'] = action.payload.profile_img_loc.substr(action.payload.profile_img_loc.lastIndexOf('/')+1, action.payload.profile_img_loc.length);
            }
            return {...state, data: imgData}
        case "UPDATE_USER_SCHOOL_NAMES":
            let newData = Object.assign({}, state.data)

            newData['user_school_names'] = action.payload.user_school_names;
            // console.log(action.payload, newData)
            return {...state, data: newData}
        case "GET_USER_DATA_FAILURE":
            return {...state, isLoading: false};
        case "SAVE_USER_ACCOUNT_PRIVACY_DATA":
            let data = Object.assign({}, state.data)
            data.userAccountSettings = action.payload.account_settings;
            return {...state, data: {...data}};
        case "GET_NETWORKING_DATA":
            let networkingDate = Object.assign({}, state.networkingDate)
            networkingDate.requests = action.payload.requests;
            networkingDate.friends =  action.payload.friends;
            return { ...state, networkingDate: networkingDate };
        case "GET_SUGGESTION_DATA":
            networkingDate = Object.assign({}, state.networkingDate)
            networkingDate.suggestionOffset+=1;
            networkingDate.suggestedUser = [...networkingDate.suggestedUser, ...action.payload];
            let flag = true;
            if(action.payload.length == 0){
                networkingDate.suggestionFlag = false;
            }
            return { 
                ...state,
                networkingDate: networkingDate,
            };
        case "GET_SUGGESTION_DATA_FOR_SIC_SUCCESS":
            networkingDate = Object.assign({}, state.networkingDate)
            networkingDate.suggestionOffsetForSic += 1;
            networkingDate.suggestionForSic = [...networkingDate.suggestionForSic, ...action.payload];
            flag = true;
            if(action.payload.length == 0){
                networkingDate.suggestionForSicFlag = false;
            }
            return {
                ...state,
                networkingDate: networkingDate,
            }
        case "ADD_IN_ONLINE_USERS":
            let newArr = state.onlineUsers;
            let _index_newArr = newArr.findIndex(userId => userId == action.payload);
            if(_index_newArr == -1){
                newArr.push(action.payload);
            }
            return {
                ...state,
                onlineUsers: _.cloneDeep(newArr)
            }
        case "REMOVE_FROM_ONLINE_USERS":
            newArr = state.onlineUsers;
            _index_newArr = newArr.findIndex(userId => userId == action.payload);
            if(_index_newArr != -1){
                newArr.splice(_index_newArr, 1);
            }
            return {
                ...state,
                onlineUsers: _.cloneDeep(newArr)
            }
        default:
            return {...state};
    }
}
export default user;
