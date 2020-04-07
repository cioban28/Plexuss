// /Checkout/PayWithCard.js

import React from 'react'
import selectn from 'selectn'
import ReactSpinner from 'react-spinjs'
import Tooltip from './../common/Tooltip'

import { spinjs_config } from './../../utils/spinjs_config'
import { createOrUpdateCustomer, getCountries, readyToCheckout, updateCreditCard } from './../../actions/Checkout'
import { StripeProvider, Elements } from 'react-stripe-elements'
import PayWithCardFields from './PayWithCardFields'

const MONTHS = 12;
const YEARS = 20;

export default React.createClass({

	render(){
		let { dispatch, handleStripeSubmit, _checkout: _c } = this.props;
		
		return (
			<Elements>
		        <PayWithCardFields {...this.props} />
		    </Elements>
		);
	}
});
