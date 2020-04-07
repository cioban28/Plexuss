import React, { Component } from 'react'
import objectToFormData from 'object-to-formdata'
import { editComment } from './../../../api/post'
import LikeComment from './likeComment'
import DeleteComment from './deleteComment'
class Functionality extends Component{
    constructor(props){
        super(props);
        this.state = {
            editFlag: false,
            text: '',
        }
        this.toggleEditFlag = this.toggleEditFlag.bind(this);
        this.keyPress = this.keyPress.bind(this);
        this.onChange = this.onChange.bind(this);
    }
    componentDidMount(){
        const { comment } = this.props;
        this.setState({
            text: comment.comment_text,
        })
    }
    toggleEditFlag(){
        this.setState({
            editFlag: !this.state.editFlag,
        })
    }
    keyPress(event){
        if(event.keyCode == 13){
            event.preventDefault();
            let { articleId, user, comment } = this.props;
            let editcomment = {};
            editcomment.comment_id = comment.id;
            editcomment.removeImage = -1;
            editcomment.parent_id = null;
            editcomment.user_id = user.user_id;
            editcomment.comment_text = this.state.text;
            editcomment.shared_link = '';
            editcomment.comment_images = -1;
            editcomment.post_id = null;
            editcomment.article_id = articleId;
            editcomment.is_gif = false;
            editcomment.gif_link = null;
            editcomment.thread_room = 'post:room:';
            editcomment.target_id = user.user_id;
            editcomment.user_name = user.fname+ ' '+ user.lname;
            editcomment.is_shared = null;
            const formData = objectToFormData(editcomment);
            editComment(formData)
            .then(()=>{
                this.setState({
                    editFlag: false,
                    text: '',
                });
            });
        }
    }
    onChange(event){
        this.setState({
            text: event.target.value,
        })
    }
    render(){
        let { comment, articleId, user } = this.props;
        const { editFlag } = this.state;
        return(
            <div className="icons_banner">
                {
                    editFlag &&
                    <textarea placeholder="Say something" className="textarea_1" onKeyDown={this.keyPress} onChange={this.onChange} value={this.state.text}></textarea>
                }{
                    !editFlag &&
                    <ul>
                        <LikeComment comment={comment} articleId={articleId} user={user}/>
                        {
                            user.user_id == comment.user_id &&
                            <div className="edit_delete_banner">
                                <li className="edit_parent" onClick={this.toggleEditFlag}>
                                    <img src="/social/images/Icons/pencil@2x.png" className="edit" />
                                </li>
                                <DeleteComment comment={comment} articleId={articleId} user={user}/>
                            </div>
                        }
                    </ul>
                }
            </div>
        )
    }
}
export default Functionality;