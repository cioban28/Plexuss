import React, { Component } from 'react';
import axios from 'axios'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import cloneDeep from 'lodash/cloneDeep'
import GifPlayer from 'react-gif-player';
import autosize from "autosize";
import Dropzone from 'react-dropzone'
import objectToFormData from 'object-to-formdata'
import { savePost } from './../../../api/post'
import { publishPostStart } from './../../../actions/posts'
import Giphy from './../giphy'
import {
  FacebookShareButton,
  TwitterShareButton,
  LinkedinShareButton,
} from 'react-share';
import imageCompression from 'browser-image-compression';

const _ = {
  cloneDeep: cloneDeep
}

const thumbInner = {
    display: 'inline-block',
    minWidth: 0,
    overflow: 'hidden',
    width: '130px',
    height: '130px',
    margin: 0,
    marginRight: '6px',
}
const addImg = {
  display: 'inline-block',
  minWidth: '130px',
  overflow: 'hidden',
  width: '130px',
  height: '130px',
  margin: 0,
  border: '2.5px solid #CCCCCC',
  borderStyle: 'dashed',
  textAlign: 'center',
  padding: '25px',
  fontWeight: 'bold',
  fontSize: '50px',
  cursor: 'pointer',
  margin: '10px'
}
const img = {
    display: 'block',
    width: 'auto',
    height: '100%',
    margin: '0',
};
const style = {
  minHeight: "10px",
  resize: "none",
  padding: "9px",
  boxSizing: "border-box",
  fontSize: "15px",
  height: 'auto',
  overflow: 'hidden',
};
const GIPHY = {
    base_url: "https://api.giphy.com/v1/gifs/",
    query: ["search", "trending", "random", "translate"],
    api_key: "1nQqARu4OFYQUFDNETrYe5L4VaRahKRg",
    offset: 0
}
let url = `${GIPHY.base_url}${GIPHY.query[0]}?api_key=${GIPHY.api_key}&limit=${GIPHY.limit}&offset=${GIPHY.offset}`;
let firstInput = '2019';

