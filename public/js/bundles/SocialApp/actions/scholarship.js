import * as types from './actionTypes';

export const fetchScholarshipsSuccess = (payload) => ({
  type: types.FETCH_SCHOLARSHIPS_SUCCESS,
  payload
})

export const fetchScholarshipsFailure = (payload) => ({
  type: types.FETCH_SCHOLARSHIPS_FAILURE,
  payload
});

export const queueScholarshipSuccess = (payload) => ({
  type: types.QUEUE_SCHOLARSHIP_SUCCESS,
  payload
})

export const queueScholarshipFailure = (payload) => ({
  type: types.QUEUE_SCHOLARSHIP_FAILURE,
  payload
});

export const deletedQueueScholarshipSuccess = (payload) => ({
  type: types.DELETED_QUEUE_SCHOLARSHIP_SUCCESS,
  payload
})

export const deletedQueueScholarshipFailure = (payload) => ({
  type: types.DELETED_QUEUE_SCHOLARSHIP_FAILURE,
  payload
});

export const deleteScholarshipSuccess = (payload) => ({
  type: types.DELETE_SCHOLARSHIP_SUCCESS,
  payload
});

export const deleteScholarshipFailure = (payload) => ({
  type: types.DELETE_SCHOLARSHIP_FAILURE,
  payload
});
