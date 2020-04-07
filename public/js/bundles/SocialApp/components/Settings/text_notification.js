import React, { Component } from 'react'
import Header from './content_header'
import ReactPhoneInput from 'react-phone-input-2'

import TextNotificationVarifiedPhone from './text_notification_varified_phone';
import PhoneVarification from './phone_varification'
import { connect } from 'react-redux'
import { savePhoneNumber, sendCode } from './../../api/user'
class TextNotification extends Component{
    constructor(props){
        super(props);
        this.state={
            checked: true,
            phone: '+1',
            confirmation: false,
            verifiedPhone: 0,
        }
        this.handleCheck = this.handleCheck.bind(this);
        this.handleConfirmation = this.handleConfirmation.bind(this);
        this.onNext = this.onNext.bind(this);
        this.sendCode = this.sendCode.bind(this);
        this.handlePhoneNumber = this.handlePhoneNumber.bind(this);
    }

    handleCheck(){
        this.setState({
            checked: !this.state.checked,
        })
    }
    componentDidMount(){
        this.setState({
            phone: this.props.phone,
            verifiedPhone: this.props.verifiedPhone,
        })
    }
    componentDidUpdate(prevProps){
        if(prevProps != this.props){
            let { savePhone, savePhoneCount , verifiedPhone} = this.props;
            if(prevProps.phone != this.props.phone){
                this.setState({
                    phone: this.props.phone,
                })
            }
            if( savePhone == "success" && prevProps.savePhoneCount != savePhoneCount){
                this.setState({
                    confirmation: !this.state.confirmation,
                })
            }
            this.setState({
                verifiedPhone: verifiedPhone,
            })
        }
    }
    handleConfirmation(){
        this.setState({
            confirmation: !this.state.confirmation,
        })
    }
    onNext(){
        let arr = this.state.phone.split(" ");
        let obj = {};
        obj.formatted_phone =this.state.phone;
        obj.phone = arr[1];
        obj.txt_opt_in = "on";
        savePhoneNumber(obj);
        
        sendCode(this.state.phone);
    }
    sendCode(){
        sendCode(this.state.phone);
    }
    handlePhoneNumber(value){
        this.setState({
            phone: value,
        })
    }
    render(){
        return(
            this.state.verifiedPhone && <TextNotificationVarifiedPhone  backClickHandler={this.props.backClickHandler} phone={this.state.phone}/> ||
            <div className="large-9 medium-9 small-12 upper_container columns">
                <div className="setting_content_container">
                    <Header imgSrc={'/social/images/settings/active_options/Subtraction 19.png'} title={'TEXT NOTIFICATIONS'} imgClass={'text_msg'} backClickHandler={this.props.backClickHandler} />
                    <div className="content_container">
                        {
                            this.state.confirmation && <PhoneVarification phone={this.state.phone}  handleConfirmation={this.handleConfirmation} sendCode={this.sendCode} /> || 
                            <span>
                                <div className="explanation">
                                    Activating a mobile number allows Plexuss to send text messages to your phone so you can receive notifications.
                                </div>
                                <div className="text_container" id="settings-phone-input">
                                    <ReactPhoneInput value={this.state.phone} onBlur={(e) =>this.handlePhoneNumber(e.target.value)}/>
                                    <div className="terms_of_service">
                                        <input type="checkbox" onChange={this.handleCheck} defaultChecked={this.state.checked}/>
                                        <div className="_text">I agree to the <a href="/terms-of-service" className="highlight">Terms of Service</a></div>
                                    </div>
                                    {
                                        !this.state.checked && 
                                        <div className="terms_msg">Must agree to the Terms and Conditions in order to continue.</div>
                                    }
                                    <div className={"next_button " + (!this.state.checked && 'disabled')} onClick={() => this.onNext()}>Next</div>
                                </div>
                            </span>
                        }
                    </div>
                </div>
            </div>
        )        
    }
}

const mapStateToProps = (state) =>{
    return{
        phone: state.setting.setting.phone,
        savePhone: state.setting.setting.savePhone,
        validateCode: state.setting.setting.validateCode,
        savePhoneCount: state.setting.setting.savePhoneCount,
        verifiedPhone: state.setting.setting.verifiedPhone,
    }
}
export default connect(mapStateToProps, null)(TextNotification);