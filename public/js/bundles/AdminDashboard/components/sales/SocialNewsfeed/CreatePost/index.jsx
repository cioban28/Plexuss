import React, { Component } from 'react';
import { connect } from 'react-redux';
import './styles.scss';
import SocialNewsfeed from '../index.jsx';
import { CustomWysiwygEditor } from '../../../common/CustomWysiwygEditor/index.jsx';
import { PostCard } from '../../../common/PostCard/index.jsx';
import { withRouter } from 'react-router';
import { saveNewsfeedPost, editNewsfeedPost } from '../../../../actions/newsfeedActions';
import $ from 'jquery';
import objectToFormData from 'object-to-formdata';
import _ from 'lodash';
import FilterAudience from './FilterAudience/index.jsx';
import Modal from 'react-modal';
import axios from 'axios';
import { createBrowserHistory } from 'history';

const history = createBrowserHistory();

class CreatePost extends Component {
  constructor(props) {
    super(props);

    const post = this.props.location.state;
    const urlArray = this.props.location.pathname.split('/');
    this.isEdit = urlArray[urlArray.length - 1] === 'edit';

    this.state = {
      editorState: !!post && post.post_text || '',
      mediaAttachments: [],
      title: !!post && post.title || '',
      linkPreview: '',
      sharedLink: '',
      ...post,
    }

    this.onEditorStateChange = this.onEditorStateChange.bind(this);
    this.handleBackClick = this.handleBackClick.bind(this);
    this.handleMediaAttachmentChange = this.handleMediaAttachmentChange.bind(this);
    this.handleFileLabelClick = this.handleFileLabelClick.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
    this.handleTitleChange = this.handleTitleChange.bind(this);
    this.handleFilterAudienceBtnClick = this.handleFilterAudienceBtnClick.bind(this);
    this.handleRemoveLinkPreview = this.handleRemoveLinkPreview.bind(this);
  }

  componentDidMount() {
    const post = this.props.location.state;
    this.setAttachmentsFromProps();
    if(this.isEdit) {
      this.props.setSalesPostId(post.id);
      post.shared_link && this.getLinkPreview(post.shared_link);
    } else {
      this.props.setSalesPostId('');
    }
  }

  componentWillReceiveProps(nextProps) {
    if(nextProps.successfulFormSubmission) {
      this.props.toggleSuccessfulSubmission(false);
      this.handleBackClick();
    }
  }

