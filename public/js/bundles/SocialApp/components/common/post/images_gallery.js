import React, { Component } from 'react'
import './imageGallery.scss'
import PreviewModal from './lightBox/index'
import { Link } from 'react-router-dom';
class ImagesGallery extends Component{
    constructor(props){
        super(props);
        this.state={
            openModal: false,
            images: [],
            topScrollPosition: 0,
        }
        this.handleModal = this.handleModal.bind(this);
    }
    componentWillReceiveProps(nextProps){
        if(nextProps.images){
            const { images } = this.props;
            if(images){
                let newImages = [];
                images.map(image=>{
                    if(image.post_comment_id == null){
                        newImages.push(image);
                    }
                })
                this.setState({images: newImages})
            }
        }
    }
    handleModal(){
        this.setState({
            openModal: !this.state.openModal,
        })
    }
    render(){
        let {  post, handleMobileCommentd, logInUser, showDesktopComment, desktopComment } = this.props;
        const { images } = this.state;
        return(
            <span>
                {
                    post.hasOwnProperty('post_text') &&
                    <div className="post-image1" onClick={this.handleModal}>
                        {
                            images[0] && images[0].image_link &&
                            <img src={images[0].image_link} alt=""/>
                        }
                        {
                            images.length > 1 &&
                                <div className="images_count">{'+'}{images.length-1}</div>
                        }
                    </div>
                }
                {
                    post.hasOwnProperty('article_title') &&  images[0] && images[0].image_link &&
                    <Link to={"/social/article/"+post.id} className="post-image1">
                        <img src={images[0].image_link} alt=""/>
                    </Link>
                }
                {
                    this.state.openModal &&
                        <div className="preview_modal">
                            <PreviewModal handleModal={this.handleModal} openModal={this.state.openModal} images={images} post={post} handleMobileCommentd={handleMobileCommentd} logInUser={logInUser} showDesktopComment={showDesktopComment} desktopComment={desktopComment}/>
                        </div>
                }
            </span>
        )
    }
}
export default ImagesGallery;
