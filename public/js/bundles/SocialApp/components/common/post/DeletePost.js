import React from 'react';
import Modal from 'react-modal';
import { deletePost, updatePostSharedCount } from './../../../api/post'
import { deleteArticle, updateArticleShares } from './../../../api/article'
import { disableBodyScroll, enableBodyScroll, clearAllBodyScrollLocks } from 'body-scroll-lock';

Modal.setAppElement(document.getElementById('_SocialApp_Component'))
class DeletePost extends React.Component{
  constructor() {
    super();
    this.state = {
      optionBox: false,
      modalIsOpen: true,
    };
    this.scrollLockEl;
    this.openModal = this.openModal.bind(this);
    this.afterOpenModal = this.afterOpenModal.bind(this);
    this.closeModal = this.closeModal.bind(this);
    this.deletePost = this.deletePost.bind(this);
  }

  openModal() {
    let { deleteModal } = this.props;
    this.setState({modalIsOpen: deleteModal});
  }
  afterOpenModal() {
    this.scrollLockEl = document.querySelector('.ReactModal__Overlay');
    disableBodyScroll(this.scrollLockEl);
  }
  closeModal() {
    let { confirmDeletePost } = this.props;
    confirmDeletePost();
    this.setState({modalIsOpen: false});
    enableBodyScroll(this.scrollLockEl);
  }
  componentWillUnmount() {
    clearAllBodyScrollLocks();
  }
  deletePost(id, type, post, logInUser){
    if(post.share_post_type){
      if(post.share_post_type == 'article'){
        let article_data = {};
        article_data.article_id = post.share_post_id;
        article_data.thread_room = 'post:room:';
        article_data.update = 'decrement';
        updateArticleShares(article_data);
      }else{
        let _data = {};
        _data.post_id = post.share_post_id;
        _data.thread_room = 'post:room:';
        _data.update = 'decrement';
        updatePostSharedCount(_data);
      }
    }
    if (type == 'Post') {
      let obj ={
        post_id: id,
        thread_room: 'post:room:',
        is_sales: logInUser.is_sales ? logInUser.is_sales : false,
      }
      deletePost(obj);
      this.setState({modalIsOpen: false});
    } else if (type == 'Article') {
      let obj ={
        article_id: id,
        thread_room: 'post:room:',
        is_sales: logInUser.is_sales ? logInUser.is_sales : false
      }
      deleteArticle(obj);
      this.setState({modalIsOpen: false});
    } else if (type == 'Comment'){

      this.setState({modalIsOpen: false});
    }
  }
  render(){
    let { entityName, id, post, logInUser } = this.props;
    return(
      <span>
        <Modal
          isOpen={this.state.modalIsOpen}
          onAfterOpen={this.afterOpenModal}
          onRequestClose={this.closeModal}
          className="delete-modal"
          contentLabel="Delete your post"
        >
          <div className="delete_article_container">
            <div className="modal_heading">
                Delete {entityName}
                <div className="modal_x" style={{float: 'right'}} onClick={this.closeModal}>&#10005;</div>
            </div>
            <div className="delete_article_block">
              <div className="modal_message">
                Are you sure you want to delete your {entityName}?
              </div>
            </div>
            <div className="action_button cancel" onClick={this.closeModal}> Cancel </div>
            <div className="action_button delete" onClick={() => this.deletePost(id, entityName, post, logInUser)}> Delete </div>
          </div>
        </Modal>
      </span>
    )
  }
}

export default DeletePost;
