import React, { Component } from 'react'
import ReactCodeInput from 'react-code-input'
import { confirmCode } from './../../api/user'
class PhoneVarification extends Component{
    constructor(props){
        super(props);
        this.state={
            code: '',
        }
        this.confirm = this.confirm.bind(this);
        this.onChange = this.onChange.bind(this);
    }
    confirm(){
        let obj ={};
        obj.code = this.state.code;
        confirmCode(obj);
    }
    onChange(value){
        this.setState({
            code: value,
        })
    }
    render(){
        let { phone, handleConfirmation, sendCode } = this.props;
        return(
            <div className="phone_varification">
                <div className="title">We've sent you a SMS code to</div>
                <div className="phone_number">{ phone }</div>
                <div className="varification_desc">To complete your phone number verification, please enter your 4 digit code below.</div>
                <ReactCodeInput type='number' fields={4} onChange={this.onChange}/>
                <div className="resend" onClick={()=> sendCode()}>Resend</div>
                <div className="footer">
                    <div className="go_back" onClick={() => handleConfirmation()}>Go back</div>
                    <div className="confirm" onClick={() => {handleConfirmation(); this.confirm() }}>Confirm</div>
                </div>
            </div>
        )
    }
}
export default PhoneVarification;