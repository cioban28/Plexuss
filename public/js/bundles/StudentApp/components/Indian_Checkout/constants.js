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
		choose_route: '/indian-checkout/monthly',
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
		price: 1,
		plan: 'onetime_unlimited',
		price_details: 'with Plexuss',
		total_savings: '',
		choose_route: '/indian-checkout/onetime_unlimited',
		change_plan_route: '/premium-plans',
		features: [
			{name: 'Apply to Unlimited', details: 'select universities', tip : "Apply to an Unlimited number of select universities in Plexuss' network"},
			{name: '1-on-1 meetings with a professional advisor'},
			{name: 'Review Unlimited Essays', details: 'from accepted students to top US universities'}
		],
	},


	premium: {
		name: 'Premium',
		confirmation_name: 'Plexuss Premium',
		success_msg: 'Plexuss Premium',
		price: 499,
		plan: 'onetime',
		price_details: 'with Plexuss',
		total_savings: 'Total Savings of $325-$575',
		choose_route: '/indian-checkout/premium',
		change_plan_route: '/premium-plans',
		features: [
			{name: 'Apply to 10', details: 'select universities for FREE', savings: 'Savings of $125-$375', tip: 'Apply to 10 select universities in the Plexuss network.'},
			{name: '1-on-1 meeting', details: 'with a professional advisor', savings: 'Savings of $200'},
			{name: 'Review 20 essays', details: 'from accepted students to top US universities'},
		],
		checkout_features: [
			{image: '/css/PremiumIndia/images/checkmark.png', description: 'Guaranteed United States University Admission'},
			{image: '/css/PremiumIndia/images/checkmark.png', description: 'One-on-one Admission Assistance'},
			{image: '/css/PremiumIndia/images/checkmark.png', description: 'Up to Twenty FREE College Applications'},
			{image: '/css/PremiumIndia/images/checkmark.png', description: 'Access to Admission Essays'},
			{image: '/css/PremiumIndia/images/checkmark.png', description: 'Access to Exclusive Scholarships'},
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
		choose_route: '/indian-checkout/premium-plus',
		change_plan_route: '/premium-plans',
		features: [
			{name: 'Apply to 10', details: 'select universities for FREE', savings: 'Savings of $350-$750', tip: 'Apply to 10 select universities in the Plexuss network.'},
			{name: '1-on-1 meeting', details: 'with a professional advisor', savings: 'Savings of $200'},
			{name: 'Review 50 essays', details: 'from accepted students to top US universities'},
		],
	},

};

export const PAYMENT_METHODS = [
	{
		name: 'Credit or Debit Card',
		value: 'credit_cards',
		images: [
			{ className: 'active-image credit-card-img', src: '/images/checkout/amex.png' },
			{ className: 'in-active-image credit-card-img', src: '/images/checkout/gray-amex.png' },
			{ className: 'active-image credit-card-img', src: '/images/checkout/Mastercard.png' },
			{ className: 'in-active-image credit-card-img', src: '/images/checkout/gray-Mastercard.png' },
			{ className: 'active-image credit-card-img', src: '/images/checkout/Visa.png' },
			{ className: 'in-active-image credit-card-img', src: '/images/checkout/gray-Visa.png' },

		],
		description: 'Visa, Mastercard, Amex'
	},

	{
		name: 'PayPal',
		value: 'pay_pal',
		images: [
			{ className: 'active-image', src: '/images/checkout/2000px-PayPal.svg.png' },
			{ className: 'in-active-image', src: '/images/checkout/gray-2000px-PayPal.svg.png' },
		],
		description: 'PayPal is the faster, safer way to send money, make an online payment, and receive money. Account not needed.'
	},

	{
		name: 'Money Transfer',
		value: 'western_union',
		images: [
			{ className: 'active-image', src: '/images/checkout/western-union_82079.png' },
			{ className: 'in-active-image', src: '/images/checkout/gray-western-union_82079.png' },
		],
		description: 'Pay using Money Transfer through Western Union'
	},

	// {
	// 	name: 'Other Payment Options',
	// 	value: 'other_payment',
	// 	images: [
	// 		{ className: 'active-image', src: '/images/checkout/alipay.png' },
	// 		{ className: 'in-active-image', src: '/images/checkout/gray-alipay.png' },
	// 	],
	// 	description: 'Pay using Alipay or other payment methods'
	// },
];

export const PREMIUM_PLANS = [
	PRODUCTS.premium,
	PRODUCTS['premium-plus'],
];

export const COUNTRIES_WITHOUT_POSTALCODES = ["Angola", "Antigua and Barbuda", "Aruba", "Bahamas", "Belize", "Benin", "Botswana", "Burkina Faso", "Burundi", "Cameroon", "Central African Republic", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Djibouti", "Dominica", "Equatorial Guinea", "Eritrea", "Fiji", "French Southern Territories", "Gambia", "Ghana", "Grenada", "Guinea", "Guyana", "Hong Kong", "Ireland", "Ivory Coast", "Jamaica", "Kenya", "Kiribati", "Macao", "Malawi", "Mali", "Mauritania", "Mauritius", "Montserrat", "Nauru", "Netherlands Antilles", "Niue", "North Korea", "Panama", "Qatar", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Sao Tome and Principe", "Saudi Arabia", "Seychelles", "Sierra Leone", "Solomon Islands", "Somalia", "South Africa", "Suriname", "Syria", "Tanzania, United Republic of", "Timor-Leste", "Tokelau", "Tonga", "Trinidad and Tobago", "Tuvalu", "Uganda", "United Arab Emirates", "Vanuatu", "Yemen", "Zimbabwe"];

