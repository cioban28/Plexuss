import React, { Component } from 'react';
import { connect } from 'react-redux';
import { closeModal, closeModalAlumni } from '../../actions/modal';
import axios from 'axios';
import toNumber from 'lodash/toNumber'
import isNumber from 'lodash/isNumber'
import './styles.scss'
const _ = {
    toNumber: toNumber,
    isNumber: isNumber,
}
class SignInModal extends Component {
    constructor(props) {
        super(props)
        this.state = {
            is_valid: false,
            is_first: true,
            is_last: true,
            is_email: true,
            is_password: true,
            is_confirm: true,
            is_birthday: true,
            is_agree: true,
            is_error: false,
            first: '',
            last: '',
            email: '',
            password: '',
            month: '',
            day: '',
            year: '',
            agree: true,
            error: '',
            loading: false,
        }
        this.validate = this.validate.bind(this)
        this.validation = this.validation.bind(this)
        this.signup = this.signup.bind(this)
        this.handleClose = this.handleClose.bind(this);
    }

    validate() {
        this.setState({is_valid: 
            this.state.is_email && this.state.is_password &&
            this.state.is_first && this.state.is_last &&
            this.state.is_confirm &&
            this.state.is_agree && this.state.is_birthday})
    }

    validation = (type, valid, value) => {
        var field = 'is_' + type

        var obj = {};
        obj[field] = valid
        if(type != 'birthday') {
            obj[type] = value
        } else {
            var tmp = value.split('/')
            obj['year'] = tmp[0]
            obj['month'] = tmp[1]
            obj['day'] = tmp[2]
        }
        this.setState(obj, ()=>{this.validate()})
    }

    signup() {
        const {url} = this.props;
        if (!this.state.is_valid)
            return;

        this.setState({loading: true})
        var data = {}
        data['email'] = this.state.email
        data['password'] = this.state.password
        data['fname'] = this.state.first
        data['lname'] = this.state.last
        data['year'] = this.state.year
        data['month'] = this.state.month
        data['day'] = this.state.day
        axios({
            method: 'post',
            url: '/signup/true?redirect_from_signin='+url,
            data: data,
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
        })
        .then(res => {
            if (res.data.url == '/signup') {
                this.setState({is_error: true, error: res.data.error_message})
            } else {
                window.location.href = res.data.url;
            }
        })
        .catch(error => {
            this.setState({loading: false})
            console.log(error)
        })
    }

    handleClose() {
        if (this.props.url == '/')
            this.props.closeModal();
        else
            this.props.closeModalAlumni();
    }

    render() {
        var saveBtnClasses = ''
        if( !this.state.is_valid ) saveBtnClasses = 'signupButton disable';
        else saveBtnClasses = 'signupButton';
        const {loading} = this.state
        return (
            <div>
                <div className="close-modal-cont" onClick={this.handleClose}>
                    <span className="close-reveal-modal closer_sec">&#215;</span>
                </div>
                <div className='row formheader'>
					<div className='large-12 column'>
						<div className='formshield'></div>
					</div>	
				</div>
				<div className='row formBody signup-body'>
					<div className="large-12 column">
                        <div className='row'>
                            <div className='large-12 column'>
                                <h1>Sign Up for Free & Get Started</h1>
                            </div>
                            {this.state.is_error && <span className='error signup-error'>{this.state.error}</span>}
                            <div className="row">
                                <div className='large-6 column'>
                                    <TextInput isValid={this.state.is_first} type={'first'} validation={this.validation}/>
                                </div>
                                <div className='large-6 column'>
                                    <TextInput isValid={this.state.is_last} type={'last'} validation={this.validation}/>
                                </div>
                            </div>

                            <div className="row">
                                <div className="large-12 columns">
                                    <EmailInput isValid={this.state.is_email} type={'email'} validation={this.validation}/>
                                </div>
                            </div>

                            <div className="row">
                                <div className='large-6 column'>
                                    <PasswordInput isValid={this.state.is_password} type={'password'} validation={this.validation}/>
                                </div>
                                <div className='large-6 column'>
                                    <ConfirmInput isValid={this.state.is_confirm} type={'confirm'} password={this.state.password} validation={this.validation}/>
                                </div>
                            </div>

                            <div className='row birthday-input'>
                                <div className='column small-12 medium-12 large-6'>
                                    <BirthdayInput type={'birthday'} validation={this.validation}/>
                                </div>
                                <div className='column small-12 medium-12 large-6 agenotice'>
                                    You must be 13 years or older to sign up.
                                </div>
                            </div>

                            <div className="row">
                                <div className='large-12 column optinmessage'>
                                    <CheckBoxField isValid={this.state.is_agree} type={'agree'} validation={this.validation}/>
                                </div>
                            </div>

                            <div className="row">
                                <div className="large-12 columns">
                                    <button className={saveBtnClasses} onClick={this.signup} disabled={loading}>
                                        {loading && <div className="lds-ring"><div></div><div></div><div></div><div></div></div>}
                                        Signup
                                    </button>
                                </div>
                            </div>
                            <div className='row text-center'>
                                <div className='large-4 columns'>
                                    <div className='orLine'></div>
                                </div>
                                <div className='large-4 column ortxt'>Or Sign up with</div>
                                <div className='large-4 columns'>
                                    <div className='orLine'></div>
                                </div>
                            </div>


                            <div className='row'>
                                <div className='large-12 columns rela signup-group'>
                                    <a href="/facebook" className='signupFB'>
                                        <img src='/images/social/facebook_white.png' className="facebook-white"/>
                                        {' Facebook'}
                                    </a>
                                    <a href="/googleSignin" className='signupGoogle'>
                                        <img src='/images/social/google-logo.svg' className="google-white"/>
                                        {' Google'}
                                    </a>
                                    {/* <a href="/linkedinSignin" className='signupLinkedIn'>
                                        <img src='/images/social/LinkedIn-in.svg' className="linkedin-white"/>
                                        {' LinkedIn'}
                                    </a> */}
                                </div>
                            </div>

                            <br />
                            <div className='row'>
                                <div className='large-12 column text-center'>
                                    <a className="haveAcct" href="/signin">Already have an account?</a>
                                </div>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        )
    }
}

