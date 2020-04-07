import React, { Component } from 'react'
import Modal from 'react-modal';
import PreviewPost from './previewPost'
import LeftPortion from './leftPostion'
import MobileComments from './../mobileComments'
import PostFunctionality from './../post_functionality'
import { disableBodyScroll, enableBodyScroll, clearAllBodyScrollLocks } from 'body-scroll-lock';
import './styles.scss'
const customStyles = {
    content : {
      top                   : '65px',
      left                  : '5%',
      right                 : 'auto',
      bottom                : 'auto',
      transform             : 'translate(0%, 0%)',
      border                : 'none',
      background            : 'transparent',
      width                 : '90%',
      padding               : '0',
      borderRadius          : '6px',
      overflow              : 'unset',
      height                : '80%',
      minHeight             : '500px'
    }
};
Modal.setAppElement(document.getElementById('social-dashboard'))
class PreviewModal extends Component{
    constructor(props) {
        super(props);
        this.state = {
          modalIsOpen: false,
          mobileModalOpen: false,
          openCommentBox: false,
        };
        this.scrollLockEl = null;
        this.openModal = this.openModal.bind(this);
        this.afterOpenModal = this.afterOpenModal.bind(this);
        this.closeModal = this.closeModal.bind(this);
        this.handleCommentBox = this.handleCommentBox.bind(this)
    }
    componentDidMount(){
      if (window.innerWidth > 767) {
        this.setState({
            modalIsOpen: this.props.openModal,
        })
      }
      else {
        this.setState({
          mobileModalOpen: true,
        }, () => {
          this.scrollLockEl = document.querySelector('.mobile-post-preview');
          disableBodyScroll(this.scrollLockEl);
        })
      }
    }
    openModal() {
        this.setState({modalIsOpen: true});
    }
    afterOpenModal() {
      this.scrollLockEl = document.querySelector('.ReactModal__Overlay');
      disableBodyScroll(this.scrollLockEl);
    }
    closeModal() {
      if (window.innerWidth > 767) {
        let { handleModal } = this.props;
        handleModal();
        this.setState({modalIsOpen: false});
      }
      else {
        this.setState({mobileModalOpen: false})
      }
      enableBodyScroll(this.scrollLockEl)
    }
    handleCommentBox() {
      this.setState({
        openCommentBox: !this.state.mobileModalOpen
      })
    }
    componentWillUnmount() {
      clearAllBodyScrollLocks();
    }
    render(){
        let { images, post, handleMobileCommentd, logInUser, showDesktopComment, desktopComment } = this.props;

        return(
          <div>
            <div className="desktop-post-preview">
              <Modal
                  isOpen={this.state.modalIsOpen}
                  onAfterOpen={this.afterOpenModal}
                  onRequestClose={this.closeModal}
                  style={customStyles}
                  contentLabel="PreviewPost"
                  >
                  <PreviewPost images={images} post={post} handleMobileCommentd={handleMobileCommentd} logInUser={logInUser} showDesktopComment={showDesktopComment} desktopComment={desktopComment} closeModal={this.closeModal}/>
              </Modal>
            </div>
            {
              this.state.mobileModalOpen &&
              <div className="mobile-post-preview">
                  <div className="cross-button" onClick={this.closeModal}>
                    &#10005;
                  </div>
                  <LeftPortion images={images}/>
                  {
                    post &&
                    <div className="post-content">
                      <PostFunctionality handleCommentBox={this.handleCommentBox} isMobilePreview={true} post={post} handleMobileCommentd={handleMobileCommentd} logInUser={logInUser} showDesktopComment={showDesktopComment} desktopComment={desktopComment}/>
                    </div>
                  }
              </div>
            }
          </div>
        )
    }
}

export default PreviewModal;
