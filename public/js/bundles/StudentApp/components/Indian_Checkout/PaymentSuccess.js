
import React from 'react'
import selectn from 'selectn'
import { Link } from 'react-router'
import { connect } from 'react-redux'
import DocumentTitle from 'react-document-title'

import { PREMIUM_PLANS } from './constants'
import { updateUserInfo } from './../../actions/User'
import { resetPaymentSuccess } from './../../actions/Checkout'

const PaymentSuccess = React.createClass({
	componentWillMount(){
		let { dispatch, _checkout: _c } = this.props,
			prod = selectn('cart[0]', _c);

		if( prod ){
			document.getElementById("_premiumUserBadge").className = 'premium-user-icon ' + prod.plan;

			dispatch( updateUserInfo({
				premium_user_level_1: 1,
				premium_user_plan: prod.name.toLowerCase(),
				premium_user_type: prod.plan,
			}) );

			dispatch( resetPaymentSuccess() );
		}
	},

	_getProd(){
		let { _checkout: _c, params } = this.props,
			plan = null;

		// if route has param, use param id to get product from cart
		// else get first prod from cart
		if( selectn('plan', params) ) plan = _.find(PREMIUM_PLANS, {plan: params.plan});
		else plan = selectn('cart[0]', _c);

		return plan || {};
	},

	render(){
		let { _checkout: _c, _user } = this.props,
			prod = this._getProd();
		
		return (
			<DocumentTitle title="Plexuss | Checkout Success">
				<div id="_Checkout">

					<div className="checkout-inner">

						<div className="checkout-container">

							<div className="success-container">

								<h3>Welcome to Plexuss!</h3>

								<div className={"badge "+(prod.name || '')} />

								<div>Congratulations on becoming a <b>{ prod.success_msg || '' }</b> member! A confirmation email has been sent to { selectn('email', _user) } for your records.</div>
								<br />
								<div>Click "Get Started" to complete your Profile! As soon as you finish, you will be able to apply to select colleges for FREE, get 1-on-1 support, and read essays from previously-admitted students. Prepare to stand out to colleges!</div>

								<div className="form-actions success">
									<a href={_c.cameFrom} className="checkout-success">Get Started</a>
								</div>

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