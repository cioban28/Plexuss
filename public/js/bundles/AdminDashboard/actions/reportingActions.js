// reportingActions.js

import axios from 'axios';

axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

export function updateData(payload) {
    return {
        type: 'REPORTING:UPDATE',
        payload
    }
}

export function updateReporting(dateRange) {
    const { start_date, end_date } = dateRange,
        queryString = `?start_date=${start_date}&end_date=${end_date}`;

    return (dispatch) => {
    
        dispatch({
            type: 'REPORTING:UPDATE',
            payload: { report_pending: true },
        });

        axios({
            method: 'GET',
            url: '/admin/ajax/getReport' + queryString,
        }).then(response => {
            dispatch({
                type: 'REPORTING:UPDATE',
                payload: { report_pending: false, report: response.data },
            });
        }).catch(error => {
            dispatch({
                type: 'REPORTING:UPDATE',
                payload: { report_pending: false, report: [] },
            });
        });
    }
};

// Data must contain: user_id, date, time
export function saveAutoReporting(data) {
    return (dispatch) => {

        dispatch({ 
            type: 'REPORTING:UPDATE',
            payload: { auto_report_save_pending: true },
        });

        axios({
            method: 'POST',
            url: '/admin/ajax/saveCRMAutoReporting',
            data,
        }).then(response => {
            dispatch({ 
                type: 'REPORTING:UPDATE',
                payload: { auto_report_save_pending: false, auto_report_save_status: response.data },
            });
        }).catch(error => {
            dispatch({ 
                type: 'REPORTING:UPDATE',
                payload: { auto_report_save_pending: false, auto_report_save_status: 'error' },
            });
        });
    }
}

export function updateReportingOrder(columnName) {
    return {
        type: 'REPORTING:UPDATE_REPORTING_ORDER',
        payload: {columnName}
    }    
}