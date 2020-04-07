import React, { Component } from 'react';
import { Link, browserHistory } from 'react-router-dom';
import './styles.scss';
import RightBar from './RightBar/index'
import GetStarted from './GetStarted/index'
import Notifications from './Notifications/index'
import Messages from './Messages/index'
import MobileHeader from './Mbl_Header/index';
import SearchResults from './SearchResults/index';
import Quad from './Quad/index'
import LoginPopup from './loginPopup'
import { getArticles } from './../../api/article'
import { getUserData } from './../../api/post'
import { connect } from 'react-redux'
import { signOut } from './../../api/post'
import { cancelPreviousRequest, resetSearchResults } from '../../actions/search';
import { getTopbarSearchResults } from '../../api/search';
import { getMessagesThreads, getThreaData } from './../../api/messages'
import { getNetworkingData } from './../../api/post'
import { fetchNotification } from './../../api/notification'
import { setUserRequestFlag, sendMessageAction, setThreadCountAction,
         addNewThread, typeMsg, cancelMsg, setLogInUserIdAction, setViewTimeAction, addThreadUserAction } from './../../actions/messages'
import { addNotification, readNotification } from './../../actions/notificationAction'
import { publishPost, deletePostAction, hidePostAction,
  addCommentSuccess, likeSuccess, updateSharePostCountAction,
  unlikeSuccess, deleteCommentAction,
  editedCommentAction } from './../../actions/posts'
import { saveArticles, updateArticleCount, addArticleComment,
         addArticleLike, editArticleComment, deleteArticleComment, removeCommentLike } from './../../actions/article'
import { addInOnlineUsersArr, removeFromOnlineUsersArr } from './../../actions/user'
import CircularProgressbar from 'react-circular-progressbar';
import isEqual from 'lodash/isEqual';
import io from 'socket.io-client'
import {toastr} from 'react-redux-toastr'
import { mePage } from './../../actions/headerTab'
import { acceptFriendRequest } from './../../actions/profile'
import ConversationArr from './ConversationArr'
import SICTutorials from './GetStarted/Tutorials/index.jsx';

let socket;
const _ = {
  isEqual: isEqual
}
const meMenuItems = [
  {title:'Profile', subtitle: 'View Public Profile', route: '/social/profile/'},
  {title:'College Application', subtitle: '% Complete', route: '/social/one-app'},
  {title:'Your Documents', subtitle: 'Documents', route: '/social/one-app/uploads', image: '/social/images/Icons/me_documents.svg'},
  {title:'Settings', subtitle: 'Manage Your Privacy', route: '/social/settings', image: '/social/images/Icons/me_settings.svg'},
]
const quadUrls=["/news", "/college-essays", "/social/celebrity-trivia", "/ranking/categories", "/international-resources"];

