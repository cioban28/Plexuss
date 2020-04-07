import axios from 'axios';
import store from '../../stores/socialStore'

import {toastr} from 'react-redux-toastr'

export const fetchScholarships = (successAction, failureAction) => {
  return (dispatch) => {
    axios.get('/social/scholarships/getScholarship')
    .then(response => {
      dispatch(successAction(response.data.data));
    })
    .catch(error => {
      dispatch(failureAction(error));
    })
  }
}

export const queueScholarship = (userId, scholarshipId, status, successAction, failureAction) => {
  return (dispatch) => {
    axios.post('/queueScholarship', { user_id: userId, scholarship: scholarshipId, status: status  })
    .then(response => {
      dispatch(successAction(response.data[0]));
    })
    .catch(error => {
      dispatch(failureAction(error));
    })
  }
}

export const deleteScholarship = (ids, successAction, failureAction) => {
  return (dispatch) => {
    axios.post('/ajax/portal/trashScholarships/data', {trashList: ids})
    .then(response => {
      dispatch(successAction(ids));
    })
    .catch(error => {
      dispatch(failureAction());
    })
  }
}
