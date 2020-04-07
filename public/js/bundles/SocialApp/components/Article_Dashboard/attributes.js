import React, { Component } from 'react'
class Attributes extends Component{
    render(){
        let { articles } = this.props;
        let likesCount = 0;
        let commentCount = 0;
        let viewsCount = 0;
        let shareCount = 0;
        let keys = [];
        if(articles){
            for (let key in articles) {
                if (articles.hasOwnProperty(key)) keys.push(articles[key]);
            }
            keys.map((article) =>{
                likesCount += article.likes.length;
                commentCount += article.comments.length;
                if(article.views){
                    viewsCount =  viewsCount + article.views.length;
                }
                shareCount += article.share_count;
            });
        }
        return(
            <div>

                <div className="row stats-area">
                <div className="large-3 medium-3 small-3 columns">
                    <div className="row">
                    <a href="#" data-reveal-id="makePostModal" className="stats-box">
                        <div className="large-5 medium-5 columns">
                        <img src="/social/images/article/top/Views/Views.png" className="stats-image" />
                        </div>
                        <div className="large-7 medium-7 small-12 columns">
                        <span>
                           {viewsCount}
                        </span>
                        <div className="stats-label">
                            <span>Views</span>
                        </div>
                        </div>
                    </a>
                    </div>
                </div>
                <div className="large-3 medium-3 small-3 columns">
                    <div className="row">
                    <a href="#" data-reveal-id="makePostModal" className="stats-box">
                        <div className="large-5 medium-5 columns">
                        <img src="/social/images/article/top/Likes/Likes.png" className="stats-image" />
                        </div>
                        <div className="large-7 medium-7 small-12 columns">
                        <span>
                            {likesCount}
                        </span>
                        <div className="stats-label">
                            <span>Likes</span>
                        </div>
                        </div>
                    </a>
                    </div>
                </div>
                <div className="large-3 medium-3 small-3 columns">
                    <div className="row">
                    <a href="#" data-reveal-id="makePostModal" className="stats-box">
                        <div className="large-5 medium-5 columns">
                        <img src="/social/images/article/top/comments/comments.png" className="stats-image" />
                        </div>
                        <div className="large-7 medium-7 small-12 columns">
                        <span>
                            {commentCount}
                        </span>
                        <div className="stats-label">
                            <span>Comments</span>
                        </div>
                        </div>
                    </a>
                    </div>
                </div>
                <div className="large-3 medium-3 small-3 columns">
                    <div className="row">
                    <a href="#" data-reveal-id="makePostModal" className="stats-box">
                        <div className="large-5 medium-5 columns">
                        <img src="/social/images/article/top/share/share.png" className="stats-image" />
                        </div>
                        <div className="large-7 medium-7 small-12 columns">
                        <span>
                            {shareCount}
                        </span>
                        <div className="stats-label">
                            <span>Shares</span>
                        </div>
                        </div>
                    </a>
                    </div>
                </div>
                </div>
            </div>
        )
    }
}
export default Attributes;
