// Checkout.js - Reducer

import omit from 'lodash/omit'
import find from 'lodash/find'
import forIn from 'lodash/forIn'

const _ = {
	omit: omit,
	find: find,
	forIn: forIn,
}
var init = {
	payment_method: 'credit_cards',
};

export default (state = init, action) => {

	switch( action.type ){

		case '_CHECKOUT:INIT':
		case '_CHECKOUT:PENDING':
		case '_CHECKOUT:EDIT_CARD':
		case '_CHECKOUT:INIT_COUNTRIES':
		case '_CHECKOUT:PAYMENT_SUCCESS':
		case '_CHECKOUT:CHARGED_CUSTOMER':
		case '_CHECKOUT:SET_PAYMENT_METHOD':
		case '_CHECKOUT:RESET_PAYMENT_SUCCESS':
		case '_CHECKOUT:STORE_URL':
			return {...state, ...action.payload};

		case '_CHECKOUT:ADD_TO_CART':
			return {
				...state, 
				cart: [action.payload], // THIS IS TEMPORARY! until we start getting multiple products, then remove this line and uncomment below ****
				// cart: [...state.cart || [], action.payload], // init cart w/product in array, else copy cart state to new array with new product
			};

		case '_CHECKOUT:CLEAR_CART':
			return {
				...state,
				cart: action.payload,
			};

		case '_CHECKOUT:UPDATE_CREDIT_CARD':
			return {
				...state,
				active_credit_card: {...state.active_credit_card || {}, ...action.payload}
			};

		case '_CHECKOUT:READY_TO_CHECKOUT':
			var newState = {...state, ..._.omit(action.payload, 'card')};

			// update active_credit_card with new card data from stripe
			newState.active_credit_card = {...newState.active_credit_card, ...action.payload.card};

			var _card = {...newState.active_credit_card},
				found = null;

			// add active credit _card to list of credit cards
			if( !newState.credit_card_list ) newState.credit_card_list = [_card];
			else{
				// search by _card number b/c there cannot be a duplicate of those
				found = _.find(newState.credit_card_list.slice(), {number: _card.number});

				// if hasn't been created before, add it to list
				if( !found ) newState.credit_card_list = [...newState.credit_card_list, _card];
				else{
					// else find and update this exisiting _card
					newState.credit_card_list = newState.credit_card_list.map((c) => {
						if( c.number === _card.number ) return {...c, ..._card}; // update match with new values
						return c; // else just return non-match
					});
				}
			}

			// then init active credit card w/ empty obj
			newState.active_credit_card = {};

			return newState; 

		case '_CHECKOUT:UPDATE_PACKAGE_WITH_WESTERN_U':
			var urls = action.payload, newState = {...state};

			if( urls ){
				_.forIn(urls, (val, key) => {
					let plan = key.split('__').pop();
					newState.cart = newState.cart.map((prod) => prod.plan === plan ? {...prod, [key]: val} : prod);
				});
			}

			return newState;

		default:
			return state;
	}
	
}