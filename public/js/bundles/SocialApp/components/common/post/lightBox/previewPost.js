import React, { Component } from 'react'
import RightPostion from './rightPortion'
import LeftPortion from './leftPostion'
class PreviewPost extends Component{
    render(){
        let { images, post, handleMobileCommentd, logInUser, showDesktopComment, desktopComment, closeModal } = this.props;
        return(
            <div className="preview_post">
                <div className="cross-button" onClick={closeModal}>
                  &#10005;
                </div>
                <LeftPortion images={images}/>
                <RightPostion post={post} handleMobileCommentd={handleMobileCommentd} logInUser={logInUser} showDesktopComment={showDesktopComment} desktopComment={desktopComment}/>
            </div>
        )
    }
}
export default PreviewPost;
