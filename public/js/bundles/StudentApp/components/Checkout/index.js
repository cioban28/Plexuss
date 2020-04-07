// /Checkout/index.js

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import DocumentTitle from 'react-document-title'

import Cart from './Cart'
import Payment from './Payment'
import PaymentSuccess from './PaymentSuccess'
import ReviewAndCheckout from './ReviewAndCheckout'

import { getStudentData} from './../../actions/User'
import { updateCartWithWesternUnionURLs,  storeUrl } from './../../actions/Checkout'

import './styles.scss'

const Checkout = React.createClass({
	componentWillMount(){
		let { dispatch, _user } = this.props;
		if( !_user.init_done ) dispatch( getStudentData() );

		let query = window.location.href.split('?')[1];

		if(query){

			
			let paramsToks = query.split('&');
			let params = {};

			for(let i = 0; i < paramsToks.length; i++){
				params[paramsToks[i].split('=')[0]] = paramsToks[i].split('=')[1];
			}

			let url = params['cameFrom'];
			if(url !== null && typeof url !== "undefined"){
				dispatch(storeUrl(url));
			}  
		}
	},

	componentWillReceiveProps(np){
		let { dispatch, _user: _u } = this.props,
			{ _user: _nu } = np; 

		if( _u.init_done !== _nu.init_done && _nu.init_done ){
			var wu_urls = _.pickBy(_nu, (v, k) => k.includes('western_union_'));

			// if western U urls were passed with studentData then proceed to adding WU urls to packages
			if( !_.isEmpty(wu_urls) ) dispatch( updateCartWithWesternUnionURLs(wu_urls) );
		}
	},

	render(){
		let { children, _checkout: _c } = this.props;	
		
		return (
			<DocumentTitle title="Plexuss | Checkout">
				<div id="_Checkout">

					<div className="checkout-inner">

						{/* only show cart when payment has not gone through */}
						{ !selectn('payment_success', _c) && <Cart {...this.props} /> }

						{/* only show payment form when payment has not gone throught */}
						{ !selectn('payment_success', _c) && <Payment {...this.props} /> }

						{/* show when payment form filled out and ready to checkout and submit payment */}
						{ selectn('ready_to_checkout', _c) && <ReviewAndCheckout {...this.props} /> }

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

export default connect(mapStateToProps)(Checkout);
