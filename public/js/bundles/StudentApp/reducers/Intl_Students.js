// Intl_Students.js - Reducer

import filter from 'lodash/filter'
import isEmpty from 'lodash/isEmpty'
import omitBy from 'lodash/omitBy'
import forIn from 'lodash/forIn'
import each from 'lodash/each'

const _ = {
	filter: filter,
	isEmpty: isEmpty,
	omitBy: omitBy,
	forIn: forIn,
	each: each,
}
var init = {};

export default (state = init, action) => {

	switch( action.type ){

		case '_INTL:ERR':
		case '_INTL:INIT':
		case '_INTL:SORT':
		case '_INTL:PENDING':
		case '_INTL:INIT_MAJORS':
		case '_INTL:SET_EX_RATES':
		case '_INTL:CONVERT_EX_RATE':
			return {...state, ...action.payload};

		case '_INTL:APPLY_FILTER':
			var newState = {...state},
				filt = action.payload;

			// set/update filters_applied w/ new filter obj
			newState.filters_applied = newState.filters_applied ? {...newState.filters_applied, ...{[filt.name]: filt.value}} : {[filt.name]: filt.value};

			if( newState.filters_applied ){
				let fil = newState.filters_applied;

				// if filters have been applied at one point, but now degree, cost, and majors are empty, then reset list back to original state
				if( !fil.degree && (!fil.majors || fil.majors.length === 0) && !fil.cost ){
					newState.list = newState.original_state_of_list.slice();
					return newState;
				}
			}

			// apply every filter in filters_applied to original state of list
			newState['filtered_list'] = _.filter(newState.original_state_of_list.slice(), (school) => {
				let apply = false,
					validFilters = _.omitBy(newState.filters_applied, _.isEmpty);

				_.forIn(validFilters, (val, key) => {

					if( key === 'degree' ){ // degree filter
						if( !school.majors || _.isEmpty(school.majors) ){
							apply = false;
							return false;
						}

						apply = !!school.majors[+val];
					}

					if( key === 'majors' ){ // majors filter
						// if school doesn't have majors, or it's empty, don't add to filtered list
						if( !school.majors || _.isEmpty(school.majors) ){
							apply = false;
							return false;
						}

						let found = false;

						// loop through each selected major
						// each major_id must be found for this school to be applied to the filter
						_.each(val, (major_id) => { // length = # of selected majors to filter by

							// loop through majors of each degree to see if selected majors is found in list
							_.forIn(school.majors, (majors_list, degree) => { // length = max of 5 loops; 1 for each degree type
								if( majors_list[+major_id] ){
									found = true;
									return false; // break out of forIn loop b/c it was found in at least on of the degrees - no need to look in the others

								}else found = false;
							});

							// if found = false after first iteration, means that this school does not have this major, ergo, do not apply to filtered list
							if( !found ) return false;

						});

						apply = found;
					}

					if( key === 'cost' ){ // annual cost filter
						let degree_filter = newState.filters_applied.degree;
						let annual_cost = school.undergrad_column_cost;
						let amt = +val;

						// If degree filter is set to Masters or Doctorate, use grad column cost.
						if ( degree_filter && ( degree_filter == 4 || degree_filter == 5 ) ) {
							annual_cost = school.grad_column_cost;
						}

						if( amt === 50001 ) apply = +annual_cost > amt; //if amt is 50,000 return those that are greater than that
						else apply = +annual_cost <= amt; // else for any other amt, return those that are less than that amt
					}

					// if this school does not match any of the filters on first iteration, break out of loop
					if( !apply ) return false;

				}); // end of forIn

				return apply;

			}); // end of filter()

			newState.list = newState['filtered_list'].slice();

			return newState;

		default:
			return state;
	}
	
}