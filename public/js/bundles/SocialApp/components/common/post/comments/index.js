import React, { Component } from 'react'
import Comment from './comment'
class Comments extends Component{
    render(){
        let { handleMobileCommentd, comments, logInUser, postId, type, editComment, post } = this.props;
        return(
            <div>
                <div className="comment-header row comment_mbl_header">
                    <div className="small-1 columns" onClick={handleMobileCommentd}> <i className="fa fa-angle-left"></i></div>
                    <div className="small-11 columns" > {comments.length} {comments.length === 1 ? ' Comment' : ' Comments' }</div>
                </div>
                {comments.map((comment, index) =>
                    <Comment key={comment.id} comment={comment} logInUser={logInUser} postId={postId} type={type} editComment={editComment} post={post}/>)}
            </div>
        )
    }
}
export default Comments;
