// /Checkout/PaymentSuccess.js

import React from 'react'
import selectn from 'selectn'
import { Link } from 'react-router'
import { connect } from 'react-redux'
import Script from 'react-load-script'
import DocumentTitle from 'react-document-title'

const PaymentSuccess = React.createClass({
	_initSkype(){
		Skype.ui({
			name: "dropdown",
			element: "SkypeButton_Call_premium_156_1_1",
			participants: ["live:premium_156"],
			imageSize: 24
		});
	},

	_initSkypeError(err){
		console.log('skype error: ', err);
	},

	render(){
		let { _checkout: _c, _user } = this.props;
		
		return (
			<DocumentTitle title="Plexuss | Payment Failed">
				<div id="_Checkout">

					<div className="checkout-inner">

						<div className="checkout-container">

							<div className="success-container">

								<h3>Oops!</h3>

								<div>Unfortunately, we were unable to process your payment. Please feel free to try again or you can contact us on Skype by using the button below.</div>

								<div className="form-actions success">
									<Link to={'/premium-plans'} className="checkout-success">Return to Choose Plan page</Link>
								</div>

							</div>

						</div>			

						<Script
							url="https://secure.skypeassets.com/i/scom/js/skype-uri.js"
							onLoad={ this._initSkype }
							onError={ this._initSkypeError } />

						<div className="text-right">
							<div className="skype-wrapper">
								<div id="SkypeButton_Call_premium_156_1_1" />
							</div>
						</div>

					</div>

				</div>
			</DocumentTitle>
			
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		_user: state._user,
		_checkout: state._checkout,
	};
};

export default connect(mapStateToProps)(PaymentSuccess);