import axios from 'axios';
import store from '../../stores/socialStore'
import { getProfileDataSuccess, getProfileDataFailure, getProfilePostsSuccess, getFriendsAction } from './../actions/profile'

export const getProfileData = (data) => {
    return axios({
        method: 'post',
        url: '/social/get-user-profile',
        data: data,
    })
    .then(res => {
        if(res.statusText == "OK"){
            store.dispatch(getProfileDataSuccess(res.data))
        }
    })
    .catch(error => {
        store.dispatch(getProfileDataFailure())
    })
}

export const getProfileCompleteness = () => {
    return axios({
        method: 'post',
        url: '/social/get-profile-completeness',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    })
    .then(res => {
        if(res.statusText == "OK"){
            store.dispatch(getProfileDataSuccess(res.data))
        }
    })
    .catch(error => {
        console.log(error);
    })
}

export const getProfilePosts = (data) => {
    return axios({
        method: 'post',
        url: '/social/get-profile-posts',
        data: data,
    })
    .then(res => {
        if(res.statusText == "OK"){
            store.dispatch(getProfilePostsSuccess(res.data))
        }
    })
    .catch(error => {
    })
}

export const getFriends = () => {
    return axios({
        method: 'get',
        url: '/social/get-friends-list',
    })
    .then(res => {
        if(res.statusText == "OK"){
            store.dispatch(getFriendsAction(res.data))
        }
    })
}

export const saveEndorsements = (data) => {
    return axios({
        method: 'post',
        url: '/social/saveMyEndorsement',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
        data: data,
    })
    .then(res => {
        if(res.statusText == "OK"){
            let newData = { user_id: data.profile_id }
            getProfileData(newData);
        }
    })
    .catch(error => {
        console.log(error);
    })
}