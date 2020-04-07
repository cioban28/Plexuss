import React, { Component } from 'react'
import Title from './title'
import AuthorInfo from './authorInfo'
import Content from './content'
class ArticleText extends Component{
    render(){
        let { article } = this.props;
        return(
            <div className="article_text">
                <Title title={article && article.article_title && article.article_title}/>
                <AuthorInfo article={article}/>
                <Content content={(article && article.article_text) ? article.article_text : ''}/>
            </div>
        )
    }
}
export default ArticleText;