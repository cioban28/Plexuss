import axios from 'axios';
import {
  getTopbarSearchResultsSuccess,
  setRequestCacellationFn,
  cancelPreviousRequest,
  setCollegeOverview,
  setCollegeStats,
  setCollegeAdmissions,
  setCollegeEnrollments,
  setCollegeRanking,
  setCollegeTuitions,
  setCollegeFinancialAid,
  setCollegeNews,
  setCollegeCurrentStudents,
  setCollegeAlumni,
  setIsFetching,
  collegeRecruitedSuccess,
  saveRecruitMeInfoSuccess,
  addToConnectionSuccess,
  cancelConnectionRequestSuccess,
  declineConnectionRequestSuccess,
} from '../actions/search';
import { openModal, closeModal } from '../actions/modal';
import {toastr} from 'react-redux-toastr';
import store from '../../stores/socialStore'


const csrfToken = document.getElementsByName('csrf-token')[0].getAttribute('content');

export const getTopbarSearchResults = (searchTerm, requestCancellationFn) => {
  const source = axios.CancelToken.source();

  return dispatch => {
    if(!(Object.entries(requestCancellationFn).length === 0 && requestCancellationFn.constructor === Object)) {
      dispatch(cancelPreviousRequest());
    }

    dispatch(setRequestCacellationFn(source));

    return axios.get(`/getTopSearchAutocomplete?type=default&term=${searchTerm}`, {
      cancelToken: source.token
    })
    .then(res => {
      if(res.statusText === 'OK'){
        dispatch(getTopbarSearchResultsSuccess(res.data));
      }
    })
    .catch(thrown => {
      if (!axios.isCancel(thrown)) {
        toastr.error('Something went wrong while searching');
      }
    });
  }
};

export const getCollegeOverview = (slug) => {
  return dispatch => {
    dispatch(setIsFetching({ isFetching: true, mainPage: true }));
    return axios.get(`/social-college/${slug}`)
      .then(res => {
        if(res.statusText === 'OK'){
          dispatch(setCollegeOverview(res.data));
        }
      })
      .catch(error => {
        dispatch(setIsFetching({ isFetching: false, mainPage: true }));
        console.log(error);
        toastr.error('Could load the requested resource');
      });
  }
}

export const getCollegeStats = (slug, type) => {
    store.dispatch(setIsFetching({ isFetching: true }));
    return axios.get(`/social-college/${slug}/${type}`)
      .then(res => {
        if(res.statusText === 'OK'){
          store.dispatch(setCollegeStats(res.data));
        }
      })
      .catch(error => {
        store.dispatch(setIsFetching({ isFetching: false }));
        console.log(error);
        toastr.error('Could load the requested resource');
      });
}

export const getCollegeAdmissions = (slug) => {
  return dispatch => {
    dispatch(setIsFetching({ isFetching: true }));
    return axios.get(`/social-college/${slug}/admissions`)
      .then(res => {
        if(res.statusText === 'OK'){
          dispatch(setCollegeAdmissions(res.data));
        }
      })
      .catch(error => {
        dispatch(setIsFetching({ isFetching: false }));
        toastr.error('Could load the requested resource');
      });
  }
}

export const getCollegeEnrollments = (slug) => {
  return dispatch => {
    dispatch(setIsFetching({ isFetching: true }));
    return axios.get(`/social-college/${slug}/enrollment`)
      .then(res => {
        if(res.statusText === 'OK'){
          dispatch(setCollegeEnrollments(res.data));
        }
      })
      .catch(error => {
        dispatch(setIsFetching({ isFetching: false }));
        toastr.error('Could load the requested resource');
      });
  }
}

export const getCollegeRanking = (slug) => {
  return dispatch => {
    dispatch(setIsFetching({ isFetching: true }));
    return axios.get(`/social-college/${slug}/ranking`)
      .then(res => {
        if(res.statusText === 'OK'){
          dispatch(setCollegeRanking(res.data));
        }
      })
      .catch(error => {
        dispatch(setIsFetching({ isFetching: false }));
        toastr.error('Could load the requested resource');
      });
  }
}

export const getCollegeTuitions = (slug) => {
  return dispatch => {
    dispatch(setIsFetching({ isFetching: true }));
    return axios.get(`/social-college/${slug}/tuition`)
      .then(res => {
        if(res.statusText === 'OK'){
          dispatch(setCollegeTuitions(res.data));
        }
      })
      .catch(error => {
        dispatch(setIsFetching({ isFetching: false }));
        console.log(error);
        toastr.error('Could load the requested resource');
      });
  }
}

