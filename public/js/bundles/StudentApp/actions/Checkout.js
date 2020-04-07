// Checkout.js - actions

import axios from 'axios'
import { Route, Redirect } from 'react-router'

export const addToCart = (product) => {
	return {
		type: '_CHECKOUT:ADD_TO_CART',
		payload: product,
	}
}

export const storeUrl = (url) => {
	return{
		type: '_CHECKOUT:STORE_URL',
		payload: {cameFrom: url}
	}
}

export const clearCart = () => {
	return {
		type: '_CHECKOUT:CLEAR_CART',
		payload: [],
	}
}

export const setPaymentMethod = (method) => {
	return {
		type: '_CHECKOUT:SET_PAYMENT_METHOD',
		payload: {payment_method: method},
	}
}

export const updateCreditCard = (field = {}) => {
	return {
		type: '_CHECKOUT:UPDATE_CREDIT_CARD',
		payload: field,
	}
}

export const editCard = (card) => {
	return {
		type: '_CHECKOUT:EDIT_CARD',
		payload: {
			active_credit_card: card,
			create_pending: false,
			ready_to_checkout: false,
		}
	}
}

export const updateCartWithWesternUnionURLs = (westernUnionUrls) => {
	return {
		type: '_CHECKOUT:UPDATE_PACKAGE_WITH_WESTERN_U',
		payload: westernUnionUrls
	}
}

export const resetPaymentSuccess = () => {
	return {
		type: '_CHECKOUT:RESET_PAYMENT_SUCCESS',
		payload: {
			payment_success: true,
			ready_to_checkout: false,
		}
	}
}

export const createOrUpdateCustomer = (card) => {
	return (dispatch) => {
		dispatch({
	 		type: '_CHECKOUT:PENDING',
	 		payload: {create_pending: true},
	 	});

		axios.post('/createCustomer', card.card)
			 .then((response) => {
			 	console.log(response.data);
			 	dispatch({
					type: '_CHECKOUT:READY_TO_CHECKOUT',
					payload: {
						ready_to_checkout: true,
						create_pending: false,
						card: card.card,
					}
				});

				if(response.data.status == 'success'){
					dispatch( chargeCustomer(card.prod) );
				}else{
					
					var url = window.location.href.split('?')[0];
					url += '?error_msg=' + response.data.error_msg;
					window.location.href = url;
					//print error message, then reload page to reset token
					// location.href('/checkout/premium?error_msg='+response.data.error_msg);
					// location.reload(false);
					// alert(response.data.error_msg);
				}
			 })
			 .catch((err) => {
				console.log('error: ', err);
			 	dispatch({
			 		type: '_CHECKOUT:PENDING',
			 		payload: {create_pending: false},
			 	});
			 });
	}
}

export const chargeCustomer = (prod) => {
	return (dispatch) => {
		dispatch({
	 		type: '_CHECKOUT:PENDING',
	 		payload: {payment_pending: true},
	 	});

		axios.post('/chargeCustomer', prod)
			 .then((response) => {
			 	dispatch({
					type: '_CHECKOUT:CHARGED_CUSTOMER',
					payload: {
						payment_pending: false,
						payment_success: true,
						payment_msg: ''
					}
				});
			 })
			 .catch((err) => {
				console.log('error: ', err);
			 	dispatch({
			 		type: '_CHECKOUT:PENDING',
			 		payload: {payment_pending: false},
			 	});
			 });
	}
}

export const getCountries = () => {
	return (dispatch) => {
		dispatch({
	 		type: '_CHECKOUT:PENDING',
	 		payload: {pending: true},
	 	});

		axios.get('/ajax/getAllCountries')
			 .then((response) => {
			 	dispatch({
					type: '_CHECKOUT:INIT_COUNTRIES',
					payload: {
						countries: response.data,
						init_countries: true,
					}
				});
			 })
			 .catch((err) => {
			 	console.log('error: ', err);
			 	dispatch({
			 		type: '_CHECKOUT:PENDING',
			 		payload: {pending: false},
			 	});
			 });
	}
}
