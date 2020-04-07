import * as types from '../actions/actionTypes';
import initialState from './initialState';

const events = (state = initialState.events, action) => {
  switch(action.type) {
    case 'UPCOMING_EVENTS_SUCCESS':
      return { ...state, events: action.payload };

    case 'NEAREST_EVENTS_SUCCESS':
      return { ...state, events: action.payload.getAllEventsReturn }

    case 'PAST_EVENTS_SUCCESS':
      return { ...state, events: action.payload };

    default:
      return state
  }
}
export default events;
