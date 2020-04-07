import React from 'react'
import {
  injectStripe,
  CardNumberElement,
  CardExpiryElement,
  CardCVCElement,
  PostalCodeElement,
} from 'react-stripe-elements'
import selectn from 'selectn'
import './styles.scss'

import { getCountries, updateCreditCard, createOrUpdateCustomer, chargeCustomer } from './../../actions/Checkout'
import { COUNTRIES_WITHOUT_POSTALCODES } from './constants'

const PayWithCardFields = React.createClass({

  getInitialState(){
    return {
      country_options: [],
      error_messages: {},
      untouched: ['cardNumber', 'cardExpiry', 'cardCvc', 'name', 'country', 'postalCode'],
      no_postal: false,
      postal: '',
    };
  },

  componentWillMount(){
    let { dispatch, _checkout: _c } = this.props;

    if( !_c.init_countries ) dispatch( getCountries() );
    else this._buildCountryOptions( _c.countries );
  },

  componentWillReceiveProps(np){
    let { dispatch, _checkout: _c, submitStripeForm, submittedStripe, editing, setStripeSubmit } = this.props;
    const allowedToProceed = (np.submitStripeForm && !np.submittedStripe)

    // if next state is different from this state AND next state saved is true, trigger toastr
    if( _c.init_countries !== np._checkout.init_countries && np._checkout.init_countries ){
      this._buildCountryOptions(np._checkout.countries);
    }

    if( allowedToProceed ) {
      setStripeSubmit(true);
      this._handleStripeSubmit();
    }
  },

  _handleStripeSubmit(){
    let { _checkout: _c } = this.props,
      form = {..._c.active_credit_card};
    this.props.stripe.createToken(form).then((response) => {
      this._stripeResponseHandler(response)
    }).catch((error) => {
        this.setState({
          stripeErr: true,
          stripeErrMsg: selectn('error.message', error) || 'There was an error processing your card with Stripe.',
      });
    });
  },

  _stripeResponseHandler(res){
    const urlParams = new URLSearchParams(window.location.search);
    const coupon = urlParams.get('coupon');
    let { dispatch, _checkout: _c } = this.props,
      updated_card = {
        card: {
          ..._c.active_credit_card,
          ...res.token.card,
          stripeToken: res.token.id,
          type: res.token.type,
        },
        prod: _c.cart[0] ? { ..._c.cart[0], coupon: coupon } : null,
      }

    this.setState({stripeErr: false});
    dispatch( createOrUpdateCustomer(updated_card) );
  },

  _buildCountryOptions(countries){
    var opts = [<option key="disabled" disabled="disabled" value="">Choose Country</option>];

    _.each(countries, (c) => opts = [...opts, <option key={c.id} value={c.id}>{c.country_name}</option>]);

    this.setState({country_options: opts});
  },

  _update(e){
    let { dispatch, _checkout: _c } = this.props,
      name = e.target.getAttribute('name'),
      value = e.target.value;

    if (e.target.getAttribute('name') == 'country'){
      if(COUNTRIES_WITHOUT_POSTALCODES.includes(_c.countries[e.target.value-1].country_name)) {
        this.setState({no_postal: true});
      }else{
        this.setState({no_postal: false});
      }
    }
    
    let card = {[name]: value},
      valid = this._preValidation(e);

    if( valid ) dispatch( updateCreditCard(card) );
  },

  _preValidation(e){
    let name = e.target.getAttribute('name'),
      value = e.target.value;

    let valid = this._validate(name, value, true);

    return valid;
  },

  _blurValidation(e){
    let name = e.target.getAttribute('name'),
      value = e.target.value;

      //remove if you find a way to trigger validate postal validate 
    if(e.target.value != ''){
      return;
    }

    this._validate(name, value);
  },

  _validate(name, value, pre){
    let { handleFormValid, removeFormError, setFormError } = this.props;

    var valid = false, msg = '';

    switch( name ){
      case 'name':
        if( value ) valid = true;
        if( pre && !value ) valid = true;
        msg = 'Name cannot be left empty.';
        break;

      case 'country':
        if( value ) valid = true;
        if( pre && !value ) valid = true;
        msg = 'Country can not be left empty';
        break;

      default:
        valid = true;
        break;
    }

    this.setState((prevState) => ({untouched: prevState.untouched.filter(field => field !== name)}))

    if (!valid) {
      this.setState((prevState) => ({
        error_messages: {
          ...prevState.error_messages,
          [name]: msg
        }
      }))

      setFormError({name: name, msg: msg})
      handleFormValid(false);
    }
    else {
      const state = {
        error_messages: _.omit(this.state.error_messages, name)
      };

      this.setState(state, () => {
        if (Object.keys(this.state.error_messages).length === 0 && this.state.untouched.length === 0) {
          handleFormValid(true);
        }
      });
      removeFormError(name)
    }

    return valid;
  },

  _validateStripeElement(stripeElementResponse) {
    let { handleFormValid, setFormError, removeFormError } = this.props;
    let name = stripeElementResponse.elementType, msg = '';

    if(stripeElementResponse.elementType == 'postalCode' && stripeElementResponse.value != '00000' ){
      this.setState({postal: stripeElementResponse.value});
    }

    removeFormError('card_error');

    if (stripeElementResponse.error) msg = stripeElementResponse.error.message || '';

    this.setState((prevState) => ({untouched: prevState.untouched.filter(field => field !== name)}))

    if (!stripeElementResponse.complete) {
      if (msg.length > 0) {
        this.setState((prevState) => ({
          error_messages: {
            ...prevState.error_messages,
            [name]: msg
          }
        }))
        setFormError({name: name, msg: msg})
      }

      handleFormValid(false);
    }
    else {
      const state = {
        error_messages: _.omit(this.state.error_messages, name)
      };

      this.setState(state, () => {
        if (Object.keys(this.state.error_messages).length === 0 && this.state.untouched.length === 0) {
          handleFormValid(true);
        }
      });
      removeFormError(name)
    }
  },

  render() {
    let { country_options } = this.state;
    let { dispatch, _checkout: _c, handleStripeSubmit, setFormError, removeFormError } = this.props;

    const stripeFieldStyles = {
      base: {
        color: '#32325D',
        fontWeight: 500,
        fontFamily: 'Source Code Pro, Consolas, Menlo, monospace',
        letterSpacing: '0.025em',
        fontSize: '16px',
        fontSmoothing: 'antialiased',

        '::placeholder': {
          color: '#CFD7DF',
        },
        ':-webkit-autofill': {
          color: '#e39f48',
        },
        ':disabled': {
          color: '#555555',
        },
      },
      invalid: {
        color: '#ea5959',

        '::placeholder': {
          color: '#CFD7DF',
        },
      },
    };

    const errorStyles = {
      // backgroundColor: '#fcf2f3',
      // borderRadius : '6px',
      // borderWidth: '1px',
      // borderStyle: 'solid',
      // borderColor: '#ea5959',
    }

    return(
      <form>
        <div className="checkout stripe-wrapper hiders pt30" >
          <input type="hidden" name="token" />
          <div data-locale-reversible>
            <div className="row">
              <div className="field">
                <label htmlFor="stripe-wrapper-card-number" data-tid="elements_checkouts.form.card_number_label">
                  CREDIT CARD NUMBER
                  {this.state.error_messages['cardNumber'] && <span><img src="/images/checkout/error-red.png" className="error-icon"/></span> }
                </label>
                <div style={(this.state.error_messages['cardNumber']) && errorStyles}> <CardNumberElement onChange={this._validateStripeElement} style={stripeFieldStyles} /> </div>
              </div>
            </div>
            <div className="custom-row">
              <div className="field large-6 custom-col">
                <label htmlFor="stripe-wrapper-card-expiry" data-tid="elements_checkouts.form.card_expiry_label">
                  EXPIRATION DATE
                  {this.state.error_messages['cardExpiry'] && <span><img src="/images/checkout/error-red.png" className="error-icon"/></span> }
                </label>
                <div style={(this.state.error_messages['cardExpiry']) && errorStyles}> <CardExpiryElement onChange={this._validateStripeElement} style={stripeFieldStyles} /> </div>
              </div>
              <div className="field large-6 custom-col">
                <label htmlFor="stripe-wrapper-card-cvc" data-tid="elements_checkouts.form.card_cvc_label">
                  CVC CODE
                  {this.state.error_messages['cardCvc'] && <span><img src="/images/checkout/error-red.png" className="error-icon"/></span> }
                </label>
                <div style={(this.state.error_messages['cardCvc']) && errorStyles}> <CardCVCElement onChange={this._validateStripeElement} style={stripeFieldStyles} /></div>
              </div>
            </div>
            <div className="row" data-locale-reversible>
              <div className="field">
                <label htmlFor="stripe-wrapper-name" data-tid="elements_checkouts.form.name_label">
                  NAME ON CARD
                  {this.state.error_messages['name'] && <span><img src="/images/checkout/error-red.png" className="error-icon"/></span> }
                </label>
                <input
                  id="stripe-wrapper-name"
                  data-tid="elements_checkouts.form.name_placeholder"
                  onChange={ this._update }
                  onBlur={ this._blurValidation }
                  value={ selectn('active_credit_card.name', _c) || '' }
                  className='input empty'
                  placeholder="John Doe"
                  name="name"
                  type="text"
                  style={(this.state.error_messages['name']) && errorStyles}
                  required="" />
              </div>
            </div>
            <div className="custom-row" data-locale-reversible>
              <div className="field large-6 custom-col">
                <label htmlFor="combobox" data-tid="elements_checkouts.form.country_label">
                  BILLING COUNTRY
                  {this.state.error_messages['country'] && <span><img src="/images/checkout/error-red.png" className="error-icon"/></span> }
                </label>
                <select
                  name="country"
                  className="select"
                  id="combobox"
                  placeholder="Choose Country"
                  onChange={ this._update }
                  value={ selectn('active_credit_card.country', _c) || '' }
                  onBlur={ this._blurValidation }>
                  { country_options }
                </select>
              </div> 
              <div className="field large-6 custom-col">
                <label htmlFor="stripe-wrapper-zip" data-tid="elements_checkouts.form.postal_code_label">
                  BILLING POSTAL CODE
                  {this.state.error_messages['postalCode'] && <span><img src="/images/checkout/error-red.png" className="error-icon"/></span> }
                </label>
                {/*this.state.no_postal ? 
                  <div style={{borderRadius: '6px', backgroundColor:'#ddd'}}> <PostalCodeElement disabled={true} value={'00000'} onChange={this._validateStripeElement} style={stripeFieldStyles} /> </div>
                  :
                  <PostalCodeElement onChange={this._validateStripeElement} style={stripeFieldStyles} />
                */}
                <div style={{borderRadius: '6px', backgroundColor: this.state.no_postal ? '#ddd' : '#fff'}}> <PostalCodeElement id="stripe-postal-code" disabled={this.state.no_postal} value={this.state.no_postal ? '00000' :this.state.postal} onChange={this._validateStripeElement} style={stripeFieldStyles} /> </div>
                  
              </div>
            </div>
          </div>
        </div>
      </form>
    )
  }
})

export default injectStripe(PayWithCardFields)
