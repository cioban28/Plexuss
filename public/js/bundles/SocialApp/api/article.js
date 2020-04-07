import axios from 'axios';
import store from '../../stores/socialStore'
import { saveArticles, getArticlesAction, getSingleArticleAction, updateArticleAction, deleteArticlesAction, saveArticleCommentAction,
        deleteArticleAction } from './../actions/article'
import { publishPost } from '../actions/posts'


import {toastr} from 'react-redux-toastr'

export const getArticles = () => {
    return axios({
        method: 'get',
        url: '/social/get-articles',
    })
    .then(res => {
        store.dispatch(getArticlesAction(res.data))
    })
    .catch(error => {
    })
} 
export const newGetArticles = (setDraftsTab=false) => {
    return axios({
        method: 'get',
        url: '/social/get-articles',
    })
    .then(res => {
        res.data.setDraftsTab = true
        store.dispatch(getArticlesAction(res.data))
    })
    .catch(error => {
    })
} 
export const getSingleArticle = (id) => {
    return axios({
        method: 'get',
        url: '/social/get-single-articles?article-id='+id,
    })
    .then(res => {
        store.dispatch(getSingleArticleAction(res.data))
        return res;
    })
    .catch(error => {
    })
}

export const saveArticle = (data, is_shared, notoaster) => {
    return axios({
        method: 'post',
        url: '/social/save-article',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
    })
    .then(res => {
        if(res.statusText == "OK"){
            if(is_shared){
                if(res.data.status == 2){
                    toastr.info('Article','Article Already Shared');
                }else{
                    toastr.success('Shared Article', 'Shared Article Successfully');
                }
            }else{
                if (!notoaster )
            {
                toastr.success('Save Article', 'Save Article Successfully');
            }
                window.history.pushState("", "", '/social/article-editor/' + res.data.article_id);
            }

        }
            newGetArticles(true);
            store.dispatch(publishPost(res.data.article));
            store.dispatch(saveArticles(res.data))
    })
    .catch(error => {
       if (!notoaster )
        {
          toastr.error('Save Article', 'Save Article Failure');
        }

    })
}

export const updateArticle = (data, notoaster) => {
    return axios({
        method: 'post',
        url: '/social/update-article',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
    })
    .then(res => {
        if(res.statusText == "OK"){
            if (!notoaster )
            {
                toastr.success('Update Article', 'Update Article Successfully');
            }
            newGetArticles(true);
            store.dispatch(updateArticleAction(res.data))
        }
    })
    .catch(error => {
      if (!notoaster )
        {
          toastr.error('Save Article', 'Save Article Failure');
        }
    })
}
export const updateArticleShares = (data) => {
    return axios({
        method: 'post',
        url: '/social/update-article-shares',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
    })
    .then(res => {
    })
}
export const deleteArticle = (data) => {
    return axios({
        method: 'delete',
        url: '/social/delete-article',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
    })
    .then(res => {
        if(res.statusText == "OK"){
            toastr.success('Delete Article', 'Delete Article Successfully');
            store.dispatch(deleteArticlesAction(data.article_id))
            getArticles()
        }
    })
    .catch(error => {
        toastr.error('Delete Article', 'Delete Article Failure');
    })
}
export const saveArticleComment = (data) => {
    return axios({
        method: 'post',
        url: '/social/save-article-comment',
        data: data,
        config: { headers: {'Content-Type': 'multipart/form-data' }},
    })
    .then(res => {
        if(res.statusText == "OK"){
            toastr.success('Save Comment', 'Save Comment Successfully');
            store.dispatch(saveArticleCommentAction(res.data))
        }
    })
    .catch(error => {
        toastr.error('Save Article', 'Save Article Failure');
    })
}
