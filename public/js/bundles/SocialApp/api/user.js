import axios from 'axios';
import { changePasswordSuccess , changePasswordFailure, getSettingDataSuccess,
        saveUserAccountPrivacySuccess, saveUserAccountPrivacyData,
        saveEmailNotificationSuccess, saveEmailNotificationFailure,
        savePhoneNumberSuccess, savePhoneNumberFailure,
        sendCodeSuccess, sendCodeFailure,
        validateCodeSuccess, validateCodeFailure,
        sendSingleInviteSuccess, sendSingleInviteFailure,
        getStoredContactsSuccess, getStoredContactsFailure,
        sendMultipleInvitesSuccess, sendMultipleInvitesFailure} from './../actions/setting'
import store from '../../stores/socialStore'
import {toastr} from 'react-redux-toastr'

export const changePassword = (data) => {
    return axios({
        method: 'post',
        url: '/social/settings',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }}
    })
    .then(res => {
        if(res.statusText == "OK"){
            store.dispatch(changePasswordSuccess(res.data));
        }
    })
    .catch(error => {
        store.dispatch(changePasswordFailure(error));
    })
}

export const deleteAccount = (data) => {
    return axios({
        method: 'post',
        url: '/setting/deleteUserAccount',
        data: data,
    })
    .then(res => {
        window.location.href = '/signout'
    })
    .catch(error => {
        console.log(error);
    })
}

export const getSettingData = () => {
    return axios({
        method: 'get',
        url: '/social/setting-data',
    })
    .then(res => {
        if(res.statusText == "OK"){
            store.dispatch(getSettingDataSuccess(res.data))
        }
    })
    .catch(error => {
    })
}

export const saveUserAccountPrivacy = (data) => {
    return axios({
        method: 'post',
        url: '/settings/save/saveUserAccountPrivacy',
        data: data,
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    })
    .then(res => {
        if(res.statusText == "OK"){
            store.dispatch(saveUserAccountPrivacySuccess(res.data));
            store.dispatch(saveUserAccountPrivacyData(res.data));
            toastr.success('Account Privacy Settings', 'Settings Saved Successfully');
        }
    })
    .catch(error => {
        console.log(error);
        //store.dispatch(saveEmailNotificationFailure(error));
        toastr.error('Account Privacy Settings', 'Error Saving Settings');
    })
}

export const saveEmailNotifications = (data) => {
    return axios({
        method: 'post',
        url: '/settings/save/saveEmailNotifications',
        data: data,
    })
    .then(res => {
        if(res.statusText == "OK"){
            store.dispatch(saveEmailNotificationSuccess(res.data));
            toastr.success('Notification Settings', 'Settings Saved Successfully');
        }
    })
    .catch(error => {
        console.log(error);
        store.dispatch(saveEmailNotificationFailure(error));
        toastr.error('Notification Settings', 'Error Saving Settings');
    })
}

export const savePhoneNumber = (data) =>{
    return axios({
        method: 'post',
        url: '/settings/save/savePhoneInfo',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }}
    })
    .then(res => {
        if(res.statusText == "OK"){
            store.dispatch(savePhoneNumberSuccess(res.data));
        }
    })
    .catch(error => {
        store.dispatch(savePhoneNumberFailure(error));
    })
}
export const sendCode = (data) =>{
    return axios({
        method: 'post',
        url: '/get_started/sendPhoneConfirmation',
        data: {phone: data, dialing_code: ''},
        config: { headers: {'Content-Type': 'multipart/form-data' }}
    })
    .then(res => {
        if(res.data == "success"){
            store.dispatch(sendCodeSuccess(res.data));
        }
        if(res.data.response == "failed"){
            store.dispatch(sendCodeFailure(res.data));
        }
    })
    .catch(error => {
        store.dispatch(sendCodeFailure(error));
    })
}

export const confirmCode = (data) =>{
    return axios({
        method: 'post',
        url: '/get_started/checkPhoneConfirmation',
        data:  data,
        config: { headers: {'Content-Type': 'multipart/form-data' }}
    })
    .then(res => {
        if(res.data.response == "success"){
            store.dispatch(validateCodeSuccess(res.data));
        }
        if(res.data.response == "failed"){
            store.dispatch(validateCodeFailure(res.data));
        }
    })
    .catch(error => {
    })
}

export const sendSingleInvite = (data) =>{
    return axios({
        method: 'post',
        url: '/ajax/sendSingleInvite',
        data:  {invite_me_email: data},
        config: { headers: {'Content-Type': 'multipart/form-data' }}
    })
    .then(res => {
        if(res.data == "success"){
            store.dispatch(sendSingleInviteSuccess(res.data));
        }
        if(res.data == "Error Occured!"){
            store.dispatch(sendSingleInviteFailure(res.data));
        }
    })
    .catch(error => {
    })
}


export const getStoredContacts = (offset) =>{
    return axios({
        method: 'GET',
        url: '/social/get-imported-contacts?offset='+offset,
        dataType: 'text json',
        config: { headers: {'Content-Type': 'application/json; charset=utf-8' }}
    })
    .then(res => {
        if(res.status == 200){
            store.dispatch(getStoredContactsSuccess(res.data));
        }
        else{
            store.dispatch(getStoredContactsFailure(res.data));
        }
    })
    .catch(error => {
    })
}


export const sendInvites = (data) =>{
    let send_these_contacts = JSON.stringify(data);
    return axios({
        method: 'post',
        url: '/ajax/sendInvites',
        data:  send_these_contacts,
        config: { headers: {'Content-Type': 'application/json; charset=utf-8' }}
    })
    .then(res => {
        if(res.data == "success"){
            store.dispatch(sendMultipleInvitesSuccess(res.data));
        }
        else{
            store.dispatch(sendMultipleInvitesFailure(res.data));
        }
    })
    .catch(error => {
    })
}
