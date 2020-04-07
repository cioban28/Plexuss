import React, { Component } from 'react'
class Options extends Component {
    render(){
        let { render_component, hanldeComponent } = this.props;
        return(
            <div className="large-3 medium-3 small-12 columns ">
                <div className="setting_options_container">
                    <div className="header">
                        SETTINGS
                    </div>
                    <div className="mbl-color">
                        <SettingOption imgSrc={'/social/images/settings/options/Subtraction 16.png'} activeImgSrc={'/social/images/settings/active_options/Subtraction 18.png'} title={'Change Password'} imgClass={'lock'} render_component={render_component} hanldeComponent={hanldeComponent} passText={'change password'} />
                        <SettingOption imgSrc={'/social/images/settings/options/envelope.png'} activeImgSrc={'/social/images/settings/active_options/envelope.png'} title={'Email Notifications'} imgClass={'email_notification'} render_component={render_component} hanldeComponent={hanldeComponent} passText={'email notifications'} />
                        <SettingOption imgSrc={'/social/images/settings/options/Subtraction 17.png'} activeImgSrc={'/social/images/settings/active_options/Subtraction 19.png'} title={'Text Notifications'} imgClass={'text_msg'} render_component={render_component} hanldeComponent={hanldeComponent} passText={'text notifications'} />
                        <SettingOption imgSrc={'/social/images/settings/options/noun_Privacy_1039048_000000.png'} activeImgSrc={'/social/images/settings/active_options/noun_Privacy_1039048_000000.png'} title={'Privacy'} imgClass={'privacy'} render_component={render_component} hanldeComponent={hanldeComponent} passText={'privacy'} />
                    </div>
                    {/* <SettingOption imgSrc={'/social/images/settings/options/noun_invitation.svg'} activeImgSrc={'/social/images/settings/active_options/noun_invitation_58165_000000.png'} title={'Invite Friends'} imgClass={'invite_friends'} render_component={render_component} hanldeComponent={hanldeComponent} passText={'invite friends'} /> */}
                </div>
            </div>
        )
    }
}
class SettingOption extends Component {
    render(){
        let { imgSrc ,activeImgSrc, title, imgClass, render_component, hanldeComponent, passText } = this.props;
        return(
            <div className={"row settings_options " + (render_component == passText ? 'active_settings_options' : '')} onClick={() => hanldeComponent(passText) }>
                <div className="large-3 medium-3 small-2 option_icon">
                    <img className={imgClass} src={ (render_component == passText ? activeImgSrc : imgSrc)}/>
                </div>
                <div className="large-9 medium-9 columns">
                    {title}
                </div>
                <div className="show-for-small-only">
                    <span className="right-arrow">
                        <i className="fa fa-angle-right" />
                    </span>
                </div>
            </div>
        )
    }
}
export default Options