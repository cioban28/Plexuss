const initialState = {
  countries: [],
}

const FindColleges = (state = initialState, action) => {
  switch(action.type){
    case 'GET_COLLEGES_INNITAIL_DATA_SUCCESS':
      return { ...state, initialCollegesData: action.payload }

    case 'SET_ALL_COUNTRIES_DATA':
      return { ...state, countries: action.payload };

    default:
      return state
  }
}

export default FindColleges;
