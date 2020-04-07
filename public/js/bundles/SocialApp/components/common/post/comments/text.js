import React, { Component } from 'react';
import { Link } from 'react-router-dom'
import { saveComment } from './../../../../api/post'
import objectToFormData from 'object-to-formdata'
import Dropzone from 'react-dropzone'
import { editComment } from './../../../../api/post';
import imageCompression from 'browser-image-compression';
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
class TextArea extends Component{
    constructor(props){
        super(props);
        this.state = {
            text: '',
            files: [],
            image_link: '',
            removeImgEditMode: false,
        }
        this.keyPress = this.keyPress.bind(this);
        this.onChange = this.onChange.bind(this);
        this.onDrop = this.onDrop.bind(this);
        this.removeImg = this.removeImg.bind(this);
        this.removeImgInEditMode = this.removeImgInEditMode.bind(this);
    }
    componentDidUpdate(prevProps){
        if(prevProps.editMode != this.props.editMode || prevProps.comment1 != this.props.comment1){
            const { editMode, comment1 } = this.props;
            if(editMode){
                if(comment1.comment_text){
                    this.setState({
                        text: comment1.comment_text,
                    })
                }
                if(comment1.images && comment1.images[0]){
                    this.setState({
                        image_link: comment1.images[0].image_link,
                    })
                }
            }
        }
    }
    keyPress(event){
        if(event.keyCode == 13){
            event.preventDefault();
            let { post, logInUser, type, editMode, comment1, offEditMode } = this.props;
            let { removeImgEditMode } = this.state;
            let thread_room = '';
            let comment = {};
            comment.parent_id = null;
            comment.user_id = logInUser.user_id;
            comment.post_id = post.id;
            comment.is_shared = post.is_shared;
            comment.target_id = post.user_id;
            comment.user_name = logInUser.fname+' '+logInUser.lname;
            comment.comment_text = this.state.text;
            comment.shared_link = '';
            comment.is_gif = false;
            comment.gif_link = null;
            if(type == 0){
                thread_room = 'post:room:';
                comment.post_id = null;
                comment.article_id = post.id;
            }else {
                thread_room = 'post:room:';
                comment.post_id = post.id;
                comment.article_id = null;
            }
            comment.thread_room = thread_room;
            comment.comment_images = -1;
            if(this.state.files.length > 0){
                comment.comment_images = this.state.files;
            }
            if(this.state.files.length > 0 || this.state.text != ''){
                if(!editMode){
                    const formData = objectToFormData(comment);
                    saveComment(formData);
                }else{
                    comment.removeImage = -1;
                    if(removeImgEditMode){
                        if(comment1.images && comment1.images[0]){
                            comment.removeImage = comment1.images[0].id;
                        }
                    }
                    else if(this.state.files.length > 0){
                        if(comment1.images && comment1.images[0]){
                            comment.removeImage = comment1.images[0].id;
                        }
                    }
                    comment.comment_id = comment1.id;
                    const formData = objectToFormData(comment);
                    offEditMode();
                    editComment(formData);
                }
                this.setState({
                    text: '',
                    files: [],
                    image_link: '',
                    removeImgEditMode: false,
                })
            }
        }
    }
    onChange(event){
        this.setState({
            text: event.target.value,
        })
    }
    componentWillUnmount() {
        this.state.files.forEach(file => URL.revokeObjectURL(file.preview))
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
        let { logInUser } = this.props;
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
          backgroundImage: (logInUser && logInUser.profile_img_loc) ? 'url("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'+logInUser.profile_img_loc+'")' : (logInUser && logInUser.fname) ? 'url(/social/images/Avatar_Letters/'+logInUser.fname.charAt(0).toUpperCase()+'.svg)' : 'url(/social/images/Avatar_Letters/P.svg)',
        }
        return(
            <div className="post-comment-area mobile-comment-bottom">
                <div className="comment-image">
                    <div className="comment-user-img" style={imgStyles} />
                </div>
                <div className="comment-content">
                {!!logInUser.userAccountSettings && !!logInUser.userAccountSettings.is_incognito ?
                    <div className="incognito-comment">
                        <img src='/social/images/settings/active_options/noun_Ghost_367889_000000.png'/>
                        <Link className="incognito-link" to={'/social/settings'}> Turn off Incognito Mode to make a comment </Link>
                    </div>
                    : (logInUser.userAccountSettings === null || !!logInUser.userAccountSettings && !logInUser.userAccountSettings.is_incognito) &&
                    <div>
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
            </div>
        )
    }
}
export default TextArea;
