import React from 'react'
import { Link } from 'react-router-dom'
import axios from 'axios';
import HoverCard from '../hover_card';
import PostFunctionality from './post_functionality'
import PostBlock from './post_block'
import ReportingModal from './reporting_modal'
import DesktopComments from './desktopComments'
import MobileComments from './mobileComments'
import SharedPostContent from './sharedPostContent'
import { addInSharedPostAction, addFrndStateInArrAction } from './../../../actions/posts'
import './styles.scss'
import { connect } from 'react-redux'

class Post extends React.Component {
  is_mount = false;
  constructor(props) {
    super(props);
    this.state = {
      mobile_comments: false,
      desktopComment: false,
      postState: {},
      sharedPostText: '',
      friendStatus: '',
      deletedPost: false,
    };
    this.handleMobileCommentd = this.handleMobileCommentd.bind(this);
    this.showDesktopComment = this.showDesktopComment.bind(this);
    this._getFriendStatus = this._getFriendStatus.bind(this);
    this.changeStatus = this.changeStatus.bind(this)
  }
  componentDidMount(){
    this.is_mount = true;
    let { post, commentsFlag, addInSharedPostAction, sharedPosts } = this.props;
    if(post.is_shared){
      if(post.hasOwnProperty('article_text')){
        let index = sharedPosts.findIndex(p => p.id == post.original_article_id && p.hasOwnProperty('article_text'));
        if(index == -1){
          axios({
            method: 'get',
            url: '/social/get-single-articles?article-id='+post.original_article_id,
          })
          .then(res => {
            if(res.data.length == 0){
              if(this.is_mount)
                this.setState({deletedPost: true})
            }else{
              addInSharedPostAction(res.data[0]);
              if(this.is_mount) this.setState({ sharedPostText: post.article_text })
            }
          })
          .catch(error => {
            if (this.is_mount)
              this.setState({deletedPost: true})
          })
        }else{
          if (this.is_mount)
            this.setState({
              postState: sharedPosts[index],
              sharedPostText: post.article_text,
            })
        }
      }else{
        if(post.share_type == 'article'){
          let index = sharedPosts.findIndex(p => p.id == post.original_post_id && p.hasOwnProperty('article_text'));
          if(index == -1){
            axios({
              method: 'get',
              url: '/social/get-single-articles?article-id='+post.original_post_id,
            })
            .then(res => {
              if(res.data.length == 0){
                if (this.is_mount)
                  this.setState({deletedPost: true})
              }
              else{
                addInSharedPostAction(res.data[0]);
                if (this.is_mount)
                  this.setState({
                    sharedPostText: post.post_text,
                  })
              }
            })
            .catch(error => {
              if (this.is_mount)
                this.setState({deletedPost: true})
            })
          }else{
            if (this.is_mount)
              this.setState({
                postState: sharedPosts[index],
                sharedPostText: post.article_text,
              })
          }
        }else{
          let index = sharedPosts.findIndex(p => p.id == post.original_post_id && p.hasOwnProperty('post_text'));
          if(index == -1){
            axios({
              method: 'get',
              url: '/social/get-single-post?post-id='+post.original_post_id,
            })
            .then(res => {
              if(res.data.length == 0){
                if (this.is_mount)
                  this.setState({deletedPost: true})
              }
              else{
                addInSharedPostAction(res.data[0]);
                if (this.is_mount)
                  this.setState({
                    sharedPostText: post.post_text,
                  })
              }
            })
            .catch(error => {
              if (this.is_mount)
                this.setState({deletedPost: true})
            })
          }else{
            if (this.is_mount)
              this.setState({
                postState: sharedPosts[index],
                sharedPostText: post.article_text,
              })
          }
        }
      }
    }else{
      if (this.is_mount)
        this.setState({
          postState: post,
          sharedPostText: '',
        })
    }
    if(this.props.post.user_id){
      this._getFriendStatus(this.props.logInUser.user_id, this.props.post.user_id);
    } 
    if(commentsFlag){
      if (this.is_mount)
        this.setState({
          desktopComment: true,
        })
    }
  }
  componentWillUnmount() {
    this.is_mount = false;
  }
  componentDidUpdate(prevProps){
    let { post, sharedPosts } = this.props;
    if(post.hasOwnProperty('post_text')){
      if(prevProps.post != this.props.post){
        if(post.is_shared){
          this.setState({
            sharedPostText: post.post_text,
            postState: this.state.postState,
          })
        }else{
          this.setState({
            postState: post,
            sharedPostText: '',
          })
        }
      }
    }
    if(post.is_shared && prevProps.sharedPosts != sharedPosts){
      let index = -1;
      if(post.hasOwnProperty('article_text')){
        index = sharedPosts.findIndex(p => p.id == post.original_article_id && p.hasOwnProperty('article_text'));
      }else{
        if(post.share_type == 'post'){
          index = sharedPosts.findIndex(p => p.id == post.original_post_id && p.hasOwnProperty('post_text'));
        }else{
          index = sharedPosts.findIndex(p => p.id == post.original_post_id && p.hasOwnProperty('article_text'));
        }
      }
      if(index == -1){
        this.setState({deletedPost: true})
      }else{
        this.setState({
          postState: sharedPosts[index],
          deletedPost: false,
        })
      }
    }
  }
  _getFriendStatus(LoggedUser, postUser){
    if(LoggedUser != postUser){
      let data = {user_one_id: LoggedUser, user_two_id: postUser}
      let index = this.props.frndsStateArr.findIndex(frnd => frnd.user_one_id == LoggedUser && frnd.user_two_id == postUser);
      if(index == -1){
        axios({
            method: 'post',
            url: '/social/friend-status',
            data: data,
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
        })
        .then(res => {
          if(res.statusText === 'OK'){
            if (this.is_mount) {
              this.setState({friendStatus: res.data})
              let _data = {
                user_one_id: LoggedUser,
                user_two_id: postUser,
                relation: res.data,
              }
              this.props.addFrndStateInArrAction(_data);
            }
          }
        })
        .catch(error => {
            console.log(error);
        })
      }else{
        if(this.is_mount){
          this.setState({friendStatus: this.props.frndsStateArr[index].relation})
        }
      }
    }
  }
  handleMobileCommentd(){
    this.setState({
      mobile_comments: !this.state.mobile_comments,
    })
  }
  showDesktopComment(){
    this.setState({
      desktopComment: !this.state.desktopComment,
    })
  }
  changeStatus(newStatus) {
    this.setState({friendStatus: newStatus})
  }
  render(){
    const { post, logInUser } = this.props;
    let user = post.user;
    let imgStyles =  { backgroundImage: (user && user.profile_img_loc) ? 'url("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'+user.profile_img_loc+'")' : (user && user.fname) ? 'url(/social/images/Avatar_Letters/'+user.fname.charAt(0).toUpperCase()+'.svg)' : 'url(/social/images/Avatar_Letters/P.svg)' }
    return (
        <div className="row post-row-container">
          { post && user &&
            <div className={"post-box " + (this.state.mobile_comments ? 'post_mbl_view': '')}>
              <div className="large-12 small-12 columns padding-0">
                <div className="post-content">
                  <div className="post-content-inner">
                    <div className="post-head">
                      <div className="post-user card-hover">
                        {
                          logInUser.user_id != post.user_id &&
                          <HoverCard user={user} logInUser ={logInUser} friendStatus={this.state.friendStatus} accountSettings={this.props.post.user_account_settings} changeStatus={this.changeStatus}/>
                        }
                        <Link to={'/social/profile/'+ (!!post && post.user_id)}><div className="post-user-img" style={imgStyles}/></Link>
                      </div>
                      <div className="title-area card-hover">
                        {
                          logInUser.user_id != post.user_id &&
                            <HoverCard user={user} logInUser={logInUser} friendStatus={this.state.friendStatus} accountSettings={this.props.post.user_account_settings} changeStatus={this.changeStatus}/>
                        }
                        <Link className="user-name user_name_hover" to={'/social/profile/'+ (!!post && post.user_id)}>{user && user.fname} {user && user.lname+" "}  <div className={"flag flag-"+ (!!user && !!user.country && user.country.country_code.toLowerCase())}></div></Link>
                        <strong className="user-title">{user.is_student === 1 ? 'Student' : ''}</strong>
                      </div>
                      {
                        (post && (post.is_shared == true) ) &&
                          <a href="" className="shared_post_flag">Shared a post</a>
                      }
                      <ReportingModal post={post} logInUser={logInUser}/>
                    </div>
                    {
                      post && post.is_shared ?
                        <SharedPostContent post={this.state.postState} sharedPostText={this.state.sharedPostText} handleMobileCommentd={this.handleMobileCommentd} logInUser={logInUser}
                         showDesktopComment={this.showDesktopComment} desktopComment={this.state.desktopComment} deletedPost={this.state.deletedPost}/>
                      :
                        <PostBlock post={post} sharedPostText={this.state.sharedPostText} handleMobileCommentd={this.handleMobileCommentd} logInUser={logInUser} showDesktopComment={this.showDesktopComment} desktopComment={this.state.desktopComment}/>
                    }
                  </div>
                  <PostFunctionality post={post} postState={this.state.postState} handleMobileCommentd={this.handleMobileCommentd} logInUser={logInUser} showDesktopComment={this.showDesktopComment} desktopComment={this.state.desktopComment}/>
                  {
                    this.state.desktopComment &&
                    <DesktopComments post={post} logInUser={logInUser} showDesktopComment={this.showDesktopComment} />
                  }
                </div>
              </div>
            </div>
          }
          <MobileComments post={post} logInUser={logInUser} mobile_comments={this.state.mobile_comments} handleMobileCommentd={this.handleMobileCommentd} />
        </div>
    )
  }
}
function mapDispatchToProps(dispatch) {
  return({
    addInSharedPostAction: (post) => { dispatch(addInSharedPostAction(post))},
    addFrndStateInArrAction: (frndState) => { dispatch(addFrndStateInArrAction(frndState))},
  })
}
function mapStateToProps(state){
  return{
    activePostId: state.posts.activePostId,
    postType: state.posts.postType,
    sharedPosts: state.posts.sharedPosts,
    frndsStateArr: state.posts.frndsStateArr,
  }
}
export default connect(mapStateToProps, mapDispatchToProps)(Post);
