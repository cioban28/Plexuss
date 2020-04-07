import React , { Component } from 'react'
import { savePublicProfileSettings } from './../../actions/Profile'

class Profile_edit_privacy_settings extends Component{
    constructor(props){
        super(props);
        this.state={
            flag: false,
            profileStatus: 'public',
        }
        this.handleProfileSetting = this.handleProfileSetting.bind(this);
        this.togglePrivacyDropDown = this.togglePrivacyDropDown.bind(this);
        this.closePrivacyDropDown = this.closePrivacyDropDown.bind(this);
    }
    componentDidMount(){
        let { initial } = this.props;
        if(!!initial){
            switch (initial){
                case 2 : this.setState({profileStatus: 'connections'}); break;
                case 3 : this.setState({profileStatus: 'onlyMe'}); break;
                default : this.setState({profileStatus: 'public'}); break;
            }
        }else {
            this.setState({profileStatus: 'public'})
        }
    }
    handleProfileSetting(status){
        this.setState({
            profileStatus: status,
        })
        let obj = {
            section: this.props.section,
            share_with_id: status === 'onlyMe' ? 3 : status === 'connections' ? 2 : 1
        }
        savePublicProfileSettings(obj);
    }
    togglePrivacyDropDown(){
        this.setState({
            flag: !this.state.flag,
        })
    }
    closePrivacyDropDown(){
        this.setState({
            flag: false,
        })
    }
    render(){
        return(
            <span className="profile_edit_privacy_setting">
                <span className={"privacy_setting cursor_pointer " + (this.state.flag ? 'selected_privacy_setting' : '')} onClick={() => this.togglePrivacyDropDown()}>

                    <span className="privacy_icon">
                        {
                            this.state.profileStatus === 'onlyMe' ?
                                <img src="/social/images/Icons/privacy-lock.svg"/>
                                : this.state.profileStatus === 'connections' ?
                                <img src="/social/images/Icons/tab-network-inactive.svg"/>
                                :
                                <img src="/social/images/Icons/privacy-public.svg"/>
                        }
                    </span>
                    <span className="carat_down_conatiner"><i className=" fa fa-caret-down"></i></span>
                </span>
                {
                    this.state.flag && 
                    <div className="privacy_setting_dropdown">
                        <div className="setting cursor_pointer public" onClick={() => {this.closePrivacyDropDown(); this.handleProfileSetting('public')}}>
                            <i className={"fa fa-check "+(this.state.profileStatus === "public" ? 'check-mark active-check' : 'check-mark')} />
                            <img src="/social/images/Icons/privacy-public.svg"/>
                            Public
                        </div>
                        <div className="setting cursor_pointer connections" onClick={() => {this.closePrivacyDropDown(); this.handleProfileSetting('connections')}}>
                            <i className={"fa fa-check "+(this.state.profileStatus === "connections" ? 'check-mark active-check' : 'check-mark')} />
                            <img src="/social/images/Icons/tab-network-inactive.svg"/>
                            My Connections Only
                        </div>
                        <div className="setting cursor_pointer only-me" onClick={() => {this.closePrivacyDropDown(); this.handleProfileSetting('onlyMe')}}>
                            <i className={"fa fa-check "+(this.state.profileStatus === "onlyMe" ? 'check-mark active-check' : 'check-mark')} />
                            <img src="/social/images/Icons/privacy-lock.svg"/>
                            Only Me & Colleges
                        </div>
                    </div>
                }
            </span>
        )
    }
}
export default Profile_edit_privacy_settings;