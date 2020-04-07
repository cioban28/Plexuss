const initialState = {
  platformReport: {},
  deviceReport: {},
  browserReport: {},
  isFetching: false,
  isError: false
};

const deviceOSReporting = (state = initialState, action) => {
  switch (action.type) {
    case "GET_SITE_PERFORMANCE_BY_PLATFORM":
      return Object.assign({}, state, {
        platformReport: action.payload.data,
      });

    case "GET_SITE_PERFORMANCE_BY_BROWSER":
      return Object.assign({}, state, {
        browserReport: action.payload.data,
      });

    case "GET_SITE_PERFORMANCE_BY_DEVICE":
      return Object.assign({}, state, {
        deviceReport: action.payload.data,
        isFetching: false,
      });

    case 'TOGGLE_SPINNER':
      return Object.assign({}, state, {
        isFetching: action.payload.isFetching,
      });

    case "RECEIVE_ERROR":
      return Object.assign({}, state, {
        isFetching: false,
        isError: true,
      });
    default:
      return state;
  }
};

export default deviceOSReporting;
