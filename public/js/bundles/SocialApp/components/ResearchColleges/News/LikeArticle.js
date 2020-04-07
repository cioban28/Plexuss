import React, { Component } from 'react'
class LikeArticle extends Component{
    constructor(props){
        super(props);
        this.state = {
            likeArticle : false,
        }
    }
    render(){
        return(
            <div className="attribute" >
                <div className="image_banner">
                    <img className="like" src={this.state.likeArticle ? '/social/images/Icons/Heart-Outline-filled@2x.png' : '/social/images/heart-icon.png'} alt=""/>
                </div>
                <div className="attribute_count">
                    1
                </div>
                <div className="attribute_name">
                    {'Likes'}
                </div>
            </div>
        )
    }
}
export default LikeArticle;