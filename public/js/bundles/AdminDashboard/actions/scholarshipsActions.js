3//scholarshipsActions.js
import axios from 'axios'

/*******************************************
* get countries
**********************************************/
export const getAllCountries = () => {

	return (dispatch) => {

		axios.get('/ajax/getAllCountries')
		.then((res)=> {
			// console.log(res);

			dispatch({
				type: 'GET_ALL_COUNTRIES',
				payload: {
					countries: res.data
				}
			})
		})
	}
}

/*******************************************
* get states
**********************************************/
export const getAllStates = () => {

	return (dispatch) => {

		axios.get('/ajax/getAllStates')
		.then((res)=> {
			// console.log(res);

			dispatch({
				type: 'GET_ALL_COUNTRIES',
				payload: {
					states: res.data
				}
			})
		})
	}
}

/*******************************************
* get All Depatment
**********************************************/
export const getAllDepartments = () => {

	return (dispatch) => {

		axios.get('/ajax/getAllDepts')
		.then((res)=> {
			//console.log(res.data);

			dispatch({
				type: 'GET_ALL_COUNTRIES',
				payload: {
					depts: res.data
				}
			})
		})
	}
}

export const getDepartmentMajors = (deptId) => {
	return (dispatch) => {
		axios.get('/ajax/getMajorByDepartmentWithIds/'+deptId)
		.then((res) => {
			dispatch({
				type: 'GET_ALL_DEPARTMENT_MAJORS',
				payload: res.data
			})
		})
		.catch((err) => {
		});
	}
}

export const getAllMajorDepartments = () => {

	return (dispatch) => {

		axios.get('/ajax/getAllMajorByDepartment')
		.then((res)=> {
			//console.log(res.data);

			dispatch({
				type: 'GET_ALL_COUNTRIES',
				payload: {
					mdepts: res.data
				}
			})
		})
	}
}

/*******************************************
* get Start Date Term
**********************************************/

export const getAllDates = () => {

	return (dispatch) => {

		axios.get('/ajax/getAllTargetDates')
		.then((res)=> {
			// console.log(res.data);

			dispatch({
				type: 'GET_ALL_COUNTRIES',
				payload: {
					startdatea: res.data
				}
			})
		})
	}
}

/*******************************************
* get getAllMilitary
**********************************************/
export const getAllMilitaries = () => {

	return (dispatch) => {

		axios.get('/ajax/getAllMilitaries')
		.then((res)=> {
			dispatch({
				type: 'GET_ALL_COUNTRIES',
				payload: {
					militaries: res.data
					
				}
			})
		})
	}
}

/*******************************************
* get Ethnicities
**********************************************/
export const getAllEthnicities = () => {

	return (dispatch) => {

		axios.get('/ajax/getAllEthnicities')
		.then((res)=> {
			dispatch({
				type: 'GET_ALL_COUNTRIES',
				payload: {
					ethnicities: res.data
					
				}
			})
		})
	}
}

/*******************************************
* get Religions
**********************************************/
export const getAllReligionsCustom = () => {

	return (dispatch) => {

		axios.get('/ajax/getAllReligionsCustom')
		.then((res)=> {
			dispatch({
				type: 'GET_ALL_COUNTRIES',
				payload: {
					religions: res.data
					
				}
			})
		})
	}
}


/*************************************
* get all providers
*************************************/
export const getAllProviders = () => {
	return (dispatch) => {

		axios.get('/ajax/getAllProviders')
		.then((res)=> {
			// console.log(res);

			dispatch({
				type: 'GET_ALL_PROVIDERS',
				payload: {
					providers: res.data
				}
			})
		})
	}

}


/*******************************************
* get all scholarships -- should implement pagination?
*********************************************/
export const getScholarships = () => {
	return (dispatch) => {

		dispatch({
			type: 'GET_ALL_SCH_START',
			payload: {
				get_sch_pending: true
			}
		})

		axios.get('/ajax/getAllScholarships')
		.then((res) => {
			// console.log(res.data);

			dispatch({
				type: 'GET_ALL_SCH_DONE',
				payload: {
					get_sch_pending: false,
					scholarshipsList: res.data
				}
			})
		})
		.catch((err) => {
			console.log("An error occured: ", err);
			dispatch({
				type: 'GET_ALL_SCH_ERR',
				payload: {
					get_sch_pending: false
				}
			})
		})
	}

}


