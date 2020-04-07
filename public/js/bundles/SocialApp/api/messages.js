import axios from 'axios';
import store from '../../stores/socialStore'
import { setThreadData, setMessagesThreads, setAllThreadMessages, appendPreviousMessages } from './../actions/messages';

export const saveMessage = (data) => {
    return axios({
        method: 'post',
        url: '/ajax/messaging/postMsg',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
    })
    .then(res => {
        return res;
    })
    .catch(error => {
    })
}

export const getThreaData = (data) => {
    return axios({
        method: 'get',
        url: '/portal/ajax/messages/getNewMsgs/'+data.id,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
    })
    .then(res => {
        store.dispatch(setAllThreadMessages(res.data));
        return;
    })
    .catch(error => {
    })
}

export const getThreadMessages = (data) => {
    return axios({
        method: 'get',
        url: '/ajax/messaging/getHistoryMsg/'+data.thread_id+'/'+data.last_msg_id+'/'+data.last_msg_id,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
    })
    .then(res => {
        res.data.thread_id = data.thread_id;
        store.dispatch(appendPreviousMessages(res.data));
    })
    .catch(error => {
    })
}

export const getMessagesThreads = (data) => {
    return axios({
        method: 'get',
        url: '/portal/ajax/messages/getUserNewTopics/'+data.user_id+'/users?page='+data.pageNumber,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
    })
    .then(res => {
        store.dispatch(setMessagesThreads(res.data));
        return res.data;
    })
    .catch(error => {
    })
}

export const addThreadApi = (data) => {
    return axios({
        method: 'post',
        url: '/social/add-thread',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
    })
    .then(res => {
    })
    .catch(error => {
    })
}

export const typeMessageApi = (data) => {
    return axios({
        method: 'post',
        url: '/social/type-message',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
    })
    .then(res => {
    })
    .catch(error => {
    })
}

export const cancelTypeMessageApi = (data) => {
    return axios({
        method: 'post',
        url: '/social/cancel-typing',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
    })
    .then(res => {
    })
    .catch(error => {
    })
}

export const makeThreadApi = (data) => {
    return axios({
        method: 'post',
        url: '/portal/ajax/messages/createThread',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
    })
    .then(res => {
        return res;
    })
    .catch(error => {
    })
}

export const setMsgReadTime = (data) => {
    return axios({
        method: 'post',
        url: '/portal/ajax/messages/setReadTime',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
    })
    .then(res => {
        return res;
    })
    .catch(error => {
    })
}
export const addUserInConversation = (data) =>{
    return axios({
        method: 'post',
        url: '/portal/ajax/messages/addUserToThread',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
    })
    .then(res => {

    })
    .catch(error => {

    })
}