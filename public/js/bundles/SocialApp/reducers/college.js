import * as types from '../actions/actionTypes';
import initialState from './initialState';

export const college = (state = initialState.colleges, action) => {
  switch(action.type) {
    case types.FETCH_COLLEGES_SUCCESS:
      return { ...state, colleges: [...action.payload.colleges] };

    case types.FETCH_COLLEGES_FAILURE:
      return state;

    // case types.DELETE_SCHOLARSHIP_SUCCESS:
    //   let scholarships = [...state.scholarships];

    //   for(var i = 0; i < action.payload; i++) {
    //     let scholarshipIndex = state.scholarships.findIndex(scholarship => scholarship.id === action.payload[i])

    //     scholarships.splice(scholarshipIndex, 1);
    //   }

    //   return { ...state, scholarships: [...scholarships] };

    // case types.DELETE_SCHOLARSHIP_FAILURE:
    //   return state;

    case types.RENDER_MANAGE_COLLEGE_INDEX:
      debugger;
      return { ...state, renderManageCollegesIndex: action.payload };

    default:
      return state;
  }
};
