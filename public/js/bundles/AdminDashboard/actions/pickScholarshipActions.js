// pickACollegeActions.js

import axios from 'axios';

export const setPage = (page) => {
	return {
		type: 'SET_PAGE',
		payload: page
	};
}

export const sortCol = (header) => {
	return {
		type: 'SORT_'+header.sortType.toUpperCase(),
		payload: {col: header}
	};
}

export const removeSchool = (school) => {
	return {
		type: 'REMOVE_PRIORITY_SCHOOL',
		payload: school
	};
}

export const openSearch = (bool) => {
	return {
		type: 'OPEN_SEARCH',
		payload: {openSearch: bool} 
	};
}

export const addSchoolToPriorityList = (school) => {
	return {
		type: 'ADD_SCHOOL_TO_PRIORITY_LIST',
		payload: {
            prioritySchools: school,
			openSearch: false,
		} 
	};
}

export const resetSavedRow = (id) => {
	return {
		type: 'RESET_SAVED_ROW',
		payload: { id } 
	};
}

export const getContractTypes = () => {
	return function(dispatch){

		dispatch({
			type: 'GET_CONTRACT_TYPES_PENDING', 
			payload: {ct_pending: true} 
		}); 

		axios.get('/getContractTypes')
			 .then((response) => {
			 	dispatch({
			 		type: 'GET_CONTRACT_TYPES_DONE', 
			 		payload: {
			 			contractTypes: response.data,
			 			ct_pending: false,
			 		}
			 	});
			 })
			 .catch((err) => {
			 	dispatch({type: 'GET_CONTRACT_TYPES_ERR', payload: {}}); //turn of loader and show error msg
			 });
	}
};

export const getPrioritySchools = () => {
	return function(dispatch){

		dispatch({
			type: 'GET_PRIORITY_SCHOOLS_PENDING', 
			payload: {pending: true} 
		}); 

		axios.get('/getPrioritySchools')
			 .then((response) => {
			 	dispatch({
			 		type: 'GET_PRIORITY_SCHOOLS_DONE', 
			 		payload: {
			 			prioritySchools: response.data,
			 			pending: false,
			 		}
			 	});
			 })
			 .catch((err) => {
			 	dispatch({type: 'GET_PRIORITY_SCHOOLS_ERR', payload: true}); //turn of loader and show error msg
			 });
	}
};

export const getAppOrderSchools = () => {
	return function(dispatch){

		dispatch({
			type: 'GET_APPORDER_SCHOOLS_PENDING', 
			payload: {pending: true} 
		}); 

		axios.get('/getApplicationCollege')
			 .then((response) => {
			 	dispatch({
			 		type: 'GET_APPORDER_SCHOOLS_DONE', 
			 		payload: {
			 			appOrderSchools: response.data,
			 			pending: false,
			 		}
			 	});
			 })
			 .catch((err) => {
			 	dispatch({type: 'GET_APPORDER_SCHOOLS_ERR', payload: true}); //turn of loader and show error msg
			 });
	}
};

export const searchForCollege = (val) => {
	return function(dispatch){

		dispatch({
			type: 'COLLEGE_SEARCH_PENDING', 
			payload: {pending: true} 
		}); 

		axios.post('/ajax/searchForCollegesForSales', {input: val})
			 .then((response) => {
			 	console.log('response: ', response.data);
			 	dispatch({
			 		type: 'COLLEGE_SEARCH_DONE', 
			 		payload: {
			 			searchResults: response.data,
			 			search_pending: false,
			 		}
			 	});
			 })
			 .catch((err) => {
			 	dispatch({type: 'COLLEGE_SEARCH_ERR', payload: true}); //turn of loader and show error msg
			 });
	}
}

export const saveChanges = (school = {}) => {
	return function(dispatch){

		dispatch({
			type: 'SAVE_EDITS_TO_PRIORITY_SCHOOLS_PENDING', 
			payload: {save_pending: true} 
		}); 

		axios.post('/saveEditsToPrioritySchools', school)
			 .then((response) => {
			 	dispatch({
			 		type: 'SAVE_EDITS_TO_PRIORITY_SCHOOLS_DONE', 
			 		payload: {
			 			school,
			 			save_pending: false,
			 		}
			 	});
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: 'SAVE_EDITS_TO_PRIORITY_SCHOOLS_ERR', 
			 		payload: {
			 			err_row: true, 
			 			err_row_msg: 'There was a problem saving this row. Check to make sure non of the rows are empty.'
			 		}
			 	});
			 });
	}
}

export const loadtabData_sc = (val,gdata) => {
	var resultElement = document.getElementById(val+'Div');
	resultElement.innerHTML = '';
	axios.post('/ajax/getScholarshipTargeting', {input: val ,gdata: gdata})
	.then((response) => {
		resultElement.innerHTML = response.data;
	})
	.catch((err) => {
	});
}

export const loadpopup = (gdata) => {
	var resultElement = document.getElementById('impData');
	resultElement.innerHTML = '';
	axios.post('/ajax/getscholarshippopup', {input: 'popup',gdata: gdata})
	.then((response) => {
		resultElement.innerHTML = response.data;
	})
	.catch((err) => {
	});	
}

export const nextFun = (curr,next) => {
	return function(){
		document.getElementById('step'+curr).style.display="none";
		document.getElementById('step'+next).style.display="block";
	}
}

