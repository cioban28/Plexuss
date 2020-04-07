import axios from 'axios';
import store from '../../stores/socialStore'
import { getFavCollege, deleteFavCollege } from './../actions/favColleges'

export const getFavColleges = () => {
    return axios({
        method: 'get',
        url: '/ajax/portal/favcolleges/data',
    })
    .then(res => {
        store.dispatch(getFavCollege(res.data.colleges))
    })
    .catch(error => {
        console.log(error);
    })
}

export const deleteFavColleges = (data, selected) => {
    return axios({
        method: 'post',
        url: '/ajax/recruiteme/adduserschooltotrash/data',
        data: data,
    })
    .then(res => {
        store.dispatch(deleteFavCollege(selected))
    })
    .catch(error => {
    })
}
