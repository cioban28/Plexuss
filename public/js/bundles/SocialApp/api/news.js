import axios from 'axios';
import store from '../../stores/socialStore'

import { getNewsSuccess } from './../actions/news';


export const getNews = (subcategory, page=1) => {
    const url = !!subcategory ? `/api/news/subcategory/${subcategory}?page=${page}` :`/api/news?page=${page}`
    return axios({
      method:'GET',
      url: url,
    })
    .then(res => {
      store.dispatch(getNewsSuccess(res.data))
    })
    .catch(error => {
      console.log("-----error", error)
    })
  }

export const getSingleNews = ( articleSlug ) => {
  return axios({
      method:'GET',
      url: `api/news/article/${article_slug}`,
    })
    .then(res => {
      store.dispatch(getNewsSuccess(res.data))
    })
    .catch(error => {
      console.log("-----error", error)
    })
  }
