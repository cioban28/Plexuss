// /Checkout/PayWithAmazon.js

import React from 'react'
import selectn from 'selectn'
import ReactSpinner from 'react-spinjs'

import { setPaymentMethod } from './../../actions/Checkout'
import { spinjs_config } from './../../utils/spinjs_config'

export default React.createClass({
	render(){
		let { dispatch, _checkout: _c } = this.props;
		
		return (
			<div className="payment-method-form">
				pay with Amazon
			</div>
		);
	}
});