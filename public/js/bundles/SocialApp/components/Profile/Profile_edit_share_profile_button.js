import React, { Component } from 'react';
import {
    FacebookShareButton,
    TwitterShareButton,
    LinkedinShareButton,

    FacebookIcon,
    TwitterIcon,
    LinkedinIcon,
  } from 'react-share';

class Profile_edit_share_profile_button extends Component{
    constructor(props){
        super(props);
        this.state={
            shareUrl: window.location.href,
        }
    }
    render(){
        return(
            <div className="share_profile_incognito_banner">
                <div className="social_media_icons_container">
                    <div className="social_media_icons">
                        <span className="share_url_banner">
                            {this.state.shareUrl}
                        </span>
                        <span className="share_on_social_tag">
                            Share on Social
                        </span>
                        <FacebookShareButton
                            url={'https://facebook.com'}
                            quote={this.state.shareUrl}
                            tabIndex="none"
                            className="socila_btn">
                            <FacebookIcon
                            size={32}
                            round />
                        </FacebookShareButton>
                        <TwitterShareButton
                            url={'https:'}
                            title={this.state.shareUrl}
                            tabIndex="none"
                            className="socila_btn">
                            <TwitterIcon
                            size={32}
                            round />
                        </TwitterShareButton>
                        <LinkedinShareButton
                            url={'https://linkedin.com'}
                            title={this.state.shareUrl}
                            tabIndex="none"
                            className="socila_btn">
                            <LinkedinIcon
                            size={32}
                            round />
                        </LinkedinShareButton>
                    </div>
                </div>
            </div>
        )
    }
}
export default Profile_edit_share_profile_button;
