// /Checkout/Payment.js

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'

import PaymentForm from './PaymentForm'
import PaymentMethod from './PaymentMethod'

import { PAYMENT_METHODS } from './constants'
import { setPaymentMethod, editCard } from './../../actions/Checkout'

const Payment = React.createClass({
	getInitialState(){
	    return {
	      formValid: false,
	      submitStripeForm: false,
	      formErrors: {},
	      editing: false,
	      submittedStripe: false,
	    };
	},

	componentDidMount() {
		if(window.location.search) {
			let urlError = new URLSearchParams(window.location.search);
			let cardErrorMsg = urlError.get('error_msg'); 
			if(cardErrorMsg){
				let cardError = { name: 'card_error', msg: cardErrorMsg };
				this._setFormError(cardError);	
			}
		}
	},

	_setEditing(val){
	  	this.setState({editing: val})
	},

	_setFormError(error) {
	  	this.setState((prevState) => ({
	      formErrors: {
	        ...prevState.formErrors,
	        [error.name]: error.msg
	      }
	    }))
	},

 	_setStripeSubmit(val) {
 		this.setState({ submittedStripe: val })
 	},

 	_resetStripeSubmit(){
 		this.setState({ submittedStripe: false, submitStripeForm: false });
 	},

  	_removeFormError(errorKey) {
	  	const state = {
	      formErrors: _.omit(this.state.formErrors, errorKey)
	    };

	    this.setState(state);
  	},

	_handleFormValid(valid) {
		this.setState({formValid: valid})
	},

	_triggerStripeSubmit() {
		this.setState({ submitStripeForm: true })
	},

	_formErrors() {
	    let errors = [];
	    _.each(this.state.formErrors, (err, index) => errors = [...errors, <span key={index} className="message"><span className="dot" />{err}<br /></span>]);

	    return errors;
  	},

	render(){
		let { _checkout: _c } = this.props;
		let { formValid, submitStripeForm } = this.state;

		return (
			<span className="large-6 columns">
				<h2>Choose your <nobr>payment method</nobr></h2>
				<div className={"payment-methods"}>

					{ (this._formErrors().length > 0 && _c.payment_method === 'credit_cards') && 
						<span className="error" role="alert" id="error-box">
				          <img src="/images/checkout/error-light.png" className="exclamation-mark" />
					      <h5 className="error-header">Please fix or enter the following:</h5>
				          { this._formErrors() }
				        </span> 
				    }

					{ PAYMENT_METHODS.map((m) =>
						<PaymentMethod
							setStripeSubmit={this._setStripeSubmit}
							submittedStripe={this.state.submittedStripe}
							setEditing={this._setEditing}
							editing={this.state.editing}
							setFormError={this._setFormError}
							removeFormError={this._removeFormError}
							submitStripeForm={submitStripeForm}
							handleFormValid={this._handleFormValid}
							handleStripeSubmit={this._handleStripeSubmit}
							key={m.value}
							method={m}
							{...this.props} />
					)}
					<PaymentForm editing={this.state.editing} formValid={formValid} triggerStripeSubmit={this._triggerStripeSubmit} method={ _c.payment_method } {...this.props} />
				</div>
	
			</span>
		);
	}
});

const CreditCard = React.createClass({
	_editCard(){
		let { dispatch, card, resetStripeSubmit } = this.props;
		resetStripeSubmit();
		dispatch( editCard(card) );
	},

	render(){
		let { card } = this.props;

		return (
			<div className="credit-card-method">
				<div className={"credit-card "+card.brand.toLowerCase()} />
				<div className="ending">Ending in { card.last4 || '' }</div>
				<div className="edit" onClick={ this._editCard }>{card.brand.toLowerCase()} Edit</div>
			</div>
		);
	}
});

export default Payment
