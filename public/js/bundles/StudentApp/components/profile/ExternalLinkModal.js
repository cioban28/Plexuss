import React, { Component } from 'react';
import Modal from 'react-responsive-modal';

export default class ExternalLinkModal extends Component {
    constructor(props) {
        super(props);
    }

    _openURL = () => {
        const { url, toggleModal } = this.props;

        window.open(url, '_blank');
    }

    render() {
        const { isOpen, toggleModal, url } = this.props;

        return (
            <Modal open={isOpen} onClose={toggleModal} closeOnEsc={false} classNames={{modal: 'external-link-modal'}} little>
                <h4>This link will take you to an external website</h4>
                <div className='url-link-display'>{url}</div>
                <div className='external-link-buttons-container'>
                    <div className='confirm-button' onClick={this._openURL}>OK, take me there.</div>
                    <div className='cancel-button' onClick={toggleModal} >Nevermind, I'll stay here.</div>
                </div>
            </Modal>
        );
    }
}