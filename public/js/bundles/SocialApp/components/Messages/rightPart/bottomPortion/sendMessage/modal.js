import React, { Component } from 'react'
import Modal from 'react-modal';
import Giphy from './../../../giphy/index'
import './styles.scss'
const GIPHY = {
    base_url: "https://api.giphy.com/v1/gifs/",
    query: ["search", "trending", "random", "translate"],
    api_key: "dc6zaTOxFJmzC",
    offset: 0
}
let url = `${GIPHY.base_url}${GIPHY.query[0]}?api_key=${GIPHY.api_key}&limit=${GIPHY.limit}&offset=${GIPHY.offset}`;
let firstInput = '2019';
const customStyles = {
    content : {
      top                   : '100px',
      left                  : '15%',
      right                 : 'auto',
      bottom                : 'auto',
      transform             : 'translate(0%, 0%)',
      border                : 'none',
      background            : 'transparent',
      width                 : '70%',
      padding               : '0',
      borderRadius          : '6px',
      height                : '500px'
    }
};
Modal.setAppElement(document.getElementById('_SocialApp_Component'))
class PreviewModal extends Component{
    constructor(props) {
        super(props);
        this.state = {
          modalIsOpen: false,
        };
        this.openModal = this.openModal.bind(this);
        this.afterOpenModal = this.afterOpenModal.bind(this);
        this.closeModal = this.closeModal.bind(this);
    }
    componentDidMount(){
        this.setState({
            modalIsOpen: this.props.modal,
        })
    }
    openModal(){
        let { previewModal } = this.props;
        previewModal();
        this.setState({modalIsOpen: true});
    }
    afterOpenModal() {
    }
    closeModal() {
        this.setState({modalIsOpen: false});
    }
    render(){
        let { setImageSrc } = this.props;
        return(
            <Modal
                isOpen={this.state.modalIsOpen}
                onAfterOpen={this.afterOpenModal}
                onRequestClose={this.closeModal}
                style={customStyles}
                contentLabel="giphyModal"
                >
                <Giphy url={url} firstInput={firstInput} setImageSrc={setImageSrc} />
            </Modal>
        )
    }
}

export default PreviewModal;