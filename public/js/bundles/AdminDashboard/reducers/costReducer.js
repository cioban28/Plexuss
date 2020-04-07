// costReducer.js

import _ from 'lodash'
import { PROGRAMS } from './../components/cms/International/constants'
import { TUITION_COST_FIELDS, EST_COST, EST_ASSIST } from './../components/cms/Cost/constants'

var init = {};

export default (state = init, action) => {

	switch( action.type ){

		case 'COST:PENDING':
		case 'COST:SET_PROGRAM':
			return {...state, ...action.payload};

		case 'COST:SAVE':
		case 'COST:INIT_DATA':
		case 'COST:SET_FIELD_VALS':
			return _calculateEstimateTotals({...state, ...action.payload});

		default:
			return state;
	}
	
}

const _calculateEstimateTotals = (newState) => {
	// for each program, 
	_.each(PROGRAMS, (pro) => {
		var cost_prop = pro.id + '_est_cost',
			assist_prop = pro.id + '_est_assist',
			annual_prop = pro.id + '_min_annual_cost',
			cost_total = 0,	assist_total = 0;

		_.each(TUITION_COST_FIELDS, (field) => {
			let prop = pro.id + '_' + field.name;

			// group groups together certain fields
			if( field.group ) cost_total += +newState[prop];
			else assist_total += +newState[prop];
		});

		newState[cost_prop] = cost_total; // update total for est_cost
		newState[assist_prop] = assist_total;// update total for est_assist 

		// subtract cost_total from assist_total to get annual_total
		newState[annual_prop] = newState[cost_prop] - newState[assist_prop];
	});

	return newState;
};