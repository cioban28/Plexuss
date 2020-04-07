import axios from 'axios'
import {toastr} from 'react-redux-toastr';

const csrfToken = document.getElementsByName('csrf-token')[0].getAttribute('content');

export const saveNewsfeedPost = (values) => {
  return dispatch => {
    return axios({
        method: 'post',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        url: `/sales/saveNewsfeedPost`,
        data: values,
    })
    .then(res => {
      if(res.statusText === 'OK'){
        dispatch({type: 'TOGGLE_FORM_SUBMISSION', payload: false});
        dispatch({type: 'TOGGLE_SUCCESSFUL_SUBMISSION', payload: true});
        toastr.success('Post published successfully');
      }
    })
    .catch(error => {
      dispatch({type: 'TOGGLE_FORM_SUBMISSION', payload: false});
      dispatch({type: 'TOGGLE_SUCCESSFUL_SUBMISSION', payload: false});
      toastr.error('Failed to publish post');
    });
  }
};

export const editNewsfeedPost = (values) => {
  return dispatch => {
    return axios({
        method: 'post',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        url: `/sales/editNewsfeedPost`,
        data: values,
    })
    .then(res => {
      if(res.statusText === 'OK'){
        dispatch({type: 'TOGGLE_FORM_SUBMISSION', payload: false});
        dispatch({type: 'TOGGLE_SUCCESSFUL_SUBMISSION', payload: true});
        toastr.success('Post updated successfully');
      }
    })
    .catch(error => {
      dispatch({type: 'TOGGLE_FORM_SUBMISSION', payload: false});
      dispatch({type: 'TOGGLE_SUCCESSFUL_SUBMISSION', payload: false});
      toastr.error('Failed to update post');
    });
  }
};

export const getNewsfeedPosts = (pageNumber=0, plexussOnly) => {
  pageNumber = pageNumber === 1 ? 0 : pageNumber;
  const url = !!plexussOnly ? `/sales/getPosts?page=${pageNumber}&only_plexuss=true` : `/sales/getPosts?page=${pageNumber}`;

  return dispatch => {
    return axios({
        method: 'get',
        url: url,
    })
    .then(res => {
      if(res.statusText === 'OK'){
        if(plexussOnly) {
          dispatch({ type: 'GET_PLEXUSS_ONLY_POSTS_SUCCESS', payload: res.data });
        } else {
          dispatch({ type: 'GET_ALL_POSTS_SUCCESS', payload: res.data });
        }
      }
    })
    .catch(error => {
      toastr.error('Failed to fetch posts');
    });
  }
};

export const setRecommendationFilter = (tabName, values) => {
  return dispatch => {
    dispatch({ type: 'IS_FETCHING', payload: true });
    return axios({
        method: 'post',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        url: `/sales/ajax/setRecommendationFilter/${tabName}`,
        data: values,
    })
    .then(res => {
      if(res.statusText === 'OK'){
        dispatch({ type: 'IS_FETCHING', payload: false });
        toastr.success('Succesfully saved filter');
        dispatch({ type: 'SET_SALES_POST_ID', payload: res.data.sales_pid });
        dispatch({ type: 'SET_SHOULD_UPDATE_METER', payload: true });
      }
    })
    .catch(error => {
      dispatch({ type: 'IS_FETCHING', payload: false });
      toastr.error('Failed to save filter');
    });
  }
};

export const resetRecommendationFilter = (tabName) => {
  return dispatch => {
    dispatch({ type: 'IS_FETCHING', payload: true });
    return axios({
        method: 'post',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        url: `/sales/ajax/resetRecommendationFilter/${tabName}`,
    })
    .then(res => {
      if(res.statusText === 'OK'){
        dispatch({ type: 'IS_FETCHING', payload: false });
        toastr.success('Succesfully reset filter');
        dispatch({
          type: `RESET_RECOMMENDATION_FILTER_${tabName.replace(/([a-z])([A-Z])/g, '$1 $2').split(' ').join('_').toUpperCase()}`,
        });
        dispatch({ type: 'SET_SHOULD_UPDATE_METER', payload: true });
      }
    })
    .catch(error => {
      dispatch({ type: 'IS_FETCHING', payload: false });
      toastr.error('Failed to reset this filter');
    });
  }
};

export const getNumberOfUsersForFilter = (salesPostId) => {
  return dispatch => {
    return axios({
        method: 'get',
        url: `/admin/ajax/getNumberOfUsersForFilter?sales_pid=${salesPostId}`,
    })
    .then(res => {
      if(res.statusText === 'OK'){
        dispatch({ type: 'SET_USERS_FOR_RECOMMENDATION_METER', payload: res.data });
      }
    })
    .catch(error => {
      toastr.error('Failed to get users for filter');
    });
  }
};
