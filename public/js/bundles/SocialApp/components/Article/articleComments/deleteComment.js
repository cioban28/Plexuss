import React, { Component } from 'react'
import { deleteComment } from './../../../api/post';
class DeleteComment extends Component{
    constructor(props){
        super(props);
        this.deleteComment = this.deleteComment.bind(this);
    }
    deleteComment(){
        let { comment } = this.props;
        let data = {
            comment_id: comment.id,
            post_id: null,
            social_article_id: comment.social_article_id,
            thread_room: 'post:room:'
        };
        deleteComment(data);
    }
    render(){
        return(
            <li className="trash_parent" onClick={this.deleteComment}>
                <img src="/social/images/Icons/trash@2x.png" className="trash" />
            </li>
        )
    }
}
export default DeleteComment