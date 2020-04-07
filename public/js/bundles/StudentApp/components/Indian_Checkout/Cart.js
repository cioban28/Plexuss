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
		const urlParams = new URLSearchParams(window.location.search);
		const coupon = urlParams.get('coupon');
		return(
			<span className="large-6 columns">
    		<h2>Confirm Plan</h2>
				<div className="payment-box confirm-plan-box">
		      <form className="payment-creditcard-form">
		        <div className="row">
		          <div className="large-12 columns product-price">
		            <strong>Membership</strong>
		            <span>Price</span>
		          </div>
							{
								( _c.cart && _c.cart.length > 0 ) && _c.cart.map((p) =>
                  coupon === "holiday2018"  && <CartItemForHolidays key={p.name} product={p} /> ||
                  coupon === "christmas2018" && <CartItemForChristmas key={p.name} product={p} /> ||
                  coupon === null && <CartItem key={p.name} product={p} />
								)
							}
		          <div className="large-12 columns total-area">
		            <strong>Total</strong>
                {
                  coupon === "holiday2018" && <span></span> ||
                  coupon === "christmas2018" && <span></span> ||
                  coupon === null && <span>${ grand_total }</span>
                }
		          </div>
		            { 
                  coupon === "holiday2018" &&
                    <span className="large-12 columns price-view-mob">
                     <span className="desktop-non grand-price">${ grand_total }</span>
                     <img className="desktop-non strikethrough-styling" src="/images/checkout/strikethrough.png" />
                     <img className="price-tag" src="/images/checkout/large499.png" />
                    </span> ||
                  coupon === "christmas2018" &&
                    <span className="large-12 columns price-view-mob">
                     <span className="desktop-non grand-price">${ grand_total }</span>
                     <img className="desktop-non strikethrough-styling" src="/images/checkout/strikethrough.png" />
                     <img className="price-tag" src="/images/checkout/large249.png" />
                    </span> ||
                  coupon === null && 
		                <span></span>
                }
		        </div>
		      </form>
		      <div className="refund-block">
		        <img src="/images/checkout/Plexuss premium badge gold.png" className="stripe-img" />
		        <span>Guaranteed I-20 Form within 12 Months or <nobr>Receive a Full Refund</nobr></span>
		      </div>
    		</div>
  		</span>
		)
	}
});

const CartItem = React.createClass({
	render(){
		let { product } = this.props;

		return (
      <div className="large-12 columns plexuspremium-imageara">
        <div className="large-8 small-12 columns">
          <span>{ product.confirmation_name }</span>
        </div>
        <div className="large-4 small-8 columns text-right mob-none">
          <span>${ product.price }</span>
        </div>
        <div className="small-12 columns">
	        {product.checkout_features &&
	        	<ul className="premium-promise-list">
		        	{
		        		product.checkout_features.map( (feature, index) => 
		        			<li key={index}>
		        				<img src={feature.image}/>
					          	<div> {feature.description}</div>
				          	</li>
		        	)}
	          </ul>
	        }
        </div>
      </div>
		);
	}
});
const CartItemForHolidays = React.createClass({
	render(){
		let { product } = this.props;

		return (
			<div className="large-12 columns plexuspremium-imageara">
        <div className="large-6 small-12 columns">
          <span>{ product.confirmation_name }</span>
          <img className="" src="/images/checkout/Santa-premium-membership.png" />
        </div>
        <div className="large-6 small-8 columns text-right mob-none">
          <span>${ product.price }</span>
          <img className="strikethrough-styling" src="/images/checkout/strikethrough.png" />
          <span className="discount-percentage"> -10% </span>
        </div>
      </div>
		);
	}
});
const CartItemForChristmas = React.createClass({
  render(){
    let { product } = this.props;

    return (
      <div className="large-12 columns plexuspremium-imageara">
        <div className="large-6 small-12 columns">
          <span>{ product.confirmation_name }</span>
          <img className="" src="/images/checkout/Santa-premium-membership.png" />
        </div>
        <div className="large-6 small-8 columns text-right mob-none">
          <span>${ product.price }</span>
          <img className="strikethrough-styling" src="/images/checkout/strikethrough.png" />
          <span className="discount-percentage christmas-discount"> -50% </span>
        </div>
      </div>
    );
  }
});