  setAttachmentsFromProps() {
    const post = this.props.location.state;
    const self = this;
    if(!!post && !!post.images && !!post.images.length) {
      return post.images.map(image => {
        const proxyUrl = 'https://cors-anywhere.herokuapp.com/';
        var getFileBlob = function (url, cb) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', proxyUrl + url, true);
        xhr.responseType = "blob";
        xhr.addEventListener('load', function() {
            cb(xhr.response);
        });
        xhr.send();
        };

        const blobToFile = (blob, name) => {
          blob.lastModifiedDate = new Date();
          blob.name = name;
          return blob;
        };

        const getFileObject = (filePathOrUrl, cb) => {
          getFileBlob(filePathOrUrl, function (blob) {
            cb(blobToFile(blob, image.image_link));
          });
        };

        getFileObject(image.image_link, function (fileObject) {
          const newFileObject = { ...image, file: fileObject, isEdit: true };
          const mediaAttachments = [...self.state.mediaAttachments];
          mediaAttachments.push(newFileObject);
          self.setState({ mediaAttachments });
        });
      });
    }
    return [];
  }

  matchUrl(str) {
    return str.match("([a-zA-Z0-9]+://)?([a-zA-Z0-9_]+:[a-zA-Z0-9_]+@)?([a-zA-Z0-9.-]+\\.[A-Za-z]{2,4})(:[0-9]+)?(/.*)?");
  }

  onEditorStateChange(editorState) {
    this.setState({ editorState });
    const urlMatches = this.matchUrl($(editorState).text());
    if(!!urlMatches && urlMatches.length){
      this.getLinkPreview(urlMatches);
    }
  }

  handleRemoveLinkPreview() {
    this.setState({ linkPreview: '', sharedLink: '' });
  }

  getLinkPreview(urlMatches) {
    const url = Array.isArray(urlMatches) ? urlMatches[0] : urlMatches;
    if(this.state.sharedLink !== url) {
      axios({
        method: 'get',
        url: `/social/link-preview-info?url=${url}`,
      })
      .then(res => {
        if(res.statusText === 'OK'){
          this.setState({
            linkPreview: res.data,
            sharedLink: url,
          })
        }
      })
      .catch(error => {
      });
    }
  }

  handleBackClick() {
    history.goBack();
  }

  handleMediaAttachmentChange(e) {
    const reader = new FileReader();
    const file = e.target.files[0];
    const mediaAttachments = [...this.state.mediaAttachments];
    reader.onloadend = () => {
      const newFileObj = { file: file, image_link: reader.result };
      mediaAttachments.push(newFileObj);
      this.setState({ mediaAttachments });
    };
    reader.readAsDataURL(file);
  }

  handleFileLabelClick() {
    this.customFileInputRef.click();
  }

  handleRemoveMediaClick(index) {
    const mediaAttachments = [...this.state.mediaAttachments];;
    if(mediaAttachments[index].id) {
      mediaAttachments[index].delete = true;
    } else {
      mediaAttachments.splice(index, 1);
    }
    this.setState({ mediaAttachments });
  }

  handleSubmit(e) {
    e.preventDefault();

    const { userData, saveNewsfeedPost, toggleFormSubmission, salesPostId, location, editNewsfeedPost } = this.props;
    const { editorState, mediaAttachments, title, sharedLink } = this.state;
    toggleFormSubmission(true);
    const post = location.state;
    const post_text = $(editorState).text();
    const post_images = mediaAttachments.length && mediaAttachments.map(media => (this.isEdit ? media : media.file));
    const values = {
      post_text,
      post_images: !!post_images && post_images.length ? post_images : null ,
      title,
      share_count: post && post.share_count || 0,
      user_id: userData.user_id,
      privacy: 1,
      shared_link: sharedLink ? sharedLink : post && post.shared_link || null,
      is_shared: post && post.is_shared || false,
      original_post_id: post && post.original_post_id || null,
      post_status: post && post.post_status || 1,
      is_gif: post && post.is_gif || false,
      thread_room: 'post:room',
      share_with_id: post && post.share_with_id || 1,
      sales_pid: salesPostId,
    };
    const formData = objectToFormData(values);

    if(this.isEdit) {
      editNewsfeedPost(formData);
    } else {
      saveNewsfeedPost(formData);
    }
  }

  handleTitleChange(e) {
    this.setState({ title: e.target.value });
  }

  handleFilterAudienceBtnClick(e) {
    e.preventDefault();
    this.props.openModal();
  }

  render() {
    const { editorState, mediaAttachments, title, linkPreview } = this.state;
    const { submitting, isOpen, closeModal, location } = this.props;

    const transformMediaAttachments = () => mediaAttachments.length ? mediaAttachments.filter(mediaAttachments => !mediaAttachments.delete) : [];

    return (
      <SocialNewsfeed>
        {
          isOpen && <Modal isOpen={isOpen} className='filter-audience-modal'>
            <FilterAudience postId={location && location.state && location.state.id} />
          </Modal>
        }
        <section id='newsfeed-create-post'>
          <header className='create-post-header'>
            <h3 className='back' onClick={this.handleBackClick}>
              <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/sales/noun_Arrow_1256499_000000.svg' />Back
            </h3>
            <h3 className='title'>{this.isEdit ? 'Update post' : 'Create new post'}</h3>
            <div></div>
          </header>
          <form className='create-post-main-cont' onSubmit={this.handleSubmit}>
            <div className='columns large-6'>
              <div className='left-side'>
                <h3 className='section-heading'>Title</h3>
                <input className='formatted-input' type='text' value={title} placeholder='Enter your title' onChange={this.handleTitleChange} />
                <CustomWysiwygEditor placeholder='Write something here' editorState={editorState} onEditorStateChange={this.onEditorStateChange} />
              </div>
            </div>
            <div className='columns large-6'>
              <div className='post-preview-cont'>
                <h3 className='section-heading'>Preview</h3>
                <PostCard
                  handleFileLabelClick={this.handleFileLabelClick}
                  description={editorState}
                  mediaAttachments={transformMediaAttachments()}
                  post={this.props.location.state}
                  linkPreview={linkPreview}
                  handleRemoveLinkPreview={this.handleRemoveLinkPreview}
                />
              </div>
            </div>
            <div className='clearfix'>
              <div className='columns large-6 targeting-cont'>
                <div className='left-side'>
                  <h3 className='section-heading'>Targeting</h3>
                  <button className='btn-filter' onClick={this.handleFilterAudienceBtnClick}>Filter Audience</button>
                </div>
              </div>
              <button type='submit' onSubmit={this.handleSubmit} className='btn-publish' disabled={submitting}>
                <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/sales/Publish.svg' /> { this.isEdit ? 'Update' : 'Publish' }
              </button>
            </div>
            <div className='clearfix'>
              <div className='columns large-6 attached-media-count'>
                <div>
                  <h3 className='section-heading'>Attached Media <span className='media-count'>{transformMediaAttachments().length ? `( ${transformMediaAttachments().length} )` : 'None'}</span></h3>
                </div>
                <div className='attached-media-cont'>
                  <label onClick={this.handleFileLabelClick} className='custom-file-upload'>
                    Upload photo(s)
                  </label>
                  <input ref={input => (this.customFileInputRef = input)} onChange={this.handleMediaAttachmentChange} type='file' multiple hidden />
                  <div className='attachments-sm-preview-cont'>
                  {
                    mediaAttachments.length > 0 && mediaAttachments.map((media, index) => (
                      !media.delete && <div className='attached-media-sm-preview' key={index}>
                        <img className='media-thumbnail' src={media.image_link} />
                        <img className='cross' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/sales/X.svg' onClick={this.handleRemoveMediaClick.bind(this, index)} />
                      </div>
                    ))
                  }
                  </div>
                </div>
              </div>
            </div>
          </form>
        </section>
      </SocialNewsfeed>
    )
  }
}

const mapStateToProps = state => {
  return {
    userData: state.userData.userProfileData,
    submitting: state.formSubmission.submitting,
    successfulFormSubmission: state.formSubmission.successfulSubmission,
    isOpen: state.modal.isOpen,
    salesPostId: state.newsfeed.salesPostId,
  }
}

const mapDispatchToProps = dispatch => {
  return {
    saveNewsfeedPost: (values) => { dispatch(saveNewsfeedPost(values)) },
    toggleFormSubmission: (payload) => { dispatch({ type: 'TOGGLE_FORM_SUBMISSION', payload: payload }) },
    toggleSuccessfulSubmission: (payload) => { dispatch({ type: 'TOGGLE_SUCCESSFUL_SUBMISSION', payload: payload }) },
    openModal: () => { dispatch({ type: 'OPEN_MODAL' }) },
    closeModal: () => { dispatch({ type: 'CLOSE_MODAL' }) },
    editNewsfeedPost: (values) => { dispatch(editNewsfeedPost(values)) },
    setSalesPostId: (value) => { dispatch({ type: 'SET_SALES_POST_ID', payload: value }) },
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(withRouter(CreatePost));
