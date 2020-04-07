import * as types from './actionTypes';

export const fetchNotificationSuccess = (payload) => ({
    type: types.FETCH_NOTIFICATIONS,
    payload
})
export const fetchNotificationFailure = (payload) => ({
    type: types.FETCH_NOTIFICATIONS_FAILURE,
    payload
})
export function addNotification(payload){
    return{
        type: types.SHOW_NOTIFICATION,
        payload: payload,
    }
}
export function readNotification(payload){
    return{
        type: types.READ_NOTIFICATION,
        payload: payload,
    }
}