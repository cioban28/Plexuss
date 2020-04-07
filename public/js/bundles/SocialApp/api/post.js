import axios from 'axios';
import store from '../../stores/socialStore'
import { getUserDataSuccess, getUserDataFailure} from './../actions/user'
import { getProfileData } from './profile'

import { unlikeSuccess, getNetworkingDataSuccess, sendRequestSuccess, getHomePost,getNetworkingDataForSicSuccess,
        getSuggestionDataSuccess, deletePostAction, publishPostComplete, publishPost, getHomePostFailureAction, publishSinglePost } from '../actions/posts'
import {toastr} from 'react-redux-toastr'

export const getUserData = () => {
    return axios({
        method: 'get',
        url: '/social/get-user-data',
    })
    .then(res => {
        if(res.statusText == "OK"){
            store.dispatch(getUserDataSuccess(res.data))
        }else{
            store.dispatch(getUserDataFailure(res.data))
        }
    })
    .catch(error => {
    })
}
export const getHomePosts = (data) => {
    return axios({
        method: 'post',
        url: '/social/get-home-posts',
        data: data,
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    })
    .then(res => {
        if(res.statusText == "OK"){
            store.dispatch(getHomePost(res.data))
        }
    })
    .catch(error => {
        store.dispatch(getHomePostFailureAction())
    })
}

export const savePost = (data, is_shared) => {
    return axios({
        method: 'post',
        url: '/social/save-post',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    })
    .then(res => {
        if(is_shared == 2){
            toastr.success('Hide Post', 'Hide Post Successfully');
        }
        else if(is_shared){
            toastr.success('Shared Post', 'Shared Post Successfully');
        }else{
            toastr.success('Save Post', 'Save Post Successfully');
        }
        store.dispatch(publishPostComplete());
    })
    .catch(error => {
        store.dispatch(publishPostComplete());
    })
}
export const updatePostSharedCount= (data) => {
    return axios({
        method: 'post',
        url: '/social/update-post-share',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    })
    .then(res => {
    })
}
export const deletePost = (data) => {
    return axios({
        method: 'delete',
        url: '/social/delete-post',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    })
    .then(res => {
        if(res.statusText == "OK"){
            toastr.success('Delete Post', 'Delete Post Successfully');
        }
    })
    .catch(error => {
        toastr.error('Delete Post', 'Delete Post Failure');
    })
}

