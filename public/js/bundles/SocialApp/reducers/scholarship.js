import * as types from '../actions/actionTypes';
import initialState from './initialState';

export const scholarship = (state = initialState.scholarships, action) => {
  switch(action.type) {
    case types.FETCH_SCHOLARSHIPS_SUCCESS:
      return {
        ...state,
        scholarships: [...action.payload.scholarships],
        signedIn: action.payload.signed_in,
        userId: action.payload.user_id,
      };

    case types.FETCH_SCHOLARSHIPS_FAILURE:
      return state;

    case types.QUEUE_SCHOLARSHIP_SUCCESS:
      return { ...state, queuedScholarships: [...state.queuedScholarships, action.payload.scholarship] };

    case types.QUEUE_SCHOLARSHIP_FAILURE:
      return state;

    case types.DELETED_QUEUE_SCHOLARSHIP_SUCCESS:
      return {...state, deletedQueuedScholarships: action.payload }

    case types.DELETED_QUEUE_SCHOLARSHIP_FAILURE:
      return state;

    case types.DELETE_SCHOLARSHIP_SUCCESS:
      
      let scholarships = [...state.scholarships];

      for(var i = 0; i < action.payload.length; i++) {
        let scholarshipIndex = state.scholarships.findIndex(scholarship => scholarship.id === action.payload[i])
        if (scholarshipIndex != -1) {
          scholarships.splice(scholarshipIndex, 1);
        }
      }
      return { ...state, scholarships: [...scholarships] };

    case types.DELETE_SCHOLARSHIP_FAILURE:
      
      return state;

    default:
      return state;
  }
};
