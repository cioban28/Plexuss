import React, { Component } from 'react'
import TextArea from './comments/text'
import Comments  from './comments/index';
class DesktopComments extends Component{
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
    let { post, logInUser } = this.props;
    const { editMode, comment } = this.state;
    return(
      <span className="post_mbl_view">
        {
          post && (post.hasOwnProperty('article_text'))&& 
          <span>
            <Comments post={post} comments={post.comments} postId={post.id} logInUser={logInUser} type={0} editComment={this.editComment}/>
            <TextArea  post={post} logInUser={logInUser} type={0} editMode={editMode} comment1={comment} offEditMode={this.offEditMode}/>
          </span> ||
          post && (post.hasOwnProperty('post_text')) &&
          <span>
            <Comments post={post} comments={post.comments} postId={post.id} logInUser={logInUser} type={1} editComment={this.editComment}/>
            <TextArea  post={post} logInUser={logInUser}  type={1} editMode={editMode} comment1={comment} offEditMode={this.offEditMode}/>
          </span>
        }
      </span>
    )
  }
}
export default DesktopComments;