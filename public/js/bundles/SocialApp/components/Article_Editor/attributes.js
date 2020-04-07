import React, { Component } from 'react'
import './attributes.scss'
class Attributes extends Component{
    render(){
        let { likes, comments, views } = this.props;
        return(
            <div className="attributes_banner">
                <div className="back-to-dashboard">
                    <button>
                        <a href="/social/article-dashboard">BACK TO DASHBOARD</a>
                    </button>
                </div>
                <div className="attributes">
                    <Arrtibute count={views} imgSrc={'/social/images/article/Group977/Group 977@2x.png'} imgClass={'img_banner_views'}/>
                    <Arrtibute count={likes} imgSrc={'/social/images/heart-icon.png'} imgClass={'img_banner'}/>
                    <Arrtibute count={comments} imgSrc={'/social/images/noun_comment.png'} imgClass={'img_banner'}/>
                    <Arrtibute count={0} imgSrc={'/social/images/noun_share.png'} imgClass={'img_banner'}/>
                </div>
            </div>
        )
    }
}
function Arrtibute(props){
    let { count, imgSrc, imgClass} = props;
    return(
        <div className="attribute">
            <div className={imgClass}>
              <img src={imgSrc} alt="" />
            </div>
            <div className='count'>{count}</div>
        </div>
    )
}
export default Attributes;