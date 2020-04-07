import axios from 'axios'

export const getUserData = () => {
  return dispatch => {
    return axios({
        method: 'get',
        url: '/social/get-user-data',
    })
    .then(res => {
      if(res.statusText == 'OK'){
        dispatch({type: 'GET_USER_DATA_SUCCESS', payload: res.data});
      }
    })
    .catch(error => {
      console.log(error);
    })
  }
}
