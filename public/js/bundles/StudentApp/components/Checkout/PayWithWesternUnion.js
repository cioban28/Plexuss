// /Checkout/PayWithWesternUnion.js

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

		let name = 'western_union__'+prod.plan;
		let wu_url = _.pick(prod, name);
		// console.log('url: ', wu_url[name]);

		window.open(wu_url[name]);
	},

	render(){
		let { dispatch, _checkout: _c } = this.props;
		
		return (
			<div className="payment-method-form">
				<br />
				<button 
					disabled={false}
					className="checkout-btn" 
					onClick={ this._pay }>Pay with Western Union</button>
			</div>
		);
	}
});