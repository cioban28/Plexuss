import axios from 'axios';
import store from '../../stores/socialStore'
import { fetchNotificationSuccess } from './../actions/notificationAction'
export const fetchNotification = (pageNumber) => {
    return axios({
        method: 'get',
        url: '/ajax/getAllNotificationsJSON?page='+pageNumber,
    })
    .then(res => {
        if(res.statusText == "OK"){
            store.dispatch(fetchNotificationSuccess(res.data))
        }
    })
    .catch(error => {
    })
}
export const readNotificationApi = (data) => {
    return axios({
        method: 'post',
        url: '/social/read-notification',
        data: data,
    })
    .then(res => {        
    })
    .catch(error => {
    })
}
export const readAllNotification = () => {
    return axios({
        method: 'get',
        url: '/ajax/notifications/setRead?type=notify',
    })
}