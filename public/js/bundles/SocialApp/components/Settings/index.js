import React, { Component } from 'react'
import Options from './options'
import Change_password from './change_password'
import Email_notifications from './email_notifications'
import TextNotification from './text_notification'
import Privacy from './privacy'
import InviteFriends from './invite_friends'
import './styles.scss'
import { getSettingData, getStoredContacts } from './../../api/user'
import { Helmet } from 'react-helmet';

class Setting extends Component{
    constructor(props){
        super(props);
        this.state={
            render_component: 'options'
        }
        this.hanldeComponent = this.hanldeComponent.bind(this);
    }
    componentDidMount(){
        getSettingData();
        getStoredContacts();
    }
    hanldeComponent(renderComponent){
        this.setState({
            render_component: renderComponent,
        })
    }
    backClickHandler = () => {
        this.setState({render_component: 'options'})
    }
    render(){
        return(
            <div>
                <Helmet>
                  <title>College Profile Settings | College Recruiting Academic Network | Plexuss.com</title>
                  <meta name="description" content="Customize your Plexuss profile here." />
                  <meta name="keywords" content="User Profile Settings" />
                </Helmet>
                <span className="settings-web-view">
                    <div className="setting_banner_top">
                        <div className="setting_banner row">
                            <Options render_component={this.state.render_component} hanldeComponent={this.hanldeComponent}/>
                            {
                                (this.state.render_component === "change password" || this.state.render_component === "options" )&& <Change_password /> ||
                                this.state.render_component === "email notifications" && <Email_notifications /> ||
                                this.state.render_component === "text notifications" && <TextNotification /> ||
                                this.state.render_component === "privacy" && <Privacy /> ||
                                this.state.render_component === "invite friends" && <InviteFriends />
                            }
                        </div>
                    </div>
                </span>
                <span className="settings-mbl-view">
                     <div className="setting_banner_top">
                        <div className="setting_banner row">
                            {
                                this.state.render_component === "options" && <Options render_component={this.state.render_component} hanldeComponent={this.hanldeComponent} /> ||
                                this.state.render_component === "change password" && <Change_password backClickHandler={this.backClickHandler} /> ||
                                this.state.render_component === "email notifications" && <Email_notifications backClickHandler={this.backClickHandler} /> ||
                                this.state.render_component === "text notifications" && <TextNotification backClickHandler={this.backClickHandler} /> ||
                                this.state.render_component === "privacy" && <Privacy backClickHandler={this.backClickHandler} /> ||
                                this.state.render_component === "invite friends" && <InviteFriends backClickHandler={this.backClickHandler} />
                            }
                        </div>
                    </div>
                </span>
            </div>
        )
    }
}

export default Setting;