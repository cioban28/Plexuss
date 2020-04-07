import React, { Component } from 'react'
import SharePost from './share'
import Modal from 'react-modal';
import ShowingModal from './showingModal';
import { like, unlike } from './../../../api/post'

class PostFunctionality extends Component{
    constructor(props){
        super(props);
        this.state={
            likePost: false,
            flag: true,
            isOpen: false,
            type: 1,
        }
        this.likePostFunctionality = this.likePostFunctionality.bind(this);
        this.handleComment = this.handleComment.bind(this)
    }
    componentDidMount(){
        let { post, logInUser } = this.props;
        let index = post.likes.findIndex(like => like.user_id == logInUser.user_id);
        if(index !== -1){
            this.setState({
                likePost: true,
            })
        }
    }
    componentWillReceiveProps(nextProps){
        if(nextProps.post != this.props.post){
            let { post, logInUser } = nextProps;
            let index = post.likes.findIndex(like => like.user_id == logInUser.user_id);
            if(index !== -1){
                this.setState({
                    likePost: true,
                })
            }else{
                this.setState({
                    likePost: false,
                })
            }
        }
    }
    likePostFunctionality(){
        let { post, logInUser } = this.props;
        let obj = {};
        obj.user_id = logInUser.user_id;
        obj.post_comment_id = null;
        obj.user_name = logInUser.fname+ ' '+logInUser.lname ;
        obj.target_id = post.user_id;
        obj.is_shared = post.is_shared;
        if(post.hasOwnProperty('article_text')){
            obj.post_id = null;
            obj.thread_room = 'post:room:';
            obj.social_article_id = post.id;
        }else{
            obj.post_id = post.id;
            obj.thread_room = 'post:room:';
            obj.social_article_id = null;
        }
        if(this.state.flag){
            this.setState({flag: false}, ()=>{
                if(this.state.likePost){
                    unlike(obj)
                    .then(()=>{
                        this.setState({
                            likePost: false,
                            flag: true,
                        })
                    })
                }
                else if(!this.state.likePost){
                    like(obj)
                    .then(()=>{
                        this.setState({
                            likePost: true,
                            flag: true
                        })
                    })
                }
            })
        }
    }
    handleComment() {
        if (this.props.isMobilePreview) {
            this.props.handleCommentBox()
        }
        this.props.showDesktopComment()
        this.props.handleMobileCommentd()
    }
    showModal = (type) => {
        this.setState({isOpen: true, type: type})
    }
    handleClose = () => {
        this.setState({isOpen: false})
    }
    render(){
        let { isMobilePreview, post, postState,  handleMobileCommentd, showDesktopComment, desktopComment } = this.props;
        let fldg_f = false;
        if(post.share_count > 0 || post.comments.length > 0 || post.likes.length > 0){
            fldg_f = true;
        }
        const {isOpen, type} = this.state
        return(
            <div>
            {
                fldg_f &&
                <div className="functionality_count">
                    <div className="share_count" onClick={()=>{this.showModal('share')}}>{post.share_count > 0 && post.share_count }  {post.share_count > 0 && (post.share_count > 1 ? ' Shares' : 'Share')}</div>
                    <div className="comments_count" onClick={()=>{this.showModal('comment')}}>{ post.comments && post.comments.length > 0 && post.comments.length } {post.comments.length > 0 && (post.comments.length > 1 ? 'Comments': 'Comment')}</div>
                    <div className="likes_count" onClick={()=>{this.showModal('like')}}>
                        {post.likes && post.likes.length > 0 && post.likes.length } {post.likes.length > 0 && (post.likes.length > 1 ? 'Likes' : 'Like')}
                    </div>
                </div>
            }
                <div className={"functionality_icons row "+ (desktopComment ? '' : 'border_radius_content') + (fldg_f ? '' : ' _inline_block') }>
                    <div className="large-4 medium-4 small-4 columns like_icon" onClick={this.likePostFunctionality}>
                        <div className="row" >
                            <div  className="large-6 medium-6 small-6 columns heart_icon">
                                <img src={ this.state.likePost ? "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Like-active.svg" : isMobilePreview ? "/social/images/like-white.svg" : "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Like-inactive.svg" } />
                            </div>
                            <span className="large-6 medium-6 small-6 columns heart_text">{ this.state.likePost ? ' Liked' : ' Like'} </span>
                        </div>
                    </div>
                    <div className="large-4 medium-4 small-4 columns comment_icon" onClick={this.handleComment}>
                        <img src={isMobilePreview ? "/social/images/comment-white.svg" : "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Comment.svg"} />
                        <span>{' Comment'}</span>
                    </div>
                    <SharePost isMobilePreview={isMobilePreview} post={postState} user={post.user} latestPost={post}/>
                </div>
                {
                isOpen &&
                  <Modal isOpen={isOpen} onRequestClose={this.handleClose} className='comment-modal'>
                    <ShowingModal onClose={this.handleClose} tab={type} post={post}/>
                  </Modal>
                }
            </div>
        )
    }
}
export default PostFunctionality;
