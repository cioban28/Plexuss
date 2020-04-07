// /Cost/constants.js

export const TUITION_COST_FIELDS = [
	{name: 'avg_tuition', label: 'Average Tuition', placeholder: 'Enter amount...', group: 1},
	{name: 'other_cost', label: 'Average of All Other Costs (i.e. Books, Food, Housing)', placeholder: 'Enter amount...', group: 1},
	{name: 'avg_scholarship', label: 'Average Scholarship Value', placeholder: 'Enter amount...', group: 0},
	{name: 'avg_work_study', label: 'Average Work Study Value', placeholder: 'Enter amount...', group: 0},
	{name: 'other_financial', label: 'Other Financial Assistance', placeholder: 'Enter amount...', group: 0},
];

export const CHANGES_ROUTE = '/international-students';

export const EST_COST = [
	'avg_tuition',
	'other_cost',
];

export const EST_ASSIST = [
	'avg_scholarship',
	'avg_work_study',
	'other_financial',
];