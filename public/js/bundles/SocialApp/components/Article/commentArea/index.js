import React, { Component } from 'react'
import { Link } from 'react-router-dom'
import objectToFormData from 'object-to-formdata'
import './styles.scss'
import { saveArticleComment } from './../../../api/article'
class CommentArea extends Component {
    constructor(props){
        super(props);
        this.state = {
            text: '',
        }
        this.keyPress = this.keyPress.bind(this);
        this.onChange = this.onChange.bind(this);
    }
    keyPress(event){
        if(event.keyCode == 13){
            event.preventDefault();
            let { articleId, user } = this.props;
            let comment = {};
            comment.parent_id = null;
            comment.user_id = user.user_id;
            comment.comment_text = this.state.text;
            comment.shared_link = '';
            comment.comment_images = null;
            comment.post_id = null;
            comment.article_id = articleId;
            comment.is_gif = false;
            comment.gif_link = null;
            comment.thread_room = 'post:room:';
            comment.target_id = user.user_id;
            comment.user_name = user.fname+ ' '+ user.lname;
            comment.is_shared = null;
            const formData = objectToFormData(comment);
            saveArticleComment(formData)
            .then(()=>{
                this.setState({text: ''});
            });
        }
    }
    onChange(event){
        this.setState({
            text: event.target.value,
        })
    }
    render(){
        let { user } = this.props;
        return(
            <div className="article_comment_area">
                <div className="image_banner">
                    <img src={user.profile_img_loc ? "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/"+user.profile_img_loc : "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/user-avatar-r1.png"} alt=""/>
                </div>
                {!!user.userAccountSettings && !!user.userAccountSettings.is_incognito ?
                    <div className="incognito-comment">
                        <img src='/social/images/settings/active_options/noun_Ghost_367889_000000.png'/>
                        <Link className="incognito-link" to={'/social/settings'}> Turn off Incognito Mode to make a comment </Link>
                    </div>
                    : (user.userAccountSettings === null || !!user.userAccountSettings && !user.userAccountSettings.is_incognito) &&
                    <textarea placeholder="Say something" className="textarea" onKeyDown={this.keyPress} onChange={this.onChange} value={this.state.text}></textarea>
                }
            </div>
        )
    }
}

export default CommentArea;