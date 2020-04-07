import * as types from '../actions/actionTypes';
import initialState from './initialState';

export const notification = (state = initialState.notifications, action) => {
  switch(action.type) {
    case types.FETCH_NOTIFICATIONS:
      let num = state.pageNumber + 1;
      let flag = true;
      if(action.payload.data.length == 0){
        flag = false;
      }
      let newNotifications = Object.assign([], state.notifications)
      newNotifications = newNotifications.concat(action.payload.data);
      let unread_count = state.unread_count;
      parseInt(unread_count)
      return { ...state,
        notifications: [...newNotifications],
        unread_count: action.payload.unread_cnt,
        pageNumber: num,
        nextNotification: flag,
      };
    case types.FETCH_NOTIFICATIONS_FAILURE:
      return { ...state, nextNotification: false }
    case types.SHOW_NOTIFICATION:
      newNotifications = Object.assign([], state.notifications)
      unread_count = state.unread_count;
      let notificatioIndex = newNotifications.findIndex(notification => notification.id == action.payload.id);
      if(notificatioIndex == -1){
        newNotifications.unshift(action.payload);
      }else{
        newNotifications[notificatioIndex].is_read = "";
      }
      unread_count +=1;
      parseInt(unread_count)
      return {
        ...state,
        notifications: [...newNotifications],
        unread_count: unread_count
      }
    case types.READ_NOTIFICATION:
      newNotifications = Object.assign([], state.notifications)
      let index = newNotifications.findIndex(notification => notification.id == action.payload);
      if(index !== -1){
        newNotifications[index].is_read = "1";
      }
      return {
        ...state,
        notifications: [...newNotifications],
        unread_count: 0
      }
    default:
      return state;
  }
};
