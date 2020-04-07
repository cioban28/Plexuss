// /Checkout/PaymentMethod.js

import React from 'react'
import selectn from 'selectn'
import ReactSpinner from 'react-spinjs'

import PayWithCard from './PayWithCard'
import PayWithAmazon from './PayWithAmazon'
import PayWithPayPal from './PayWithPayPal'
import PayWithWesternUnion from './PayWithWesternUnion'
import ReviewAndCheckout from './ReviewAndCheckout'

import { setPaymentMethod } from './../../actions/Checkout'
import { spinjs_config } from './../../utils/spinjs_config'

export default React.createClass({
	_getPaymentField(){
		let { method } = this.props;

		switch( method ){
			case 'pay_pal': return <PayWithPayPal {...this.props} />;
			case 'western_union': return <PayWithWesternUnion {...this.props} />;
			case 'credit_cards' : return <CreditCardSubmit {...this.props} />
			default: return '';
		}
	},

	render(){
		let fields = this._getPaymentField();

		return (
			<div>
			{ fields == '' ? 
				(<div className="accordion-radio active checkout-radio">
					<div className="checkout-btnarea">
			  			<a disabled={true} className="button custom-btn checkout-btn disabled-btn">CHECK OUT</a>
			  			<img src="/images/checkout/stripe_secure.png" className="stripe-img"/>
					</div>
				</div>)
				:
				(<div>{ fields }</div>)
			}
			</div>
		);
	}
});

const CreditCardSubmit = React.createClass({
	render(){
		let { triggerStripeSubmit, formValid, _checkout: _c } = this.props;
		let disabled = !formValid || _c.create_pending || _c.payment_pending;

		return(
			<div className="accordion-radio active checkout-radio" onClick={ triggerStripeSubmit }>
		  		<div className="checkout-btnarea">
		    		<a disabled={ disabled } className={"button custom-btn checkout-btn " + (disabled ? 'disabled-btn' : '')}>
		    			{ formValid && _c.create_pending || _c.payment_pending ? <ReactSpinner config={spinjs_config} /> : 'CHECK OUT' }
		    		</a>
		    		<img src="/images/checkout/stripe_secure.png" className="stripe-img"/>
		  		</div>
		  	</div>
		)
	}

})
