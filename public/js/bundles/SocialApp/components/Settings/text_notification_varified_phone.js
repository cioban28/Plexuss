import React , { Component } from 'react'
import Header from './content_header'
import Notification_switch from './notification_switch'
import SubNotifications from './subnotification_switch'
import { saveEmailNotifications } from './../../api/user'
import { connect } from 'react-redux'

class TextNotificationVarifiedPhone extends Component{
    constructor(props) {
        super(props);
        this.state = {
            schoolNotification: false,
            plexussNotification: false,
            openSchoolNotificationDetaile: false,
            openPlexussNotificationDetaile: false,

            schoolRecruitYou: false,
            schoolViewedYou: false,
            sentAMessage: false,
            handshakes: false,
            stateUpdate: false,
            newsUpdate: false,
            otherUpdate: false,

            pending: false,
        };
        this.handleSchoolNotification = this.handleSchoolNotification.bind(this);
        this.hanldlePlexussNotification = this.hanldlePlexussNotification.bind(this);
        this.handleSchoolNotificationDetail = this.handleSchoolNotificationDetail.bind(this);
        this.hanldlePlexussNotificationDetail = this.hanldlePlexussNotificationDetail.bind(this);

        this.handleSchoolRecruitYou = this.handleSchoolRecruitYou.bind(this);
        this.handleSchoolViewedYou = this.handleSchoolViewedYou.bind(this);
        this.handleSentAMessage = this.handleSentAMessage.bind(this);
        this.handleHandshakes = this.handleHandshakes.bind(this);
        this.handleStateUpdate = this.handleStateUpdate.bind(this);
        this.handleNewsUpdate = this.handleNewsUpdate.bind(this);
        this.handleOtherUpdate = this.handleOtherUpdate.bind(this);

        this.updateComponent = this.updateComponent.bind(this);
        this.checkSubNotifications = this.checkSubNotifications.bind(this);
        this.onSave = this.onSave.bind(this);

    }
    componentDidMount(){
        this.updateComponent();
    }
    componentDidUpdate(prevProps){
        if(this.props.setting_notification != prevProps.setting_notification){
            this.updateComponent();
            this.setState({pending: false})
        }
    }
    checkSubNotifications(){
        if(this.state.schoolRecruitYou && this.state.schoolViewedYou && this.state.sentAMessage && this.state.handshakes ){
            this.setState({
                schoolNotification: true,
            })
        }
        else if(!this.state.schoolRecruitYou || !this.state.schoolViewedYou || !this.state.sentAMessage || !this.state.handshakes ){
            this.setState({
                schoolNotification: false,
            })
        }
        if(this.state.stateUpdate && this.state.newsUpdate && this.state.otherUpdate){
            this.setState({
                plexussNotification: true,
            })
        }
        else if(!this.state.stateUpdate || !this.state.newsUpdate || !this.state.otherUpdate){
            this.setState({
                plexussNotification: false,
            })
        }
    }


    updateComponent(){
        let { setting_notification } = this.props;
        if(setting_notification && setting_notification.text && setting_notification.text.all_school_notifications){
            this.setState({
                schoolNotification: setting_notification.text.all_school_notifications,
                schoolRecruitYou: true,
                schoolViewedYou: true,
                sentAMessage: true,
                handshakes: true,
            })
        }
        else if(setting_notification && setting_notification.text){
            this.setState({
                schoolRecruitYou: setting_notification.text.wants_to_recruit_you === undefined ? true : setting_notification.text.wants_to_recruit_you,
                schoolViewedYou: setting_notification.text.viewed_your_profile === undefined ? true : setting_notification.text.viewed_your_profile,
                sentAMessage: setting_notification.text.sent_you_a_message === undefined ? true : setting_notification.text.sent_you_a_message,
                handshakes: setting_notification.text.handshakes_with_colleges === undefined ? true : setting_notification.text.handshakes_with_colleges,
            })
        }

        if(setting_notification && setting_notification.text && setting_notification.text.all_plexuss_notifications){
            this.setState({
                plexussNotification: setting_notification.text.all_plexuss_notifications,
                stateUpdate: true,
                newsUpdate: true,
                otherUpdate: true,
            })
        }
        else if(setting_notification && setting_notification.text){
            this.setState({
                stateUpdate: setting_notification.text.college_stats_updates === undefined ? true : setting_notification.text.college_stats_updates,
                newsUpdate: setting_notification.text.college_news_updates === undefined ? true : setting_notification.text.college_news_updates,
                otherUpdate:  setting_notification.text.other_plexuss_notifications === undefined ? true : setting_notification.text.other_plexuss_notifications,
            })
        }
    }

    handleSchoolNotification(checked){
        this.setState({
            schoolNotification: checked,
            schoolRecruitYou: checked,
            schoolViewedYou: checked,
            sentAMessage: checked,
            handshakes: checked,
        })
    }
    hanldlePlexussNotification(checked){
        this.setState({
            plexussNotification: checked,
            stateUpdate: checked,
            newsUpdate: checked,
            otherUpdate: checked,
        })
    }
    handleSchoolNotificationDetail(){
        this.setState({
            openSchoolNotificationDetaile: !this.state.openSchoolNotificationDetaile
        })
    }
    hanldlePlexussNotificationDetail(){
        this.setState({
            openPlexussNotificationDetaile: !this.state.openPlexussNotificationDetaile,
        })
    }