class Header extends Component{
  constructor(props){
    super(props);

    this.state = {
      activeTab: '',
      rb_menu: 'get-started',
      settings: false,
      searchTerm: '',
      renderSearch: false,
      showMeMenu: false,
      showMsgCount: true,
      topicUsrFlag: true,
      loginPopup: false,
    }

    this._renderSubComp = this._renderSubComp.bind(this);
    this.handleClick = this.handleClick.bind(this);
    this.handleSettings = this.handleSettings.bind(this);
    this.signOutUser = this.signOutUser.bind(this);
    this.handleSearchChange = this.handleSearchChange.bind(this);
    this.handleActiveTab = this.handleActiveTab.bind(this);
    this.handleSICMenu = this.handleSICMenu.bind(this);
    this.handleSearchUnmount = this.handleSearchUnmount.bind(this);
    this.handleSearchReset = this.handleSearchReset.bind(this);
    this.handleOutsideMeMenu = this.handleOutsideMeMenu.bind(this);
    this.loginPopup = this.loginPopup.bind(this)
    this.scrollUp = this.scrollUp.bind(this)
    this.hidePopup = this.hidePopup.bind(this)
    this._keyDown = this._keyDown.bind(this)
    this.meMenuBtn;
    this.meMenuContainer;
    this.searchInput;
    this.searchResultsContainer;

    //socket
    var host = process.env.SOCKET_SERVER;
    socket = io(host + ':3001', {
      secure: window.location.protocol.includes('https'),
      transports: ['websocket'],
      upgrade: true
    });
    this._connect = this._connect.bind(this);
    this._joinRoom = this._joinRoom.bind(this);
    this._joinThread1 = this._joinThread1.bind(this);
    this._showNotification = this._showNotification.bind(this);
    this._updateMessages = this._updateMessages.bind(this);
    this._sendMessage = this._sendMessage.bind(this);
    this.readNotification = this.readNotification.bind(this);
    this._addNewThread = this._addNewThread.bind(this);
    this._typingMsg = this._typingMsg.bind(this);
    this._cancelTypingMsg = this._cancelTypingMsg.bind(this);
    this._publishPost = this._publishPost.bind(this);
    this._deletePost = this._deletePost.bind(this);
    this._postComment = this._postComment.bind(this);
    this._addLike = this._addLike.bind(this);
    this._removeLike = this._removeLike.bind(this);
    this._updateShareCount = this._updateShareCount.bind(this);
    this._deletedComment = this._deletedComment.bind(this);
    this._editedComment = this._editedComment.bind(this);
    this._joinedRoom = this._joinedRoom.bind(this);
    this._disconnect = this._disconnect.bind(this);
    this._disconnectUsr = this._disconnectUsr.bind(this);
    this._joindedUser = this._joindedUser.bind(this);
    this._setViewTime = this._setViewTime.bind(this);
    this._addThreadUsr = this._addThreadUsr.bind(this);
    // socket listeners
    socket.on('pushed:notification', this._showNotification);
    socket.on('sent:msgNotification', this._updateMessages);
    socket.on('sent:message', this._sendMessage);
    socket.on('read_:notification', this.readNotification);
    socket.on('added:messageThread',this._addNewThread);
    socket.on('typing:message',this._typingMsg);
    socket.on('canceled-typing:message',this._cancelTypingMsg);
    socket.on('published:post', this._publishPost);
    socket.on('deleted:post', this._deletePost);
    socket.on('posted:comment', this._postComment);
    socket.on('added:like', this._addLike);
    socket.on('removed:like', this._removeLike);
    socket.on('updated:shareCount', this._updateShareCount);
    socket.on('deleted:comment', this._deletedComment);
    socket.on('edited:comment', this._editedComment);
    socket.on('disconnect', this._disconnect);
    socket.on('joined:room', this._joinedRoom);
    socket.on('disconnect:user', this._disconnectUsr);
    socket.on('joined:user', this._joindedUser);
    socket.on('set:viewTime', this._setViewTime);
    socket.on('added:threadUser', this._addThreadUsr);
  }
  // actions for socket connections
    //--------------------socket methods--------------------------//
  _connect(user){
    let room = 'post:room:';
    this._joinRoom(user.user_id, user.fname, room);
    this._joinThread1(user.user_id, user.fname, room);

    room = 'post:room:'+user.user_id;
    this._joinRoom(user.user_id, user.fname, room);
    this._joinThread1(user.user_id, user.fname, room);
  }
  _joinRoom(id, fname, room){
      socket.emit('join:room', {
          room: room,
          user_id: id,
          name: fname
      });
  }
  _joinThread1(id, fname, room){
    let thread_room = 'thread:'+room;
    socket.emit('join:thread', {
        name: fname,
        user_id: id,
        thread_room,
    });
  }
  _showNotification(notification){
    console.log(notification)
      toastr.info(notification.name+' '+notification.msg);
      this.props.addNotification(notification);
      if (notification.command == 12 || notification.command == 13) {
        if (notification.command == 12)
          this.props.acceptFriendRequest()
        getNetworkingData()
      }
  }
  _updateMessages() {
    this.setState({showMsgCount: true})
  }
  _sendMessage(data){
    const { messageThreads } = this.props;
    let index = messageThreads.findIndex(user => user.thread_id == data[0].msg_of_thread);
    if(index == -1){
      this.props.addNewThread(data[0].msg_of_thread);
      let data_1={
        id: data[0].msg_of_thread,
      }
      getThreaData(data_1)
      .then(()=>{
        this.props.sendMessage(data);
      })
    }else{
      this.props.sendMessage(data);
    }
  }
  readNotification(data){
    this.props.readNotification(data);
  }
  _addNewThread(id){
  }
  loginPopup() {
    this.setState({loginPopup: !this.state.loginPopup})
  }
  _typingMsg(data){
    let { user } = this.props;
    if(data.user_id != user.user_id){
      this.props.typeMsg(data);
    }
  }
  _cancelTypingMsg(data){
    let { user } = this.props;
    if(data.user_id != user.user_id){
      this.props.cancelMsg(data);
    }
  }

