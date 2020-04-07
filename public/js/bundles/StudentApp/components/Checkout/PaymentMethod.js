// /Checkout/PaymentMethod.js

import React from 'react'
import selectn from 'selectn'
import ReactSpinner from 'react-spinjs'

import { setPaymentMethod } from './../../actions/Checkout'
import { spinjs_config } from './../../utils/spinjs_config'

export default React.createClass({
	render(){
		let { dispatch, _checkout: _c, method } = this.props;

		return (
			<div className="method">
				<label htmlFor={ method.value }>
					<input
						id={ method.value }
						className={ method.value }
						value={ method.value }
						checked={ _c.payment_method === method.value }
						onChange={ () => dispatch( setPaymentMethod(method.value) ) }
						type="radio" />

					{ method.icons.map((ic) => <div key={ic} className={"credit-card "+ic} />) }
				</label>
			</div>
		);
	}
});