export function getTopbarSearchResultsSuccess(payload) {
  return{
    type: 'GET_TOPBAR_SEARCH_RESULTS_SUCCESS',
    payload,
  };
}

export function setRequestCacellationFn(payload) {
  return {
    type: 'SET_REQUEST_CANCELLATION_FN',
    payload,
  }
}

export function cancelPreviousRequest(payload) {
  return {
    type: 'CANCEL_PREVIOUS_REQUEST',
    payload,
  }
}

export function setCollegeOverview(payload) {
  return {
    type: 'SET_COLLEGE_OVERVIEW',
    payload,
  }
}

export function setCollegeStats(payload) {
  return {
    type: 'SET_COLLEGE_STATS',
    payload,
  }
}

export function setCollegeAdmissions(payload) {
  return {
    type: 'SET_COLLEGE_ADMISSIONS',
    payload,
  }
}

export function setCollegeEnrollments(payload) {
  return {
    type: 'SET_COLLEGE_ENROLLMENTS',
    payload,
  }
}

export function setIsFetching(payload) {
  return {
    type: 'SET_IS_FETCHING',
    payload,
  }
}

export function resetSearchResults(payload) {
  return {
    type: 'RESET_SEARCH_RESULTS',
    payload,
  }
}

export function resetCollegeData(payload) {
  return {
    type: 'RESET_COLLEGE_DATA',
    payload,
  }
}

export function setCollegeRanking(payload) {
  return {
    type: 'SET_COLLEGE_RANKING',
    payload,
  }
}

export function setCollegeTuitions(payload) {
  return {
    type: 'SET_COLLEGE_TUITIONS',
    payload,
  }
}

export function setCollegeFinancialAid(payload) {
  return {
    type: 'SET_COLLEGE_FINANCIAL_AID',
    payload,
  }
}

export function setCollegeNews(payload) {
  return {
    type: 'SET_COLLEGE_NEWS',
    payload,
  }
}

export function setCollegeCurrentStudents(payload) {
  return {
    type: 'SET_COLLEGE_CURRENT_STUDENTS',
    payload,
  }
}

export function setCollegeAlumni(payload) {
  return {
    type: 'SET_COLLEGE_ALUMNI',
    payload,
  }
}

export function collegeRecruitedSuccess(payload) {
  return {
    type: 'COLLEGE_GET_RECRUITED_SUCCESS',
    payload,
  }
}

export function resetRecruitMeSucess(payload) {
  return {
    type: 'RESET_RECRUIT_ME_SUCCESS',
    payload,
  }
}

export function saveRecruitMeInfoSuccess(payload) {
  return {
    type: 'SAVE_RECRUIT_ME_INFO_SUCCESS',
    payload,
  }
}

export function setDontTakeToPortal(payload) {
  return {
    type: 'SET_DONT_TAKE_TO_PORTAL',
    payload,
  }
}

export function setShouldGetSearchedCollege(payload) {
  return {
    type: 'SET_SHOULD_GET_SEARCHED_COLLEGE',
    payload,
  }
}

export function resetShouldGetSearchedCollege(payload) {
  return {
    type: 'RESET_SHOULD_GET_SEARCHED_COLLEGE',
    payload,
  }
}

export function addToConnectionSuccess(payload) {
  return {
    type: 'ADD_TO_CONNECTION_SUCCESS',
    payload,
  }
}

export function cancelConnectionRequestSuccess(payload) {
  return {
    type: 'CANCEL_CONNECTION_REQUEST',
    payload,
  }
}

export function declineConnectionRequestSuccess(payload) {
  return {
    type: 'DECLINE_CONNECTION_REQUEST',
    payload,
  }
}
