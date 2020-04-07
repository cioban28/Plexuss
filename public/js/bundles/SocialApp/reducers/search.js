import cloneDeep from 'lodash/cloneDeep';
const _ = {
  cloneDeep: cloneDeep
}
const initialState = {
  topbarSearchResults: [],
  requestCancellationFn: {},
  college: {},
  overview: {},
  stats: {},
  admissions: {},
  enrollments: {},
  ranking: {},
  tuitions: {},
  financialAid: {},
  news: [],
  currentStudents: [],
  alumni: [],
  shouldGetSearchedCollege: false,
  getRecruited: {},
  recruitMeSuccess: false,
  shouldTakeToPortal: true,
  isFetchingCollegePage: false,
  isFetchingCollegeSubPage: false,
}

const search = (state = initialState, action) => {
  switch(action.type){
    case 'GET_TOPBAR_SEARCH_RESULTS_SUCCESS':
      return { ...state, topbarSearchResults: action.payload };

    case 'SET_REQUEST_CANCELLATION_FN':
      return { ...state, requestCancellationFn: action.payload };

    case 'CANCEL_PREVIOUS_REQUEST':
      state.requestCancellationFn.cancel();
      return { ...state, requestCancellationFn: {} };

    case 'SET_IS_FETCHING':
      if(!!action.payload.mainPage)
        return { ...state, isFetchingCollegePage: action.payload.isFetching };

      return { ...state, isFetchingCollegeSubPage: action.payload.isFetching };

    case 'SET_COLLEGE_OVERVIEW':
      return { ...state, college: action.payload, overview: action.payload.college_data, isFetchingCollegePage: false };

    case 'SET_COLLEGE_STATS':
      return { ...state, stats: action.payload.college_data, isFetchingCollegeSubPage: false };

    case 'SET_COLLEGE_ADMISSIONS':
      return { ...state, admissions: action.payload.college_data, isFetchingCollegeSubPage: false };

    case 'SET_COLLEGE_ENROLLMENTS':
      return { ...state, enrollments: action.payload.college_data, isFetchingCollegeSubPage: false };

    case 'SET_COLLEGE_RANKING':
      return { ...state, ranking: action.payload.college_data, isFetchingCollegeSubPage: false };

    case 'SET_COLLEGE_TUITIONS':
      return { ...state, tuitions: action.payload.college_data, isFetchingCollegeSubPage: false };

    case 'SET_COLLEGE_FINANCIAL_AID':
      return { ...state, financialAid: action.payload.college_data, isFetchingCollegeSubPage: false };

    case 'SET_COLLEGE_NEWS':
      return { ...state, news: action.payload, isFetchingCollegeSubPage: false };

    case 'SET_COLLEGE_CURRENT_STUDENTS':
      return { ...state, currentStudents: action.payload, isFetchingCollegeSubPage: false };

    case 'SET_COLLEGE_ALUMNI':
      return { ...state, alumni: action.payload, isFetchingCollegeSubPage: false };

    case 'RESET_SEARCH_RESULTS':
      return { ...state, topbarSearchResults: [] };

    case 'RESET_COLLEGE_DATA':
      return { ...state, college: {}, overview: {}, stats: {}, admissions: {}, enrollments: {}, ranking: {}, tuitions: {}, financialAid: {}, news: [], currentStudents: [], alumni: []  };

    case 'COLLEGE_GET_RECRUITED_SUCCESS':
      return { ...state, getRecruited: action.payload }

    case 'SAVE_RECRUIT_ME_INFO_SUCCESS':
      return { ...state, recruitMeSuccess: true }

    case 'SAVE_RECRUIT_ME_INFO_SUCCESS':
      return { ...state, recruitMeSuccess: true }

    case 'RESET_RECRUIT_ME_SUCCESS':
      return { ...state, recruitMeSuccess: false, shouldTakeToPortal: true }

    case 'SET_DONT_TAKE_TO_PORTAL':
      return { ...state, shouldTakeToPortal: false }

    case 'SET_SHOULD_GET_SEARCHED_COLLEGE':
      return { ...state, shouldGetSearchedCollege: true }

    case 'RESET_SHOULD_GET_SEARCHED_COLLEGE':
      return { ...state, shouldGetSearchedCollege: false }

    case 'ADD_TO_CONNECTION_SUCCESS':
      let { pageName, userId } = action.payload;
      let userIndex = state[pageName].findIndex(user => user.user_id === userId);
      if (userIndex !== -1) {
        const modifiedStudents = _.cloneDeep(state[pageName]);
        modifiedStudents[userIndex] = { ...state[pageName][userIndex], has_any_relationship: 1, relationship: { friend_status: 'request_sent'}  };

        if(pageName === 'currentStudents') {
          return { ...state, currentStudents: [...modifiedStudents] };
        }

        return { ...state, alumni: [...modifiedStudents] };
      }
      return { ...state };

    case 'CANCEL_CONNECTION_REQUEST':
      pageName = action.payload.pageName;
      userId = action.payload.userId;
      userIndex = state[pageName].findIndex(user => user.user_id === userId);
      if (userIndex !== -1) {
        const modifiedStudents = _.cloneDeep(state[pageName]);
        modifiedStudents[userIndex] = { ...state[pageName][userIndex], has_any_relationship: 0, relationship: {}  };

        if(pageName === 'currentStudents') {
          return { ...state, currentStudents: [...modifiedStudents] };
        }

        return { ...state, alumni: [...modifiedStudents] };
      }
      return { ...state };

    case 'DECLINE_CONNECTION_REQUEST':
      pageName = action.payload.pageName;
      userId = action.payload.userId;
      userIndex = state[pageName].findIndex(user => user.user_id === userId);
      if (userIndex !== -1) {
        const modifiedStudents = _.cloneDeep(state[pageName]);
        modifiedStudents[userIndex] = { ...state[pageName][userIndex], has_any_relationship: 0, relationship: {}  };

        if(pageName === 'currentStudents') {
          return { ...state, currentStudents: [...modifiedStudents] };
        }

        return { ...state, alumni: [...modifiedStudents] };
      }
      return { ...state };

    default:
      return { ...state };
  }
}

export default search;
