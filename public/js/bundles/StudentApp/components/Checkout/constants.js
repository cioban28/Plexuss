// /Checkout/constants.js

export const PRODUCTS = {
	
	basic : {
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
	},
	
	monthly: {
		name: 'Custom',
		confirmation_name: 'Plexuss Custom Membership',
		success_msg: 'Plexuss Premium Monthly',
		price: 10,
		plan: 'monthly',
		price_details: 'with Plexuss',
		total_savings: '',
		choose_route: '/checkout/monthly',
		change_plan_route: '/premium-plans',
		features: [
			{name: 'Includes 1 additional Application to select universities in the Plexuss network '},
			{name: 'Includes 1 additional Essay view from an accepted student to a top US university '}
		],
	},

	onetime_unlimited: {
		name: 'Unlimited',
		confirmation_name: 'Plexuss Unlimited Membership',
		success_msg: 'Plexuss Premium Unlimited',
		price: 499,
		plan: 'onetime_unlimited',
		price_details: 'with Plexuss',
		total_savings: '',
		choose_route: '/checkout/onetime_unlimited',
		change_plan_route: '/premium-plans',
		features: [
			{name: 'Apply to Unlimited', details: 'select universities', tip : "Apply to an Unlimited number of select universities in Plexuss' network"},
			{name: '1-on-1 meetings with a professional advisor'},
			{name: 'Review Unlimited Essays', details: 'from accepted students to top US universities'}
		],
	},

	
	premium: {
		name: 'Premium',
		confirmation_name: 'Plexuss Premium Membership',
		success_msg: 'Plexuss Premium',
		price: 499,
		plan: 'onetime',
		price_details: 'with Plexuss',
		total_savings: 'Total Savings of $325-$575',
		choose_route: '/checkout/premium',
		change_plan_route: '/premium-plans',
		features: [
			{name: 'Apply to 10', details: 'select universities for FREE', savings: 'Savings of $125-$375', tip: 'Apply to 10 select universities in the Plexuss network.'},
			{name: '1-on-1 meeting', details: 'with a professional advisor', savings: 'Savings of $200'},
			{name: 'Review 20 essays', details: 'from accepted students to top US universities'},
		],
	},

	'premium-plus': {
		name: 'Premium Plus',
		confirmation_name: 'Plexuss Premium Plus Membership',
		success_msg: 'Plexuss Premium Plus',
		price: 199,
		plan: 'onetime_plus',
		price_details: 'with Plexuss',
		total_savings: 'Total Savings of $550-$1,050',
		choose_route: '/checkout/premium-plus',
		change_plan_route: '/premium-plans',
		features: [
			{name: 'Apply to 10', details: 'select universities for FREE', savings: 'Savings of $350-$750', tip: 'Apply to 10 select universities in the Plexuss network.'},
			{name: '1-on-1 meeting', details: 'with a professional advisor', savings: 'Savings of $200'},
			{name: 'Review 50 essays', details: 'from accepted students to top US universities'},
		],
	},

};

export const PAYMENT_METHODS = [
	{name: 'payment_method', value: 'credit_cards', icons: ['visa', 'mastercard', 'american express', 'discover']},
	// {name: 'payment_method', value: 'amazon_pay', icons: ['amazon']},
	{name: 'payment_method', value: 'pay_pal', icons: ['paypal']},
	{name: 'payment_method', value: 'western_union', icons: ['western_union']},
];

export const PREMIUM_PLANS = [
	PRODUCTS.premium,
	PRODUCTS['premium-plus'],
];
