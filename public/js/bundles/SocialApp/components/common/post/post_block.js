import React, { Component } from 'react'
import axios from 'axios';
import ImagesGallery from './images_gallery'
import GifPlayer from 'react-gif-player';
import { Link } from 'react-router-dom';
import Parser from 'html-react-parser';
import './article.scss'
class PostBlock extends Component{
  is_mount = false;
  constructor(props) {
    super(props);
    this.state = {
      linkContent: {},
    };

    this.strip_html_tags = this.strip_html_tags.bind(this)

  }

  componentDidMount(){
    this.is_mount = true;
    let { post } = this.props;
    if(post && post.shared_link){
      axios({
        method: 'get',
        url: '/social/link-preview-info?url='+post.shared_link,
      })
      .then(res => {
        if(this.is_mount)
          this.setState({
            linkContent: res.data,
          })
      })
      .catch(error => {
      })
    }
    else{
      this.setState({
        linkContent: {},
      })
    }
  }

  componentWillUnmount() {
    this.is_mount = false;
  }

  strip_html_tags(str)
  {
     if ((str===null) || (str===''))
         return false;
    else
     str = str.toString();
    return str.replace(/<[^>]*>/g, '');
  }
  
  render(){
      let { post, sharedPostText, handleMobileCommentd, logInUser, showDesktopComment, desktopComment } = this.props;
      let {linkContent} = this.state;
      return(
          <div className="post-block">
              {
                  sharedPostText &&
                  <div className="post-title">{sharedPostText}</div>
              }
              {
                post && post.post_text &&
                <div className="post-title">{post.post_text}</div>
              }
              {
                  post && post.shared_link && linkContent &&
                  <div className="shared-link-content" onClick={() => window.open(linkContent.url, '_blank')}>
                      {/* <div className="post-link">
                          <a href={linkContent.url} target="_blank">LINK</a>
                      </div> */}
                      { !!linkContent.embed_code && linkContent.embed_code ?
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
                post && post.article_title &&
                <div className="shared-link-content __content1">
                  <Link to={{pathname: "/social/article/"+post.id, state: { prevPath: 'home' }}}>
                    {
                      post.images && post.images.length >= 1 && 
                      <img src={post.images[0].image_link} className="link-image" />
                    }
                    <div className="link-info">
                      <div>
                        <div className="link-title">{post.article_title}</div>
                        <div className="link-description line_limit">{this.strip_html_tags(post.article_text ? post.article_text : '')}</div>
                        <div className="shared-url">PLEXUSS ARTICLE</div>
                      </div>
                    </div>
                  </Link>
                </div>
              }
              {
                  (post && !post.article_title && post.images && post.images.length >= 1 && !post.images[post.images.length-1].gif_link) &&
                      <ImagesGallery images={post.images} post={post} handleMobileCommentd={handleMobileCommentd} logInUser={logInUser} showDesktopComment={showDesktopComment} desktopComment={desktopComment}/>||
                  (post && post.images && post.images.length >= 1 && post.images[post.images.length-1].gif_link) &&
                      <div className="giph_parent">
                          <GifPlayer gif={post.images[post.images.length-1].gif_link} still={post.images[post.images.length-1].image_link} pauseRef={pause => this.pauseGif = pause} autoplay={false}/>
                      </div>
              }
              <div className="post-text">
                  { post && post.uploadImageTitle && <a className="post-sub-title" href="#">{post.uploadImageTitle}</a> }
                  { post && post.uploadImageDescription &&  <p>{post.uploadImageDescription}</p>}
              </div>
          </div>
      )
  }
}
export default PostBlock;
