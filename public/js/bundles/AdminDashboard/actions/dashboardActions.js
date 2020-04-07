// dashboardActions.js
import axios from 'axios';

const CancelToken = axios.CancelToken;
let cancel; 

export const resetCancelTokenList = () => ({
	type: 'RESET_CANCELTOKENLIST',
	payload: {cancelTokenList: null},
});

export const toggleInterestedPremiumService = (service) => ({
    type: 'TOGGLE_INTERESTED_PREMIUM_SERVICE',
    payload: {service},
});

export function postInterestedPremiumServices(services){
    return (dispatch) => {
        dispatch({
            type: 'UPDATE_DASHBOARD_DATA',
            payload: { sendInterestedServicesPending: true }
        });

        axios({
            url: '/admin/postInterestedPremiumServices',
            method: 'POST',
            data: {services},
        })
        .then((response) => {
            dispatch({
                type: 'UPDATE_DASHBOARD_DATA',
                payload: { 
                    sendInterestedServicesPending: false, 
                    sendInterestedServicesResponse: response.data,
                }
            })
        })
        .catch((response) => {
            dispatch({
                type: 'UPDATE_DASHBOARD_DATA',
                payload: { 
                    sendInterestedServicesPending: false, 
                    sendInterestedServicesResponse: 'fail',
                }
            })
        });
    }
};

export function getDashboardData(){
	return function(dispatch){
		dispatch({
			type: 'GET_DASHBOARD_PENDING', 
			payload: {fetching: true}
		}); 

		axios.get('/admin/getDashboardData')
			 .then((response) => {
			 	dispatch({
			 		type: 'GET_DASHBOARD_DONE', 
			 		payload: {...response.data, fetching: false}
			 	});
			 })
			 .catch((err) => {
			 	console.log(err);
			 	console.log('is an error');
			 	dispatch({
			 		type: 'GET_DASHBOARD_ERR', 
			 		payload: {
			 			fetching: false,
			 			errmsg: 'There was an error fetching the data for this portal'
			 		} 
			 	}); //turn of loader and show error msg
			 });
	}
};

export function appointmentSet(input) {
	return function(dispatch) {
		// payload control the loader
		dispatch({
			type: 'SET_APPOINTMENT_PENDING',
			payload: true
		});

		axios.post('/admin/appointmentWasSet', input)
			 .then((response) => {
			 	dispatch({
			 		type: 'SET_APPOINTMENT_DONE',
			 		payload: false
			 	});
			 })
			 .catch((err) => {
			 	console.log(err);
			 	dispatch({type: 'SET_APPOINTMENT_ERR', payload: false}); // turn off loader and show err msg
			 });
	}
};

export function setSchedule() {
	return {
		type: 'START_SCHEDULE',
		payload: {startToSchedule: 1}
	};
};

export function setGoal(input) {
	return function(dispatch) {
		dispatch({
			type: 'SET_GOAL_PENDING',
			payload: true
		});

		axios.post('/admin/setgoals', input)
			 .then((response) => {
			 	dispatch({
			 		type: 'SET_GOAL_DONE',
			 		payload: response.data
			 	});
			 })
			 .catch((err) => {
			 	console.log(err);
			 	dispatch({type: 'SET_GOAL_ERR', payload: false});
			 });
	}
};

export function openGoal() {
	return {
		type: 'START_GOAL_SETTING',
		payload: {startGoalSetting: 1}
	};
};

export function setGoalMeter(val) {
	return {
		type: 'SET_GOAL_METER',
		payload: {activeMeter: val}
	};
};

export function dismissSlide(slideID){
	var slides = { id: slideID};

	return function(dispatch){
		axios.post('/admin/ajax/dismissPlexussAnnouncement', slides)
		.then((response) =>{
			var slide = {id: slideID};

			dispatch({
				type: 'DISMISSED_SLIDE',
				payload: slide
			});

		})
		.catch((err) => {
			console.log(err);
		});
	}
}

export function initStats(block){
	return function(dispatch){
		var pending = block+'_pending';

		let cancelToken = new CancelToken(function executor(c) {
			    cancel = c;
			});

		dispatch({
			type: '_DASH:INIT_STATS_PENDING', 
			payload: {
				[pending]: true,
				cancelToken: cancel,
			}
		}); 

		axios.get('/admin/initDashboardStats/'+block, {
			cancelToken
		})
		.then((res) => {
		 	dispatch({
		 		type: '_DASH:INIT_STATS_DONE', 
		 		payload: {
		 			[pending]: false,
		 			data: res.data,
		 		}
		 	});
		})
		.catch((err) => {
		 	dispatch({
		 		type: '_DASH:INIT_STATS_ERR', 
		 		payload: {[pending]: false} 
		 	});
		});
	}
};