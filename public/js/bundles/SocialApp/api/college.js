import axios from 'axios';
import store from '../../stores/socialStore'

import {toastr} from 'react-redux-toastr'

export const fetchColleges = (successAction, failureAction) => {
  return (dispatch) => {
    axios.get('/ajax/portal/favcolleges/data')
    .then(response => {
      dispatch(successAction(response.data.colleges));
    })
    .catch(error => {
      dispatch(failureAction(error));
    })
  }
}

// export const deleteScholarship = (ids, successAction, failureAction) => {
//   return (dispatch) => {
//     axios.post('/ajax/portal/trashScholarships/data', {trashList: ids})
//     .then(response => {
//       dispatch(successAction(ids));
//     })
//     .catch(error => {
//       dispatch(failureAction());
//     })
//   }
// }
