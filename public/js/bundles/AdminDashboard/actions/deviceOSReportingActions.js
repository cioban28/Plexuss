import axios from 'axios'

export const getSitePerformanceByPlatform = (user_type, start_date, end_date) => {
  return (dispatch) => {
    dispatch({
      type: 'TOGGLE_SPINNER',
      payload: { isFetching: true }
    });

    axios.get(`/sales/getSitePerfomanceByPlatform?type=${user_type}&start_date=${start_date}&end_date=${end_date}`)
    .then((res)=> {
      dispatch({
        type: 'GET_SITE_PERFORMANCE_BY_PLATFORM',
        payload: {
          data: res.data
        }
      })
    })
    .catch(err => dispatch({type: 'RECEIVE_ERROR'}));
  }
}

export const getSitePerformanceByBrowser = (user_type, start_date, end_date) => {
  return (dispatch) => {
    axios.get(`/sales/getSitePerfomanceByBrowser?type=${user_type}&start_date=${start_date}&end_date=${end_date}`)
    .then((res)=> {
      dispatch({
        type: 'GET_SITE_PERFORMANCE_BY_BROWSER',
        payload: {
          data: res.data
        }
      })
    })
    .catch(err => dispatch({type: 'RECEIVE_ERROR'}));
  }
};

export const getSitePerformanceByDevice = (user_type, start_date, end_date) => {
  return (dispatch) => {
    axios.get(`/sales/getSitePerfomanceByDevice?type=${user_type}&start_date=${start_date}&end_date=${end_date}`)
    .then((res)=> {
      dispatch({
        type: 'GET_SITE_PERFORMANCE_BY_DEVICE',
        payload: {
          data: res.data
        }
      })
    })
    .catch(err => dispatch({type: 'RECEIVE_ERROR'}));
  }
};
