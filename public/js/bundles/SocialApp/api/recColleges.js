import axios from 'axios';
import store from '../../stores/socialStore'
import { getRecCollege, deleteRecCollege, stopRecCollegesInfiniteScroll } from './../actions/recColleges'

export const getRecColleges = (pageNumber=1) => {
    return axios({
        method: 'get',
        url: `/ajax/portal/reccolleges/data?page=${pageNumber}`,
    })
    .then(res => {
        if(!res.data || !res.data.colleges || !res.data.colleges.length) {
            store.dispatch(stopRecCollegesInfiniteScroll());
        } else {
            store.dispatch(getRecCollege(res.data.colleges))
        }
    })
    .catch(error => {
        console.log(error);
        store.dispatch(stopRecCollegesInfiniteScroll());
    })
}

export const deleteRecColleges = (data, selected, single) => {
    return axios({
        method: 'post',
        url: '/ajax/recruiteme/adduserschooltotrash/data',
        data: data,
    })
    .then(res => {
        store.dispatch(deleteRecCollege(selected, single))
    })
    .catch(error => {
    })
}
