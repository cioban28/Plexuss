import React, { Component } from 'react'
import { Link } from 'react-router-dom'
import axios from 'axios';
import TimeAgo from 'react-timeago'
import moment from 'moment'
import Parser from 'html-react-parser';
import GifPlayer from 'react-gif-player';
import ImagesGallery from './images_gallery'
import { AttachmentUnavailable } from './helper'
import './article.scss'
class SharedPostContent extends Component{
    is_mount = false
    constructor(props) {
        super(props);
        this.state = {
          linkContent: {},
        };
    }

    componentDidMount() {
      this.is_mount = true
    }

    componentWillUnmount() {
      this.is_mount = false
    }

    componentWillReceiveProps(nextProps){
      const { post } = nextProps;
      if(post && post.shared_link){
        axios({
          method: 'get',
          url: '/social/link-preview-info?url='+post.shared_link,
        })
        .then(res => {
          if (this.is_mount)
            this.setState({
              linkContent: res.data,
            })
        })
        .catch(error => {
        })
      }
      else{
        if (this.is_mount)
          this.setState({
            linkContent: {},
          })
      }
    }

    render(){
        let { post, sharedPostText, handleMobileCommentd, logInUser, showDesktopComment, desktopComment, deletedPost } = this.props;
        let user = post && post.user;
        const { linkContent } = this.state;
        let imgStyles = {
          backgroundImage: (user && user.profile_img_loc) ? 'url("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'+user.profile_img_loc+'")' : (user && user.fname) ? 'url(/social/images/Avatar_Letters/'+user.fname.charAt(0).toUpperCase()+'.svg)' : 'url(/social/images/Avatar_Letters/P.svg)',
        }
        return(
            <div className="post-block">
            {
             post && !deletedPost &&
             <div className="shared-content">
                 { sharedPostText != '' && !!post && (!!post.post_text) && post.post_text && sharedPostText && <div className="post-title">{sharedPostText}</div> }
                 { sharedPostText != '' && !!post && !!post.article_title && post.article_title && sharedPostText && <div className="post-title">{sharedPostText}</div> }
                 {
                     (!!post && !!post.images && post.images && post.images.length >= 1 && !post.images[post.images.length-1].gif_link) &&
                         <ImagesGallery images={post.images} post={post} handleMobileCommentd={handleMobileCommentd} logInUser={logInUser} showDesktopComment={showDesktopComment} desktopComment={desktopComment}/>||
                     (!!post && !!post.images && post.images && post.images.length >= 1 && post.images[post.images.length-1].gif_link) &&
                         <div className="giph_parent">
                             <GifPlayer gif={post.images[post.images.length-1].gif_link} still={post.images[post.images.length-1].image_link} pauseRef={pause => this.pauseGif = pause} autoplay={true}/>
                         </div>
                 }
                 <div className="shared-post-user-info">
                     <div className='shared-post-header'>
                         <div className='user-info'>
                             <Link className="user-img" to={'/social/profile/'+(user && user.id)}>
                                 <div className="user-img-inner" style={imgStyles}/>
                             </Link>
                             <Link className="user-name" to={'/social/profile/'+(user && user.id)}>
                                 {user && user.fname} {user && user.lname}
                                 {/* <div className="user-role">{user && user.is_student === 1 ? 'Student' : ''}</div> */}
                             </Link>
                         </div>

                         <div className="post-date"><TimeAgo date={!!post && moment.utc(post.created_at).local()} /></div>
                     </div>
                     { !!post && (!!post.post_text) &&
                         <div className="shared-post-text">
                             {post.post_text || post.article_title}
                         </div>
                     }
                     {
                         post && post.shared_link && linkContent &&
                         <div className="shared-link-content" onClick={() => window.open(linkContent.url, '_blank')}>
                             { !!linkContent.embed_code ?
                               <div className="link-preview-video" dangerouslySetInnerHTML={{__html : linkContent.embed_code}} />
                               :
                               linkContent.image &&
                               <img src={`${linkContent.image}`} className="link-image" />
                             }
                             <div className="link-info">
                               <div className="link-title">{linkContent.title}</div>
                               <div className="link-description">{linkContent.description}</div>
                               <a href={linkContent.url} className="shared-url" target="_blank">{linkContent.url}</a>
                             </div>
                         </div>
                     }
                     {
                         post && !!post.article_title && post.hasOwnProperty('article_title') &&
                         <div className="link-info">
                             <Link to={"/social/article/"+post.id}>
                                 <div className="link-title">{post.article_title}</div>
                                 <div className="link-description">{post.article_text ? Parser(post.article_text) : ''}</div>
                                 <div className="shared-url">PLEXUSS ARTICLE</div>
                             </Link>
                         </div>
                     }
                 </div>
             </div>
            }
            {
              deletedPost &&
              <AttachmentUnavailable />
            }
            </div>
        )
    }
}
export default SharedPostContent;
