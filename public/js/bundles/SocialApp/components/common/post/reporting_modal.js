import React, { Component } from 'react'
import moment from 'moment'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import { hidePostOrArticle } from './../../../api/post'
import { hidePostAction } from './../../../actions/posts'
import ShareModal from './../feedBlock/ShareModal'
import ReportingReasonModal from './ReportingReasonModal'
import TimeAgo from 'react-timeago'
import DeletePost from './DeletePost'

class ReportingModal extends Component{
    constructor(props) {
        super(props);
        this.state = {
          modalIsOpen: false,
          deleteModal: false,
          localDate: new Date(),
          openEditModalFlag: false,
        };
        this.openModal = this.openModal.bind(this);
        this.closeModal = this.closeModal.bind(this);
        this.openOptionBox = this.openOptionBox.bind(this);
        this.closeOptionBox = this.closeOptionBox.bind(this);
        this.toggleOptionBox = this.toggleOptionBox.bind(this);
        this.reportPost = this.reportPost.bind(this);
        
        this.confirmDeletePost = this.confirmDeletePost.bind(this);
        this.handleClickOptionsBox = this.handleClickOptionsBox.bind(this);
        this.handleOutsideClick = this.handleOutsideClick.bind(this);
        this.openEditModal = this.openEditModal.bind(this);
        this.closeEditModal = this.closeEditModal.bind(this);
        this.hidePost = this.hidePost.bind(this);
    }
    componentDidMount(){
        const { post } = this.props;
        let localtime = moment.utc(post.created_at).local();
        this.setState({localDate: localtime})
    }
    openModal() {
        this.setState({modalIsOpen: true});
    }
    closeModal() {
        this.setState({modalIsOpen: false});
    }
    openOptionBox(){
        this.setState({optionBox: true});
    }
    closeOptionBox(){
        this.setState({optionBox: false});
    }
    toggleOptionBox(){
        this.setState({optionBox: !this.state.optionBox});
    }
    reportPost(){
        this.closeOptionBox();
        this.openModal();
    }
    confirmDeletePost(){
        this.setState({deleteModal: !this.state.deleteModal});
    }
    EditPost(id){
        window.location.href = '/social/article-editor/' + id;
    }
    handleClickOptionsBox() {
      if (!this.state.optionBox) {
          // attach/remove event handler
          document.addEventListener('click', this.handleOutsideClick, false);
        } else {
          document.removeEventListener('click', this.handleOutsideClick, false);
        }

        this.setState(prevState => ({
           optionBox: !prevState.optionBox,
        }));
    }
    
    handleOutsideClick(e) {
        if (this.node.contains(e.target)) {
        return;
      }
      this.handleClickOptionsBox();
    }
    openEditModal(){
        this.setState({openEditModalFlag: true})
    }
    closeEditModal(){
        this.setState({openEditModalFlag: false})
    }
    hidePost(){
        let { post, hidePostAction } = this.props;
        let data = {};
        let _obj = {};
        _obj.id = post.id;
        if(post.hasOwnProperty('post_text')){
            data.post_id = post.id;
            data.social_article_id = null;
            _obj.type = 'post';
        }else{
            data.post_id = null;
            data.social_article_id = post.id;
            _obj.type = 'article';
        }
        hidePostOrArticle(data);
        hidePostAction(_obj);
    }

    render(){
        let { post, logInUser } = this.props;
        let localtime = moment.utc(post.created_at).local();
        return(
            <span ref={node => { this.node = node; }}>
                <div className="timearea" onClick={this.handleClickOptionsBox}>
                <img src="/social/images/arrow.svg" className="arrow-image" />
                <span className="time"><TimeAgo date={localtime} /></span>
                {this.state.optionBox &&
                    <div className="postOptions">
                        {
                            post.user_id === logInUser.user_id &&
                            <span>
                                {
                                    post.hasOwnProperty('article_text') &&
                                        <Link to={'/social/article-editor/'+post.id}><div>Edit</div></Link> ||
                                    post.hasOwnProperty('post_text') &&
                                        <div onClick={this.openEditModal}>Edit</div>
                                }
                            </span>
                        }                            
                        {
                            (post.user_id === logInUser.user_id || logInUser.is_sales) &&
                            <div onClick={this.confirmDeletePost}>Delete</div>
                        }
                        <div onClick={this.hidePost}>Hide Post</div>
                        {
                            post.user_id != logInUser.user_id &&
                            <span>
                                <div onClick={this.reportPost}>Report</div>
                                {/* <div>Block</div> */}
                            </span>
                        }

                    </div>
                }
                </div>
                {
                    this.state.openEditModalFlag && post &&
                    <ShareModal openGiphyFlag={false} closeModal={this.closeEditModal} user={this.props.logInUser} editMode={true} post={post}/>
                }
                {this.state.modalIsOpen && <ReportingReasonModal post={post} modalIsOpen={this.state.modalIsOpen} closeModal={this.closeModal} /> }
                {
                    this.state.deleteModal &&
                    <DeletePost logInUser={logInUser} post={post} id={post.id} entityName= {post.hasOwnProperty('post_text') ? 'Post' : 'Article'} deleteModal={this.state.deleteModal} confirmDeletePost={this.confirmDeletePost}/>
                }
            </span>
        )
    }
}
function mapDispatchToProps(dispatch) {
    return({
        hidePostAction: (data) => {dispatch(hidePostAction(data))}
    })
}
export default connect(null, mapDispatchToProps)(ReportingModal);
