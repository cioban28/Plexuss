import React from 'react';
import Modal from 'react-modal';
import { connect } from 'react-redux'
import { deleteArticle } from './../../../api/article'
import { disableBodyScroll, enableBodyScroll, clearAllBodyScrollLocks } from 'body-scroll-lock';

Modal.setAppElement(document.getElementById('_SocialApp_Component'))
class SharePost extends React.Component{
  constructor() {
    super();
    this.state = {
      optionBox: false,
      modalIsOpen: false,
    };
    this.scrollLockEl = null;
    this.openModal = this.openModal.bind(this);
    this.afterOpenModal = this.afterOpenModal.bind(this);
    this.closeModal = this.closeModal.bind(this);
    this.deleteArticle = this.deleteArticle.bind(this);
  }
  openModal() {
    this.setState({modalIsOpen: true});
  }
  afterOpenModal() {
    this.scrollLockEl = document.querySelector('.ReactModal__Overlay');
    disableBodyScroll(this.scrollLockEl);
  }
  closeModal() {
    this.setState({modalIsOpen: false});
  }
  deleteArticle(){
    let { article, user } = this.props;
    let obj ={
      article_id: article.id,
      thread_room: 'post:room:',
      is_sales: user.is_sales ? user.is_sales : false,
    }
    deleteArticle(obj);
    this.setState({modalIsOpen: false});
    enableBodyScroll(this.scrollLockEl);
  }
  componentWillUnmount() {
    clearAllBodyScrollLocks();
  }
  render(){
    let { article } = this.props;
    return(
      <span>
        <img className="action_icon" onClick={this.openModal} src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/trash.svg" />
        <Modal
          isOpen={this.state.modalIsOpen}
          onAfterOpen={this.afterOpenModal}
          onRequestClose={this.closeModal}
          className="delete-modal"
          contentLabel="sharing"
        >
          <div className="delete_article_container">
            <div className="modal_heading">
                Delete Article
                <div style={{float: 'right'}} onClick={this.closeModal}>&#10005;</div>
            </div>
            <div className="delete_article_block">
              <div className="modal_message">
                Are you sure you want to delete your article?
              </div>
              <div className="article_title">
                {article.article_title}
              </div>
              <div className="published_time">
                {article.created_at}
              </div>
            </div>
            <div className="action_button cancel" onClick={this.closeModal}> Cancel </div>
            <div className="action_button delete" onClick={this.deleteArticle}> Delete </div>
          </div>
        </Modal>
      </span>
    )
  }
}
const mapStateToProps = (state) =>{
  return{
    user: state.user && state.user.data,
  }
}
export default connect(mapStateToProps, null)(SharePost);