class Share extends Component{
    constructor(props){
        super(props);
        this.state={
            text: "",
            uploadImage: '',
            post: {},
            files: [],
            giphyImages: [],
            shareWithOption: 'Public',
            shareWithOptioNumber: 1,
            shareWithFlag: false,
            sharedLink: '',
            linkContent: {},

            share_count: 0,
            original_post_id: null,
            is_shared: false,
            remove_images: [],
            post_images: [],
        }
        this.handleText = this.handleText.bind(this);
        this.sharePost = this.sharePost.bind(this);
        this.removeImg = this.removeImg.bind(this);

        this.setImageSrc = this.setImageSrc.bind(this);
        this.removeGiphy = this.removeGiphy.bind(this);
        this.toggleShareWithFlag = this.toggleShareWithFlag.bind(this);
        this.setShareWithOption = this.setShareWithOption.bind(this);
        this.handleOutsideShareMenu = this.handleOutsideShareMenu.bind(this);
        this.editGiphyCheck = this.editGiphyCheck.bind(this);

        this.shareMenuBtn;
        this.shareMenuContainer;

    }
    componentDidMount() {
      this.textarea.focus();
      autosize(this.textarea);
      document.addEventListener('click', this.handleOutsideShareMenu, false);
      if(this.props.editMode){
        const { post } = this.props;
        let arr = [];
        if(post.images && post.images[post.images.length-1] && post.images[post.images.length-1].is_gif == 1){
          let obj = {};
          obj.imageSrc = post.images[post.images.length-1].gif_link;
          obj.previewImg = post.images[post.images.length-1].image_link;
          obj.id = post.images[post.images.length-1].id;
          arr.push(obj);
        }else if(post.images){
          this.setState({post_images: _.cloneDeep(post.images)})
        }
        this.setState({
          text: post.post_text,
          share_count: post.share_count,
          original_post_id: post.original_post_id,
          is_shared: post.is_shared,
          giphyImages: arr,
          shareWithOptioNumber: post.share_with_id,
        }, ()=>{
          let matches = [];
          if(this.state.text){
            matches = this.state.text.match("([a-zA-Z0-9]+://)?([a-zA-Z0-9_]+:[a-zA-Z0-9_]+@)?([a-zA-Z0-9.-]+\\.[A-Za-z]{2,4})(:[0-9]+)?(/.*)?")
            if(!!matches && matches.length > 0){
              this.getResponse(matches)
            }
            else if(this.state.sharedLink !== ''){return;}
            else{
              this.setState({
                linkContent: '',
                sharedLink: '',
              })
            }
          }
          this.setShareOption();
        })
      }
    }
    setShareOption(){
      const { post } = this.props;
      let option = 'public';
      if(post.share_with_id === 2){
        option = "My Connections only"
      }
      else if(post.share_with_id === 3){
        option = "Only Me & Colleges"
      }
      else if(post.share_with_id === 4){
        option = "Only Me"
      }else{

      }
      this.setState({
        shareWithOption: option,
        shareWithFlag: false,
      })
    }
    toggleShareWithFlag(){
      this.setState({
        shareWithFlag: !this.state.shareWithFlag,
      })
    }
    setShareWithOption(option){
      var optionNumber = 1;
      if(option === "public"){
        optionNumber = 1;
      }
      else if(option === "My Connections only"){
        optionNumber = 2;
      }
      else if(option === "Only Me & Colleges"){
        optionNumber = 3;
      }
      else if(option === "Only Me"){
        optionNumber = 4;
      }
      this.setState({
        shareWithOption: option,
        shareWithFlag: false,
        shareWithOptioNumber: optionNumber,
      })
    }
    removeGiphy(index){
      let test_files = this.state.giphyImages;
      test_files.splice(index, 1);
      this.setState({
        giphyImages: test_files,
      });
    }
    editGiphyCheck(){
      let { editMode } = this.props;
      const { remove_images, giphyImages } = this.state;
      if(editMode && giphyImages.length > 0){
        let newRMImg = remove_images;
        newRMImg.push(giphyImages[0].id);;
        this.setState({
          remove_images: newRMImg,
        })
      }
    }
    setImageSrc(imageUrl, previewImg) {
      this.editGiphyCheck();
      let obj = {};
      obj.imageSrc = imageUrl;
      obj.previewImg = previewImg;
      let arr = [];
      arr.push(obj);
      this.setState({
        giphyImages: arr,
        files: [],
      });
      this.props.hideGiphy();
    }
    componentWillUnmount() {
        this.state.files.forEach(file => URL.revokeObjectURL(file.preview))
        document.removeEventListener('click', this.handleOutsideShareMenu, false);
    }
    onDrop(files) {
      this.editGiphyCheck();
      let test_files = this.state.files;
      files.map(file => test_files.push(Object.assign(file, {
        preview: URL.createObjectURL(file)
      })))
      this.setState({
        files: test_files,
        giphyImages: [],
      });
    }
    onDropRejected(files) {
      this.editGiphyCheck();
      const compressionOptions = {
        maxSizeMB: 1,
        maxWidthOrHeight: 1920,
        useWebWorker: true
      };

      imageCompression(files[0], compressionOptions).then(compressedFile => {
        const newFiles = this.state.files;
        newFiles.push(Object.assign(compressedFile, {
          preview: URL.createObjectURL(compressedFile)
        }))
        this.setState({
          files: newFiles,
          giphyImages: [],
        });
      }).catch(error => {
        console.log('Error occured while compressing the image', error);
      });
    }
    removeImg(index){
      let test_files = this.state.files;
      test_files.splice(index, 1);
      this.setState({
        files: test_files,
      });
    }
    sharePost(){
      this.props.publishPostStart();
        let { user, post, editMode } = this.props;
        let post1 = {};
        if(this.state.giphyImages.length > 0){
          post1.is_gif = true;
          post1.gif_link = this.state.giphyImages[0].imageSrc;
          post1.image_link = this.state.giphyImages[0].previewImg;
          post1.post_images = null;
        }else{
          post1.is_gif = false;
          post1.gif_link = null;
          post1.post_images = this.state.files.length > 0 ? this.state.files : null;
        }
        post1.share_count = this.state.share_count;
        post1.original_post_id = this.state.original_post_id;
        post1.is_shared = this.state.is_shared;
        post1.user_id = user.user_id;
        post1.post_text = this.state.text;
        if (this.state.sharedLink == '') {
          post1.shared_link = '';
        }
        else{
          post1.shared_link = this.state.sharedLink;
        }
        post1.share_with_id = this.state.shareWithOptioNumber;
        post1.thread_room = 'post:room:';
        post1.private_thread_room = 'post:room:'+user.user_id;
        if(editMode){
          post1.post_id = post.id;
          post1.remove_images = this.state.remove_images;
        }
        const formData = objectToFormData(post1);
        savePost(formData, false);
    }
    handleText(event){
      this.setState({text: event.target.value});
      let matches = []
      matches = event.target.value.match("([a-zA-Z0-9]+://)?([a-zA-Z0-9_]+:[a-zA-Z0-9_]+@)?([a-zA-Z0-9.-]+\\.[A-Za-z]{2,4})(:[0-9]+)?(/.*)?")
      if(!!matches && matches.length > 0){
        this.getResponse(matches)
      }
      else if(this.state.sharedLink !== ''){return;}
      else{
        this.setState({
          linkContent: '',
          sharedLink: '',
        })
      }
    }

