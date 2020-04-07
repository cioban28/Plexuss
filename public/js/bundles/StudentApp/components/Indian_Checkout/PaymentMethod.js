// /Checkout/PaymentMethod.js

import React from 'react'
import selectn from 'selectn'
import ReactSpinner from 'react-spinjs'

import { setPaymentMethod } from './../../actions/Checkout'
import { spinjs_config } from './../../utils/spinjs_config'
import PayWithCard from './PayWithCard'

export default React.createClass({
	getInitialState: function() {
    return { touched: false }
  },

	render(){
		let { dispatch, _checkout: _c, method } = this.props;

		const handleOnChange = (value) => {
			dispatch( setPaymentMethod(method.value) )
			this.setState({ touched: true })
		}

		const showCreditCardForm = _c.payment_method === 'credit_cards' && this.state.touched && method.value === 'credit_cards'
		let accordionClasses = ['accordion-radio2', 'accordion-radio']
		
		if (_c.payment_method === method.value && this.state.touched)
			accordionClasses.push('active')
		else
			accordionClasses.push('in-active')

		accordionClasses = accordionClasses.join(' ')

		return (
			<span>
				<div className={accordionClasses}>
	    		    <div className="payment-boader-bottom">
	          		<div className="payment-textbox">
	            		<div className="radio-box">
	              		<input
	              		 	id={ method.value }
											value={ method.value }
											checked={ _c.payment_method === method.value && this.state.touched }
											onChange={ () => { handleOnChange(method.value) } }
											type="radio" />
	              		<label htmlFor={ method.value}>{ method.name }</label>
	            		</div>
	            		
	            		<div className="payment-textarea">
	              		<p>{ method.description }</p>
	            		</div>
	          		</div>
	          		
	          		<div className="payment-image-icon img-opacity">
	          			<ul className="card-list">
	          				{ method.images.map((img, index) => <img key={index} className={img.className} src={img.src} />) }
	          			</ul>
	          		</div>
	        		</div>
	  		</div>

	  		{ showCreditCardForm && <PayWithCard {...this.props} /> }
	  	</span>
		);
	}
});
