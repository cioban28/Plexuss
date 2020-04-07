import React, { Component } from 'react'
import ImageUpload from './imageUpload'
import './coverImage.scss'
class ArticleImage extends Component{
    render(){
        let { removeImage, onDrop, onDropRejected, files } = this.props; 
        return(
            <div className="article_image">
                <div className="title">Article Image</div>
                <ImageUpload removeImage={removeImage} onDrop={onDrop} onDropRejected={onDropRejected} files={files}/>
            </div>
        )
    }
}
export default ArticleImage;