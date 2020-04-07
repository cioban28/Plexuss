const initialState = {
  isFetching: false,
  siteData: [],
  isError: false
};

const sitePerformance = (state = initialState, action) => {
  switch (action.type) {
    case "GET_SITE_PERFORMANCE":
      return Object.assign({}, state, {
        siteData: action.payload.data,
        isError: true,
        isFetching: false
      });

    case 'TOGGLE_SPINNER':
      return Object.assign({}, state, {
        isFetching: action.payload.isFetching,
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

export default sitePerformance;
