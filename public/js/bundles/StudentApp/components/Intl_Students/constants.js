// Intl_Students/constants.js

export const SORT_COLS = [
	{
		name: 'Rank', 
		sortType: 'number', 
		sortCol: 'rank', 
		width: 1
	},
	{
		name: 'School', 
		sortType: 'name', 
		sortCol: 'school_name', 
		width: 3, 
		mobileClass: 'forMobile w50'
	},
	{
		name: 'Location', 
		sortType: 'name', 
		sortCol: 'state', 
		width: 1
	},
	{
		name: 'English Programs', 
		sortType: 'number', 
		sortCol: 'epp_column_cost', // used for SortBar component to know what val to use to sort
		width: 2, 
		abbr: 'EP', 
		tip: 'Estimated annual cost for English Programs.'
	},
	{
		name: 'Undergraduate', 
		sortType: 'number', 
		sortCol: 'undergrad_column_cost', // used for SortBar component to know what val to use to sort
		width: '1-5', 
		abbr: 'UG', 
		tip: 'Estimated annual cost for an Undergraduate degree.'
	},
	{
		name: 'Graduate', 
		sortType: 'number', 
		sortCol: 'grad_column_cost', // used for SortBar component to know what val to use to sort
		width: '1-5', 
		abbr: 'GD', 
		tip: 'Estimated annual cost for a Graduate degree.', 
		mobileClass: 'forMobile w25',
	},
	{
		name: 'Apply Now', 
		sortType: '', 
		width: 2, 
		mobileClass: 'forMobile w25'
	},
];

export const FILTER_BY_COST = {
	title: 'Total Estimated Cost', 
	type: 'select',
	options: [
		{name: 'Up to $10,000 annually', value: 10000},
		{name: 'Up to $20,000 annually', value: 20000},
		{name: 'Up to $30,000 annually', value: 30000},
		{name: 'Up to $50,000 annually', value: 50000},
		{name: 'Over $50,000 annually', value: 50001},
	],
};

export const FILTER_BY_DEGREE = {
	title: 'Degree Offered', 
	type: 'select',
	options: [
		{name: "Certificate", value: 1},
		{name: "Associate's", value: 2},
		{name: "Bachelor's", value: 3},
		{name: "Master's", value: 4},
		{name: "Doctorate", value: 5},
	],
};

export const FILTER_BY_MAJOR = {
	title: 'Major Offered',
	type: 'select',
	options: [],
};

export const EX_RATES = {
	title: 'USD',
	type: 'select',
	options: [],
}