  _publishPost(post){
    const { logInUser } = this.props;
    this.props.publishPost(post[0]);
    if(post[0].hasOwnProperty('article_text') && logInUser.user_id == post[0].user_id){
        this.props.saveArticles(post[0])
    }
  }
  _deletePost(data){
    this.props.deletePostAction(data);
  }
  _postComment(message){
    this.props.addComment(message);
    if(message[0].social_article_id){
      this.props.addArticleComment(message);
    }
  }
  _addLike(likeData){
    this.props.addLike(likeData);
    if(likeData.social_article_id){
      this.props.addArticleLike(likeData);
    }
  }
  _removeLike(data){
    this.props.removeLike(data);
  }
  _updateShareCount(data){
      this.props.updateSharePostCountAction(data);
      if(data.type == "article"){
        this.props.updateArticleCount(data);
      }
  }
  _deletedComment(data){
    this.props.deleteCommentAction(data);
    if(data.social_article_id){
      this.props.deleteArticleComment(data);
    }
  }
  _editedComment(data){
    this.props.editedCommentAction(data);
    if(data[0].social_article_id){
      this.props.editArticleComment(data);
    }
  }
  _joinedRoom({ online }){
    const { onlineUsers } = this.props;
    let arr = Object.values(online);
    arr.map((entity) =>{
      if(entity){
        let index = -1;
        index = onlineUsers.findIndex((userId => userId == entity));
        if(index == -1){
          this.props.addInOnlineUsersArr(entity);
        }
      }
    })
  }
  _disconnect(){
  }
  _disconnectUsr(online){
    this.props.removeFromOnlineUsersArr(online);
  }
  _joindedUser(online){
    const { onlineUsers } = this.props;
    if(online){
      let index = -1;
      index = onlineUsers.findIndex((userId => userId == online));
      if(index == -1){
        this.props.addInOnlineUsersArr(online);
      }
    }
  }

  _setViewTime(data){
    this.props.setViewTimeAction(data);
  }
  _addThreadUsr(data){
    this.props.addThreadUserAction(data);
  }
  //------------------end of socket connection---------------------//

  componentDidMount(){
    this.props.setThreadCountAction();
    getArticles();
    fetchNotification(0)
    this.handleSICMenu();
    this.handleActiveTab();
    getUserData()
    .then(() => {
      if(this.props.user.signed_in == 1) {
        document.addEventListener('click', this.handleOutsideMeMenu, false);
      }
      this.props.setLogInUserIdAction(this.props.user.user_id);
      document.addEventListener('click', this.handleSearchReset, false);
    })
  }
  componentDidUpdate(prevProps){
    if(this.props.location !== prevProps.location){
      getUserData()
      .then(() => {
        if(this.props.user.signed_in == 1) {
          document.addEventListener('click', this.handleOutsideMeMenu, false);
        }
        document.addEventListener('click', this.handleSearchReset, false);
      })
      this.handleSICMenu();
      this.handleActiveTab();
      if(this.showMeMenu === true){
        this.setState({showMeMenu: false});
      }
    }
  }
  componentWillReceiveProps(nextProps) {
    if(!_.isEqual(this.props.topbarSearchResults, nextProps.topbarSearchResults) && !this.state.renderSearch) {
      this.setState({ renderSearch: true })
    }
    if(nextProps.user && nextProps.user.user_id && nextProps.user.user_id != this.props.user.user_id){
      this._connect(nextProps.user);
      let data ={ user_id: nextProps.user.user_id, pageNumber: 1 };
      getMessagesThreads(data);
    }
  }
  componentWillUnmount() {
    if (this.props.user.signed_in == 1) {
      document.removeEventListener('click', this.handleOutsideMeMenu, false);
    }
    document.removeEventListener('click', this.handleSearchReset, false);
  }

  hidePopup() {
    this.setState({loginPopup: false})
  }

