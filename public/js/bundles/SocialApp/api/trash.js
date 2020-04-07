import axios from 'axios';
import store from '../../stores/socialStore'
import { getTrashCollege, deleteTrashCollege } from './../actions/trash'

export const getTrashColleges = () => {

  axios.get('/ajax/portal/trash/data')
  .then(response => {
    store.dispatch(getTrashCollege(response.data.colleges))
  })
  .catch(error => {

  })
}

export const deleteTrashColleges = (data, selected, single) => {
    return axios({
        method: 'post',
        url: '/ajax/recruiteme/restore/data',
        data: data,
    })
    .then(res => {
        store.dispatch(deleteTrashCollege(selected, single))
    })
    .catch(error => {
    })
}