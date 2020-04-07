const initialState = {
}
  
const collegeEssays = (state = initialState, action) => {
  switch(action.type){
    case 'COLLEGE_ESSAYS_GET_SUCCESS':
      return   [...state, ...action.payload ];
  
  default:
    return state ;
  }
}
  
export default collegeEssays;
  