  _keyDown(e) {
    const {topbarSearchResults} = this.props
    if (e.key === 'Enter' && topbarSearchResults.length > 0) {
      e.preventDefault();
      if (topbarSearchResults[0].category === 'college')
        this.setState({ renderSearch: false, searchTerm: '' }, ()=> (this.props.history.push('/college/' + topbarSearchResults[0].slug)))
      else if (topbarSearchResults[0].category === 'news')
        this.setState({ renderSearch: false, searchTerm: '' }, ()=> (this.props.history.push('/news'+(!!topbarSearchResults[0].slug ? ('/'+topbarSearchResults[0].slug) : ''))))
    }
  }

  handleClick(compName){
    this.setState({rb_menu:compName});
    if (compName == 'messages') {
      this.setState({showMsgCount: false})
    }
  }

  handleSettings() {
    this.setState({settings: !this.state.settings})
  }

  handleActiveTab() {
    let { user_id } = this.props.user;
    let path = window.location.pathname;
    if(path.includes('/social/networking')){ this.setState({activeTab: 'networking'}); }
    else if(path.includes('/social/manage-colleges')){ this.setState({activeTab: 'manage-colleges'}); }
    else if(path.includes('/social/profile/'+user_id) || path.includes('/social/settings') || path.includes('/social/edit-profile') ){ this.setState({activeTab: 'me'}); }
    else { this.setState({activeTab: 'home'}); }
  }
  handleSICMenu(){
    if ( quadUrls.includes(window.location.pathname )) {
        this.setState({rb_menu: 'quad'})
    }
    let flag = window.location.pathname.split('/')[2];
    if(window.location.pathname == '/social/article-dashboard'){
      this.setState({rb_menu: 'right-bar'});
    }
    else if(flag == 'article'){
      this.setState({rb_menu: 'right-bar'});
    }
    else if(window.location.pathname == '/social/article-editor'){
      this.setState({rb_menu: 'right-bar'});
    }
    else if(window.location.pathname.includes('/social/manage-colleges')){
      this.setState({rb_menu: 'get-started'})
    }
    else if(window.location.pathname.includes('/home')){
      this.setState({rb_menu: 'messages'})
    }
  }

  handleOutsideMeMenu(e) {
    if (this.meMenuContainer.contains(e.target) || this.meMenuBtn.contains(e.target)) {
      return;
    }
    if(this.state.showMeMenu === true){
      this.setState({showMeMenu: false})
    }
  }

  _renderSubComp(){
    switch(this.state.rb_menu){
      case 'right-bar': return <RightBar />
      case 'notifications' : return <Notifications />
      case 'get-started' : return <GetStarted location={this.props.location}/>
      case 'messages' : return <Messages />
      case 'quad' : return <Quad />
    }
  }

  signOutUser(){
    signOut();
    this.setState({settings: false})
  }

  handleSearchChange(e) {
    const { getTopbarSearchResults, requestCancellationFn, cancelPreviousRequest, topbarSearchResults, resetSearchResults } = this.props;
    const searchTerm = e.target.value;

    topbarSearchResults.length && resetSearchResults();

    if(!searchTerm.trim().length && !(Object.entries(requestCancellationFn).length === 0 && requestCancellationFn.constructor === Object)) {
      cancelPreviousRequest();
    }

    this.setState({ searchTerm: searchTerm }, () => {
      searchTerm.trim().length && getTopbarSearchResults(searchTerm.split(' ').join('+'), requestCancellationFn);
    })
  }

  handleSearchReset(e) {
    if (!!this.state.renderSearch && (this.searchResultsContainer.contains(e.target) || this.searchInput.contains(e.target))) {
      return;
    }
    if(this.state.renderSearch){
      this.setState({renderSearch: false, searchTerm: ''});
      return;
    }
  }

  handleSearchUnmount() {
    this.setState({ renderSearch: false, searchTerm: '' });
  }

 scrollUp(){
  if (window.location.pathname == "/home")
    {
      document.body.scrollTop = document.documentElement.scrollTop = 0;
    }
  }

