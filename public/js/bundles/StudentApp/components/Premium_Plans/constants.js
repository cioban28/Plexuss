// /Premium_Plans/constants.js

import { PRODUCTS } from './../Checkout/constants'

//add this here to the products from checkout -- basic not used in checkout and do not want to modify what is there

const BASIC = {
		name: 'Basic',
		confirmation_name: 'Plexuss Basic Membership',
		success_msg: 'Plexuss Basic',
		price: 'FREE',
		plan: 'forever',
		price_details: 'with Plexuss',
		total_savings: '',
		choose_route: '',
		change_plan_route: '/premium-plans',
		features: [
			{name: 'Apply to 1', details: 'select university for FREE', savings: 'Savings of $100', tip: 'Apply to 1 select university in the Plexuss network.'},
			{name: 'Review 1 Essay', details: 'from an accepted student to a top US university'},
		],
	};
	
export const CUSTOM = [
	{
		name: 'Custom',
		confirmation_name: 'Plexuss Custom Membership',
		success_msg: 'Plexuss Custom',
		price: 10,
		plan: 'custom',
		price_details: 'with Plexuss',
		total_savings: '',
		choose_route: '/checkout/monthly',
		change_plan_route: '/checkout/monthly',
		features: [
			{name: 'Includes 1 additional Application to select universities in the Plexuss network '},
			{name: 'Includes 1 additional Essay view from an accepted student to a top US university '}
		],
	},
	{
		name: 'Unlimited',
		confirmation_name: 'Plexuss Unlimited Membership',
		success_msg: 'Plexuss Unlimited',
		price: 499,
		plan: 'forever',
		price_details: 'with Plexuss',
		total_savings: '',
		choose_route: '/checkout/onetime_unlimited',
		change_plan_route: '/checkout/onetime_unlimited',
		features: [
			{name: 'Apply to Unlimited', details: 'select universities', tip : "Apply to an Unlimited number of select universities in Plexuss' network"},
			{name: '1-on-1 meetings with a professional advisor'},
			{name: 'Review Unlimited Essays', details: 'from accepted students to top US universities'}
		],
	}

];


export const premTip = {
		backgroundColor: '#fff',
		color: '#797979',
		fontSize: '13px',
		padding: '15px',
		marginLeft: '-50px',
		marginTop: '6px',
		maxWidth: '250px'
	};



(function(){

	let plan_type = document.getElementById('_StudentApp_Component').getAttribute('data-premium');
	
	switch(plan_type){
		case '':
			BASIC.current = true;
			break;
		case 'onetime':
			PRODUCTS.premium.current = true;
			break;
		case 'onetime_plus':
			PRODUCTS['premium-plus'].current = true;
			break;
		case 'monthly':
			CUSTOM[0].current = true;
			PRODUCTS['onetime_unlimited'].current = true;
			break;
		case 'onetime_unlimited':
			CUSTOM[1].current = true;
			PRODUCTS['monthly'].current = true;
			break;
	}	
	

})();

export const PLANS = [ BASIC , PRODUCTS.premium, PRODUCTS['premium-plus']];