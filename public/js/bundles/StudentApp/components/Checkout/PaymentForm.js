// /Checkout/PaymentMethod.js

import React from 'react'
import selectn from 'selectn'
import ReactSpinner from 'react-spinjs'

import PayWithCard from './PayWithCard'
import PayWithAmazon from './PayWithAmazon'
import PayWithPayPal from './PayWithPayPal'
import PayWithWesternUnion from './PayWithWesternUnion'

import { setPaymentMethod } from './../../actions/Checkout'
import { spinjs_config } from './../../utils/spinjs_config'

export default React.createClass({
	_getPaymentField(){
		let { method } = this.props;

		switch( method ){
			case 'credit_cards': return <PayWithCard {...this.props} />;
			case 'amazon_pay': return <PayWithAmazon {...this.props} />;
			case 'pay_pal': return <PayWithPayPal {...this.props} />;
			case 'western_union': return <PayWithWesternUnion {...this.props} />;
			default: return '';
		}
	},

	render(){
		let fields = this._getPaymentField();

		return (
			<div>{ fields }</div>
		);
	}
});