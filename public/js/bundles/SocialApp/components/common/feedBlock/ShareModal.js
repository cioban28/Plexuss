import React, { Component } from 'react'
import Modal from 'react-modal';
import Share from './share';
import Confirmation from './confirmationModal'
import { disableBodyScroll, enableBodyScroll, clearAllBodyScrollLocks } from 'body-scroll-lock';
const customStyles = {
    content : {
        top                   : '60px',
        left                  : '0',
        right                 : '0',
        bottom                : 'auto',
        transform             : 'translate(0%, 0%)',
        border                : 'none',
        background            : 'transparent',
        maxWidth              : '600px',
        margin                : 'auto',
        width                 : 'auto',
        padding               : '0px',
        height                : 'auto',
        overflow              : 'hidden',
        padding               : '10px',
    }
};
Modal.setAppElement(document.getElementById('_SocialApp_Component'))
class ShareModal extends Component{
    constructor() {
        super();
        this.state = {
            modalIsOpen: true,
            giphy: false,
            giphyFlag: false,
            forEditPost: false,
        };
        this.scrollLockEl = null;
        this.showGiphy = this.showGiphy.bind(this);
        this.hideGiphy = this.hideGiphy.bind(this);
        this.toggleGiphyFlag = this.toggleGiphyFlag.bind(this);
        this.toggleHidePostFlag = this.toggleHidePostFlag.bind(this);
        this._closeModal = this._closeModal.bind(this);
        this.handleAfterModalOpened = this.handleAfterModalOpened.bind(this);
    }
    componentDidMount(){
        const { openGiphyFlag } = this.props;
        if(openGiphyFlag){
            this.setState({
                giphy: true,
            })
        }
    }
    _closeModal(){
        const { closeModal, editMode } = this.props;
        if(editMode){
            this.toggleHidePostFlag();
        }else{
            closeModal();
            // enableBodyScroll(this.scrollLockEl);
        }
    }
    toggleGiphyFlag(){
        this.setState({giphyFlag: !this.state.giphyFlag})
    }
    showGiphy(){
        this.setState({giphy: !this.state.giphy});
        this.toggleGiphyFlag();
    }
    hideGiphy() {
        this.setState({giphy: !this.state.giphy});
        this.toggleGiphyFlag();
    }
    toggleHidePostFlag(){
        this.setState({
          forEditPost: !this.state.forEditPost,
        })
    }
    handleAfterModalOpened() {
        // this.scrollLockEl = document.querySelector('.ReactModal__Overlay');
        // disableBodyScroll(this.scrollLockEl);
    }
    componentWillUnmount() {
    //   clearAllBodyScrollLocks();
    }
    render(){
        const { closeModal } = this.props;
        const { forEditPost } = this.state;
        return(
            <Modal
                isOpen={this.state.modalIsOpen}
                onRequestClose={this.closeModal}
                style={customStyles}
                // onAfterOpen={this.handleAfterModalOpened}
                contentLabel="Share Modal"
              >
              {
                this.props.editMode &&
                <Confirmation closeModal={closeModal} toggleHidePostFlag={this.toggleHidePostFlag} forEditPost={forEditPost}/>
              }
              <Share _closeModal={this._closeModal} closeModal={closeModal} showGiphy= {this.showGiphy} hideGiphy={this.hideGiphy} giphy = {this.state.giphy} giphyFlag={this.state.giphyFlag}
                toggleGiphyFlag={this.toggleGiphyFlag} user={this.props.user} editMode={this.props.editMode} post={this.props.post}
                forEditPost={forEditPost}/>
            </Modal>
        )
    }
}
export default ShareModal;
