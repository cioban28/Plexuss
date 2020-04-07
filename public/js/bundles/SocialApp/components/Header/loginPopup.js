import React, { Component } from 'react';
import axios from 'axios';

class LoginPopup extends Component {
    constructor(props) {
        super(props)
        this.state = {
            is_valid: false,
            validEmail: true,
            validPassword: true,
            email: '',
            password: '',
            loading: false,
        }
        this.validate = this.validate.bind(this)
        this.validation = this.validation.bind(this)
        this.signin = this.signin.bind(this)
        this.checkEnterKey = this.checkEnterKey.bind(this)
    }

    validate() {
        this.setState({is_valid: this.state.validEmail && this.state.validPassword})
    }

    validation = (type, valid, value) => {
        var field = 'validEmail'
        if (type !== 'email')
            field = 'validPassword'

        var obj = {};
        obj[field] = valid
        obj[type] = value
        this.setState(obj, ()=>{this.validate()})
    }

    signin() {
        if (!this.state.is_valid)
            return;

        this.setState({loading: true})
        var data = {}
        data['is_api'] = true
        data['email'] = this.state.email
        data['password'] = this.state.password

        axios({
            method: 'post',
            url: '/signin',
            data: data,
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
        })
        .then(res => {
            window.location.href = res.data
        })
        .catch(error => {
            this.setState({loading: false})
            console.log(error)
        })
    }

    checkEnterKey(e) {
        if (e.keyCode == 13) {
            this.signin();
        }
    }

    componentDidMount() {
        document.addEventListener('keydown', this.checkEnterKey, false)
    }

    componentWillUnmount() {
        document.removeEventListener('keydown', this.checkEnterKey, false)
    }
    
    render() {
        const { isDisplay } = this.props;
        const { loading } = this.state
        var saveBtnClasses = ''
        if( !this.state.is_valid ) saveBtnClasses = 'signupButton disable';
        else saveBtnClasses = 'signupButton';
        return(
            <div>
            {isDisplay && 
            <div className="loginForm">
                <div className="row">
                    <div className="large-12 columns"><h1>Welcome Back!</h1></div>
                </div>
                <div className="row">
                    <div className="large-12 columns">
                        <EmailInput isValid={this.state.validEmail} signIn={this.signin} validation={this.validation}/>
                    </div>
                </div>
                <div className="row">
                    <div className="large-12 columns">
                        <PasswordInput isValid={this.state.validPassword} signIn={this.signin} validation={this.validation}/>
                    </div>
                </div>
                <div className="row">
                    <div className="large-12 columns">
                        <button className={saveBtnClasses} onClick={this.signin} disabled={loading}>
                            {loading && <div className="lds-ring"><div></div><div></div><div></div><div></div></div>}
                            Sign In
                        </button>
                    </div>
                </div>
                <div className='row text-center'>
                    <div className='show-for-large-up large-5 columns'>
                        <div className='orLine'></div>
                    </div>
                    <div className='large-2 column ortxt'>OR</div>
                    <div className='show-for-large-up large-5 columns'>
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

                <div className='row forgottxt'>
                    <div className='large-7 columns text-center'>
                        <a href="/signup?utm_source=SEO&utm_medium=frontPage">Don’t have an account yet?</a>
                    </div>
                    <div className='large-5 columns text-center'>
                        <a href="/forgotpassword">Forgot password?</a>
                    </div>
                </div>
            </div>}
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
        this.keyPress = this.keyPress.bind(this)
    }

    keyPress(e) {
        if (e.keyCode == 13) {
            this.props.signIn();
        }
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
                    value={this.state.val}
                    onKeyDown={this.keyPress}></input>

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
        this.keyPress = this.keyPress.bind(this)
    }

    keyPress(e) {
        if (e.keyCode == 13) {
            this.props.signIn();
        }
    }
    
    validate(e) {
        var isValid = e.target.value.match(/^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}/)
        this.setState({val:e.target.value}, ()=> {this.props.validation('password', isValid, this.state.val)})
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
                    value={this.state.val}
                    onKeyDown={this.keyPress}></input>

                {!isValid && <span className='error'>*Please enter a valid password with these requirements:<br/>
                                                8-13 letters and numbers<br/>
                                                Starts with a letter<br/>
                                                Contains at least one number</span>}
            </div>
        )
    }
}

export default LoginPopup;