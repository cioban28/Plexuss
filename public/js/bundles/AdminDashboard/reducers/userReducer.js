const initialState = {
  userProfileData: {},
}

const userData = (state = initialState, action) => {
  switch(action.type){
    case 'GET_USER_DATA_SUCCESS':
      return { ...state, userProfileData: action.payload };

    default:
      return { ...state };
  }
}

export default userData;
