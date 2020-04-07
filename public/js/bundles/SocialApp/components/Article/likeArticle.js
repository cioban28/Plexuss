import React, { Component } from 'react'
import { like, unlike } from './../../api/post'
class LikeArticle extends Component{
    constructor(props){
        super(props);
        this.state = {
            likeArticle : false,
            count: 0,
        }
        this.handleLikeArticle = this.handleLikeArticle.bind(this);
        this.handleUnLikeArticle = this.handleUnLikeArticle.bind(this);
        this.handler = this.handler.bind(this);
    }
    componentDidMount(){
        const { article, user } = this.props;
        if(article && article.likes){
            let index = article.likes.findIndex(like => like.user_id === user.user_id);
            if(index !== -1){
                this.setState({likeArticle: true});
            }
        }
    }
    componentDidUpdate(prevProps){
        if(prevProps != this.props){
            let { article, user } = this.props;
            if(article && article.likes){
                if(article.likes.some(e => e.user_id === user.user_id)) {
                    this.setState({likeArticle: true})
                }
            }
        }
    }
    handleLikeArticle(){
        let { user, article } = this.props;
        let obj = {};
        obj.user_id = user.user_id;
        obj.post_id = null;
        obj.post_comment_id = null;
        obj.social_article_id = article.id;
        obj.thread_room = "post:room:";
        obj.user_name = user.fname+ ' ' + user.lname;
        obj.target_id = user.user_id;
        like(obj)
        .then(()=>{
            this.setState({
                likeArticle: true,
                count: 1,
            })
        })
    }
    handleUnLikeArticle(){
        let { user, article } = this.props;
        let obj = {};
        obj.user_id = user.user_id;
        obj.post_id = null;
        obj.post_comment_id = null;
        obj.social_article_id = article.id;
        obj.thread_room = "post:room:";
        obj.user_name = user.fname+ ' ' + user.lname;
        obj.target_id = user.user_id;
        unlike(obj)
        .then(()=>{
            this.setState({
                likeArticle: false,
                count: 0,
            })
        })
    }
    handler(){
        if(this.state.likeArticle){
            this.handleUnLikeArticle();
        }else{
            this.handleLikeArticle();
        }
    }
    render(){
        let { article } = this.props;
        let { count } = this.state;
        return(
            <div className="attribute" onClick={()=>this.handler()}>
                <div className="image_banner">
                    <img className="like" src={this.state.likeArticle ? '/social/images/Icons/Heart-Outline-filled@2x.png' : '/social/images/heart-icon.png'} alt=""/>
                </div>
                <div className="attribute_count">
                    {article && article.likes && article.likes.length}
                </div>
                <div className="attribute_name">
                    {'Likes'}
                </div>
            </div>
        )
    }
}
export default LikeArticle;