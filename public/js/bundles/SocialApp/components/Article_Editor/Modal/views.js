import React from 'react'
import Body from '../../Article/body'
import Parser from 'html-react-parser';

export function Article(props){
    let { article } = props;
    return(
        <div className="article_body_banner">
            <Body article={article}/>
        </div>
    )
}

function strip_html_tags(str)
{
   if ((str===null) || (str===''))
       return false;
  else
   str = str.toString();
  return str.replace(/<[^>]*>/g, '');
}

export function NewsFeed(props){
    let { article } = props
    return(
        <div className="news_block_view">
            {
                article.images && article.images[0] && article.images[0].image_link && 
                    <div className="img_parent">
                        <img src={article.images[0].image_link} alt=""/>
                    </div>
            }
            {
                article.article_title &&
                    <div className="post-title">{article.article_title}</div>
            }
            {
                article.article_text &&
                    <div className="post-text post-text_a">{strip_html_tags(article.article_text)}</div>
            }
            <p className="link">PLEXUSS ARTICLE</p>
        </div>
    )
}