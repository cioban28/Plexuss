// /Checkout/ReviewAndCheckout.js

import React from 'react'
import ReactSpinner from 'react-spinjs'
import { browserHistory } from 'react-router'

import { chargeCustomer, resetPaymentSuccess } from './../../actions/Checkout'
import { spinjs_config } from './../../utils/spinjs_config'

export default React.createClass({
	getInitialState(){
		return {
			read: true,
		};
	},

	componentWillReceiveProps(np){
		let { dispatch, _checkout: _c } = this.props;

		if( _c.payment_success !== np._checkout.payment_success && np._checkout.payment_success ){
			browserHistory.push('/payment-success');
		}
	},

	_checkout(e){
		e.preventDefault();
		let { dispatch, _checkout: _c } = this.props,
			prod = _c.cart[0] || null;

		dispatch( chargeCustomer(prod, true) );
	},

	render(){
		let { dispatch, _checkout: _c } = this.props,
			{ read } = this.state;

		return (
			<div className="payment-method-form">

				<div className="checkout-container">
					<div className="header">Review Order</div>

					<label>
						<input
							onChange={ () => this.setState({read: !read}) }
							checked={ read }
							type="checkbox" />

						By placing this order, you agree to our <a href="/terms-of-service" target="_blank">terms and conditions</a>
					</label>

					<div className="form-actions">
						<button
							onClick={ this._checkout }
							className="checkout-btn"
							disabled={ !read || _c.payment_pending }>
								{ _c.payment_pending ? <div className="spin-wrap"><ReactSpinner config={spinjs_config} /></div> : 'Checkout' }
						</button>

						<div className="with-icon ssl" />
					</div>

				</div>

			</div>
		);
	}
});