export const saveComment = (data) => {
    return axios({
        method: 'post',
        url: '/social/save-post-comment',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    })
    .then(res => {
        if(res.statusText == "OK"){
            toastr.success('Save Comment', 'Save Comment Successfully');
        }
    })
    .catch(error => {
        toastr.error('Save Comment', 'Save Comment Failure');
    })
}
export const deleteComment = (data) => {
    return axios({
        method: 'delete',
        url: '/social/delete-comments',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    })
    .then(res => {
        if(res.statusText == "OK"){
            toastr.success('Delete Comment', 'Delete Comment Successfully');
        }
    })
    .catch(error => {
        toastr.error('Delete Comment', 'Delete Comment Failure');
    })
}
export const editComment = (data) => {
    return axios({
        method: 'post',
        url: '/social/edit-comments',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    })
    .then(res => {
        if(res.statusText == "OK"){
            toastr.success('Edit Comment', 'Edit Comment Successfully');
        }
    })
    .catch(error => {
        toastr.error('Edit Comment', 'Edit Comment Failure');
    })
}
export const like = (data) => {
    return axios({
        method: 'post',
        url: '/social/add-like',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    })
    .then(res => {
    })
    .catch(error => {
    })
}
export const unlike = (data) => {
    return axios({
        method: 'post',
        url: '/social/remove-like',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    })
    .then(res => {
    })
    .catch(error => {
    })
}
export const getNetworkingData = () => {
    return axios({
        method: 'get',
        url: '/social/get-network-users',
    })
    .then(res => {
        if(res.statusText == "OK"){
            store.dispatch(getNetworkingDataSuccess(res.data))
        }
    })
    .catch(error => {
    })
}
export const getNetworkingDataSic = (data) => {
    return axios({
        method: 'get',
        url: '/social/getNetworkingSuggestions?from_sic=true&offset='+data.offset,
    })
    .then(res => {
        if(res.statusText == "OK"){
            store.dispatch(getNetworkingDataForSicSuccess(res.data))
        }
    })
    .catch(error => {
    })
}
export const getSuggestionData = (data) => {
    return axios({
        method: 'get',
        url: '/social/getNetworkingSuggestions?offset='+data.offset,
    })
    .then(res => {
        store.dispatch(getSuggestionDataSuccess(res.data))
    })
    .catch(error => {
    })
}
export const friendRequest = (data) => {
    return axios({
        method: 'post',
        url: '/social/add-friend',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    })
    .then(res => {
        if(res.statusText == "OK"){
            toastr.success('Request Sent');
            store.dispatch(sendRequestSuccess(res.data))
            let newData = { user_id: data.user_two_id }
            getProfileData(newData);
        }
    })
    .catch(error => {
        toastr.error('Friend Request', 'Friend Request Fail');
    })
}
export const acceptRequest = (data) => {
    return axios({
        method: 'post',
        url: '/social/add-friend',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    })
    .then(res => {
        if(res.statusText == "OK"){
            toastr.success('You have accepted friend request!');
            store.dispatch(sendRequestSuccess(res.data))
            let newData = { user_id: data.user_two_id }
            getProfileData(newData);
        }
    })
    .catch(error => {
        toastr.error('Friend Request', 'Friend Request Fail');
    })
}
export const declineRequest = (data) => {
    return axios({
        method: 'post',
        url: '/social/add-friend',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    })
    .then(res => {
        if(res.statusText == "OK"){
            toastr.warning('Friend request declined!');
            store.dispatch(sendRequestSuccess(res.data))
            let newData = { user_id: data.user_two_id }
            getProfileData(newData);
        }
    })
    .catch(error => {
        toastr.error('Friend Request', 'Friend Request Fail');
    })
}
export const removeFriend = (data) => {
    return axios({
        method: 'post',
        url: '/social/add-friend',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    })
    .then(res => {
        if(res.statusText == "OK"){
            toastr.success('Remove Friend', 'Remove Friend Successfully');
            let newData = { user_id: data.user_two_id }
            getProfileData(newData);
        }
    })
    .catch(error => {
        toastr.success('Remove Friend', 'Remove Friend Fail');
    })
}
export const blockFriend = (data) => {
    return axios({
        method: 'post',
        url: '/social/add-friend',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    })
    .then(res => {
        if(res.statusText == "OK"){
            toastr.success('Block Friend', 'Block Friend Successfully');
        }
    })
    .catch(error => {
        toastr.success('Block Friend', 'Block Friend Fail');
    })
}
export const signOut = () => {
    return axios({
        method: 'get',
        url: '/signout/true',
    })
    .then(res => {
        if(res.data.response == "success"){
            getUserData().then(()=> {window.location.href = res.data.url})
        }
    })
    .catch(error => {
    })
}
export const getSinglePost = (id) =>{
    return axios({
        method: 'get',
        url: '/social/get-single-post?post-id='+id,
    })
    .then(res => {
        store.dispatch(publishPost(res.data[0]))
    })
    .catch(error => {
    })
}
export const publishSinglePosts = (id) =>{
    return axios({
        method: 'get',
        url: '/social/get-single-post?post-id='+id,
    })
    .then(res => {
        store.dispatch(publishSinglePost(res.data[0]))
    })
    .catch(error => {
    })
}
export const hidePostOrArticle = (data) =>{
    return axios({
        method: 'post',
        url: '/social/save-hide-post-article',
        data: data,
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    })
    .then(res =>{

    })
    .catch(error => {
        
    })
}


export const reportPostOrArticle = (data) =>{
    return axios({
        method: 'post',
        url: '/social/addAbuser',
        data:  data,
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    })
    .then(res => {
    })
    .catch(error => {
    })
}