import React from 'react';
import { connect } from 'react-redux'
import axios from 'axios';
import Modal from 'react-modal';
import PostBlock from './post_block'
import objectToFormData from 'object-to-formdata'
import {toastr} from 'react-redux-toastr'
import { savePost, updatePostSharedCount } from './../../../api/post'
import { addNewThread } from './../../../actions/messages'
import Select from 'react-select';
import { updateArticleShares } from './../../../api/article'
import { saveMessage, addThreadApi } from './../../../api/messages'

Modal.setAppElement(document.getElementById('_SocialApp_Component'))
class SharePost extends React.Component{
  constructor(props) {
    super(props);
    this.state = {
      optionBox: false,
      modalIsOpen: false,
      shareWithOption: 'Public',
      shareWithOptioNumber: 1,
      shareWithFlag: false,
      selectedOption: [],
      friendsState: [],
      text: '',
      shareAs: 1,
      shareState: 'share-to-feed',
    };
    this.scrollLockEl = null;
    this.openModal = this.openModal.bind(this);
    this.afterOpenModal = this.afterOpenModal.bind(this);
    this.closeModal = this.closeModal.bind(this);
    this.toggleShareWithFlag = this.toggleShareWithFlag.bind(this);
    this.setShareWithOption = this.setShareWithOption.bind(this);
    this.sharePost = this.sharePost.bind(this);
    this.onChangeText = this.onChangeText.bind(this);
    this.setShareAs = this.setShareAs.bind(this);
    this.shareAsMessage = this.shareAsMessage.bind(this);
    this.handleShareState = this.handleShareState.bind(this);
    this.getLinkData = this.getLinkData.bind(this);
    this.sendMessage = this.sendMessage.bind(this);
  }
  componentDidMount(){
    if(this.props.friends){
      let newFriends = [];
      this.props.friends.map(friend =>{
        let obj = {
          value: friend.user_id, label: friend.fname+' '+friend.lname,
        };
        newFriends.push(obj);
      })
      this.setState({friendsState: newFriends})
    }
  }
  componentWillReceiveProps(nextProps){
    if(nextProps.friends != this.props.friends){
      let newFriends = [];
      nextProps.friends.map(friend =>{
        let obj = {
          value: friend.user_id, label: friend.fname+' '+friend.lname,
        };
        newFriends.push(obj);
      })
      this.setState({friendsState: newFriends})
    }
  }
  handleShareState(value) {
    this.setState({
      shareState: value,
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
    else if(option === "My Connections Only"){
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
  openModal() {
    this.setState({modalIsOpen: true});
  }
  afterOpenModal() {
  }
  closeModal() {
    this.setState({modalIsOpen: false});
  }
  componentWillUnmount() {
    // clearAllBodyScrollLocks();
  }
  sharePost(){
    let { logInUser, post, latestPost } = this.props;
    if (this.state.shareState === "share-to-feed") {
      let post1 = {};
      if(post.hasOwnProperty('article_text')){
        post1.share_type = 'article';
      }else{
        post1.share_type = 'post';
      }
      let _data = {};
      let article_data = {};
      post1.is_gif = false;
      post1.gif_link = null;
      post1.post_images = null;
      if(post.is_shared){
        post1.original_post_id = post.original_post_id;
      }else{
        post1.original_post_id = post.id;
      }
      post1.target_id = post.user_id;
      post1.user_name = logInUser.fname+' '+logInUser.lname;
      post1.is_shared = true;
      post1.share_count = 0;
      post1.user_id = logInUser.user_id;
      post1.post_text = this.state.text;
      post1.shared_link = '';
      post1.privacy = this.state.shareWithOptioNumber;
      post1.share_with_id = this.state.shareWithOptioNumber;
      post1.thread_room = 'post:room:';
      post1.share_post_id = latestPost.id;
      if(latestPost.hasOwnProperty('article_text')){
        post1.share_post_type = 'article';
      }else{
        post1.share_post_type = 'post';
      }

      const formData = objectToFormData(post1);
      savePost(formData, true);
      if(latestPost.hasOwnProperty('article_text')){
        article_data.article_id = latestPost.id;
        article_data.thread_room = 'post:room:';
        article_data.update = 'increment';
        updateArticleShares(article_data);
      }else{
        _data.post_id = latestPost.id;
        _data.thread_room = 'post:room:';
        _data.update = 'increment';
        updatePostSharedCount(_data);
      }
    }
    else if(this.state.shareState === "share-as-msg"){
      this.shareAsMessage();
    }
    this.setState({modalIsOpen: false});
  }
  handleChange = (selectedOption) => {
    this.setState({ selectedOption });
  }
  onChangeText(event){
    this.setState({
        text: event.target.value,
    })
  }
  setShareAs(option){
    this.setState({shareAs: option});
  }
  shareAsMessage(){
    const { post } = this.props;
    var type = '';
    var post_id = null;
    var share_article_id = null;
    if(post.shared_link){
      this.getLinkData();
    }else{
      if(post && post.hasOwnProperty('post_text')){
        type = 'post';
        post_id = post && post.id;
          var message = `<div className='article_message'>
                          <div className="img_parent">
                            ${!!post && !!post.images && post.images && post.images[0] && post.images[0].image_link ? `<img src=${post.images[0].image_link} >` : '' }
                          </div>
                          <div className='send_post_as_msg article_msg'>
                            <div className='description post_text'>${!!post && !!post.post_text ? post.post_text : '' } </div>
                          </div>
                        </div>`
      }
      else if(post && post.hasOwnProperty('article_text')){
        type = 'article';
        share_article_id = post && post.id;
        var message = `<div className='article_message'>
                        <div className="img_parent">
                          ${!!post && !!post.images && post.images[0] && post.images[0].image_link && `<img src=${post.images[0].image_link} >`}
                        </div>
                        <div className='send_post_as_msg article_msg'>
                          <div className='title'>${post && post.article_title ? post.article_title : '' }</div>
                          <div className='description'>${!!post && !!post.article_text ? $(post.article_text).text().substring(0, 100) : ''}</div>
                          <div>PLEXUSS ARTICLE</div>
                        </div>
                      </div>`
      }
      this.sendMessage(message, type, post_id, share_article_id);
    }
  }

  sendMessage(message, type, post_id,  share_article_id){
    const { logInUser, messageThreads, user } = this.props;
    const { selectedOption } = this.state;
    selectedOption && selectedOption.map((option)=>{
      let index = messageThreads.findIndex(thread => thread.thread_type_id == option.value);
      let thread_room_list = ['post:room:'+option.value, 'post:room:'+logInUser.user_id];
      if(index !== -1){
        let msg = {
          message: message,
          msg_type: type,
          thread_id: messageThreads[index].thread_id,
          to_user_id: messageThreads[index].thread_type_id,
          thread_type: 'users',
          user_id: logInUser.user_id,
          thread_room: messageThreads[index].thread_id,
          post_id: post_id,
          share_article_id: share_article_id,
          thread_room_list: thread_room_list,
        };
        saveMessage(msg);
      }else{
        let msg = {
          message: message,
          msg_type: type,
          thread_id: -1,
          to_user_id: option.value,
          thread_type: 'users',
          user_id: logInUser.user_id,
          thread_room: option.value+':'+logInUser.user_id,
          post_id: post_id,
          share_article_id: share_article_id,
          thread_room_list: thread_room_list,
        };
        saveMessage(msg)
        .then(res=>{
          let data = {
              thread_room: 'post:room:'+option.value,
              id: res.data,
              user_thread_room: 'post:room:'+logInUser.user_id,
          }
          addThreadApi(data)
        })
      }
    })
    toastr.success(`Share ${type} as message successfully`);
  }

  getLinkData(){
    const { post } = this.props;
    axios({
      method: 'get',
      url: '/social/link-preview-info?url='+post.shared_link,
    })
    .then(res => {
      var message = `<a href=${res.data.url} className='article_message'>
                  <div className="img_parent">
                    ${!!res.data && !!res.data.image ? `<img src=${res.data.image} >` : '' }
                  </div>
                  <div className='send_post_as_msg article_msg'>
                    <div className='title'>${res.data.title}</div>
                    <div className='description'>${res.data.description}</div>
                    <div>${post.shared_link}</div>
                  </div>
                </a>`
      this.sendMessage(message, 'post', post.id, null);
    })
  }
  render(){
    const { post, isMobilePreview, user } = this.props;
    const { selectedOption, friendsState } = this.state;
    return(
      <span>
      <div className="large-4 medium-4 small-4 columns share_icon" onClick={this.openModal}>
          <img src={isMobilePreview ? "/social/images/share-white.svg" : "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Share.svg"} onClick={this.openModal} />
          <span className="share_share"> Share</span>
      </div>
      <Modal
            isOpen={this.state.modalIsOpen}
            onRequestClose={this.closeModal}
            contentLabel="sharing"
            className="delete-modal"
          >
            <div id="shareModelContainer">
              <div className="crossButton" onClick={this.closeModal}>&#10005;</div>
              <div className="shareBox">
                <div className="tabs">
                  <ul className="tab-list">
                    <li className={this.state.shareState === "share-to-feed" ? "tab-list-item tab-list-active" : "tab-list-item" } onClick={() => this.handleShareState("share-to-feed")}>
                      Share to feed
                    </li>
                    <li className={this.state.shareState === "share-as-msg" ? "tab-list-item tab-list-active" : "tab-list-item" } onClick={() => this.handleShareState("share-as-msg")}>
                      Send as Message
                    </li>
                  </ul>
                  {
                    this.state.shareState === "share-to-feed" ?
                      <textarea className="text_for_mobile" name="share_note" placeholder="Have any additional thoughts to add?" rows="1" onChange={this.onChangeText} value={this.state.text}></textarea>
                      :
                      this.state.shareState === "share-as-msg" ?
                      <Select
                        value={selectedOption}
                        onChange={this.handleChange}
                        options={friendsState}
                        isMulti={true}
                      />
                      : ""
                  }
                </div>

                <div >
                  <div className="large-12 small-12 columns padding-0">
                    <div className="post-content">
                      <div className="post-content-inner">
                        <div className="post-head">
                          <div className="post-user">
                            <img src={user.profile_img_loc ? "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/"+user.profile_img_loc : '/social/images/Avatar_Letters/'+user.fname.charAt(0).toUpperCase()+'.svg'} />
                          </div>
                          <div className="title-area">
                            <div href="#" className="user-name user_name_hover"> {user && user.fname} {user && user.lname} </div>
                            <strong className="user-title">
                              {user && user.is_student === 1 ? 'Student' : ''}
                            </strong>
                          </div>
                        </div>
                        <PostBlock post={post} />
                      </div>
                    </div>
                  </div>
                </div>
                <button className="showAll">Show all</button>
              </div>
              <div className="actionBox">
                <div className="row actionRow">
                  <div className="large-7 medium-7 small-12 columns share-column">
                  <span onClick={this.toggleShareWithFlag}>SHARE WITH: {this.state.shareWithOption}  <img src="/social/images/arrow.svg" className="new-arrow" /></span>
                  {
                      this.state.shareWithFlag &&
                      <div className="share_width">
                        <div className="item" onClick={() => this.setShareWithOption('Public')}>Public</div>
                        <div className="item" onClick={() => this.setShareWithOption('My Connections Only')}>My Connections Only</div>
                        <div className="item" onClick={() => this.setShareWithOption('Only Me & Colleges')}>Only Me & Colleges</div>
                        <div className="item" onClick={() => this.setShareWithOption('Only Me')}>Only Me</div>
                      </div>
                  }
                  </div>
                  <div className="large-5 medium-5 small-12 buttons">
                    <button className="cancelButton small-5" onClick={this.closeModal}>Cancel</button>
                    <button className="shareButton small-5" onClick={this.sharePost}>Share</button>
                  </div>
                </div>
              </div>
            </div>
        </Modal>
      </span>
    )
  }
}
function mapStateToProps(state){
  return{
    logInUser : state.user && state.user.data,
    friends: state.user && state.user.networkingDate && state.user.networkingDate.friends,
    messageThreads: state.messages.messageThreads && state.messages.messageThreads.topicUsr,
  }
}
function mapDispatchToProps(dispatch) {
  return({
      addNewThread: (id) => { dispatch(addNewThread(id))},
  })
}
export default connect(mapStateToProps, mapDispatchToProps)(SharePost);
