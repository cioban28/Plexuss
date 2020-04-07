// /Checkout/index.js

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import DocumentTitle from 'react-document-title'
import { browserHistory } from 'react-router'

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
		let { dispatch, _user: _u, _checkout: _c } = this.props,
			{ _user: _nu } = np;

		if( _u.init_done !== _nu.init_done && _nu.init_done ){
			var wu_urls = _.pickBy(_nu, (v, k) => k.includes('western_union_'));

			// if western U urls were passed with studentData then proceed to adding WU urls to packages
			if( !_.isEmpty(wu_urls) ) dispatch( updateCartWithWesternUnionURLs(wu_urls) );
		}

	    if( _c.payment_success !== np._checkout.payment_success && np._checkout.payment_success ){

		  window.location.replace('/congratulations-page');
	    }
	},

	render(){
		let { children, _checkout: _c } = this.props;

		return (
			<DocumentTitle title="Plexuss | Checkout">
				<div className="payment-background">
		  			<div className="payment-method-block">

						{/* only show cart when payment has not gone through */}
						{ !selectn('payment_success', _c) && <Cart {...this.props} /> }

						{/* only show payment form when payment has not gone throught */}
						{ !selectn('payment_success', _c) && <Payment {...this.props} /> }

				  	</div>
				  	<div className="payment-footer small-12 columns">
				  		<div className="small-12 large-1 columns footer-text">
				  			<img src="/images/logo-plexuss-gray.png" className="footer-logo"/>
				  		</div>
				  		<div className="small-12 large-2 columns footer-text">
				  			 © 2019 Plexuss.com
				  		</div>
				  		<div className="small-12 large-3 columns footer-text">
				  			<a href="/terms-of-service">Terms of Service</a> | 
				  			<a href="/privacy-policy"> <nobr>Privacy Policy</nobr></a>
						</div>
				  		<div className="small-12 large-3 columns footer-text footer-text2">
				  			<span style={{marginRight: '10px', marginLeft: '10px'}}> Connect with us </span> 
				  			<nobr>
				  			<a target="_blank" href="http://www.linkedin.com/company/plexuss-com">        <img src="/images/icon-linkedin.png" className="footer-icon" /> </a>
				  			<a target="_blank" href="http://www.twitter.com/plexussupdates"> <img src="/images/icon-twitter.png" className="footer-icon" /> </a>
				  			<a target="_blank" href="https://www.facebook.com/pages/Plexusscom/465631496904278"> <img src="/images/icon-facebook.png" className="footer-icon" /> </a>
				  			<a target="_blank" href="https://www.youtube.com/channel/UCLBI8NqybOCZYmjxq8f6P1Q"> <img src="/images/icon-youtube.png" className="footer-icon" /> </a>
							</nobr>
						</div>
				  		<div className="small-12 large-3 columns footer-text">
				  			Any questions? Email us at <span className="footer-text2">support@plexuss.com</span>
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

export default connect(mapStateToProps)(Checkout);