    handleSchoolRecruitYou(checked){
        this.setState({schoolRecruitYou: checked},() =>{
            this.checkSubNotifications();
        })
    }
    handleSchoolViewedYou(checked){
        this.setState({schoolViewedYou: checked}, () =>{
            this.checkSubNotifications();
        })
    }
    handleSentAMessage(checked){
        this.setState({sentAMessage: checked},()=>{
            this.checkSubNotifications();
        })
    }
    handleHandshakes(checked){
        this.setState({handshakes: checked},()=>{
            this.checkSubNotifications();
        })
    }
    handleStateUpdate(checked){
        this.setState({stateUpdate: checked},()=>{
            this.checkSubNotifications();
        })
    }
    handleNewsUpdate(checked){
        this.setState({newsUpdate: checked},()=>{
            this.checkSubNotifications();
        })
    }
    handleOtherUpdate(checked){
        this.setState({otherUpdate: checked},()=>{
            this.checkSubNotifications();
        })
    }
    onSave(){
        let data = {};
        this.setState({pending: true})
        data.other_plexuss_notifications = this.state.otherUpdate;
        data.college_news_updates = this.state.newsUpdate;
        data.college_stats_updates = this.state.stateUpdate;
        data.handshakes_with_colleges = this.state.handshakes;
        data.sent_you_a_message = this.state.sentAMessage;
        data.type = "text";
        data.viewed_your_profile  = this.state.schoolViewedYou;
        data.wants_to_recruit_you = this.state.schoolRecruitYou;
        // data.all_school_notifications = this.state.schoolNotification;
        // data.all_plexuss_notifications = this.state.plexussNotification;
        saveEmailNotifications(data);
    }
    render(){
        let { phone } = this.props;
        return(
            <div className="large-9 medium-9 small-12 upper_container columns">
                <div className="setting_content_container">
                    <Header imgSrc={'/social/images/settings/active_options/Subtraction 19.png'} title={'TEXT NOTIFICATIONS'} imgClass={'text_msg'} backClickHandler={this.props.backClickHandler} />
                    <div className="content_container">
                        <div className="explanation">
                            { phone }
                        </div>
                        <Notification_switch text={'School Notifications'} id={'icon-switch1'} htmlFor={'normal-switch1'} checked={this.state.schoolNotification} handleChange={this.handleSchoolNotification} handleDetail={this.handleSchoolNotificationDetail} subOpen={this.state.openSchoolNotificationDetaile}/>
                        {
                            this.state.openSchoolNotificationDetaile &&
                            <span>
                                <SubNotifications text={'A school wants to recruit you'} id={'school-switch1'} htmlFor={'normal-switch3'} checked={this.state.schoolRecruitYou} handleChange={this.handleSchoolRecruitYou}/>
                                <SubNotifications text={'Schools viewed your Profile'} id={'school-switch2'} htmlFor={'normal-switch4'} checked={this.state.schoolViewedYou} handleChange={this.handleSchoolViewedYou}/>
                                <SubNotifications text={'A school messaged you'} id={'school-switch3'} htmlFor={'normal-switch5'} checked={this.state.sentAMessage} handleChange={this.handleSentAMessage}/>
                                <SubNotifications text={'Handshakes with colleges'} id={'school-switch4'} htmlFor={'normal-switch6'} checked={this.state.handshakes} handleChange={this.handleHandshakes}/>
                            </span>
                        }
                        <Notification_switch text={'Plexuss Notficiations'} id={'icon-switch2'} htmlFor={'normal-switch2'} checked={this.state.plexussNotification} handleChange={this.hanldlePlexussNotification} handleDetail={this.hanldlePlexussNotificationDetail} subOpen={this.state.openPlexussNotificationDetaile}/>
                        {
                            this.state.openPlexussNotificationDetaile &&
                            <span>
                                <SubNotifications text={'College Stats Updates'} id={'plexuss-switch1'} htmlFor={'normal-switch7'} checked={this.state.stateUpdate} handleChange={this.handleStateUpdate}/>
                                <SubNotifications text={'College News Updates'} id={'plexuss-switch1'} htmlFor={'normal-switch8'} checked={this.state.newsUpdate} handleChange={this.handleNewsUpdate}/>
                                <SubNotifications text={'Other Plexuss Notifications'} id={'plexuss-switch1'} htmlFor={'normal-switch9'} checked={this.state.otherUpdate} handleChange={this.handleOtherUpdate}/>
                            </span>
                        }
                        <div className={"save_settings_btn " + (this.state.pending && 'disabled')} onClick={this.onSave}> {this.state.pending ? 'Saving' : 'Save Settings'} </div>
                    </div>
                </div>
            </div>
        )
    }
}
const mapStateToProps = (state) =>{
    return{
        setting_notification: state.setting.setting.setting_notification,
    }
}
export default connect(mapStateToProps, null)(TextNotificationVarifiedPhone)
