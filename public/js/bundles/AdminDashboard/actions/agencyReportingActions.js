// agencyReportingActions.js

import axios from 'axios';

axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

export function updateData(payload) {
    return {
        type: 'AGENCY_REPORTING:UPDATE',
        payload
    }
}

export function updateAgencyReporting(start_date, end_date) {
	return (dispatch) => {
		dispatch({
			type: 'AGENCY_REPORTING:UPDATE',
			payload: { agency_reporting_pending: true }
		});

		axios({
			url: '/ajax/getAgencyReportingData',
			method: 'POST',
			data: { start_date, end_date },
		})
		.then((response) => {
			dispatch({
				type: 'AGENCY_REPORTING:UPDATE',
				payload: { 
					start_date, end_date,
					report: response, 
					agency_reporting_pending: false 
				}
			});
		});
	}
};

export function savePlexussNote(agency_id, note) {
	return (dispatch) => {
		dispatch({
			type: 'AGENCY_REPORTING:UPDATE',
			payload: { save_plexuss_note_pending: true }
		});
		axios({
			url: '/sales/agency/setPlexussNote',
			method: 'POST',
			data: { agency_id, note },
		})
		.then((response) => {
			dispatch({
				type: 'AGENCY_REPORTING:UPDATE',
				payload: { save_plexuss_note_pending: false, new_note: note }
			})
		})
	}
}