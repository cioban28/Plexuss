import React, { Component } from 'react'
import ArticleImage from './articleImage'
import ArticleText from './articleText'
import { DeletedPost } from './helper'
class Body extends Component{
    isEmpty(obj) {
        for(var key in obj) {
            if(obj.hasOwnProperty(key))
                return false;
        }
        return true;
    }
    render(){
        let { article, deletedShareArticle } = this.props;
        return(
            !deletedShareArticle && !this.isEmpty(article) &&
            <div className="article_body_banner">
                <ArticleImage article={article}/>
                <ArticleText article={article}/>
            </div> ||
            deletedShareArticle &&
            <DeletedPost />
        )
    }
}


export default Body;