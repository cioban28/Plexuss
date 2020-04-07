import React, { Component } from 'react'
import Modal from 'react-modal';
import { Article, NewsFeed } from './views'
import ActionBox from './actionBox'
import './styles.scss'
const customStyles = {
    content : {
      top                   : '100px',
      left                  : '13%',
      right                 : 'auto',
      bottom                : 'auto',
      transform             : 'translate(0%, 0%)',
      border                : 'none',
      background            : 'transparent',
      width                 : '74%',
      padding               : '0',
      borderRadius          : '6px',
    }
};
Modal.setAppElement(document.getElementById('_SocialApp_Component'))
class PreviewModal extends Component{
    constructor(props) {
        super(props);
        this.state = {
          modalIsOpen: false,
          rendComp: 'post',
        };
        this.openModal = this.openModal.bind(this);
        this.afterOpenModal = this.afterOpenModal.bind(this);
        this.closeModal = this.closeModal.bind(this);
        this.changeView = this.changeView.bind(this);
    }
    componentDidMount(){
        this.setState({
            modalIsOpen: this.props.modal,
        })
    }
    openModal() {
        this.setState({modalIsOpen: true});
    }
    afterOpenModal() {
    }
    closeModal() {
        let { previewModal } = this.props;
        previewModal();
        this.setState({modalIsOpen: false});
    }
    changeView(comp){
        this.setState({
            rendComp: comp,
        })
    }
    render(){
        let { article, publish, disablePublish } = this.props;
        return(
            <Modal
                isOpen={this.state.modalIsOpen}
                onAfterOpen={this.afterOpenModal}
                onRequestClose={this.closeModal}
                style={customStyles}
                contentLabel="PreviewArticle"
                >
                <div className="preview_modal">
                    <div className="title">
                        Preview
                    </div>
                    <div className="sub_title">
                        <div onClick={() =>this.changeView('post')} className={'text ' + (this.state.rendComp == "post" ? 'active_preview' : '')}>Newsfeed view</div>
                        <div onClick={() =>this.changeView('article')} className={'text ' + (this.state.rendComp == "article" ? 'active_preview' : '')}>Article view</div>
                    </div>
                </div>
                {
                    this.state.rendComp == "post" && 
                        <NewsFeed article={article} /> ||
                    this.state.rendComp == "article" && 
                        <Article article={article} />
                }
                <ActionBox closeModal={this.closeModal} publish={publish} privacy={this.props.privacy} handleShareWith={this.props.handleShareWith} disablePublish={disablePublish}/>
            </Modal>
        )
    }
}

export default PreviewModal;