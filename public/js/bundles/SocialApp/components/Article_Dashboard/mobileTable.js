import React, { Component } from 'react'
import { Link } from 'react-router-dom'

class MobileTable extends Component{
  render(){
    let { articles, type } = this.props;
    let ARTICLES = ''
    let keys = [];
    if(articles){
      for (let key in articles) {
        if (articles.hasOwnProperty(key)) keys.push(articles[key]);
      }
      ARTICLES = keys.map((article, index) =>{
        if(article.status === type){
          return <TableRow key={index} article={article} />
        }
      });
    }
    return(
        <div>
          { ARTICLES }
        </div>
    )
  }
}

class TableRow extends Component{
  render(){
    let { article } = this.props;
    return(
      <div className="row mobile-table-row">
        <div className="small-4 columns">
          <span className="article-date">{ article && article.created_at }</span>
        </div>
        <div className="small-8 columns">
          <Link to={"/social/article/"+article.id} className="article-title">{article && article.article_title}</Link>
        </div>
      </div>
    )
  }
}

export default MobileTable;
