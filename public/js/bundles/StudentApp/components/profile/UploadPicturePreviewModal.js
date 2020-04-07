import React, { Component } from 'react';
import Modal from 'react-responsive-modal';

export default class UploadPicturePreviewModal extends Component {
    constructor(props) {
        super(props);

        this.state = {
            imageSource: '',
            errorMessage: '',
        }

        this.reader = new FileReader;
    }

    componentWillMount() {
        this.reader.onload = (event) => {
            this.setState({ imageSource: event.target.result });
        }
    }

    componentWillReceiveProps(newProps) {
        const { isOpen, file } = this.props;
        const { isOpen: newIsOpen, file: newFile } = newProps;

        if (isOpen !== newIsOpen && newIsOpen && newFile) {
            this.reader.readAsDataURL(newFile);
        }
    }

    _previewFileSource = (file) => {
        this.reader.readAsDataURL(file);
    }

    render() {
        const { isOpen, toggleModal, confirmUpload } = this.props;
        const { imageSource } = this.state;

        const avatarStyles = {
            background: 'url(' + imageSource + ')',
        }

        return (
            <Modal open={isOpen} onClose={toggleModal} closeOnEsc={false} classNames={{modal: 'upload-profile-picture-modal'}} little>
                <h4>This is how your avatar will look.</h4>
                <div className='secondary-header-text'>Click <b>Upload</b> to confirm your new profile picture.</div>
                <div className='preview-image-source' style={avatarStyles} />
                <div className='upload-picture-button-container'>
                    <div className='upload-action-button upload-button' onClick={confirmUpload}>Upload</div>
                    <div className='upload-action-button cancel-button' onClick={toggleModal}>Cancel</div>
                </div>
            </Modal>
        );
    }
}