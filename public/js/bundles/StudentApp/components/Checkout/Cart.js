// /Checkout/PaymentSuccess.js

import React from 'react'
import selectn from 'selectn'
import { Link } from 'react-router'

import { PRODUCTS } from './constants'
import { addToCart } from './../../actions/Checkout'
import { updateCartWithWesternUnionURLs } from './../../actions/Checkout'

/*****
	* For single listed products, route param name is used to get the product from constants.js	
		* these have their own routes for easy access/adding to cart

	* For multiple products, they are added to cart and mapped through each
		* have no route params b/c these products are selected from a wide range of options that we can't know for sure they're choosing

	* It is possible to have both a routed Product and products added to the cart
		* Be careful of duplicate products though
*****/

export default React.createClass({
	componentWillMount(){
		let { dispatch, params, _user: _u } = this.props;

		// if user is directed to checkout w/ a sub-path (which should be a product), then immediately add that prod to cart
		if( selectn('product', params) ){
			if( _u.init_done ){
				let plan_name = 'western_union__'+PRODUCTS[params.product].plan;
				let plan = _.pick(_u, plan_name);

				PRODUCTS[params.product][plan_name] = plan[plan_name];
			}

			dispatch( addToCart(PRODUCTS[params.product]) );
		}
	},

	_getCartTotal(){
		let { _checkout: _c } = this.props,
			total = 0;

		// if there are any items in the cart, add that products price to total
		if( _c.cart && _c.cart.length > 0 ) _.each(_c.cart, (item) => total += item.price );

		return total;
	},

	render(){
		let { route, dispatch, params, _checkout: _c } = this.props,
			grand_total = this._getCartTotal();
		
		return (
			<div className="cart-review-container checkout-container">
				<div className="header">Confirm Plan</div>

				{ ( _c.cart && _c.cart.length > 0 ) && _c.cart.map((p) => <CartItem key={p.name} product={p} />) }

				<div className="grand-total clearfix">
					<div className="left">Grand Total</div>
					<div className="right">${ grand_total }</div>
				</div>
			</div>
		);
	}
});

const CartItem = React.createClass({
	render(){
		let { product } = this.props;

		return (
			<div className="cart-item">
				<div className="clearfix">
					<div className="left">{ product.confirmation_name }</div>
					<div className="right">${ product.price }</div>
				</div>
				{/*<Link to={ product.change_plan_route }>Change Plan</Link>*/}
			</div>
		);
	}
});
