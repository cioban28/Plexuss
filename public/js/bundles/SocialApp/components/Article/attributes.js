import React, { Component } from 'react'
import Attribute from './attribute'
import LikeArticle from './likeArticle.js'
import { Link, withRouter } from 'react-router-dom'
import SharePost from './../common/post/share'
class Attributes extends Component{
    render(){
        let { article, user, handleScrollToElement, history } = this.props;
        return(
            <div className="attributes_banner">
                <span className="back" onClick={() => history.goBack()}>
                    <i className="fa fa-arrow-left"></i>
                    <span className="text">Back</span>
                </span>
                <div className="attributes">
                    <LikeArticle article={article} user={user}/>
                    <Attribute imageSrc={'/social/images/noun_comment.png'} count={article && article.comments && article.comments.length > 0 ? article.comments.length : ''} name={'Comment'} classname={'comment'} handleScrollToElement={handleScrollToElement}/>
                    {
                        article && article.user &&
                        <div className="attribute">
                            <SharePost post={article} user={article.user} latestPost={article}/>
                        </div>
                    }
                </div>
            </div>
        )
    }
}
export default withRouter(Attributes);