export const getCollegeFianancialAid = (slug) => {
  return dispatch => {
    dispatch(setIsFetching({ isFetching: true }));
    return axios.get(`/social-college/${slug}/financial-aid`)
      .then(res => {
        if(res.statusText === 'OK'){
          dispatch(setCollegeFinancialAid(res.data));
        }
      })
      .catch(error => {
        dispatch(setIsFetching({ isFetching: false }));
        toastr.error('Could load the requested resource');
      });
  }
}

export const getCollegeNews = (collegeId) => {
  return dispatch => {
    dispatch(setIsFetching({ isFetching: true }));
    return axios.get(`/api/getSingleCollegeInfo/news/${collegeId}`)
      .then(res => {
        if(res.statusText === 'OK'){
          dispatch(setCollegeNews(res.data));
        }
      })
      .catch(error => {
        dispatch(setIsFetching({ isFetching: false }));
        toastr.error('Could load the requested resource');
      });
  }
}

export const getCollegeCurrentStudents = (collegeId, userId) => {
  return dispatch => {
    dispatch(setIsFetching({ isFetching: true }));
    return axios.get(`/api/getSingleCollegeInfo/current-students/${collegeId}/${userId}`)
      .then(res => {
        if(res.statusText === 'OK'){
          dispatch(setCollegeCurrentStudents(res.data));
        }
      })
      .catch(error => {
        dispatch(setIsFetching({ isFetching: false }));
        console.log(error);
        toastr.error('Could load the requested resource');
      });
  }
}

export const getCollegeAlumni = (collegeId, userId) => {
  return dispatch => {
    dispatch(setIsFetching({ isFetching: true }));
    return axios.get(`/api/getSingleCollegeInfo/alumni/${collegeId}/${userId}`)
      .then(res => {
        if(res.statusText === 'OK'){
          dispatch(setCollegeAlumni(res.data));
        }
      })
      .catch(error => {
        dispatch(setIsFetching({ isFetching: false }));
        toastr.error('Could load the requested resource');
      });
  }
}

export const addToConnection = (data, pageName) => {
  return dispatch => {
    return axios({
        method: 'post',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        url: `/social/add-friend`,
        data: data,
    })
    .then(res => {
      if(res.statusText === 'OK'){
        dispatch(addToConnectionSuccess({ userId: res.data.user_id, pageName: pageName }));
        toastr.success('Friend Request', 'Friend request sent Successfully');
      }
    })
    .catch(error => {
      toastr.error('Friend Request', 'Failed to send friend request');
    });
  }
}

export const cancelConnectionRequest = (data, pageName) => {
  return dispatch => {
    return axios({
        method: 'post',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        url: `/social/cancel-friend`,
        data: data,
    })
    .then(res => {
      if(res.statusText === 'OK'){
        dispatch(cancelConnectionRequestSuccess({ userId: res.data.user_id, pageName: pageName }));
        toastr.success('Friend Request', 'Friend request cancelled Successfully');
      }
    })
    .catch(error => {
      toastr.error('Friend Request', 'Failed to cancel friend request');
    });
  }
}

export const declineConnectionRequest = (data, pageName) => {
  return dispatch => {
    return axios({
        method: 'post',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        url: `/social/decline-friend`,
        data: data,
    })
    .then(res => {
      if(res.statusText === 'OK'){
        dispatch(declineConnectionRequestSuccess({ userId: res.data.user_id, pageName: pageName }));
        toastr.success('Friend Request', 'Friend request declined Successfully');
      }
    })
    .catch(error => {
      toastr.error('Friend Request', 'Failed to decline friend request');
    });
  }
}

export const getCollegeRecruited = (collegeId) => {
  return dispatch => {
    return axios.get(`/ajax/json/recruiteme/${collegeId}`)
      .then(res => {
        if(res.statusText === 'OK'){
          dispatch(collegeRecruitedSuccess(res.data));
          dispatch(openModal());
        }
      })
      .catch(error => {
      });
  }
}

export const saveRecruitMeInfo = (collegeId, values) => {
  const csrfToken = document.getElementsByName('csrf-token')[0].getAttribute('content');

  return dispatch => {
    return axios({
        method: 'post',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        url: `/ajax/json/recruiteme/${collegeId}`,
        data: values,
    })
    .then(res => {
      if(res.statusText === 'OK'){
        dispatch(closeModal());
        dispatch(saveRecruitMeInfoSuccess())
      }
    })
    .catch(error => {
      toastr.error('Faild to save info');
    });
  }
}
