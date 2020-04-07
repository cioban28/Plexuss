// asyncActions.js

import axios from 'axios'

export const getSitePerformance = (start_date, end_date) => {
  return (dispatch) => {
    dispatch({
      type: 'TOGGLE_SPINNER',
      payload: { isFetching: true }
    });

    axios.get(`/sales/getSitePerfomanceReport?start_date=${start_date}&end_date=${end_date}`)
    .then((res)=> {
      dispatch({
        type: 'GET_SITE_PERFORMANCE',
        payload: {
          data: res.data
        }
      })
    })
    .catch(err => dispatch({type: 'RECEIVE_ERROR'}));
  }
}

export const getSitePerformanceByFilter = (start_date, end_date, filter, user_type) => {
  return (dispatch) => {
    dispatch({
      type: 'TOGGLE_SPINNER',
      payload: { isFetching: true }
    });

    let url = '';

    if(user_type === 'all')
      url = `/sales/getSitePerfomanceReportByFilter?start_date=${start_date}&end_date=${end_date}&type=${filter}`;
    else
      url = `/sales/getSitePerfomanceReportByFilter?start_date=${start_date}&end_date=${end_date}&type=${filter}&unique_users=true`;

    axios.get(url)
    .then((res)=> {
      dispatch({
        type: 'GET_SITE_PERFORMANCE',
        payload: {
          data: res.data
        }
      })
    })
    .catch(err => dispatch({type: 'RECEIVE_ERROR'}));
  }
}
