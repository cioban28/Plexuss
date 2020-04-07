import axios from 'axios';
import store from '../../stores/socialStore'
import { getViewCollege, deleteViewCollege } from './../actions/viewColleges'

export const getViewColleges = () => {
    return axios({
        method: 'get',
        url: '/ajax/portal/viewedcolleges/data',
    })
    .then(res => {
        store.dispatch(getViewCollege(res.data.colleges))
    })
    .catch(error => {
        console.log(error);
    })
}

export const deleteViewColleges = (data, selected, single) => {
    return axios({
        method: 'post',
        url: '/ajax/recruiteme/adduserschooltotrash/data',
        data: data,
    })
    .then(res => {
        store.dispatch(deleteViewCollege(selected, single))
    })
    .catch(error => {
    })
}
