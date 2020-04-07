import React, { Component } from 'react';
import { connect } from 'react-redux'
import Loader from '../Header/loader'
import {withRouter, Link} from 'react-router-dom';
import { getStepStatuses, getStepDatas, getDataFor } from '../../api/step';
import { SpinningBubbles } from './../Header/loader/loader'
import _ from 'lodash'

var styles = {
	bad: {
		border: '1px solid firebrick',
	},
	good: {
		border: '1px solid skyblue',
  },
  good_green: {
		border: '1px solid #24b26b',
	},
	bad_code: {
		borderTopColor: 'firebrick',
		borderLeftColor: 'firebrick',
		borderBottomColor: 'firebrick',
	},
}

class Step4 extends Component {
    constructor(props) {
      super(props)
      this.state = {
        isLoading: true,
        save_route: '/get_started/save',
        get_route: '/get_started/getDataFor/step',
        confirm_code_route: '/get_started/checkPhoneConfirmation',
        resend_code_route: '/get_started/sendPhoneConfirmation',
        new_phone_num_route: '/get_started/saveNewPhone',
        validate_with_twilio_route: '/get_started/validatePhone',
        step_num: this.props.currentPage,
        is_valid: false,
        is_sending: false,
        back_route: null,
        next_route: null,
        save_btn_classes: 'right btn submit-btn text-center',
        save_has_been_clicked: !1,
        needConfirmation: false,
        _zipValid: true,
        confirmationCode: '',
        openDialingCodes: false,
      }

      this._getData = this._getData.bind(this)
      this._validate = this._validate.bind(this)
      this._save = this._save.bind(this)
      this._keyPressed = this._keyPressed.bind(this)
      this._resetConfirmation = this._resetConfirmation.bind(this)
      this._validate = this._validate.bind(this)
      this._validatePhone = this._validatePhone.bind(this)
      this._validateWithTwilio = this._validateWithTwilio.bind(this)
      this._enableNextIfFormIsValid = this._enableNextIfFormIsValid.bind(this)
      this._toggleDialingCodes = this._toggleDialingCodes.bind(this)
      this._updateCode = this._updateCode.bind(this)
      this._toNextPage = this._toNextPage.bind(this)
      this._closeDifferentCountryModal = this._closeDifferentCountryModal.bind(this)
      this._confirmCode = this._confirmCode.bind(this)
      this._showConfirmErrMsg = this._showConfirmErrMsg.bind(this)
      this._resendCode = this._resendCode.bind(this)
      this._showResendMsg = this._showResendMsg.bind(this)
      this._skipConfirmationCode = this._skipConfirmationCode.bind(this)
      this._saveNewNumber = this._saveNewNumber.bind(this)
      this._showNewNumberMsg = this._showNewNumberMsg.bind(this)
    }

    _getData(){
      var _this = this;

      getStepDatas('6')
      .then(() => {
        _this._initData(this.props.step)
        getDataFor('states')
        .then(() => {
          getDataFor('country')
          .then(() => {
            this.setState({isLoading:false})
          })
        })
      })
    }
  
    _initData(data){
      var valid = false, dataForValidation = {};
  
      for( var prop in data ){
        if( data.hasOwnProperty(prop) && prop !== 'txt_opt_in' && prop !== 'dialing_code' ){
          dataForValidation.id = '_'+prop;
          dataForValidation.name = prop;
          dataForValidation.value = data[prop];
          this._validate(dataForValidation);
        }
      }
    }
  