/**********************************
* sorting wrapper takes sort type and column
**********************************/
export const sortCol = (direction, type) => {
	return {
		type: 'SORT_COL',
		payload: {
			direction: direction,
			type: type
		}
	}
}


/******************************************
*
***************************************/
export const addScholarship = (data,  callback) => {
	//alert(data.toSource());
	return (dispatch) => {

		dispatch({
			type: 'ADD_SCH_START',
			payload: {
				add_sch_pending: true
			}
		})

		axios.post('/ajax/addScholarshipSales', data)
		.then((res) => {
			// console.log(res);
			callback();

			dispatch({
				type: 'ADD_SCH_DONE',
				payload: {
					add_sch_pending: false,
					added: res.data
				}
			})
		})
		.catch((err) => {
			console.log("An error occured: ", err);
			dispatch({
				type: 'ADD_SCH_ERR',
				payload: {
					add_sch_pending: false
				}
			})
		})
	}
}


export const editScholarship = (data, callback,alldata) => {

	return (dispatch) => {

		dispatch({
			type: 'EDIT_SCH_START',
			payload: {
				edit_sch_pending: true
			}
		})

		axios.post('/ajax/editScholarshipSales', data)
		.then((res) => {
			// console.log(res);
			if(data['step']==1){
				//callback();
			}else if(data['step']==2){
				//callback();	
			}else{
				callback();
			}
			
			dispatch({
				type: 'EDIT_SCH_DONE',
				payload: {
					edit_sch_pending: false,
					editted: res.alldata,
					
				}
			})
		})
		.catch((err) => {
			console.log("An error occured: ", err);
			dispatch({
				type: 'EDIT_SCH_ERR',
				payload: {
					edit_sch_pending: false
				}
			})
		})
	}
}


export const deleteScholarship = (id) => {

	return (dispatch) => {

		dispatch({
			type: 'DELETE_SCH_START',
			payload: {
				delete_sch_pending: true
			}
		})

		axios.post('/ajax/deleteScholarshipSales', {id: id})
		.then((res) => {
			// console.log(res);

			dispatch({
				type: 'DELETE_SCH_DONE',
				payload: {
					delete_sch_pending: false,
					deleted: res.data
				}
			})
		})
		.catch((err) => {
			console.log("An error occured: ", err);
			dispatch({
				type: 'DELETE_SCH_ERR',
				payload: {
					delete_sch_pending: false
				}
			})
		})
	
	}
}

export const getScholarshipData = (scholarship_id = '') => {
	return (dispatch) => {
		dispatch({
	 		type: '_PROFILE:INIT_PROFILE_PENDING',
	 		payload: {init_profile_pending: true},
	 	});
		axios.post('/ajax/getScholarshipFilter', {id : scholarship_id})
		.then((response) => {
			console.log(response.data);
			dispatch({
				type: '_PROFILE:INIT',
				payload: {
					init_done: true,
					tdata: response.data,
					init_pending: false,
					init_profile_pending: false,
				}
			});
		})
		.catch((err) => {
		 	console.log('error profile: ', err);
		 	dispatch({
		 		type: '_PROFILE:ERR',
		 		payload: {
		 			init_pending: false,
		 			init_profile_pending: false,
		 		},
		 	});
		});
	}
}

export const searchScholarships = (term) => {
	return (dispatch) => {

		dispatch({
			type: 'SEARCH_SCH_START',
			payload: {
				search_sch_pending: true
			}
		})

		axios.get('/ajax/searchScholarships' + '?term=' + term)
		.then((res) => {
			console.log(res);

			dispatch({
				type: 'SEARCH_SCH_DONE',
				payload: {
					search_sch_pending: false,
					scholarshipsList: res.data
				}
			})
		})
		.catch((err) => {
			console.log("An error occured: ", err);
			dispatch({
				type: 'SEARCH_SCH_ERR',
				payload: {
					search_sch_pending: false
				}
			})
		})
	
	}	
}