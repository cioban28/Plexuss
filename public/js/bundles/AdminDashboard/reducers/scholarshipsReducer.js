import _ from 'lodash';

let init = {
	scholarshipsList: [],
	countries: [],
	providers: [],
	deptsMajors: [],
	// scholarshipsList: [],
};


export default (state = init, action) => {
	let newState = {...state};

	switch(action.type){
		case 'SORT_COL':
			let compareFunc = null;
			let type = action.payload.type;

			switch(type){
				case 'name':
					compareFunc = _byName;
					break;
				case 'amount':
					compareFunc = _byAmount;
					break;
				case 'due':
					compareFunc = _byDue;
					break;
				case 'created':
					compareFunc = _byCreated;
					break;
				default:
					break;
			}

			let result = {};
			if(action.payload.direction === 'asc'){
				result = _sortBy('asc', compareFunc, newState.scholarshipsList);
			}else{
				result = _sortBy('desc', compareFunc,  newState.scholarshipsList);
			}
			return newState;


		case 'ADD_SCH_DONE':
			newState.scholarshipsList.push(action.payload.added);
			// _.sortBy(newState.scholarshipsList, [(o)=> {
			// 	return o.id;
			// }]);
			newState.add_sch_pending = false;
			return newState;

		case 'EDIT_SCH_DONE':
			let i = newState.scholarshipsList.findIndex((o)=>{ return  o.id == action.payload.editted.id; });

			if(i !== -1)
				newState.scholarshipsList[i] = action.payload.editted;
			else
				console.log('Error occured (reducer): editted item not found in store' );

			newState.edit_sch_pending = false;
			return newState;

		case 'DELETE_SCH_DONE':
			let j = newState.scholarshipsList.findIndex((o)=>{ return  o.id == action.payload.deleted; });

			if(j !== -1)
				newState.scholarshipsList.splice(j,1);
			else
				console.log('Error occured: deleted item not found' );

			newState.delete_sch_pending = false;
			return newState;

		case 'DELETE_SCH_START':
		case 'DELETE_SCH_ERR':	
		case 'GET_ALL_SCH_DONE':	
		case 'GET_ALL_SCH_START':	
		case 'GET_ALL_SCH_ERR':	
		case 'EDIT_SCH_START':
		case 'EDIT_SCH_ERR':
		case 'ADD_SCH_START':
		case 'ADD_SCH_ERR':
		case 'GET_ALL_COUNTRIES':
		case 'GET_ALL_PROVIDERS':
		case 'SEARCH_SCH_START':
		case 'SEARCH_SCH_DONE':
		case 'SEARCH_SCH_ERR':
			return {...newState, ...action.payload}

		case 'GET_ALL_DEPARTMENT_MAJORS':
			const deptsMajors = [...state.deptsMajors];
			deptsMajors.push(action.payload);
			return { ...newState, deptsMajors: [...deptsMajors] }

		default:
			return state;
	}//end switch
}


////////////////////// helper functions ////////////////////////////

const _sortBy = (direction, compareBy, list) => {
	list.sort(compareBy(direction));	
} 

const _byName = (direction) => {
	return function(a,b){
		if(direction === 'asc'){
			if(a.scholarship_name < b.scholarship_name)
				return 1;
			else if (a.scholarship_name > b.scholarship_name)
				return -1;
			else
				return 0;
		}else{
			if(a.scholarship_name > b.scholarship_name)
				return 1;
			else if (a.scholarship_name < b.scholarship_name)
				return -1;
			else
				return 0;
		}
	}
}

const _byAmount = (direction) => {
	return function(a,b){
		if(direction === 'asc'){
			return a.amount - b.amount;
		}else{
			return b.amount - a.amount;
		}
	}
}

const _byDue = (direction) => {
	return function(a,b){
		if(direction === 'asc'){
			if(a.deadline < b.deadline)
				return 1;
			else if (a.deadline > b.deadline)
				return -1;
			else
				return 0;
		}else{
			if(a.deadline > b.deadline)
				return 1;
			else if (a.deadline < b.deadline)
				return -1;
			else
				return 0;
		}
	}
}


const _byCreated = (direction) => {
	return function(a,b){
		if(direction === 'asc'){
			if(a.created_at < b.created_at)
				return 1;
			else if (a.created_at > b.created_at)
				return -1;
			else
				return 0;
		}else{
			if(a.created_at > b.created_at)
				return 1;
			else if (a.created_at < b.created_at)
				return -1;
			else
				return 0;
		}
	}
}