class CheckBoxField extends Component {
    constructor(props) {
        super(props)
        this.state = {
            agree: true,
        }
        this.validate = this.validate.bind(this)
    }

    validate(e) {
        this.setState({agree: e.target.checked}, () => {this.props.validation(this.props.type, this.state.agree, this.state.agree)})
    }

    render() {
        const { isValid } = this.props;
        return(
            <div>
                <input type="checkbox"
                    name='agree'
                    className="checkbox-margin"
                    onChange={this.validate}
                    checked={this.state.agree}></input>
                By clicking signup you agree to the <a href='/terms-of-service' target='_blank'>Plexuss Terms of Service</a> &amp; <a target='_blank' href='/privacy-policy'>Privacy Policy</a>

                {!isValid && <span className='error'>*You need to agree to the terms to join.</span>}
            </div>
        )
    }
}
class BirthdayInput extends Component {
    constructor(props) {
        super(props)
        this.state = {
            month: '',
            day: '',
            year: '',
            is_month: true,
            is_day: true,
            is_year: true,
            is_over: true,
        }
        this.validate = this.validate.bind(this)
        this.validate_month = this.validate_month.bind(this)
        this.validate_day = this.validate_day.bind(this)
        this.validate_year = this.validate_year.bind(this)
    }

    validate_month(e) {
        var isValid = false
        if (e.target.value < 13 && e.target.value > 0 && _.isNumber(_.toNumber(e.target.value)))
            isValid = true;
        this.setState({is_month:isValid, month: e.target.value} , () => {this.validate()})
    }
    validate_day(e) {
        var isValid = false
        if (e.target.value < 32 && e.target.value > 0 && _.isNumber(_.toNumber(e.target.value)))
            isValid = true;
        this.setState({is_day:isValid, day: e.target.value} , () => {this.validate()})
    }
    validate_year(e) {
        var isValid = false, isOver = false
        var year = new Date().getFullYear()
        if (e.target.value > year-100 && _.isNumber(_.toNumber(e.target.value)))
            isValid = true
        if (e.target.value <= year-13 )
            isOver = true;
        this.setState({is_over: isOver, is_year:isValid, year: e.target.value} , () => {this.validate()})
    }
    validate() {
        var isValid = this.state.is_year && this.state.is_month && this.state.is_day && this.state.is_over
        var val = this.state.year + '/' + this.state.month + '/' + this.state.day
        this.props.validation(this.props.type, isValid, val)
    }

