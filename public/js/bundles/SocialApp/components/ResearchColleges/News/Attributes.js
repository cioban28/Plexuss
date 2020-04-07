import React, { Component } from 'react'
import { Link, withRouter } from 'react-router-dom'
import LikeArticle from './LikeArticle'
class Attributes extends Component{
    render(){
        let { singleNews, history } = this.props;
        return(
            <div className="attributes_banner">
                <span className="back" onClick={() => history.goBack()}>
                    <i className="fa fa-arrow-left"></i>
                    <span className="text">Back</span>
                </span>
                <div className="attributes">
                    <LikeArticle/>
                    {
                        singleNews &&
                        <div className="attribute">
                            <div className="image_banner">
                                <img className='share_img' src='/social/images/noun_share.png' alt=""/>
                            </div>
                            <div className="attribute_count">
                                {'1'}
                            </div>
                            <div className="attribute_name">
                                {'Share'}
                            </div>
                        </div>
                    }
                </div>
            </div>
        )
    }
}
export default withRouter(Attributes);
