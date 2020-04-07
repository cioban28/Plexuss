// /Checkout/PayWithPayPal.js

import React from 'react'
import selectn from 'selectn'
import ReactSpinner from 'react-spinjs'

import { setPaymentMethod } from './../../actions/Checkout'
import { spinjs_config } from './../../utils/spinjs_config'

export default React.createClass({
	_pay(e){
		e.preventDefault();

		let { _checkout: _c } = this.props,
		prod = _c.cart[0];

		window.location.href = 'https://www.paypal.me/plexuss/'+prod.price;
	},

	render(){
		let { dispatch, _checkout: _c } = this.props;
		
		return (
			<div className="payment-method-form">
				<br />
				<button className="checkout-btn" onClick={ this._pay }>Pay with PayPal</button>
			</div>
		);
	}
});