    render() {
        return(
            <div>
                <div className='formDateWrapper row collapse text-center'>
                    <div className='column small-3 text-rigth'>
                        <input type="text"
                            name='month'
                            placeholder='M'
                            maxLength='2'
                            onChange={this.validate_month}
                            onBlur={this.validate_month}
                            value={this.state.month}></input>
                    </div>
                    <div className='column small-1' style={{fontSize: '20px'}}>/</div>
                    <div className='column small-3 text-rigth'>
                        <input type="text"
                            name='day'
                            placeholder='D'
                            maxLength='2'
                            onChange={this.validate_day}
                            onBlur={this.validate_day}
                            value={this.state.day}></input>
                    </div>
                    <div className='column small-1' style={{fontSize: '20px'}}>/</div>
                    <div className='column small-3 text-rigth'>
                        <input type="text"
                            name='year'
                            placeholder='Year'
                            maxLength='4'
                            onChange={this.validate_year}
                            onBlur={this.validate_year}
                            value={this.state.year}></input>
                    </div>
                </div>

                {!this.state.is_month && <span className='error'>*Please enter a valid Month.</span>}
                {!this.state.is_day && <span className='error'>*Please enter a valid Day.</span>}
                {!this.state.is_year && <span className='error'>*Please enter a valid Year.</span>}
                {!this.state.is_over && <span className='error'>Sorry, You must be 13 years or older to sign up.</span>}
            </div>
        )
    }
}

class TextInput extends Component {
    constructor(props) {
        super(props)
        this.state = {
            val: '',
        }
        this.validate = this.validate.bind(this)
    }

    validate(e) {
        var isValid = e.target.value.match(/^[a-zA-Z]{1,50}$/)
        this.setState({val:e.target.value}, ()=> {this.props.validation(this.props.type, isValid, this.state.val)})
    }

    render() {
        const { isValid, type } = this.props;
        let error = type == 'first' ? '*Please input your first name.' : '*Please input your last name.'
        return(
            <div>
                <input type="text"
                    name={type=='first' ? 'fname' : 'lname'}
                    className={!isValid ? 'name-input input-error' : 'name-input'}
                    placeholder={type=='first' ? 'First Name' : 'Last Name'}
                    onChange={this.validate}
                    onBlur={this.validate}
                    value={this.state.val}></input>

                {!isValid && <span className='error'>{error}</span>}
            </div>
        )
    }
}

class EmailInput extends Component {
    constructor(props) {
        super(props)
        this.state = {
            val: '',
        }
        this.validate = this.validate.bind(this)
    }

    validate(e) {
        var isValid = e.target.value.match(/^([\w.%+-]+)@([\w-]+\.)+([\w]{2,})$/i)
        this.setState({val:e.target.value}, ()=> {this.props.validation('email', isValid, this.state.val)})
    }

    render() {
        const { isValid } = this.props;
        return(
            <div>
                <input type="email"
                    name="email"
                    className={!isValid ? 'input-error' : ''}
                    placeholder="Email Address"
                    onChange={this.validate}
                    onBlur={this.validate}
                    value={this.state.val}></input>

                {!isValid && <span className='error'>*Please enter an Email Address.</span>}
            </div>
        )
    }
}

class PasswordInput extends Component {
    constructor(props) {
        super(props)
        this.state = {
            val: '',
        }
        this.validate = this.validate.bind(this)
    }
    
    validate(e) {
        var isValid = e.target.value.match(/^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}/)
        this.setState({val:e.target.value}, ()=> {this.props.validation(this.props.type, isValid, this.state.val)})
    }

    render() {
        const { isValid } = this.props;
        return (
            <div>
                <input type="password"
                    name="password"
                    className={!isValid ? 'input-error' : ''}
                    placeholder="Password"
                    onChange={this.validate}
                    onBlur={this.validate}
                    value={this.state.val}></input>

                {!isValid && <span className='error'>*Please enter a valid password with these requirements:<br/>
                                                8-13 letters and numbers<br/>
                                                Starts with a letter<br/>
                                                Contains at least one number</span>}
            </div>
        )
    }
}

class ConfirmInput extends Component {
    constructor(props) {
        super(props)
        this.state = {
            val: '',
        }
        this.validate = this.validate.bind(this)
    }
    
    validate(e) {
        var isValid = false
        if (e.target.value == this.props.password)
            isValid = true
        this.setState({val:e.target.value}, ()=> {this.props.validation(this.props.type, isValid, this.state.val)})
    }

    render() {
        const { isValid } = this.props;
        return (
            <div>
                <input type="password"
                    name="confirm"
                    className={!isValid ? 'input-error' : ''}
                    placeholder="Confirm Password"
                    onChange={this.validate}
                    onBlur={this.validate}
                    value={this.state.val}></input>

                {!isValid && <span className='error'>*Please re enter a password</span>}
            </div>
        )
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
        closeModal: () => { dispatch(closeModal()) },
        closeModalAlumni: () => { dispatch(closeModalAlumni()) },
    }
  }

export default connect(null, mapDispatchToProps)(SignInModal);