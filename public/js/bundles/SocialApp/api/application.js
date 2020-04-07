import axios from 'axios';
import store from '../../stores/socialStore'
import { getApplications } from './../actions/application'

export const fetchApplications = () => {

  axios.get('/ajax/portal/applications/data')
  .then(response => {
    store.dispatch(getApplications(response.data.colleges))
  })
  .catch(error => {

  })
}
