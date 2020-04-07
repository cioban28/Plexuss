import { orderBy, sortBy } from 'lodash';

var init = {};

export default (state = init, action) => {

    switch( action.type ){

        case 'REPORTING:UPDATE':
            return { ...state, ...action.payload };
        
        case 'REPORTING:UPDATE_FAKE_REPORT':
            return fakeReport({...state});

        case 'REPORTING:UPDATE_REPORTING_ORDER':
            return updateReportingOrder({...state}, action.payload.columnName);

        default:
            return state;
    }
}

// For test cases
const fakeReport = function(state) {
    const newState = {...state};

    const report = [
        {date: '7/12/2017', name: 'Tony T.', num_of_calls: 5, avg_duration: 5, total_duration: '230', num_of_texts: 25},
        {date: '1/10/2018', name: 'Samual J.', num_of_calls: 2, avg_duration: 12, total_duration: '102', num_of_texts: 27},
        {date: '3/13/2017', name: 'Michelle O.', num_of_calls: 10, avg_duration: 5, total_duration: '500', num_of_texts: 10},
        {date: '12/26/2017', name: 'Jessa P.', num_of_calls: 7, avg_duration: 16, total_duration: '300', num_of_texts: 0},
    ];

    newState['report'] = report;

    return newState;
}

const updateReportingOrder = function(state, columnName) {
    const newState = {...state};

    let newReport = null,
        order = newState[columnName + '_orderBy'] || 'asc';

    order = order === 'asc' ? 'desc' : 'asc';

    switch (columnName) {
        case 'date':
            newReport = sortBy(newState.report, agent => new Date(agent.date).getTime());
            if (order === 'desc') newReport = newReport.reverse();
            break;

        default:
            newReport = orderBy(newState.report, [columnName], [order]);
    }

    newState[columnName + '_orderBy'] = order;
    newState['report'] = newReport;

    return newState;
}