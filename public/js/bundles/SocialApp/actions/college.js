import * as types from './actionTypes';

export const fetchCollegesSuccess = (payload) => ({
  type: types.FETCH_COLLEGES_SUCCESS,
  payload
})

export const fetchCollegesFailure = (payload) => ({
  type: types.FETCH_COLLEGES_FAILURE,
  payload
});

export const setRenderManageCollegesIndex = (payload) => ({
  type: types.RENDER_MANAGE_COLLEGE_INDEX,
  payload
});

// export const deleteScholarshipSuccess = (payload) => ({
//   type: types.DELETE_SCHOLARSHIP_SUCCESS,
//   payload
// });

// export const deleteScholarshipFailure = (payload) => ({
//   type: types.DELETE_SCHOLARSHIP_FAILURE,
//   payload
// });
