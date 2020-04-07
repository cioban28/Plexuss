const initialState = {
  isFetching: false,
  studentTrackingData: [],
  isError: false
};

const studentTracking = (state = initialState, action) => {
  switch (action.type) {
    case "GET_STUDENT_TRACKING":
      return Object.assign({}, state, {
        studentTrackingData: action.payload.data,
        isError: true,
        isFetching: false
      });
    case "RECEIVE_ERROR":
      return Object.assign({}, state, {
        isError: true,
        isFetching: false
      });
    default:
      return state;
  }
};

export default studentTracking;