    handleOutsideShareMenu(e) {
      if (!!this.state.shareWithFlag && (this.shareMenuContainer.contains(e.target) || this.shareMenuBtn.contains(e.target)) ) {
        return;
      }
      if(this.state.shareWithFlag === true){
        this.setState({shareWithFlag: false})
      }
    }

    getResponse = (matches) => {
      axios({
          method: 'get',
          url: '/social/link-preview-info?url='+matches[0],
        })
        .then(res => {
          this.setState({
            linkContent: res.data,
            sharedLink: matches[0],
          })
        })
        .catch(error => {
          console.log("error",error)
        })
    }
    removeImagesInEditMode(image){
      let newArr = this.state.remove_images;
      let newPostImages = this.state.post_images;
      newArr.push(image.id);
      let a_index = newPostImages.findIndex(img => img.id == image.id);
      if(a_index != -1){
        newPostImages.splice(a_index, 1);
      }
      this.setState({
        remove_images: newArr,
        post_images: newPostImages,
      })
    }
    render(){
      let {linkContent, sharedLink} = this.state
      var showGiphy = this.props.showGiphy;
      var hideGiphy = this.props.hideGiphy;
      var giphy = this.props.giphy;

      var closeModal = this.props.closeModal;
      let { giphyFlag, editMode, post, forEditPost } = this.props;
      const {files} = this.state;
      const thumbs = files.map((file, index) => (
        <div className="thumb_cross_parent" key={index}>
          <div style={thumbInner}>
            <img
              src={file.preview}
              style={img}
            />
            <div className="thumb_overlay">
              <div className="thumb_cross" onClick={() => this.removeImg(index)}>&#10005;</div>
            </div>
          </div>
        </div>
      ));
      let thumbs1 = '';
      if(editMode && post && post.images){
        thumbs1 = this.state.post_images.map((image, index) => (
            <div className="thumb_cross_parent" key={'post_'+index}>
              <div style={thumbInner}>
                <img
                  src={image.image_link}
                  style={img}
                />
                <div className="thumb_overlay">
                  <div className="thumb_cross" onClick={() => this.removeImagesInEditMode(image)}>&#10005;</div>
                </div>
              </div>
            </div>
        ));
      }
      const giphs = this.state.giphyImages.map((giph, index) => (
        <div key={index} className="cross_giphy_parent">
          <div className="cross_giphy" onClick={() => this.removeGiphy(index)}>&#10005;</div>
          <GifPlayer gif={giph.imageSrc} still={giph.previewImg} className="img-styling"/>
        </div>
      ));
      return(
        <div id= "modal-body" className={(forEditPost ? 'dd-none' : '')}>
          <div id="myModal3">
            <div className={"posting "+ (giphyFlag ? 'mbl_none' : '')}>
              <div className={"share-connection "+(giphyFlag ? 'mbl_none' : '')}>
                <a>
                  <span className="right-text-connection">SHARE WITH</span>
                  <span ref={(ref) => {this.shareMenuBtn = ref;}} className="left-text" onClick={this.toggleShareWithFlag}>: &nbsp;&nbsp; {this.state.shareWithOption} <img src="/social/images/arrow.svg" className="new-arrow" />
                  </span>
                </a>
                <div className="closeModalButton" onClick={() => this.props._closeModal()}>&#10005;</div>
              </div>
              {
                this.state.shareWithFlag &&
                  <div ref={(ref) => {this.shareMenuContainer = ref;}} className="share_width">
                    <div className="item" onClick={() => this.setShareWithOption('Public')}>Public</div>
                    <div className="item" onClick={() => this.setShareWithOption('My Connections only')}>My Connections only</div>
                    <div className="item" onClick={() => this.setShareWithOption('Only Me & Colleges')}>Only Me & Colleges</div>
                    <div className="item" onClick={() => this.setShareWithOption('Only Me')}>Only Me</div>
                  </div>
              }
              <div className={"post-text-area " +(giphyFlag ? 'mbl_none' : '')}>
                <div className="post-area ">
                  <textarea
                    style={style}
                    ref={c => (this.textarea = c)}
                    placeholder="Express an idea, ask a question or write an article"
                    rows={1}
                    defaultValue=""
                    cols="25"
                    value={this.state.text ? this.state.text : ""}
                    onChange={this.handleText}
                  />
                  {
                    sharedLink && linkContent &&
                    <div className="shared-link-content">
                      <div className="link-remove" onClick={() => this.setState({linkContent: '', sharedLink: ''})}>&#10005;</div>
                      <div className="parent_link-image"><img src={`${linkContent.image}`} className="link-image" /></div>
                      <div className="link-title">{linkContent.title}</div>
                      <div className="link-description">{linkContent.description}</div>
                      <a href={linkContent.url} className="shared-url" target="_blank">{linkContent.url}</a>
                    </div>
                  }
                  <div className="post-area-image">
                    {giphs}
                    {
                      editMode &&
                      thumbs1
                    }
                    {thumbs}
                    {
                      files.length > 0 ?
                        <div style={addImg}>
                          <Dropzone accept="image/*"
                          minSize={0}
                          maxSize={1048576}
                          onDrop={this.onDrop.bind(this)}
                          onDropRejected={this.onDropRejected.bind(this)}
                          >
                              {({getRootProps, getInputProps}) => (
                                <div {...getRootProps()}>
                                    <input {...getInputProps()} />
                                    +
                                </div>
                              )}
                          </Dropzone>
                        </div> : ''
                    }
                  </div>
                </div>
              </div>
              <div className={"row share-bottom " +(giphyFlag ? 'mbl_none' : '')} >
                <div className="large-7 medium-7 small-12 columns images-box">
                    <a>
                        <Dropzone
                            accept="image/*"
                            minSize={0}
                            maxSize={1048576}
                            onDrop={this.onDrop.bind(this)}
                            onDropRejected={this.onDropRejected.bind(this)}
                        >
                            {({getRootProps, getInputProps}) => (
                            <div {...getRootProps()}>
                                <input {...getInputProps()} />
                                <img className="camera-image" src="/social/images/Subtraction 10.svg" /><span className="desktop_none post_btn_text">Add photos</span>
                            </div>
                            )}
                        </Dropzone>
                    </a>
                    <a onClick={() => showGiphy()} ><img className={"gif-image " + (giphy ? 'active' : '')} src="/social/images/Subtraction 11.png" /> <span className="desktop_none post_btn_text"> Add a GIF </span></a>
                </div>
                <div className="large-5 medium-5 small-12 columns tooltip_parent">
                  <div className="share_on">Share on:</div>
                  <span className="">
                    <div className="circles tooltip">
                      <LinkedinShareButton
                        url={'https://linkedin.com'}
                        title={this.state.text}
                      >
                        <img className="icons" src="/social/images/in.svg" />
                      </LinkedinShareButton>
                      <span className="tooltiptext">Share this post on linkedin</span>
                    </div>

                    <div className="circles tooltip social_media_styling">
                      <TwitterShareButton
                          url={'https:'}
                          title={this.state.text}
                        >
                        <img className="icons" src="/social/images/twitter.svg" />
                      </TwitterShareButton>
                      <span className="tooltiptext">Share this post on twitter</span>
                    </div>
                    <div className="circles tooltip social_media_styling">
                      <FacebookShareButton
                          url={'https://facebook.com'}
                          quote={this.state.text}
                        >
                        <img className="icons " src="/social/images/fb.svg" />
                      </FacebookShareButton>
                      <span className="tooltiptext">Share this post on facebook</span>
                    </div>
                  </span>
                  <div className="small-12">
                    <div className="share-btn">
                      <button disabled={this.state.text == "" && this.state.files.length == 0 && this.state.giphyImages.length == 0 && this.state.sharedLink == ""  &&  Object.keys(this.state.linkContent).length  === 0 } onClick={() => {this.sharePost(); closeModal();}} className="mbl_none">{editMode ? 'Save' : 'Share'}</button>
                      <button disabled={this.state.text == "" && this.state.files.length == 0 && this.state.giphyImages.length == 0 && this.state.sharedLink == ""  &&  Object.keys(this.state.linkContent).length  === 0  } onClick={() => {this.sharePost(); closeModal();}} className="desktop_none">{editMode ? 'Save' : 'POST'}</button>
                    </div>
                  </div>
                </div>
                <div className="detailed_post">
                  <div>Want to make a more detailed post?</div>
                  <div><Link to="/social/article-editor" className="article_write">Write an article</Link></div>
                </div>
              </div>
            </div>
          </div>
          { giphy && <Giphy url={url} firstInput={firstInput} closeGif={hideGiphy} setImageSrc={this.setImageSrc} /> }
        </div>
      )
    }
}
function mapDispatchToProps(dispatch){
  return({
    publishPostStart: () => {dispatch(publishPostStart())}
  })
}
export default connect(null, mapDispatchToProps)(Share);