  render() {
    let { user, topbarSearchResults, showTutorials } = this.props;
    const { searchTerm, renderSearch } = this.state;
    let userImg = user && user.profile_img_loc ?
      'url("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'+user.profile_img_loc+'")'
      :
      user && user.fname ? 'url(/social/images/Avatar_Letters/'+user.fname.charAt(0).toUpperCase()+'.svg)'
      :
      'url(/social/images/Avatar_Letters/P.svg)'

    let profileImg = {
      backgroundImage: userImg,
      backgroundSize: 'cover',
      backgroundPosition: 'center',
    }
    let sicClass = this.props.user.signed_in == 1 ? {'justifyContent': 'flex-start'} : {'paddingRight':'25px'};
    let logoLink = this.props.user.signed_in == 1 ? '/home' : '/';
    return (
      <span>
        {
            showTutorials && <SICTutorials />
        }
        <header id="header" className={this.props.header_zIndex ? "set-zIndex" : ""}>
          <div id="top-header" className="mbl_none">
            <div className="header-main">

              <div className="columns logo_parent">
                <Link className="logo" to={logoLink}><img src="/social/images/plexuss-logo.svg" /></Link>
                <span className="slogan">The Global Student Network</span>
              </div>

              <div className="large-3 medium-3 columns search_form_parent">
                <form className="search-form">
                  <a href="#" className={'button postfix fa fa-search btn-search '+ (searchTerm.length > 0 && topbarSearchResults.length > 0 && 'search-btn-top-border-radius')}></a>
                  <input ref={(ref) => {this.searchInput = ref;}} placeholder="Search Universities" value={searchTerm} className={'input-contral '+ (searchTerm.length > 0 && topbarSearchResults.length > 0 && 'input-top-border-radius') } onFocus={this.hidePopup} onChange={this.handleSearchChange} onBlur={this.handleSearchReset} onKeyDown={this._keyDown}/>
                  {
                    renderSearch && searchTerm.trim().length > 0 && topbarSearchResults.length > 0 &&
                    <span ref={(ref) => {this.searchResultsContainer = ref;}} id='search-main-results-container'><SearchResults unmountSearchResults={this.handleSearchUnmount} searchResults={topbarSearchResults} /></span>
                  }
                </form>
              </div>

              <div className="clearfix sic-area" style={sicClass}>
              <div className="new-navbar columns">
                <nav className="top-bar header navarea mbl_none" data-topbar role="navigation">
                  <section className="top-bar-section">
                    <div className="header-main">
                      <div className="main-menu">
                        <a href="javascript:void(0);" className="icon">
                          <i className="fa fa-bars"></i>
                        </a>
                        {this.props.user.signed_in == 1 &&
                          <div id="myLinks">
                            <ul className="left">
                              <li onClick={() => this.scrollUp()}>
                                <Link to={window.location.pathname == "/home" ? "#" : "/home"}  className="align_text">
                                {this.state.activeTab === 'home' ? (<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/home-active-icon.svg"/>) : (<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/home-inactive-icon.svg"/>) }
                                <br/>Home</Link>
                              </li>
                              <li>
                                <Link to="/social/networking/connection" className="align_text">
                                {this.state.activeTab === 'networking' ? (<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/network-active-icon.svg"/>) : (<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/networks-inactive-icon.svg"/>) }
                                <br/>My Network</Link>
                              </li>
                              <li>
                                <Link to="/social/manage-colleges" className="align_text" className="align_text">
                                {this.state.activeTab === 'manage-colleges' ? (<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/college-active-icon.svg"/>) : (<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/college-inactive-icon.svg"/>) }
                                <br/>My Colleges</Link>
                              </li>
                              <li>
                                <a ref={(ref) => {this.meMenuBtn = ref;}} onClick={() => {this.setState({showMeMenu: !this.state.showMeMenu}); this.props.mePage();}}  className="align_text" className="align_text">
                                <div className={"prf-img " + (this.state.activeTab === 'me' && 'active')} style={profileImg}></div>
                                Me<img className={this.state.showMeMenu ? "me-arrow rotate" : "me-arrow"} src="/social/images/Icons/me_arrow.svg"/></a>
                              </li>
                            </ul>
                          </div>
                        }
                        {this.props.user.signed_in == 0 &&
                          <div id="login-group">
                            <ul className="left">
                              <li>
                                <div className="loginBtnHomepage" onClick={this.loginPopup}>
                                  Login
                                </div>
                                <LoginPopup isDisplay={this.state.loginPopup} />
                              </li>
                              <li>
                                <a href="/signup?utm_source=SEO&utm_medium=frontPage" className="signupBtnHomepage">
                                  <div className="signupBtnHref">Sign Up</div>
                                </a>
                              </li>
                            </ul>
                          </div>
                        }
                        {this.props.user.signed_in == 1 ? (
                          <span ref={(ref) => {this.meMenuContainer = ref;}}><MeMenu user={user} visible={this.state.showMeMenu} signOut={this.signOutUser} profileImg={profileImg}/></span>
                        ): (null)}
                      </div>
                    </div>
                  </section>
                </nav>
              </div>
              </div>

              <div className="header-sic">
                <div className="right tabsarea right-menu">
                  <ul className="tabs" data-tab role="tablist">
                    <li onClick={() => this.handleClick('get-started')} className={"tab-title" + (this.state.rb_menu === 'get-started' ? ' active' : '')}><img src={this.state.rb_menu === 'get-started' ? "/social/images/Icons/sic-getstarted-active.svg" : "/social/images/Icons/sic-getstarted.svg" } /></li>
                    <li onClick={() => this.handleClick('messages')} className={"tab-title" + (this.state.rb_menu === 'messages' ? ' active' : '')}>
                      <img src={this.state.rb_menu === 'messages' ? "/social/images/Icons/sic-messages-active.svg" : "/social/images/Icons/sic-messages.svg"} />
                      {
                        this.state.showMsgCount && this.props.msgs_count > 0 &&
                          <span className="count_of_notification">{this.props.msgs_count}</span>
                      }
                    </li>
                    <li onClick={() => this.handleClick('notifications')} className={"tab-title" + (this.state.rb_menu === 'notifications' ? ' active' : '')}>
                      <img src={this.state.rb_menu === 'notifications' ? "/social/images/Icons/sic-bell-active.svg" : "/social/images/Icons/sic-bell.svg"} />
                      {
                        this.props.notifications_count > 0 &&
                          <span className="count_of_notification">{this.props.notifications_count}</span>
                      }
                    </li>
                    <li onClick={() => this.handleClick('quad')} className={"tab-title" + (this.state.rb_menu === 'quad' ? ' active' : '')} ><img src={this.state.rb_menu === 'quad' ? "/social/images/Icons/sic-quad-active.svg" : "/social/images/Icons/sic-quad.svg"} /></li>
                    <li onClick={() => this.handleClick('right-bar')} className={"tab-title" + (this.state.rb_menu === 'right-bar' ? ' active' : '')}><img src={this.state.rb_menu === 'right-bar' ? "/social/images/Icons/sic-more-active.svg" : "/social/images/Icons/sic-more.svg"} /></li>
                  </ul>
                </div>
              </div>

            </div>


          </div>
          <span className="mbl_header"><MobileHeader user={this.props.user}/></span>
        </header>
        <span className=" mbl_none">
          {this._renderSubComp()}
        </span>
      <ConversationArr />
      </span>
    );
  }
}


