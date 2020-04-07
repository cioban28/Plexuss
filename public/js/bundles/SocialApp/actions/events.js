export function upcomingEventsSuccess(payload){
	return{
		type: 'UPCOMING_EVENTS_SUCCESS',
		payload
	}
}

export function nearestEventsSuccess(payload){
	return{
		type: 'NEAREST_EVENTS_SUCCESS',
		payload
	}
}

export function pastEventsSuccess(payload){
	return{
		type: 'PAST_EVENTS_SUCCESS',
		payload
	}
}
