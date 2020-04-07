import React, {Component} from 'react'
import { Link } from 'react-router-dom'
import { connect } from 'react-redux'
import axios from 'axios';
import Parser from 'html-react-parser';
import moment from 'moment'
import ReactTooltip from 'react-tooltip'
import TimeAgo from 'react-timeago'
import Dropzone from 'react-dropzone'
import objectToFormData from 'object-to-formdata'
import { like, unlike } from './../../../../api/post'
import HoverCard from './../../hover_card'
import { deleteComment, editComment } from './../../../../api/post';
import { addFrndStateInArrAction } from './../../../../actions/posts'
import imageCompression from 'browser-image-compression';
import './styles.scss'
const cross = {
  position: 'absolute',
  right: '13px',
  fontSize: '22px',
  cursor: 'pointer',
  fontWeight: '600',
  display: 'flex',
  justifyContent: 'center',
  alignItems: 'center',
  width: '27px',
  height: '27px',
  borderRadius: '50%'
}
class Comment extends Component {
    constructor(props){
        super(props);
        this.state = {
          likeComment: false,
          friendStatus: '',
          flag: true,
          text: '',
          editComment: false,
          removeImgEditMode: false,
          files: [],
          image_link: '',
          localTime: new Date(),
        }
        this.toggleLikeComment = this.toggleLikeComment.bind(this);
        this._getFriendStatus = this._getFriendStatus.bind(this);
        this._deleteComment = this._deleteComment.bind(this);
        this._editComment = this._editComment.bind(this);
        this.keyPress = this.keyPress.bind(this);
        this.onChange = this.onChange.bind(this);
        this.onDrop = this.onDrop.bind(this);
        this.removeImg = this.removeImg.bind(this);
        this.removeImgInEditMode = this.removeImgInEditMode.bind(this);
    }
    componentDidMount(){
      let { comment, logInUser } = this.props;
      let localtime = moment.utc(comment.created_at).local();
      this.setState({localTime: localtime});
      for(let i=0; i< comment.likes.length ; i++){
          if(comment.likes[i].user_id === logInUser.user_id){
              this.setState({
                likeComment: true,
              })
          }
      }
      this._getFriendStatus(logInUser.user_id, comment.user_id);
      let newText = '';
      let arr = [];
      arr = comment.comment_text && comment.comment_text.split(' ');
      arr && arr.map(word => {
        if(this.validURL(word)){
          var prefix = 'http';
          if(word.toLowerCase().substr(0, prefix.length) != prefix)
          {
            word = 'http://' + word;
          }
          newText =newText + ' ' + `<a href=${word} target='_blank'>${word}</a>`;
        }else{
          newText =newText + ' ' + word;
        }
      })
      this.setState({
        text: newText,
      })
      if(comment.images && comment.images[0]){
        this.setState({
            image_link: comment.images[0].image_link,
        })
      }
    }
    componentDidUpdate(prevProps){
      if(prevProps.comment !== this.props.comment){
        let { comment, logInUser } = this.props;
        let index = comment.likes.findIndex(like => like.user_id == logInUser.user_id);
        if(index !== -1){
          this.setState({
            likeComment: true,
          })

        }else{
          this.setState({
            likeComment: false,
          })
        }
        let newText = '';
        let arr = [];
        arr = comment.comment_text && comment.comment_text.split(' ');
        arr && arr.map(word => {
          if(this.validURL(word)){
            var prefix = 'http';
            if(word.toLowerCase().substr(0, prefix.length) != prefix)
            {
              word = 'http://' + word;
            }
            newText =newText + ' ' + `<a href=${word} target='_blank'>${word}</a>`;
          }else{
            newText =newText + ' ' + word;
          }
        })
        this.setState({
          text: newText,
        })
        if(comment.images && comment.images[0]){
          this.setState({
              image_link: comment.images[0].image_link,
          })
        }
      }
    }
    _getFriendStatus(LoggedUser, postUser){
      if(LoggedUser != postUser){
        const { frndsStateArr } = this.props;
        let data = {user_id: LoggedUser, user_two_id: postUser}
        let index = frndsStateArr.findIndex(frnd => frnd.user_one_id == LoggedUser && frnd.user_two_id == postUser);
        if(index == -1){
          axios({
              method: 'post',
              url: '/social/friend-status',
              data: data,
              headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
          })
          .then(res => {
            if(res.statusText === 'OK'){
              this.setState({friendStatus: res.data})
              let _data = {
                user_one_id: LoggedUser,
                user_two_id: postUser,
                relation: res.data,
              }
              this.props.addFrndStateInArrAction(_data);
            }
          })
          .catch(error => {
              console.log(error);
          })
        }else{
          this.setState({friendStatus: frndsStateArr[index].relation})
        }
      }
    }
    _deleteComment(){
      let { comment} = this.props;
      let postId = -1;
      let data = {
        comment_id: comment.id,
        post_id: comment.post_id,
        social_article_id: comment.social_article_id,
      };
      data.thread_room = 'post:room:';
      deleteComment(data);
    }
    _editComment(){
      const { comment } = this.props;
      this.setState({
        editComment: !this.state.editComment,
        text: comment.comment_text,
      })
    }
    onChange(event){
      this.setState({
        text: event.target.value,
      })
    }
    keyPress(event){
      if(event.keyCode == 13){
        event.preventDefault();
        let { post, logInUser, type, comment } = this.props;
        let { removeImgEditMode } = this.state;
        let thread_room = '';
        let comment1 = {};
        comment1.parent_id = null;
        comment1.user_id = logInUser.user_id;
        comment1.post_id = post.id;
        comment1.target_id = post.user_id;
        comment1.user_name = logInUser.fname+' '+logInUser.lname;
        comment1.comment_text = this.state.text;
        comment1.shared_link = '';
        comment1.is_gif = false;
        comment1.gif_link = null;
        if(type == 0){
            thread_room = 'post:room:';
            comment1.post_id = null;
            comment1.article_id = post.id;
        }else {
            thread_room = 'post:room:';
            comment1.post_id = post.id;
            comment1.article_id = null;
        }
        comment1.thread_room = thread_room;
        comment1.comment_images = -1;
        if(this.state.files.length > 0){
            comment1.comment_images = this.state.files;
        }
        if(this.state.files.length > 0 || this.state.text != ''){
          comment1.removeImage = -1;
          if(removeImgEditMode){
              if(comment.images && comment.images[0]){
                  comment1.removeImage = comment.images[0].id;
              }
          }
          else if(this.state.files.length > 0){
              if(comment.images && comment.images[0]){
                  comment1.removeImage = comment.images[0].id;
              }
          }
          comment1.comment_id = comment.id;
          const formData = objectToFormData(comment1);
          editComment(formData);
        }
        this.setState({
          editComment: false,
          files: [],
          image_link: '',
          removeImgEditMode: false,
        })
      }
  }
  onDrop(files) {
    files.map(file => (Object.assign(file, {
      preview: URL.createObjectURL(file)
    })))
    this.setState({
      files: files,
      image_link: '',
      removeImgEditMode: true
    });
  }
  onDropRejected(files) {
    const compressionOptions = {
      maxSizeMB: 1,
      maxWidthOrHeight: 1920,
      useWebWorker: true
    };

    imageCompression(files[0], compressionOptions).then(compressedFile => {
      let newFiles = this.state.files;
      newFiles.push(Object.assign(compressedFile, {
        preview: URL.createObjectURL(compressedFile)
      }))
      this.setState({
        files: newFiles,
        image_link: '',
        removeImgEditMode: true
      });
    }).catch(error => {
      console.log('Error occured while compressing the image', error);
    });
  }
    toggleLikeComment(){
      let { comment, logInUser, postId, type } = this.props;
      let obj = {};
      obj.user_id = logInUser.user_id;
      obj.post_comment_id = comment.id;
      obj.user_name = logInUser.fname+ ' '+logInUser.lname ;
      obj.target_id = comment.user_id;
      obj.liked_comment = this.state.likeComment ? 0 : 1;

      if(type == 0){
        obj.post_id = null;
        obj.thread_room = 'post:room:';
        obj.social_article_id = postId;
      }else{
        obj.post_id = postId;
        obj.thread_room = 'post:room:';
        obj.social_article_id = null;
      }
      if(this.state.flag){
        this.setState({flag: false},()=>{
          if(this.state.likeComment){
            unlike(obj)
            .then(()=>{
              this.setState({
                likeComment: false,
                flag: true,
              })
            })
          }
          else{
            like(obj)
            .then(()=>{
              this.setState({
                likeComment: true,
                flag: true,
              })
            })
          }
        })
      }
    }
    validURL(str) {
      var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
        '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
      return !!pattern.test(str);
    }
    removeImg(index){
      let test_files = this.state.files;
      test_files.splice(index, 1);
      this.setState({
        files: test_files,
      });
    }
    removeImgInEditMode(){
      this.setState({removeImgEditMode: true})
    }
    render(){
        let { comment, logInUser } = this.props;
        let commentUser = comment.user;
        let thumbs = this.state.files.map((file, index) => (
          <div key={index} className="comment_text_img_conatiner">
              <div style={cross} onClick={() => this.removeImg(index)}>x</div>
              <img src={file.preview} />
          </div>
        ));
        if(this.state.files.length == 0 && this.state.image_link != '' && this.state.image_link && !this.state.removeImgEditMode){
          thumbs = <div className="comment_text_img_conatiner">
              <div style={cross} onClick={this.removeImgInEditMode}>x</div>
              <img src={this.state.image_link} />
          </div>
        }
        let imgStyles = {
          backgroundImage: (commentUser && commentUser.profile_img_loc) ? 'url("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'+commentUser.profile_img_loc+'")' : (commentUser && commentUser.fname) ? 'url(/social/images/Avatar_Letters/'+commentUser.fname.charAt(0).toUpperCase()+'.svg)' : 'url(/social/images/Avatar_Letters/P.svg)',
        }
        return(
            <div className="post-comment-area">
              <div className="comment-image card-hover">
                {
                  logInUser.user_id != comment.user_id &&
                    <HoverCard user={commentUser} logInUser ={logInUser} friendStatus={this.state.friendStatus} accountSettings={comment.user_account_settings}/>
                }
                <div className="comment-user-img" style={imgStyles} />
              </div>
              {
                !this.state.editComment && commentUser &&
                <div className="comment-content">
                  <div className="comment-content-text">
                    <strong className="comment-title">
                      <Link to={"/social/profile/"+comment.user_id} className="card-hover">
                      {commentUser.fname} {commentUser.lname}
                      {
                        logInUser.user_id != comment.user_id &&
                          <HoverCard user={commentUser} logInUser ={logInUser} friendStatus={this.state.friendStatus} accountSettings={comment.user_account_settings}/>
                      }
                      </Link>
                    </strong>
                    {
                      this.state.text &&
                        <div className="comment_content"> {Parser(this.state.text)}</div>
                    }
                    {
                      comment.images.length > 0 && comment.images[0].image_link &&
                        <div className="comment_img_parent">
                          <img src={comment.images[0].image_link} alt=""/>
                        </div>
                    }
                  </div>
                  <div className="comment-msg-socail-icon">
                    <ul>
                      <li>
                        <img src={this.state.likeComment ? "/social/images/Icons/Heart-Outline-filled@2x.png" : "/social/images/heart-icon.png" } onClick={() => this.toggleLikeComment() }/>
                        <span>
                          {comment.likes.length != 0 && comment.likes.length}
                        </span>
                      </li>
                      <li>
                        <span><TimeAgo date={this.state.localTime} /></span>
                      </li>
                      {
                        logInUser.user_id == comment.user_id &&
                        <li className="edit_trash_banner">
                          <div data-tip data-for='edit_tooltip' className="_edit_img_parent" onClick={this._editComment}>
                            <img src="/social/images/Icons/pencil@2x.png"/>
                          </div>
                          {
                            !this.state.editComment &&
                            <ReactTooltip id='edit_tooltip' aria-haspopup='true' role='example'>
                              <span className="_tooltip">Edit</span>
                            </ReactTooltip>
                          }
                          <div data-tip data-for='trash_tooltip' className="_trash_img_parent"  onClick={this._deleteComment}>
                            <img src="/social/images/Icons/trash@2x.png"/>
                          </div>
                          <ReactTooltip id='trash_tooltip' aria-haspopup='true' role='example'>
                            <span className="_tooltip">Delete</span>
                          </ReactTooltip>
                        </li>
                      }
                    </ul>
                  </div>
                </div>||
                this.state.editComment &&
                <div className="comment-content">
                <a  className="cancel-edit-comment" onClick={this._editComment}>&times;</a>
                    <textarea placeholder="Say something" onKeyDown={this.keyPress} onChange={this.onChange} value={this.state.text}></textarea>
                    {thumbs}
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
                            <i className="fa fa-camera camera-icon"></i>
                        </div>
                        )}
                    </Dropzone>
                </div>
              }
            </div>
        )
    }
}
function mapDispatchToProps(dispatch) {
  return({
    addFrndStateInArrAction: (frndState) => { dispatch(addFrndStateInArrAction(frndState))},
  })
}
function mapStateToProps(state){
  return{
    frndsStateArr: state.posts.frndsStateArr,
  }
}
export default connect(mapStateToProps, mapDispatchToProps)(Comment)
