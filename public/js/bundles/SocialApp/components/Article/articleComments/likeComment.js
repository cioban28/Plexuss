import React, { Component } from 'react'
import { like, unlike } from './../../../api/post';
class LikeComment extends Component{
    constructor(props){
        super(props);
        this.state = {
            likeArticleComment : false,
            count: 0,
        }
        this.handleLikeArticleComment = this.handleLikeArticleComment.bind(this);
        this.handleUnLikeArticleComment = this.handleUnLikeArticleComment.bind(this);
        this.handler = this.handler.bind(this);
    }
    componentDidMount(){
        let { comment, user } = this.props;
        if(comment && comment.likes){
            if (comment.likes.some(e => e.user_id === user.user_id)) {
                this.setState({likeArticleComment: true})
            }
        }
    }
    componentDidUpdate(prevProps){
        if(prevProps != this.props){
            let { comment, user } = this.props;
            if(comment && comment.likes){
                if (comment.likes.some(e => e.user_id === user.user_id)) {
                    this.setState({likeArticleComment: true})
                }
            }
        }
    }
    handleLikeArticleComment(){
        let { user, articleId, comment } = this.props;
        let obj = {};
        obj.user_id = user.user_id;
        obj.post_id = null;
        obj.post_comment_id = comment.id;
        obj.social_article_id = articleId;
        obj.thread_room = "post:room:";
        obj.user_name = user.fname+ ' ' + user.lname;
        obj.target_id = user.user_id;
        like(obj)
        .then(()=>{
            this.setState({
                likeArticleComment: true,
                count: 1,
            })
        })
    }
    handleUnLikeArticleComment(){
        let { user, articleId, comment } = this.props;
        let obj = {};
        obj.user_id = user.user_id;
        obj.post_id = null;
        obj.post_comment_id = comment.id;
        obj.social_article_id = articleId;
        obj.thread_room = "post:room:";
        obj.user_name = user.fname+ ' ' + user.lname;
        obj.target_id = user.user_id;
        unlike(obj)
        .then(()=>{
            this.setState({
                likeArticleComment: false,
                count: 0,
            })
        })
    }
    handler(){
        if(this.state.likeArticleComment){
            this.handleUnLikeArticleComment();
        }else{
            this.handleLikeArticleComment();
        }
    }
    render(){
        let { comment } = this.props;
        const { count } = this.state;
        return(
            <li onClick={()=>this.handler()}>
                <img src={this.state.likeArticleComment ? '/social/images/Icons/Heart-Outline-filled@2x.png' : '/social/images/heart-icon.png'} alt=""/>
                <span className="count">{comment && comment.likes && (comment.likes.length)}</span>
            </li>
        )
    }
}
export default LikeComment;