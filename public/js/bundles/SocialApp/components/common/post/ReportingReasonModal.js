import React, { Component } from 'react'
import Modal from 'react-modal'
import { reportPostOrArticle } from './../../../api/post'
import ReportingReason from './ReportingReason'
import ReportingReason2 from './ReportingReason2'
import ReportingReason3 from './ReportingReason3'
import { disableBodyScroll, enableBodyScroll, clearAllBodyScrollLocks } from 'body-scroll-lock';

const customStyles = {
    content : {
      top                   : '50%',
      left                  : '50%',
      right                 : 'auto',
      bottom                : 'auto',
      transform             : 'translate(50%, 50%)',
      border                : 'none',
      background            : '#fff',
      width                 : '50%',
      padding               : '0',
      borderRadius          : '6px',
    }
};
Modal.setAppElement(document.getElementById('_SocialApp_Component'))

export default class ReportingReasonModal extends Component{
	constructor(props) {
        super(props);
        this.state = {
          render: 'modal1',
          reason: '',
          otherReason: '',
          moreInfo: '',
        };
        this.scrollLockEl = null;
        this._renderSubComp = this._renderSubComp.bind(this);
        this.handleClick =this.handleClick.bind(this);
        this.handleReason =this.handleReason.bind(this);
        this.handleOtherReason =this.handleOtherReason.bind(this);
        this.handleMoreInfo =this.handleMoreInfo.bind(this);
        this.clearReport = this.clearReport.bind(this);
        this.submitReport = this.submitReport.bind(this);
        this.handleAfterModalOpened = this.handleAfterModalOpened.bind(this);
        this.closeModal = this.closeModal.bind(this);
    }
	handleClick(compName){
        this.setState({render:compName});
    }
    handleReason(evt){
        this.setState({reason: evt.target.value});
    }
    handleOtherReason(evt){
        this.setState({otherReason: evt.target.value});
    }
    handleMoreInfo(evt){
        this.setState({moreInfo: evt.target.value});
    }
    clearReport(){
    	this.setState({render: 'modal1', reason: ''});
    }
    handleAfterModalOpened() {
      this.scrollLockEl = document.querySelector('.ReactModal__Overlay');
      disableBodyScroll(this.scrollLockEl);
    }
    closeModal() {
      this.props.closeModal();
      enableBodyScroll(this.scrollLockEl);
    }
    componentWillUnmount() {
      clearAllBodyScrollLocks();
    }
    submitReport(){
        let data = {
            type: this.state.reason === 'other' ? this.state.otherReason : this.state.reason,
            explanation: this.state.moreInfo,
            share_article_id: !!this.props.post && !!this.props.post.article_title ? this.props.post.id : null,
            post_id: !!this.props.post && !!this.props.post.article_title === false ? this.props.post.id : null,
            user_id: !!this.props.profile && !!this.props.profile.user_id ? this.props.profile.user_id : null,
        }
        reportPostOrArticle(data);
        this.handleClick('modal3');
    }


    _renderSubComp(){
        switch(this.state.render){
          case 'modal1': return <ReportingReason checked={this.state.reason} otherReason={this.state.otherReason} handleReason={this.handleReason} handleOtherReason={this.handleOtherReason} closeClick={this.closeModal} handleClick={this.handleClick} />
          case 'modal2' : return <ReportingReason2  moreInfo={this.state.moreInfo} handleMoreInfo={this.handleMoreInfo} closeClick={this.closeModal} handleClick={this.handleClick} handleSubmit={this.submitReport} />
          case 'modal3' : return <ReportingReason3  closeClick={this.closeModal} />
        }
    }

    render(){
    	return(
    		<Modal
                isOpen={this.props.modalIsOpen}
                onAfterOpen={this.handleAfterModalOpened}
                onRequestClose={() => {this.clearReport(); this.closeModal(); } }
                style={customStyles}
                contentLabel="Reporting"
                className='reporting-modal'
                >
                { this._renderSubComp() }
            </Modal>
    	)
    }
}
