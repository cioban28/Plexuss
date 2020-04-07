// datesReducer.js

import moment from 'moment'

let init = {
	today: {
		date: moment(),
		dateFormatted: moment().format('YYYY/MM/DD'),
		dateCasual: moment().format('ddd, DD MMM YYYY'),
	}
};

export default function(state = init, action) {

    switch(action.type) {

        case 'SET_DATE_RANGE':
        case 'SAVE_DATE_RANGE_ERR':
        case 'SAVE_DATE_RANGE_DONE':
        case 'RESET_DATE_SAVED_OR_ERR':
        case 'SAVE_DATE_RANGE_PENDING':
        case 'GET_DATE_FOR_PICKACOLLEGE_ERR':
        case 'GET_DATE_FOR_PICKACOLLEGE_DONE':
        case 'GET_DATE_FOR_PICKACOLLEGE_PENDING':
            return Object.assign({}, state, {...action.payload});

        case 'GET_ALL_INTL_DATA':
            var newState = {...state},
                { admission_info } = action.payload.data,
                deadline_prop = '_application_deadline';

            _.forIn(admission_info, (val, key) => {
                if( val[key+deadline_prop] && val[key+deadline_prop] !== 'rolling_admissions' ){
                    var newDate = {
                        startDate: moment(val[key+deadline_prop]),
                        start_date: moment(val[key+deadline_prop]).format('YYYY/MM/DD'),
                        endDate: moment(val[key+deadline_prop]),
                        end_date: moment(val[key+deadline_prop]).format('YYYY/MM/DD'),
                    };

                    newState[key+deadline_prop] = newDate;
                }
            });

            return newState;

        default:
            return state;
            
    }

}