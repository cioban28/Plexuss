// pickACollegeReducer.js

let init = {
	prioritySchools: null,
	orderMap: {},
	contractTypes: [],
};

export default function( state = init, action ){

	switch( action.type ){

		case 'COLLEGE_SEARCH_PENDING':
		case 'COLLEGE_SEARCH_DONE':
		case 'GET_PRIORITY_SCHOOLS_PENDING':
		case 'GET_APPORDER_SCHOOLS_PENDING':
		case 'GET_APPORDER_SCHOOLS_ERR':
		case 'GET_PRIORITY_SCHOOLS_DONE':
		case 'GET_APPORDER_SCHOOLS_DONE':
		case 'OPEN_SEARCH':
		case 'SET_PAGE':
		case 'SAVE_EDITS_TO_PRIORITY_SCHOOLS_PENDING':
		case 'GET_CONTRACT_TYPES_PENDING':
		case 'GET_CONTRACT_TYPES_DONE':
			return {...state, ...action.payload};

		case 'SORT_ALPHABET':
		case 'SORT_NUMBER':
			var newState = Object.assign({}, state),
				col = action.payload.col.sortname,
				is_priority = !!newState.prioritySchools,
				list_name = is_priority ? 'prioritySchools' : 'appOrderSchools',
				schools = newState.appOrderSchools || newState.prioritySchools,
				list = schools.slice(),
				order = newState.orderMap[col],
				orderObj = {}, sortedPropName = '';

			// 1. determine order based on orderMap - object w/col names as prop and 'asc' or 'desc' as value
			// 2. check if we already have a saved sort for a particular column
			// 3. if not, create new orderMap with column name and order

			// if order has not been set or order for this column is desc, set to asc
			// else set to desc
			if( !order || order === 'desc' ) order = 'asc';
			else order = 'desc';

			// add/update column order value
			orderObj[col] = order;

			sortedPropName = 'sorted_'+col+'_'+order;

			// if we have already saved a certain column ordering, set sortedList to that array
			// else sort by column name and reverse array if order is desc
			if( newState[sortedPropName] ){
				newState[list_name] = newState[sortedPropName]; //save existing sorted list to priorityList 
			}else{
				// creating new sorted column arrays - order by promoted (putting promoted one's at the top no matter what column is sorted)
				// then order will be either asc/desc depending if we have already done that sort or not already
				newState[sortedPropName] = _.orderBy(list, ['promoted', col], ['desc', order]); 

				// save new sort list to priorityList 
				newState[list_name] = newState[sortedPropName];
			}

			// set orderMap with new object
			newState.orderMap = Object.assign({}, newState.orderMap, orderObj);

			return newState;

		case 'SAVE_EDITS_TO_PRIORITY_SCHOOLS_DONE':
			var data = action.payload,
				newState = Object.assign({}, state, {save_pending: data.save_pending}),
				updatedSchool = data.school,
				recent_school_edited = newState.recently_edited_school_id || 0,
				promotedChanged = false,
				list_name = 'prioritySchools';

			if( newState.page ) list_name = 'appOrderSchools';

			newState[list_name] = newState[list_name].map((school) => {
				if( school.college_id === updatedSchool.college_id ){
					let msg = 'Edits to '+updatedSchool.name + ' have been saved.';

					//save promotion value
					promotedChanged = school.promoted !== updatedSchool.promoted;
					//edit msg if promoted
					if( promotedChanged ) msg = updatedSchool.name + ' has been promoted!';

					//make saved true and save saved_msg
					updatedSchool.saved = true;
					updatedSchool.saved_msg = msg;

					//save this schools id as the most recently updated school
					recent_school_edited = updatedSchool.college_id;

					return Object.assign({}, school, updatedSchool);
				}

				return school;
			});

			// save the id of the latest school that was updated
			newState.recently_edited_school_id = recent_school_edited;

			//if promoted value changed, then reorder list based of promoted value
			if( promotedChanged ) newState[list_name] = _.orderBy(newState[list_name], ['promoted'], ['desc']);

			return newState;

		case 'REMOVE_PRIORITY_SCHOOL': 
			var newState = Object.assign({}, state),
				list_name = 'prioritySchools';

			if( newState.page ) list_name = 'appOrderSchools';

			newState[list_name] = _.reject(newState[list_name], action.payload);

			return newState;

		case 'ADD_SCHOOL_TO_PRIORITY_LIST':
			var newState = {...state, openSearch: action.payload.openSearch},
				list_name = 'prioritySchools';

			if( newState.page ) list_name = 'appOrderSchools';

			// newState.prioritySchools = newState.prioritySchools.concat([action.payload.school]);
			// below is the es6 way of doing the above - spread operator on array instead of using concat
			newState[list_name] = [...newState[list_name], action.payload.school];

			return newState;

		case 'RESET_SAVED_ROW':
			var newState = Object.assign({}, state),
				id = action.payload.id,
				list_name = 'prioritySchools';

			if( newState.page ) list_name = 'appOrderSchools';

			newState[list_name] = newState[list_name].map((school) => {
				if( school.college_id === id ) return Object.assign({}, school, {saved: false, err: false});
				return school;
			});

			return newState;

		default: return state;
	}

}