    _save(e){
      e.preventDefault();
  
      var _this = this, s = this.state, formData = new FormData( $('form')[0] );
  
      if( s.enableConfirmationCode ){
        this.setState({is_sending: true});
  
        $.ajax({
          url: s.save_route,
          type: 'POST',
          data: formData, 
          enctype: 'multipart/form-data',
          contentType: false,
              processData: false,
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).done(function(data){
          //if an object is returned, it's an error
          if( data && _.isObject(data) && !_.isNumber(data) ){
            _this.setState({is_sending: false, err: data.error, errMsg: data.msg});//remove loader
          }else{
            getStepStatuses('')
            .then(() => {
              _this._getData();
              amplitude.getInstance().logEvent('step4_completed', {content: 'Contact Info'} );
                        
              var txt_opt = $('#_text').is(':checked');
              if( txt_opt ){
                _this.setState({is_sending: false, needConfirmation: true});//remove loader
                _this._resendCode();
              }else{
                _this.props.history.push(s.next_route)
              }
            })
          }
  
          if( JSON.parse(sessionStorage.getItem('college_id')) ){
            _this.setState({is_sending: !1});//remove loader
            // $(document).trigger('saved');
  
          }else if( s.needConfirmation ){
            window.location.href = s.next_route;	
          }
        });
      }
    }
  
    _keyPressed(e){
      if( e.charCode < 48 || e.charCode > 57 ) e.preventDefault();
    }
  
    _resetConfirmation(){
      this.setState({needConfirmation: false});
    }
  
    _validate(e){
      var newState = {}, valid = false,
        id = e.target ? e.target.id : e.id, //if e has target prop, it's coming from event, else e is an object created in _initData
        name = e.target ? e.target.getAttribute('name') : e.name,
        value = e.target ? e.target.value : e.value;
  
      switch( name ){
        case 'phone':
          valid = true; // phone no longer required
          break;
  
        case 'address':
          if( /^[a-zA-Z0-9\.,#\- ]+$/.test(value) && value ) valid = true;
           break;
  
        case 'city':
          if( /^[a-zA-Z\.\- ]+$/.test(value) && value ) valid = true;
          break;
  
        case 'country':
          // if changing country from other country (or first time changing) then validate state also
          if( +value === 1 ){
            valid = true; // country is valid if here
  
            // if state is empty while country is US, make invalid
            if( !this.state.state ){
              this.setState({
                _stateValid: false,
                _stateValidated: true
              })
            }
            if (!this.state.zip) {
              this.setState({
                _zipValid: false,
                _zipValidated: true
              })
            }
  
          }else if( value ) { 
            valid = true;
            this.setState({
              _zipValid: true,
              _zipValidated: true
            })
          }
  
          value = +value;
          break;
  
        case 'state':
          // state is only required for US students only - optional for all international
          if( this.state.country === 1 ){
            if( value ) valid = true;
  
          }else valid = true;
  
          break;
  
        case 'zip':
          //if zip has a value, validate it, otherwise empty is ok b/c it's not required
          if (this.state.country === 1) {
            if( value )	{
              if( /^[a-zA-Z0-9\.,\- ]+$/.test(value) ) valid = true;
              else valid = false;
            }else{
              valid = false;
            }
          } else {
            valid = true;
          }
  
          break;
  
        case 'code':
          if( value && value.length === 4 && _.isNumber(+value) && (+value >= 1000 && +value <= 9999) ) valid = true;
          break;
  
        default: return;
      }
  
      let obj = {}
      obj[name] = value;
      obj[id+'Valid']=valid
      obj[id+'Validated']=true
      obj['focused']=id
      this.setState(obj, ()=>this._enableNextIfFormIsValid())
    }
  
    _validatePhone(e){
      this.setState({phone: e.target.value});
      this._validateWithTwilio(e.target.value);
    }
  
    _validateWithTwilio(phone, country_code){
      var _this = this, s = this.state, 
        phoneNum = '',
        countryCode = '',
        full_phone_num = '';
  
      phoneNum = phone || s.phone;
      countryCode = country_code || s.dialing_code;
      full_phone_num = countryCode+phoneNum;
  
      $.ajax({
              url: '/phone/validatePhoneNumber',
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              data: {phone: full_phone_num},
              type: 'POST'
          }).done(function(data){
        //if data is an object and has an error property
        if( data && _.isObject(data) && _.has(data, 'error') ){
          var valid = false, 
  
          valid = !data.error;
  
          if( valid ){
            //remove country phone code from phoneNumber
            var split = data.phoneNumber.substr(data.country_phone_code.length, data.phoneNumber.length);
            var formatted = data.country_phone_code + ' ' + split;
  
            _this.setState({formatted_phone: formatted})
          }
  
          _this.setState({
            _phoneValid: valid,
            _phoneValidated: true,
            focused: '_phone'
          })
  
          _this._enableNextIfFormIsValid();
        }
      });
    }
  
    _enableNextIfFormIsValid(){
      var s = this.state, valid = false, newState = {};
  
      if( !s.needConfirmation ){
        if( s._countryValid && s._addressValid && s._cityValid && s._stateValid && s._zipValid ) valid = true;
        this.setState({enableConfirmationCode: valid})
      }else{
        if( s._confirmationCodeValid ) valid = true;
        this.setState({enableNextStep: valid})
      }
    }
  
    _toggleDialingCodes(){
      this.setState({openDialingCodes: !this.state.openDialingCodes});
    }
  
    _updateCode(country){
      var code = country.country_phone_code;
  
      this.setState({dialing_code: code, openDialingCodes: false});
      this._validateWithTwilio(null, code);
    }
  
    _toNextPage(){
      var txt_opt = $('#_text').is(':checked');
      if( !txt_opt ){
        window.location.href = this.state.next_route;
      }else{
        this._resendCode();
        this.setState({err: false, needConfirmation: true});
      }
    }
  
    _closeDifferentCountryModal(e){
      this.setState({err: false});
    }
  
    _togglePhoneEditor(){
      this.setState({openPhoneEditor: !this.state.openPhoneEditor});
    }
  
    _confirmCode(e){
      e.preventDefault();
      var _this = this, s = this.state, formData = new FormData( $('form')[0] ), _data = null;
  
      this.setState({is_sending: true});
  
      $.ajax({
        url: s.confirm_code_route,
        type: 'POST',
        data: formData, 
        enctype: 'multipart/form-data',
        contentType: false,
            processData: false,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      }).done(function(data){
        _data = JSON.parse(data);
        if( _data && _data.response === 'success' ){
          _this.setState({is_sending: false});
          window.location.href = s.next_route;
        }
        else{
          _this.setState({confirmation_err: true, confirmation_msg: _data.response, is_sending: false, resend_done: true});
          _this._showConfirmErrMsg();
        }
      });
    }
  
    _showConfirmErrMsg(){
      var _this = this;
  
      setTimeout(function(){
        _this.setState({confirmation_err: false});
      }, 10000);
    }
  
    _resendCode(e){
      if(e) e.preventDefault();
      var _this = this, s = this.state, _data = null, msg = '';
  
      this.setState({is_sending: true});
  
      $.ajax({
        url: s.resend_code_route,
        type: 'POST',
        data: {phone: s.phone, dialing_code: s.dialing_code}, 
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      }).done(function(data){
        try {
          _data = JSON.parse(data);
        } catch(e) {
          var stringify = data.match(/{.*}/) && data.match(/{.*}/).join('');
                  _data = JSON.parse(stringify);
        }
        msg = _data.response === 'failed' ? 'Failed to send phone authentication code to this number. Please input your correct phone number and country calling number.' : _data.response;
        if (_data.response == 'failed' && _data.error_message && _data.error_message.includes('reached the maximum')) {
          msg = _data.error_message;
        }
        _this.setState({
          is_sending: false, 
          resend_done: false, 
          resend_msg: msg, 
          confirmation_err: false
        });
        _this._showResendMsg();
      });
    }
  
    _showResendMsg(){
      var _this = this;
  
      setTimeout(function(){
        _this.setState({resend_done: true});
      }, 20000);
    }
  
    _skipConfirmationCode(){
      window.location.href = this.state.next_route;
    }
  
    _saveNewNumber(e){
      e.preventDefault();
      var _this = this, s = this.state, _data = null;
  
      this.setState({is_sending: true});
  
      $.ajax({
        url: s.new_phone_num_route,
        type: 'POST',
        data: {phone: s.phone, dialing_code: s.dialing_code}, 
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      }).done(function(data){
        if( data && data.response === 'success' ){
          _this.setState({
            is_sending: false, 
            newNumberSaved: true, 
            newNumberMsg: data.msg, 
            openPhoneEditor: false, 
          });
          _this._showNewNumberMsg();
        }
      });
    }
  
    _showNewNumberMsg(){
      var _this = this;
  
      setTimeout(function(){
        _this.setState({newNumberSaved: false});
      }, 10000);
    }

    componentDidMount() {
      var classes = this.state.save_btn_classes, prev, next, num,
			Carousel = null, carou = [], _this = this;

      // Facebook event tracking
      fbq('track', 'GetStarted_Step6_PhoneNumber_Page');

      //build prev step route
      num = parseInt(this.state.step_num);
      prev = num - 1;
      next = num + 1;

      this.setState({
        back_route: '/get_started/' + prev,
        next_route: '/get_started/' + next
      })

      this._getData();
    }

    render() {
      var s = this.state, _this = this;
      return (
        <div className="step_container">
          {s.isLoading ? ( <SpinningBubbles/> ) : (
            <span>
            <div className="row phone-container">
            <div className="column small-12">
              <form onSubmit={ this._save }>
                <input type="hidden" name="step" value={s.step_num} />		
              {
                !s.needConfirmation ?
                <div className="row">
                  <h3 className="columns small-12 phone-title">Colleges need a way to communicate with you</h3>
                  <div className="columns small-12 medium-4">
                    <label htmlFor="_country">{'Country'}</label>
                  </div>

                  <div className="columns small-12 medium-8">
                    <select 
                      id="_country" 
                      onChange={ this._validate }
                      onFocus={ this._validate }
                      value={ s.country || '' }
                      style={ s.focused === '_country' ? (s._countryValid ? styles.good : styles.bad) : (s._countryValidated && !s._countryValid ? styles.bad : styles.good_green) }
                      name="country">
                        <option value="" disabled="disabled">{'Select your country...'}</option>
                        { this.props.country_list.map(function(country){ return <option key={country.id} value={country.id}>{country.country_name}</option> }) }
                    </select>
                  </div>

                  <div className="columns small-12 medium-4">
                    <label htmlFor="_address">{'Address'}</label>
                  </div>

                  <div className="columns small-12 medium-8">
                    <input 
                      id="_address" 
                      name="address" 
                      type="text" 
                      onChange={ this._validate }
                      onFocus={ this._validate }
                      value={ s.address || '' }
                      style={ s.focused === '_address' ? (s._addressValid ? styles.good : styles.bad) : (s._addressValidated && !s._addressValid ? styles.bad : styles.good_green) }
                      placeholder="Enter address" />
                  </div>

                  <div className="columns small-12 medium-4">
                    <label htmlFor="_city">{'City'}</label>
                  </div>

                  <div className="columns small-12 medium-8">
                    <input 
                      id="_city" 
                      type="text" 
                      name="city" 
                      onChange={ this._validate }
                      onFocus={ this._validate }
                      value={ s.city || '' }
                      style={ s.focused === '_city' ? (s._cityValid ? styles.good : styles.bad) : (s._cityValidated && !s._cityValid ? styles.bad : styles.good_green) }
                      placeholder="Enter city" />
                  </div>

                  <div className="columns small-12 medium-4">
                    <label htmlFor="_state">{'State/Province'}</label>
                  </div>

                  <div className="columns small-12 medium-4">
                    {
                      s.country && s.country == 1 ?
                      <select
                        id="_state" 
                        type="text" 
                        name="state" 
                        onChange={ this._validate }
                        onFocus={ this._validate }
                        value={ s.state || '' }
                        style={ s.focused === '_state' ? (s._stateValid ? styles.good : styles.bad) : (s._stateValidated && !s._stateValid ? styles.bad : styles.good_green) } >
                          <option value='' disabled="disabled">{'Select state...'}</option>
                          { this.props.states.map(function(state){ return <option key={state.state_abbr} value={state.state_abbr}>{state.state_name}</option> }) }
                      </select>
                      :
                      <div>
                        <input
                          id="_state" 
                          type="text" 
                          name="state" 
                          onChange={ this._validate }
                          onFocus={ this._validate }
                          value={ s.state || '' }
                          style={ s.focused === '_state' ? (s._stateValid ? styles.good : styles.bad) : (s._stateValidated && !s._stateValid ? styles.bad : styles.good_green) }
                          placeholder="Enter state/province" />
                      </div>
                    }
                    
                  </div>

                  <div className="columns small-12 medium-1 text-center">
                    <label htmlFor="_zip">{'Zip'}</label>
                  </div>

                  <div className="columns small-12 medium-3">
                    <input 
                      id="_zip" 
                      type="text" 
                      name="zip" 
                      onChange={ this._validate }
                      onFocus={ this._validate }
                      value={ s.zip || '' }
                      style={ s.focused === '_zip' ? (s._zipValid ? styles.good : styles.bad) : (s._zipValidated && !s._zipValid ? styles.bad : styles.good_green) }
                      placeholder={s.country && s.country == 1 ? "Zip" : "Zip (optional)"} />
                  </div>

                  <div className="columns small-12 medium-6 phone-back">
                    <Link to={s.back_route}>Go Back</Link>
                  </div>

                  <div className="columns small-12 medium-6 medium-text-right">
                    <button 
                      className="phone-submit radius button" 
                      disabled={ !s.enableConfirmationCode } >
                        Next
                    </button>
                  </div>

                </div>
                :
                <div className="row text-center text-wrapper">
                  <div className="text-title">{"We've sent you an SMS code to"}</div>
                  {
                    s.openPhoneEditor ?
                    <div className="phone-editor-container">
                      <div className="row collapse">
                        <div className="columns small-9">
                          <input 
                            id="_phone" 
                            name="phone" 
                            type="text" 
                            className="phone-num"
                            onChange={ this._validatePhone }
                            onFocus={ this._validatePhone }
                            value={ s.phone || '' }
                            style={ s.focused === '_phone' ? (s._phoneValid ? styles.good : styles.bad) : (s._phoneValidated && !s._phoneValid ? styles.bad : {}) }
                            placeholder="Enter phone number" />

                          <div className="dialing-codes-btn editing">
                            <span onClick={ this._toggleDialingCodes }>{ s.dialing_code ? '+'+s.dialing_code : '' }</span>
                            <div className="arrow" onClick={ this._toggleDialingCodes }></div>
                          </div>
                          { s.openDialingCodes ? 
                            <div className="dialing-codes-container editing">
                              { this.props.unique_countries.map(function(country){ return <DialingCode key={country.id+'_'+country.country_code} country={country} updateCode={_this._updateCode} />}) }
                            </div>
                            :
                            null
                          }

                        </div>
                        <div className="columns small-3">
                          <button className="button save-edited-phone" onClick={ this._saveNewNumber }>Save</button>
                        </div>

                        { s._phoneValidated && !s._phoneValid ? 
                          <div className="columns small-12 text-left" style={{color: 'firebrick', margin: '-24px 0 0'}}>
                            <small>Invalid phone number. Is your country code correct?</small>
                          </div> 
                          : null }
                      </div>
                    </div>
                    :
                    <div className="text-phone">{s.phone ? '+'+s.dialing_code+' '+s.phone : ''}</div>
                  }

                  { s.newNumberSaved && _.isBoolean(s.newNumberSaved) ? <div className="new-phone-success">{s.newNumberMsg || ''}</div> : null }

                  <div className="text-center"><div className="text-changenum change cursor" onClick={this._togglePhoneEditor}><u>{'Change Number'}</u></div></div>
                  <div className="text-options">
                    <div className="text-changenum text-left">To complete your phone number verification, please enter your 4 digit code below.</div>
                    <input 
                      id="_confirmationCode"
                      type="text" 
                      name="code"
                      value={ s.code || '' }
                      maxLength={4} 
                      onFocus={ this._validate }
                      onChange={ this._validate }
                      style={ s.focused === '_confirmationCode' ? (s._confirmationCodeValid ? styles.good : styles.bad) : (s._confirmationCodeValidated && !s._confirmationCodeValid ? styles.bad : {}) }
                      onKeyPress={ this._keyPressed } />
                  </div>
                  <div className="text-center"><div className="text-changenum cursor" onClick={ this._resendCode }><u>Resend code</u></div></div>

                  { _.isBoolean(s.resend_done) && !s.resend_done ? <div className="err">{s.resend_msg || ''}</div> : null }
                  { _.isBoolean(s.confirmation_err) && s.confirmation_err ? <div className="err">{s.confirmation_msg || ''}</div> : null }

                  <div className="text-options">
                    <div>
                      <button 
                        disabled={ !s.enableNextStep }
                        onClick={ this._confirmCode }
                        className="phone-submit text-submit radius button">
                          Confirm
                      </button>
                    </div>
                    <div className="clearfix">
                      <div className="left cursor" onClick={ this._resetConfirmation }>Go back</div>
                      <SkipModal skip={ this._skipConfirmationCode } />
                    </div>
                  </div>
                </div>
              }
              </form>
            </div>
          </div>

          <DifferentCountryErrorModal open={ s.err } closeModal={ this._closeDifferentCountryModal } toNext={ this._toNextPage } />

          { s.is_sending ? <Loader /> : null }
          </span>
          )}
        </div>
      );
    }
}

class DialingCode extends Component {
  constructor(props) {
    super(props)
    this._update = this._update.bind(this)
  }
	_update(){
		this.props.updateCode(this.props.country);
	}

	render(){
		var country = this.props.country;

		return (
			<div className="codes" onClick={ this._update }>
				<div className={'flag flag-'+country.country_code.toLowerCase()}></div>
				<div className="country-name">{country.country_name+' (+'+country.country_phone_code+')'}</div>
			</div>
		);
	}
}

class DifferentCountryErrorModal extends Component {

  constructor(props) {
    super(props)
    this._close = this._close.bind(this)
    this._next = this._next.bind(this)
  }
	_close(){
		this.props.closeModal();
	}

	_next(){
		this.props.toNext();
	}

	render(){
		return ( this.props.open ) ? 
			<div className="skip-modal">
				<div className="skip-container">
					<div className="close text-right" onClick={ this._close }>x</div>
					<div className="title">We have noticed your country phone number differs from your country of origin. Make sure your country of origin is reflected properly on Plexuss.</div>
					<div className="text-center">
						<button
							onClick={ this._next }
							className="skip-btn button radius">
								Ok
						</button>
					</div>
				</div>
			</div>
			:
			null
	}
}

class SkipModal extends Component {
  constructor(props) {
    super(props)
    this.state = {
      open: false
    }
    this._skip = this._skip.bind(this)
    this._toggle = this._toggle.bind(this)
  }

  _skip(){
		this.props.skip();
	}

	_toggle(){
		this.setState({open: !this.state.open});
	}

	render(){
		var s = this.state;

		return (
			<div 
				className="right"
				onClick={ this._toggle }>
					<span className="cursor">Skip</span>
					{
						s.open ?
						<div className="skip-modal">
							<div className="skip-container">
								<div className="close text-right" onClick={ this._toggle }>x</div>
								<div className="title">By clicking Skip, you are opting out of receiving texts from colleges. You can always opt back in by going to your settings and allowing text notifications.</div>
								<div className="text-center">
									<button
										onClick={ this._skip }
										className="skip-btn button radius">
										Ok
									</button>
								</div>
							</div>
						</div>
						:
						null
					}
			</div>
		);
	}
}

const mapStateToProps = (state) =>{
  return{
    step: state.steps.step6,
    states: state.steps.states,
    unique_countries: state.steps.unique_countries,
    country_list: state.steps.country_list
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
  }
}

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Step4));
