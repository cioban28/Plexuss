import axios from 'axios'

export const getStudentTracking = (start_date, end_date) => {
  return (dispatch) => {
    axios.get(`/sales/getOverviewReport?start_date=${start_date}&end_date=${end_date}`)
    .then((res)=> {
      dispatch({
        type: 'GET_STUDENT_TRACKING',
        payload: {
          data: res.data
        }
      })
    })
    .catch(err => dispatch({type: 'RECEIVE_ERROR'}));
  }
}
