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
		// window.open(wu_url[name]);

		window.open('https://gpx.globalpay.wu.com/geo-buyer/?locale=en_GB&sellerId=1000127030#!/');
	},

	render(){
		let { dispatch, _checkout: _c } = this.props;
		
		return (
			<div className="accordion-radio active checkout-radio" onClick={ this._pay }>
      		<div className="checkout-btnarea">
        		<a className="button custom-btn checkout-btn">CHECK OUT</a>
        		<img src="/images/checkout/stripe_secure.png" className="stripe-img"/>
      		</div>
    	</div>
		);
	}
});
