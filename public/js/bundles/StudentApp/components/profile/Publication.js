import React, { Component } from 'react';
import ExternalLinkModal from './ExternalLinkModal';
import Tooltip from 'react-tooltip';
import Modal from 'react-modal';

const YOUTUBE_STYLES = {
    background: 'url(https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/public-profile-sprite-1.png)',
    backgroundPosition: '0px 4px',
    width: '47px',
    height: '37px',
    margin: '0 auto',
}

const DOCUMENT_STYLES = {
    background: 'url(https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/public-profile-sprite-1.png)',
    backgroundPosition: '-44px 0px',
    width: '52px',
    height: '64px',
    margin: '0 auto',
}

export default class Publication extends Component {
    constructor(props) {
        super(props);

        this.state = {
            showExternalLinkModal: false,
            showDeleteModal: false,
        };
    }

    _openUrl = () => {
        this.setState({ showExternalLinkModal: true });
    }

    _onDelete = () => {
        this.setState(prevState => ({ showDeleteModal: !prevState.showDeleteModal }));
    }

    closeDeleteModal = () => {
        this.setState(prevState => ({ showDeleteModal: !prevState.showDeleteModal }));
    }

    render() {
        const { url, shortDescription, editMode, publication_id, removePublication } = this.props;
        const { showExternalLinkModal, showDeleteModal } = this.state;

        let PICTURE_STYLES = {};

        const evenShorterDescription = shortDescription && shortDescription.length > 28
            ? (shortDescription.substr(0, 27) + '...')
            : shortDescription;

        // Find a better solution
        let urlPrepend = window.location.protocol+"//"+window.location.hostname+"/";
        const urlDetails = url.startsWith('social') ? new URL(urlPrepend+url) : new URL(url);
        let newUrl = url.startsWith('social') ? (urlPrepend+url) : url;

        if (urlDetails.hostname.includes('youtube.com')) {
            PICTURE_STYLES = YOUTUBE_STYLES;
        } else {
            PICTURE_STYLES = DOCUMENT_STYLES;
        }

        const onClick = editMode ? this._onDelete : this._openUrl;

        const containerClasses = 'single-publication-container' + (editMode ? ' edit-mode' : '');

        return (
            <div>
                {
                    showDeleteModal &&
                        <Modal
                          isOpen={showDeleteModal}
                          className="delete-modal"
                          contentLabel="Delete your post"
                        >
                          <div className="delete_article_container">
                            <div className="modal_heading">
                                Remove Publication?
                                <div className="modal_x" style={{float: 'right'}} onClick={this.closeDeleteModal}>&#10005;</div>
                            </div>
                            <div className="delete_article_block">
                              <div className="modal_message">
                                Are you sure you want to remove the publication '{evenShorterDescription}'?
                              </div>
                            </div>
                            <div className="action_button cancel" onClick={this.closeDeleteModal}>Cancel</div>
                            <div className="action_button delete" onClick={() => removePublication(publication_id)}>Remove</div>
                          </div>
                        </Modal>
                }
                <div onClick={onClick} className={containerClasses}>
                    <div className='single-publication-picture-container'>
                        <div style={PICTURE_STYLES} />
                    </div>

                    <div data-tip data-for={url + publication_id}>
                        <div className='single-publication-short-description'>{evenShorterDescription}</div>
                        <div className='single-publication-delete-notice'>Remove</div>
                    </div>

                    {/* <Tooltip id={url + publication_id} effect='solid'>
                        <span>{editMode && 'REMOVE '}{shortDescription}</span>
                    </Tooltip> */}

                    <ExternalLinkModal
                        toggleModal={() => this.setState({ showExternalLinkModal: !showExternalLinkModal })}
                        isOpen={showExternalLinkModal}
                        url={newUrl} />
                </div>
            </div>
        );
    }

}
