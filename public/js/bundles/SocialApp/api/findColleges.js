import axios from 'axios';
import store from '../../stores/socialStore'

import { getCollegesInitialDataSuccess } from './../actions/findColleges';


export const getCollegesInitialData = () => {
    return axios({
        method:'GET',
        url: `/api/getFindCollegesInitialData`,
    })
    .then(res => {
        store.dispatch(getCollegesInitialDataSuccess(res.data))
      })
      .catch(error => {
        console.log("-----error", error)
      })
  }

export const getAllCountries = () => {
  return (dispatch) => {
    axios.get('/ajax/getAllCountries')
    .then((res)=> {
      dispatch({
        type: 'SET_ALL_COUNTRIES_DATA',
        payload: res.data
      });
    });
  }
};
