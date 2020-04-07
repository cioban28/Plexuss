// /Checkout/Payment.js

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'

import PaymentForm from './PaymentForm'
import PaymentMethod from './PaymentMethod'

import { PAYMENT_METHODS } from './constants'
import { setPaymentMethod, editCard } from './../../actions/Checkout'

export default React.createClass({
	render(){
		let { _checkout: _c } = this.props;
		
		return (
			<div id="_PaymentMethod" className="checkout-container">

				<div className="header">Choose Payment Method</div>

				<div className={"payment-methods "+(selectn('ready_to_checkout', _c) ? 'hide' : '')}>
					{ PAYMENT_METHODS.map((m) => <PaymentMethod key={m.value} method={m} {...this.props} />) }
				</div>

				<div className={ selectn('ready_to_checkout', _c) ? 'ready_to_checkout' : 'hide' }>
					{ _c.credit_card_list && _c.credit_card_list.map((c) => <CreditCard key={c.number} card={c} {...this.props} />) }
				</div>

				<PaymentForm method={ _c.payment_method } {...this.props} />

			</div>
		);
	}
});

const CreditCard = React.createClass({
	_editCard(){
		let { dispatch, card } = this.props;
		dispatch( editCard(card) );
	},

	render(){
		let { card } = this.props;

		return (
			<div className="credit-card-method">
				<div className={"credit-card "+card.brand.toLowerCase()} />
				<div className="ending">Ending in { card.last4 || '' }</div>
				<div className="edit" onClick={ this._editCard }>Edit</div>
			</div>
		);
	}
});