export function getNewsSuccess(payload){
  return{
    type: "GET_NEWS_SUCCESS",
    payload,
  };
}

export function getNewsFailure(payload){
  return{
    type: "GET_NEWS_FAILURE",
    payload,
  };
}

export function getSingleNewsSuccess(payload){
  return{
    type: "GET_SINGLE_NEWS_SUCCESS",
    payload,
  };
}


