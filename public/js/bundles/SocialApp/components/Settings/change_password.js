import React, { Component } from 'react'
import Header from './content_header'
import { changePassword } from './../../api/user'
import { connect } from 'react-redux'
class Change_password extends Component{
    constructor(props){
        super(props);
        this.state={
            oldPassword: '',
            newPassword: '',
            varifyPassword: '',
            errors: [],
        }
        this.handleOldPassword = this.handleOldPassword.bind(this);
        this.handleNewPassword = this.handleNewPassword.bind(this);
        this.varifyPassword = this.varifyPassword.bind(this);
        this.onSubmit = this.onSubmit.bind(this);
    }
    onSubmit(){
        const { oldPassword, newPassword, varifyPassword } = this.state;
        let errors = [];
        if( !oldPassword || !newPassword || !varifyPassword){
            errors.push("Empty fields are not allow."); 
            return;
        }
        let data = {
            action: 'change_pass',
            old_pass: this.state.oldPassword,
            new_pass: this.state.newPassword,
            verify_pass: this.state.varifyPassword
        };
        if (newPassword.length < 8) {
            errors.push("Your password must be at least 8 characters"); 
        }
        if (newPassword.length > 13) {
            errors.push("Your password must be at max 13 characters"); 
        }
        if (newPassword.search(/[a-z]/i) < 0) {
            errors.push("Your password must contain at least one letter.");
        }
        if (newPassword.search(/[0-9]/) < 0) {
            errors.push("Your password must contain at least one digit."); 
        }
        let arr = newPassword.split('');
        if (arr[0].search(/[a-z]/i) < 0) {
            errors.push("Your password must be start with letter."); 
        }
        if(errors.length == 0){
            changePassword(data);
            this.setState({
                oldPassword: '',
                newPassword: '',
                varifyPassword: '',
            })
        }else{
            this.setState({errors: errors})
        }
    }
    handleOldPassword(e){
        this.setState({oldPassword: e.target.value})
    }
    handleNewPassword(e){
        this.setState({newPassword: e.target.value})
    }
    varifyPassword(e){
        this.setState({varifyPassword: e.target.value})
    }
    render(){
        let { setting } = this.props;
        let { errors } = this.state;
        return(
            <div className="large-9 medium-9 small-12 columns upper_container">
                <div className="setting_content_container">
                    <Header imgSrc={'/social/images/settings/active_options/Subtraction 18.png'} title={'CHANGE PASSWORD'} imgClass={'lock'} backClickHandler={this.props.backClickHandler} />
                    <div className="content_container">
                        <div className="explanation">
                            Your password should contain 8-13 letters and numbers, start with and contain at least one number.
                        </div>
                        {
                            setting && setting.errorMsg && <div className="explanation4 alert-box alert radius">
                                { setting.errorMsg }
                            </div> || 
                            setting && setting.passNotMatch && <div className="explanation4 alert-box alert radius">
                                { setting.passNotMatch }
                            </div> || 
                            setting && setting.successMsg && <div className="explanation4 alert-box success radius">
                                { setting.successMsg }
                            </div>||
                            errors && errors.length > 0 && <div className="explanation4">
                                {
                                    errors.map((error, index)=>{
                                        return <div className="error_list_elem" key={'error'+index}>{error}</div>
                                    })
                                }
                            </div>
                        }
                        <form className="change_password">
                            <label className="lable">
                                OLD PASSWORD
                                <input onChange={this.handleOldPassword}
                                    name="oldPassword"
                                    type="password"
                                    value={this.state.oldPassword}
                                    className="input"/>
                            </label>
                            <label className="lable">
                                NEW PASSWORD
                                <input onChange={this.handleNewPassword}
                                    name="oldPassword"
                                    type="password" 
                                    value={this.state.newPassword}
                                    className="input"/>
                            </label>
                            <label className="lable">
                                VERIFY PASSWORD
                                <input onChange={this.varifyPassword}
                                    name="oldPassword"
                                    type="password" 
                                    value={this.state.varifyPassword}
                                    className="input"/>
                            </label>
                            <div className="change_password_btn" onClick={this.onSubmit}>Change Password</div>
                        </form>
                    </div>
                </div>
            </div>
        )
    }
}
const mapStateToProps = (state) =>{
    return{
        setting: state.setting.setting,
    }
}

export default connect(mapStateToProps, null)(Change_password);