class MeMenu extends Component {
  constructor(props) {
    super(props);
    this.state = {}
  }
  render() {
    const { user, visible, signOut, profileImg } = this.props;
    const percentage = 50;

    return (
      <div className={visible ? "me-menu-container" : "me-menu-container collapsed" }>
        <img className="me-triangle" src="/social/images/Icons/me_triangle.png"/>
        <ul>
          { meMenuItems.map((item, i) =>
            <li key={i} className="me-menu-li">
              <Link className="me-row row" to={item.title === 'Profile' ? item.route+user.user_id : item.route}>
                <div className="large-4 medium-4 small-2 columns">
                  { item.title === 'Profile' ? (<div className="prf-img me-prf" style={profileImg}></div>)
                  :
                    item.title === 'College Application' ? (<div className="progress_bar"><CircularProgressbar percentage={!!user.one_app_percent ? (user.one_app_percent > 100) ? 100 : user.one_app_percent : 0} text={`${!!user.one_app_percent ? (user.one_app_percent > 100) ? 100 : user.one_app_percent : 0}%`} /></div>)
                  :
                    (<img src={item.image} alt=""/>)
                  }
                </div>
                <div className="large-8 medium-8 small-10 columns">
                  {item.title === 'Profile' ? (<div className="me-title">{user.fname + ' ' + user.lname}</div>) : (<div className="me-title">{item.title}</div>)}
                  <div className="me-subtitle">{item.title === 'College Application' ? (!!user.one_app_percent ? (user.one_app_percent > 100) ? 100 : user.one_app_percent : 0) + item.subtitle : item.subtitle}</div>
                </div>
              </Link>
            </li>
          )}
          <li key={'sign-out'} onClick={signOut}>
            <div className="me-signout">
              <img src="/social/images/Icons/me_logout.svg"/>
              <div className="me-signout-text">{'Sign Out'}</div>
            </div>
          </li>
        </ul>
      </div>
    );
  }
}
const mapStateToProps = (state) =>{
  return{
    tab: state.headerTabs.tab,
    user: state.user && state.user.data,
    topbarSearchResults: state.search.topbarSearchResults,
    requestCancellationFn: state.search.requestCancellationFn,
    messageThreads: state.messages.messageThreads && state.messages.messageThreads.topicUsr,
    msgs_count: state.messages && state.messages.unreadThread,
    allThreadMessages: state.messages && state.messages.allThreadMessages && state.messages.allThreadMessages,
    isThreads: state.messages && state.messages.isThreads,
    notifications_count: state.notification && state.notification.unread_count,
    header_zIndex: state.posts.headerState,
    articles: state.articles && state.articles.userArticles,
    showTutorials: state.tutorials.show,
    onlineUsers: state.user && state.user.onlineUsers,
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    getTopbarSearchResults: (searchTerm, requestCancellationFn) => { dispatch(getTopbarSearchResults(searchTerm, requestCancellationFn)) },
    cancelPreviousRequest: () => { dispatch(cancelPreviousRequest()) },
    resetSearchResults: () => { dispatch(resetSearchResults()) },
    setUserRequest: (data) => { dispatch(setUserRequestFlag(data)) },
    addNotification: (notification) => { dispatch(addNotification(notification))},
    readNotification: (data) => { dispatch(readNotification(data))},
    mePage: () => {dispatch(mePage())},
    acceptFriendRequest: () => {dispatch(acceptFriendRequest())},
    sendMessage: (data) => {dispatch(sendMessageAction(data))},
    setThreadCountAction: () => { dispatch(setThreadCountAction())},
    addNewThread: (id) => { dispatch(addNewThread(id))},
    cancelMsg: (data) => { dispatch(cancelMsg(data))},
    typeMsg: (data) => { dispatch(typeMsg(data))},
    setLogInUserIdAction: (data) => { dispatch(setLogInUserIdAction(data))},

    publishPost: (post) => {dispatch(publishPost(post))},
    deletePostAction: (data) => {dispatch(deletePostAction(data))},
    saveArticles: (data) => { dispatch(saveArticles(data))},
    hidePostAction: (data) => {dispatch(hidePostAction(data))},
    addComment: (comment) => {dispatch(addCommentSuccess(comment))},
    addLike: (likeData) => {dispatch(likeSuccess(likeData))},
    removeLike: (data) => {dispatch(unlikeSuccess(data))},
    updateSharePostCountAction: (data) => {dispatch(updateSharePostCountAction(data))},
    updateArticleCount: (data) => {dispatch(updateArticleCount(data))},
    deleteCommentAction: (data) => {dispatch(deleteCommentAction(data))},
    editedCommentAction: (data) => {dispatch(editedCommentAction(data))},

    addArticleComment: (comment) => {dispatch(addArticleComment(comment))},
    addArticleLike: (likeData) => {dispatch(addArticleLike(likeData))},
    editArticleComment: (data) => {dispatch(editArticleComment(data))},
    deleteArticleComment: (data) => { dispatch(deleteArticleComment(data))},
    removeCommentLike: (data) => { dispatch(removeCommentLike(data))},
    addInOnlineUsersArr: (data) => { dispatch(addInOnlineUsersArr(data))},
    removeFromOnlineUsersArr: (data) => { dispatch(removeFromOnlineUsersArr(data))},
    setViewTimeAction: (data) => { dispatch(setViewTimeAction(data))},
    addThreadUserAction: (data) => { dispatch(addThreadUserAction(data))}
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(Header);
