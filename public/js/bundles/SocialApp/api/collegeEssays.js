import axios from 'axios';
import store from '../../stores/socialStore'

import { getPremiumArticlesSuccess } from './../actions/collegeEssays';


export const getPremiumArticles = (page = 1) => {
  return axios({
    method: 'POST',
    url: `/api/getPremiumArticles?page=${page}`,
    })
    .then(res => {
      store.dispatch(getPremiumArticlesSuccess(res.data.newsdata))
    })
    .catch(error => {
      console.log("error", error)
    }
  )
}
