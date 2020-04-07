//scholarshipscmsActions.js
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
	//return (dispatch) => {
	var html = '<option value="">Select a state...</option>';
	var resultElement = document.getElementById('state_filter');
		axios.get('/ajax/getAllStates')
		.then((res)=> {
			// console.log(res);

			//dispatch({
				//type: 'GET_ALL_COUNTRIES',
				//payload: {
					//states: res.data
				//}
			//})
			
			var fdata = res.data;
			//console.log(fdata);
			for (var i = 0; i < fdata.length; i++) {
				html +='<option key="c'+fdata[i]+'" value="'+fdata[i].name+'">'+fdata[i].name+'</option>';
			}
			resultElement.innerHTML = html;
			
		})
	//}
}

/*******************************************
* get All Depatment
**********************************************/
export const getAllDepartments = () => {
	var html = '<option value="">Select ...</option>';
	var resultElement = document.getElementById('specificDepartment_filter');
	

		axios.get('/ajax/getAllDepts')
		.then((res)=> {
			
			
			var fdata = res.data;
			//console.log(fdata);
			for (var i = 0; i < fdata.length; i++) {
				html +='<option key="'+fdata[i].id+'" value="'+fdata[i].name+'" data-id="'+fdata[i].id+'">'+fdata[i].name+'</option>';
			}
			resultElement.innerHTML = html;
		})
	
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
	var html = '<option value="">Select ...</option>';
	var resultElement = document.getElementById('militaryAffiliation_filter');
	
	axios.get('/ajax/getAllMilitaries')
		.then((res)=> {
			
			
			var fdata = res.data;
			//console.log(fdata);
			for (var i = 0; i < fdata.length; i++) {
				html +='<option key="c'+fdata[i]+'" value="'+fdata[i].name+'">'+fdata[i].name+'</option>';
			}
			resultElement.innerHTML = html;
		})
		

}

/*******************************************
* get Ethnicities
**********************************************/
export const getAllEthnicities = () => {

	var html = '<option value="">Select ...</option>';
	var resultElement = document.getElementById('ethnicity_filter');
	

		axios.get('/ajax/getAllEthnicities')
		.then((res)=> {
			
			
			var fdata = res.data;
			//console.log(fdata);
			for (var i = 0; i < fdata.length; i++) {
				html +='<option key="c'+fdata[i]+'" value="'+fdata[i].name+'">'+fdata[i].name+'</option>';
			}
			resultElement.innerHTML = html;
		})
	
}

/*******************************************
* get Religions
**********************************************/
export const getAllReligionsCustom = () => {
	var html = '<option value="">Select ...</option>';
	var resultElement = document.getElementById('religion_filter');
	

		axios.get('/ajax/getAllReligionsCustom')
		.then((res)=> {
			
			
			var fdata = res.data;
			//console.log(fdata);
			for (var i = 0; i < fdata.length; i++) {
				html +='<option key="c'+fdata[i]+'" value="'+fdata[i].name+'">'+fdata[i].name+'</option>';
			}
			resultElement.innerHTML = html;
		})
		
	
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
* get scholarships -- should implement pagination?
*********************************************/
export const getScholarships = () => {
	return (dispatch) => {

		dispatch({
			type: 'GET_ALL_SCH_START',
			payload: {
				get_sch_pending: true
			}
		})

		axios.get('/ajax/getScholarshipsCms')
		.then((res) => {
			//console.log(res.data);
			//console.log(res.data[0]);
			
			dispatch({
				type: 'GET_ALL_SCH_DONE',
				payload: {
					get_sch_pending: false,
					scholarshipsList: res.data
				}
			})
		})
		.catch((err) => {
			//console.log("An error occured: ", err);
			dispatch({
				type: 'GET_ALL_SCH_ERR',
				payload: {
					get_sch_pending: false
				}
			})
		})
	}

}


/*******************************************
* get scholarships -- should implement pagination?
*********************************************/
export const getAllScholarships = () => {
	return (dispatch) => {

		dispatch({
			type: 'GET_ALL_SCH_START',
			payload: {
				get_sch_pending: true
			}
		})

		axios.get('/ajax/getAllScholarshipsCms')
		.then((res) => {
			//console.log(res.data);
			//console.log(res.data[0]);
			
			dispatch({
				type: 'GET_ALL_SCH_DONE',
				payload: {
					get_sch_pending: false,
					scholarshipsList: res.data
				}
			})
		})
		.catch((err) => {
			//console.log("An error occured: ", err);
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
export const addScholarship = (data) => {
	//alert(data.toSource());
	return (dispatch) => {

		dispatch({
			type: 'ADD_SCH_START',
			payload: {
				add_sch_pending: true
			}
		});

		axios.post('/ajax/addScholarshipCms', data)
		.then((res) => {
			dispatch({
				type: 'ADD_SCH_DONE',
				payload: {
					add_sch_pending: false,
					added: res.data
				}
			});
			//console.log("*****"+res.data);
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

		axios.post('/ajax/deleteScholarshipCms', {id: id})
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