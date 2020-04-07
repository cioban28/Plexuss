import axios from 'axios';
import store from '../../stores/socialStore'
import { getRecuCollege, deleteRecuCollege } from './../actions/recuColleges'

export const getRecuColleges = () => {
    return axios({
        method: 'get',
        url: '/ajax/portal/recruitcolleges/data',
    })
    .then(res => {
        store.dispatch(getRecuCollege(res.data.colleges))
    })
    .catch(error => {
        console.log(error);
    })
}

export const deleteRecuColleges = (data, selected, single) => {
    return axios({
        method: 'post',
        url: '/ajax/recruiteme/adduserschooltotrash/data',
        data: data,
    })
    .then(res => {
        store.dispatch(deleteRecuCollege(selected, single))
    })
    .catch(error => {
    })
}