export const hideFun = (curr) => {
	return function(){
		//alert(JSON.stringify(ff));
		document.getElementById('step1').style.display="none";
		document.getElementById('step2').style.display="none";
		//document.getElementById('step3').style.display="none";
		document.getElementById('step'+curr).style.display="block";
		document.getElementById('activetab').innerHTML = curr;
		
	}
}

export const checkSelect = (coption) => {
	//return function(){
		if(coption=="country"){
			var cvalue = document.getElementById('country_filter').value;
			if(cvalue=="United States"){
				document.getElementById('stateMainDiv').style.display="block";
				document.getElementById('cityMainDiv').style.display="block";
			}else{
				document.getElementById('stateMainDiv').style.display="none";
				document.getElementById('cityMainDiv').style.display="none";
			}
		}
		
		if(coption=="military"){
			var sval = document.getElementById('inMilitary_filter').value;
			if(sval=="1"){
				document.getElementById('militaryselect').style.display="block";
			}else{
				document.getElementById('militaryselect').style.display="none";
			}
		}
	//}
}

export const checkchkbox = (coption,val,subid='') => {
	//return function(){
		if(coption=="country"){
			if(val=='all'){
				document.getElementById('countryDiv').style.display="none";
				document.getElementById('stateMainDiv').style.display="none";
				document.getElementById('cityMainDiv').style.display="none";
			}else{
				document.getElementById('countryDiv').style.display="block";
				//document.getElementById('stateMainDiv').style.display="block";
				//document.getElementById('cityMainDiv').style.display="block";				
			}
		}
		
		if(coption=="state"){
			if(val=='all'){
				document.getElementById('stateDiv').style.display="none";
				document.getElementById('cityMainDiv').style.display="none";
			}else{
				document.getElementById('stateDiv').style.display="block";
				document.getElementById('cityMainDiv').style.display="block";
			}
		}
		
		if(coption=="city"){
			if(val=='all'){
				document.getElementById('cityDiv').style.display="none";
			}else{
				document.getElementById('cityDiv').style.display="block";	
			}
		}
		
		if(coption=="department"){
			if(val=='all'){
				document.getElementById('majorDeptDiv').style.display="none";
			}else{
				document.getElementById('majorDeptDiv').style.display="block";	
			}
		}
		
		if(coption=="ethnicty"){
			if(val=='all'){
				document.getElementById('ethnicityDiv').style.display="none";
			}else{
				document.getElementById('ethnicityDiv').style.display="block";	
			}
		}
		
		if(coption=="religion"){
			if(val=='all'){
				document.getElementById('religionDiv').style.display="none";
			}else{
				document.getElementById('religionDiv').style.display="block";	
			}
		}
		
		if(coption=="submajor"){
			if(val=='all'){
				document.getElementById('subdiv'+subid).style.display="none";
			}else{
				document.getElementById('subdiv'+subid).style.display="block";	
			}
		}
		
	//}
}

export const getCity = (val ='') => {
	if(val!=''){
		var html = '<option value="">Select a city...</option>';
		var resultElement = document.getElementById('city_filter');
		axios.post('/ajax/homepage/getCityByStateFilt', {state_name : val})
		.then((response) => {
			//console.log(response);
			var fdata = response.data;
			for (var i = 0; i < fdata.length; i++) {
				html +='<option value="'+fdata[i]+'">'+fdata[i]+'</option>';
			}
			resultElement.innerHTML = html;
		})
		.catch((err) => {
		});
	}
}

export const onChangeFunc = (did) => {
	var html = '';
	axios.get('/ajax/getMajorByDepartmentWithIds/'+did)
	.then((response) => {
		for (const [key, value] of Object.entries(response.data)) {
			html += '<option key='+key+' value="'+value+'" data-pid='+did+'>'+value+'</option>';
		}
		
		//console.log(html);
		document.getElementById("subdept_"+did).innerHTML = html;
	})
	.catch((err) => {
	});
}



export const loadtabData = (val) => {
	
		var form = document.getElementById("form2"); 
		var isedit = document.getElementById("isedit").innerHTML;
		
		/*----------------------------------------------*/
		if (!form || !form.nodeName || form.nodeName.toLowerCase() != "form") return null;
		var changed = [], n, c, def, o, ol, opt;
		
		
		
		if (changed.length != 0) {
			if (confirm('Do you want to save the changes?')){
				document.getElementById("isedit").innerHTML =1;
			}else{
				document.getElementById("isedit").innerHTML =0;
			}
		}
		
		
		if(document.getElementById("isedit").innerHTML==0 || document.getElementById("isedit").innerHTML==2){
			document.getElementById("isedit").innerHTML = 0;
			var Prevmenu = document.getElementById("activemenu").innerHTML;
			document.getElementById("activemenu").innerHTML = val;
			
			if(Prevmenu!='default'){
				document.getElementById(Prevmenu).removeAttribute("class", "active");
			}
			document.getElementById(val).setAttribute("class", "active");
			
			document.getElementById('defaultDiv').style.display="none";		
			document.getElementById(Prevmenu+'Div').style.display="none";	
			document.getElementById(val+'Div').style.display="block";	
			
			
		}else if(document.getElementById("isedit").innerHTML==1){
			document.getElementById('frm2btn').click(); 
			document.getElementById("isedit").innerHTML = 2;
			return true;
		}
		return true;
	
}