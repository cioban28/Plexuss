import axios from 'axios';
import store from '../../stores/socialStore'
import {
  upcomingEventsSuccess,
  nearestEventsSuccess,
  pastEventsSuccess,
} from './../actions/events';

export const getUpcomingEvents = (countryname = null) => {
  return axios.get('/ajax/getOnlineEvents?countryname=' + ( countryname == null ? "": countryname))
    .then(res => {
      store.dispatch(upcomingEventsSuccess(res.data))
    }).catch(error => {

    });
}

export const getNearestEvents = () => {
  return axios.get('/ajax/getnearestEvents')
    .then(res => {
      store.dispatch(nearestEventsSuccess(res.data))
    }).catch(error => {

    });
}

export const getPastEvents = (countryname = null) => {
  return axios.get('/ajax/getOfflineEvents?countryname=' + ( countryname == null ? "": countryname))
    .then(res => {
      store.dispatch(pastEventsSuccess(res.data))
    }).catch(error => {

    });
}

