export function getCollegesInitialDataSuccess(payload){
  return{
    type: "GET_COLLEGES_INNITAIL_DATA_SUCCESS",
    payload,
  };
}
  
export function getCollegesInitialDataFailure(payload){
  return{
    type: "GET_COLLEGES_INNITAIL_DATA_FAILURE",
    payload,
  };
}
  