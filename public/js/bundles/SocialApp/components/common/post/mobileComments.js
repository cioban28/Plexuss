import React, { Component } from 'react'
import TextArea from './comments/text'
import Comments  from './comments/index';
class MobileComments extends Component{
    constructor(props){
        super(props);
        this.state={
            editMode: false,
            comment: {}
        }
        this.editComment = this.editComment.bind(this);
        this.offEditMode = this.offEditMode.bind(this);
    }
    editComment(comment){
        this.setState({
            editMode: true,
            comment: comment
        })
    }
    offEditMode(){
        this.setState({
            editMode: false,
        })
    }
    render(){
        let { mobile_comments, post, logInUser, handleMobileCommentd  } = this.props;
        const { editMode, comment } = this.state;
        return(
            <span className={"desktop_none " + (mobile_comments ? 'comments_mbl_view' : '')}>
            {
                mobile_comments && post && (post.hasOwnProperty('article_text')) && 
                <span>
                    <Comments post={post} comments={post.comments} postId={post.id} logInUser={logInUser} handleMobileCommentd={handleMobileCommentd} type={0} editComment={this.editComment}/>               
                    <TextArea  post={post} logInUser={logInUser} type={0} editMode={editMode} comment1={comment} offEditMode={this.offEditMode}/>
                </span> ||
                mobile_comments && post && (post.hasOwnProperty('post_text')) &&
                <span>
                    <Comments post={post} comments={post.comments} postId={post.id} logInUser={logInUser} handleMobileCommentd={handleMobileCommentd} type={1} editComment={this.editComment}/>
                    <TextArea  post={post} logInUser={logInUser} type={1} editMode={editMode} comment1={comment} offEditMode={this.offEditMode}/>
                </span>
            }
            </span>
        )
    }
}

export default MobileComments;