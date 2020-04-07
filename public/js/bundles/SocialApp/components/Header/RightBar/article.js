import React, { Component } from 'react'
import DeleteArticle from './Delete_Article';
import { Link } from 'react-router-dom'
import orderBy from 'lodash/orderBy'
import moment from 'moment'

const _ = {
    orderBy: orderBy
}

class Article extends Component{
    render(){
        let { articles, type } = this.props;

        return(
            <span>
                { _.orderBy(articles,['created_at'], ['desc']).map((article, index) => <SingleArticle key={index} article={article} type={type} /> )}
            </span>
        )
    }
}
class SingleArticle extends Component{
    _handleDate(_date) {
        let date = moment.utc(_date).local().format('YYYY-MM-DD HH:mm:ss');
        // date = moment(date).format('YYYY-MM-DD HH:mm:ss');
        let dateArray = date.split(" ");
        let dayArray = dateArray[0].split("-");
        let timeArray = dateArray[1].split(":");
        let day = dayArray[1]+"/"+dayArray[2]+"/"+dayArray[0];
        let time = (timeArray[0] > 12 ? timeArray[0] - 12 : timeArray[0]) +":"+ timeArray[1] + (timeArray[0] > 12 ? 'PM' : 'AM')

        return day +" @ "+ time;
    }

    render(){
        let { article, type } = this.props;
        return(
            <div className="article_container">
                <div className="article_title"> <Link to={`/social/article/${article.id}`}>{article && article.article_title}</Link></div>
                <div className="article_actions">
                    <Link to={`/social/article-editor/${article.id}`}><img className="action_icon" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Edit.svg" /></Link>
                    <DeleteArticle article={article && article}/>
                </div>
                <div className="published_time">
                    {(type === 0 ? 'DRAFTED ' : 'PUBLISHED ') + (article && article.created_at && this._handleDate(article.created_at) )}
                </div>
            </div>
        )
    }
}
export default Article;
