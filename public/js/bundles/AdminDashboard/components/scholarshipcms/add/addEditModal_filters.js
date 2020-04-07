import React from 'react';
import Targeting from './../../Targeting';
import { isEmpty, findIndex } from 'lodash';
import {loadtabData, loadtabData_sc, checkSelect, checkchkbox, onChangeFunc, getCity,getScholarshipData} from './../../../actions/pickScholarshipActions'
import $ from "jquery";
import update from 'immutability-helper';
import {sortCol, getScholarships, getAllCountries, getAllStates, getAllDepartments, getAllMilitaries, getAllEthnicities, getAllReligionsCustom, getAllProviders, deleteScholarship, getAllDates,getAllMajorDepartments} from './../../../actions/scholarshipscmsActions';


export default class AddEditModal_Step2 extends React.Component{
	
	constructor(props){
		super(props);
		this.state = {
			country_filter_array: [],
			state_filter_array: [],
			city_filter_array: [],
			start_date_array: [],
            financial_fil_array: [],
			military_affiliation_array: [],
			ethnicity_filter_array: [],
			religion_filter_array: [],
			major_filter_array: [],
			majorid_filter_array: [],
			majorsub_filter_array:[],
			major_dept:[],
			
			gpaMin_filter: '',
			gpaMax_filter: '',
			satMin_filter: '',
			satMax_filter: '',
			actMin_filter: '',
			actMax_filter: '',
			toeflMin_filter: '',
			toeflMax_filter: '',
			ieltsMin_filter: '',
			ieltsMax_filter: '',
			ageMin_filter: '',
			ageMax_filter: '',
		}
		
		this.handle_financial_Check = this.handle_financial_Check.bind(this)
		this.onSelectFinancial = this.onSelectFinancial.bind(this)
		this.handledelfinancialItem = this.handledelfinancialItem.bind(this)
		
		this.handle_startdate_Check = this.handle_startdate_Check.bind(this)
		this.onSelectStartDate = this.onSelectStartDate.bind(this)
		this.handledel_startdate_Item = this.handledel_startdate_Item.bind(this)
		
		this.handle_military_Check = this.handle_military_Check.bind(this)
		this.onSelectMilitary = this.onSelectMilitary.bind(this)
		this.handledel_military_Item = this.handledel_military_Item.bind(this)
		
		this.handle_country_Check = this.handle_country_Check.bind(this)
		this.onSelectCountry = this.onSelectCountry.bind(this)
		this.handledel_country_Item = this.handledel_country_Item.bind(this)
		
		this.handle_state_Check = this.handle_state_Check.bind(this)
		this.onState_change = this.onState_change.bind(this)
		this.handledel_state_Item = this.handledel_state_Item.bind(this)
		
		this.handle_city_Check = this.handle_city_Check.bind(this)
		this.onSelectCity = this.onSelectCity.bind(this)
		this.handledel_city_Item = this.handledel_city_Item.bind(this)
		
		this.handle_ethnicity_Check = this.handle_ethnicity_Check.bind(this)
		this.onSelectethnicity = this.onSelectethnicity.bind(this)
		this.handledel_ethnicity_Item = this.handledel_ethnicity_Item.bind(this)
		
		this.handle_religion_Check = this.handle_religion_Check.bind(this)
		this.onSelectreligion = this.onSelectreligion.bind(this)
		this.handledel_religion_Item = this.handledel_religion_Item.bind(this)
		
		this.handle_major_Check = this.handle_major_Check.bind(this)
		this.onSelectmajor = this.onSelectmajor.bind(this)
		this.handledel_major_Item = this.handledel_major_Item.bind(this)
		this.handledel_major_hs = this.handledel_major_hs.bind(this)
		//this.onChangeState = this.onChangeState.bind(this)
		
		this.onSelectsubmajor = this.onSelectsubmajor.bind(this)
		this.count_obj_length = this.count_obj_length.bind(this)
		
		
	}
	
	
	
	componentDidMount(){
		let {setInput} = this.props;
		setInput("country_filter_array", this.state.country_filter_array);
		setInput("state_filter_array", this.state.state_filter_array);
		setInput("city_filter_array", this.state.city_filter_array);
		setInput("start_date_array", this.state.start_date_array);
		setInput("financial_fil_array", this.state.financial_fil_array);
		setInput("military_affiliation_array", this.state.military_affiliation_array);
		setInput("ethnicity_filter_array", this.state.ethnicity_filter_array);
		setInput("religion_filter_array", this.state.religion_filter_array);
		
		setInput("gpaMin_filter", this.state.gpaMin_filter);
		setInput("gpaMax_filter", this.state.gpaMax_filter);
		setInput("satMin_filter", this.state.satMin_filter);
		setInput("satMax_filter", this.state.satMax_filter);
		setInput("actMin_filter", this.state.actMin_filter);
		setInput("actMax_filter", this.state.actMax_filter);
		setInput("toeflMin_filter", this.state.toeflMin_filter);
		setInput("toeflMax_filter", this.state.toeflMax_filter);
		setInput("ieltsMin_filter", this.state.ieltsMin_filter);
		setInput("ieltsMax_filter", this.state.ieltsMax_filter);
		setInput("ageMin_filter", this.state.ageMin_filter);
		setInput("ageMax_filter", this.state.ageMax_filter);
		
		setInput("major_filter_array", this.state.major_filter_array);
		setInput("majorsub_filter_array", this.state.majorsub_filter_array);
		
		getAllStates();
		getAllDepartments();
		getAllEthnicities();
		getAllReligionsCustom();
		getAllMilitaries();
		
		//console.log(this.state);
		
	}
	
	
	onChangeMajor(e) {
		let value = e.target.value;
	}
	
	/*onChangeState(e){
		let value = e.target.value;
		if(value!=''){
			getCity(value);
		}
	}*/

	onChangetab(tabname){
		loadtabData(tabname);
	}
	
	onCheckOption(checkop,chkval,subid){
		checkchkbox(checkop,chkval,subid);
	}
	
	onSelectOption(soval){
		checkSelect(soval);
	}
	
	
	onSelectCountry(e){
		var fval = e.target.value;
		if(this.handle_country_Check(fval) == false){
			this.state.country_filter_array.push(fval)
			this.setState(
				this.state
			)
			this.state
			//console.log(this.state.country_filter_array)
		}
		if(this.handle_country_Check("United States") == true){
			document.getElementById('stateMainDiv').style.display="block";
			document.getElementById('cityMainDiv').style.display="block";
		}else{
			document.getElementById('stateMainDiv').style.display="none";
			document.getElementById('cityMainDiv').style.display="none";
		}
	}
	
	handledel_country_Item(v){
		for(var i = 0; i < this.state.country_filter_array.length; i++){
			if(this.state.country_filter_array[i] == v){
				var index = this.state.country_filter_array.indexOf(v)
				this.state.country_filter_array.splice(index, 1);
				
				if(v=="United States"){
					document.getElementById('stateMainDiv').style.display="none";
					document.getElementById('cityMainDiv').style.display="none";
				}else{
					document.getElementById('stateMainDiv').style.display="block";
					document.getElementById('cityMainDiv').style.display="block";
				}
			}
		}
		this.setState({
			country_filter_array:this.state.country_filter_array
		})
	}
	
	handle_country_Check(val) {
		return this.state.country_filter_array.includes(val);
	}
	
	onState_change(e){
		var fval = e.target.value;
		if(this.handle_state_Check(fval) == false){
			this.state.state_filter_array.push(fval)
			this.setState(
				this.state
			)
			this.state
			//console.log(this.state.state_filter_array)
		}
		if(fval!=''){
			getCity(fval);
		}
	}
	
	handledel_state_Item(v){
		for(var i = 0; i < this.state.state_filter_array.length; i++){
			if(this.state.state_filter_array[i] == v){
				var index = this.state.state_filter_array.indexOf(v)
				this.state.state_filter_array.splice(index, 1);
			}
		}
		this.setState({
			state_filter_array:this.state.state_filter_array
		})
	}
	
	handle_state_Check(val) {
		return this.state.state_filter_array.includes(val);
	}
	
	onSelectCity(e){
		var fval = e.target.value;
		if(this.handle_city_Check(fval) == false){
			this.state.city_filter_array.push(fval)
			this.setState(
				this.state
			)
			this.state
			//console.log(this.state.city_filter_array)
		}
	}
	
	handledel_city_Item(v){
		for(var i = 0; i < this.state.city_filter_array.length; i++){
			if(this.state.city_filter_array[i] == v){
				var index = this.state.city_filter_array.indexOf(v)
				this.state.city_filter_array.splice(index, 1);
			}
		}
		this.setState({
			city_filter_array:this.state.city_filter_array
		})
	}
	
	handle_city_Check(val) {
		return this.state.city_filter_array.includes(val);
	}
	
	onSelectStartDate(e){
		var fval = e.target.value;
		if(this.handle_startdate_Check(fval) == false){
			this.state.start_date_array.push(fval);
			this.setState(
				this.state
			);
			this.state;
			//console.log(this.state.start_date_array);
			//this.props.setInput("start_date_array", this.state.start_date_array);
		}
	}
	
	handledel_startdate_Item(v){
		for(var i = 0; i < this.state.start_date_array.length; i++){
			if(this.state.start_date_array[i] == v){
				var index = this.state.start_date_array.indexOf(v)
				this.state.start_date_array.splice(index, 1);
			}
		}
		this.setState({
			start_date_array:this.state.start_date_array
		})
	}
	
	handle_startdate_Check(val) {
		return this.state.start_date_array.includes(val);
	}
	
	onSelectFinancial(e){
		var fval = e.target.value;
		if(this.handle_financial_Check(fval) == false){
			this.state.financial_fil_array.push(fval)
			this.setState(
				this.state
			)
			this.state
			//console.log(this.state.financial_fil_array);
			//this.props.setInput("financial_fil_array", this.state.financial_fil_array);
		}
		
	}
	
	handledelfinancialItem(v){
		for(var i = 0; i < this.state.financial_fil_array.length; i++){
			if(this.state.financial_fil_array[i] == v){
				var index = this.state.financial_fil_array.indexOf(v)
				this.state.financial_fil_array.splice(index, 1);
			}
		}
		this.setState({
			financial_fil_array:this.state.financial_fil_array
		})
	}
	
	handle_financial_Check(val) {
		return this.state.financial_fil_array.includes(val);
		
	}
	
	onSelectethnicity(e){
		var fval = e.target.value;
		if(this.handle_ethnicity_Check(fval) == false){
			this.state.ethnicity_filter_array.push(fval)
			this.setState(
				this.state
			)
			this.state
			//console.log(this.state.ethnicity_filter_array);
			//this.props.setInput("ethnicity_filter_array", this.state.ethnicity_filter_array);
		}
	}
	
	handledel_ethnicity_Item(v){
		for(var i = 0; i < this.state.ethnicity_filter_array.length; i++){
			if(this.state.ethnicity_filter_array[i] == v){
				var index = this.state.ethnicity_filter_array.indexOf(v)
				this.state.ethnicity_filter_array.splice(index, 1);
			}
		}
		this.setState({
			ethnicity_filter_array:this.state.ethnicity_filter_array
		})
	}
	
	handle_ethnicity_Check(val) {
		return this.state.ethnicity_filter_array.includes(val);
	}
	
	onSelectreligion(e){
		var fval = e.target.value;
		if(this.handle_religion_Check(fval) == false){
			this.state.religion_filter_array.push(fval)
			this.setState(
				this.state
			)
			this.state
			//console.log(this.state.religion_filter_array)
			//this.props.setInput("religion_filter_array", this.state.religion_filter_array);
		}
	}
	
	handledel_religion_Item(v){
		for(var i = 0; i < this.state.religion_filter_array.length; i++){
			if(this.state.religion_filter_array[i] == v){
				var index = this.state.religion_filter_array.indexOf(v)
				this.state.religion_filter_array.splice(index, 1);
			}
		}
		this.setState({
			religion_filter_array:this.state.religion_filter_array
		})
	}
	
	handle_religion_Check(val) {
		return this.state.religion_filter_array.includes(val);
	}
	
	onSelectMilitary(e){
		var fval = e.target.value;
		if(this.handle_military_Check(fval) == false){
			this.state.military_affiliation_array.push(fval)
			this.setState(
				this.state
			)
			this.state
			//console.log(this.state.military_affiliation_array)
			//this.props.setInput("military_affiliation_array", this.state.military_affiliation_array);
		}
	}
	
	handledel_military_Item(v){
		for(var i = 0; i < this.state.military_affiliation_array.length; i++){
			if(this.state.military_affiliation_array[i] == v){
				var index = this.state.military_affiliation_array.indexOf(v)
				this.state.military_affiliation_array.splice(index, 1);
			}
		}
		this.setState({
			military_affiliation_array:this.state.military_affiliation_array
		})
	}
	
	handle_military_Check(val) {
		return this.state.military_affiliation_array.includes(val);
	}
	
	onSelectmajor(e){
		var fval = e.target.value;
		
		for (let node of e.target.children) {
			if (node.value === e.target.value) {
				var did = node.getAttribute('data-id');
			}
		}
		
		var degreelevel = [ "1", "2", "3", "4", "5"];
		
		if(this.handle_major_Check(did) == false){
			this.state.major_filter_array.push({"id": did,"value": fval,"type":"all","degreelevel":degreelevel});
			onChangeFunc(did);
		
			this.setState(
				this.state
			)
			this.state
			console.log(this.state.major_filter_array)
		}
		
	}
	
	handledel_major_Item(v){
		for(var i = 0; i < this.state.major_filter_array.length; i++){
			if(this.state.major_filter_array[i].id == v){
				var index = this.state.major_filter_array.indexOf(v)
				this.state.major_filter_array.splice(index, 1);
			}
		}
		this.setState({
			major_filter_array:this.state.major_filter_array
		})
		
		this.setState(
			this.state
		)
		this.state
		//console.log(this.state.major_filter_array)
	}
	
	handle_major_Check(id) {
		return this.state.major_filter_array.some(function(el) {
			return el.id === id;
		}); 
	}
	
	onSelectsubmajor(e){
		var fval = e.target.value;
		for (let nodec of e.target.children) {
			if (nodec.value === e.target.value) {
				var did = nodec.getAttribute('key');
				var pid = nodec.getAttribute('data-pid');
			}
		}
		
		
		var degreelevel = [ "1", "2", "3", "4", "5"];
		if(this.handle_majorsub_Check(did) == false){
			this.state.majorsub_filter_array.push({"pid":pid, "id": did, "value": fval, "degreelevel": degreelevel});
			this.setState(
				this.state
			)
			this.state
			console.log(this.state.majorsub_filter_array);
		}
		
		$("#chk1_"+pid).hide();
		$("#chk2_"+pid).hide();
		$("#chk3_"+pid).hide();
		$("#chk4_"+pid).hide();
		$("#chk5_"+pid).hide();
		var data = this.state.major_filter_array;
		var commentIndex = data.findIndex(function(c) { 
			return c.id == pid;
		});
		
		//console.log(commentIndex);
		data[commentIndex].degreelevel = [];
		this.setState(
			this.state
		)
		this.state
		
	}
	
	handledel_majorsub_Item(v){
		var pcount = 0;
		for(var i = 0; i < this.state.majorsub_filter_array.length; i++){
			if(this.state.majorsub_filter_array[i].id == v){
				var index = this.state.majorsub_filter_array.indexOf(v)
				this.state.majorsub_filter_array.splice(index, 1);
				//pcount = this.count_obj_length(v.pid);
			}
		}
		this.setState({
			majorsub_filter_array:this.state.majorsub_filter_array
		})
		
		this.setState(
			this.state
		)
		this.state
		
	}
	
	handle_majorsub_Check(id) {
		return this.state.majorsub_filter_array.some(function(el) {
			return el.id === id;
		}); 
	}
	
	count_obj_length(did){
		var data  = this.state.majorsub_filter_array
		var count = 0;
		for (const [key, value] of Object.entries(data)) {
			if(value==did){
				count = count + 1;
			}
		}
		return count;
	}
	
	change_major_type(valtype,did){
		checkchkbox("submajor",valtype,did);
		
		var data = this.state.major_filter_array;
		var commentIndex = data.findIndex(function(c) { 
			return c.id == did; 
		});
		
		data[commentIndex].type = valtype;
		this.setState(
			this.state
		)
		this.state
		
		//console.log(this.state.major_filter_array);
		
	}
	
	change_parent_chkobjval(did){
		var degreelevel = [];
		$("input[name='chk_"+did+"[]']:checked").each(function(){degreelevel.push($(this).val());});
		
		var data = this.state.major_filter_array;
		var commentIndex = data.findIndex(function(c) { 
			return c.id == did; 
		});
		
		data[commentIndex].degreelevel = degreelevel;
		this.setState(
			this.state
		)
		this.state
		
		//console.log(this.state.major_filter_array);
	}
	
	change_sub_chkobjval(did){
		var degreelevel = [];
		$("input[name='subchk_"+did+"[]']:checked").each(function(){degreelevel.push($(this).val());});
		
		var data = this.state.majorsub_filter_array;
		var commentIndex = data.findIndex(function(c) { 
			return c.id == did; 
		});
		
		data[commentIndex].degreelevel = degreelevel;
		this.setState(
			this.state
		)
		this.state
		
		//console.log(this.state.majorsub_filter_array);
	}
	
	handledel_major_hs(idd,type){
		document.getElementById('mj'+idd).style.display="block";
	}
	
	render(){
		let {financial_fil_array,start_date_array,military_affiliation_array,country_filter_array,state_filter_array,city_filter_array,ethnicity_filter_array,religion_filter_array,major_filter_array,majorsub_filter_array,majorid_filter_array,major_dept} = this.state;
		let {data, next, prev, setInput,countries, states, depts, mdepts, startdatea, militaries, ethnicities, religions} = this.props;
		let {financial_val,interested_in_aid,interested_school_type, profileCompletion,militaryAffiliation,college_users,hs_users,educationLevel,uploadLevel,transcript_filter,financialInfo_filter,ielts_fitler,toefl_filter,resume_filter,passport_filter,essay_filter,other_filter,startDateLevel,countryDs,stateDs,cityDs,eth_filterSel,rgs_filterSel,ageMin_filter_n,ageMax_filter_n,gpaMinLevel,gpaMaxLevel,satMinLevel,satMaxLevel,actMinLevel,actMaxLevel,toeflMinLevel,toeflMaxLevel,ieltsMinLevel,ieltsMaxLevel,majorDeptVal,chk1,chk2,chk3,chk4,chk5} = '';
		
		let countryLevel = 'none';
		let stateLevel = "none";
		let cityLevel = "none";
		let statesubLevel = "none";
		let citysubLevel = "none";
		let eth_filterVal = 'none';
		let rgs_filterVal = 'none';
		
		//let militaries = getAllMilitaries();
		//let states = getAllStates();
		
		let majorDeptArr = Array();
		let startDate_array = Array();
		let financial_data = Array();
		let interested_in_aid_array = Array();
		let interested_school_type_array = Array();
		let inMilitary_array = Array();
		let profileCompletion_array = Array();
		let militaryAffiliation_array = Array();
		let educationLevel_array = Array();
		let uploads_array = Array();
		let gpa_filter_array = Array();
		
		let country_array = Array();
		let state_array = Array();
		let city_array = Array();
		
		let country_type ="all";
		let state_type ="all";
		let city_type = "all";
		let genderLevel = "all";
		let eth_filterLevel = "all";
		let rgs_filterLevel = "all";
		let majorDeptDegreeLevel = "all";
		let inMilitary = 0;
		let display_style ="none";
		
		let ageMin_filter_array = Array();
		let ageMax_filter_array = Array();
		let gender_array = Array();
		let eth_filter_array = Array();
		let rgs_filter_array = Array();
		
		let gpaMin_filter_array = Array();
		let gpaMax_filter_array = Array();
		let satMin_filter_array = Array();
		let satMax_filter_array = Array();
		let actMin_filter_array = Array();
		let actMax_filter_array = Array();
		let toeflMin_filter_array = Array();
		let toeflMax_filter_array = Array();
		let ieltsMin_filter_array = Array();
		let ieltsMax_filter_array = Array();
		
		let majorDeptDegree_array = Array();
		//console.log(mdepts);
		
		const filterLength = isEmpty(data.filter) ? 0 : data.filter.length;
		if(filterLength>0){
			{data.filter.map((item, i) => {
				if(item.filter == "gpa_filter"){
					gpa_filter_array.push(item.gpa_filter);
				}
				
				if(item.filter == "country"){
					if(item.country){
						country_array = item.country;
						this.state.country_filter_array = country_array
					}
					country_type  = item.type;
					
				}
				if(country_type!= "all"){
					countryLevel = "block";
				}else{
					countryLevel = "none";
				}
				if(item.filter == "state"){
					if(item.state){
						state_array = item.state;
						this.state.state_filter_array = state_array
					}
					state_type  = item.type;
					
				}
				if(state_type!= "all" && this.handle_country_Check("United States") == true){
					stateLevel = "block";
					cityLevel = "block";
					statesubLevel = "block";
				}else{
					stateLevel = "none";
					cityLevel = "none";
					statesubLevel = "none";
				}
				if(item.filter == "city"){
					if(item.city){
						city_array = item.city;
						this.state.city_filter_array = city_array
					}
					
					city_type  = item.type;
					
				}
				if(city_type!= "all"){ citysubLevel = "block"; }else{ citysubLevel = "none"; }
				
				if(item.filter == "startDateTerm"){
					if(item.startDateTerm){
						startDate_array = item.startDateTerm;
						this.state.start_date_array = startDate_array
					}
				}
				
				if(item.filter == "financial"){
					if(item.financial){
						financial_data = item.financial;
						this.state.financial_fil_array = financial_data
					}
					
				}
				if(item.filter == "interested_in_aid"){
					interested_in_aid_array = item.interested_in_aid;
				}
				
				if(item.filter == "interested_school_type"){
					interested_school_type_array = item.interested_school_type;
				}
				
				if(item.filter == "majorDeptDegree"){ 
					majorDeptDegree_array = item.majorDeptDegree;
					//console.log(majorDeptDegree_array);
					var degreelevel =[];
					var array1 =[];
					var array2 =[];
					for(var i =0;i< majorDeptDegree_array.length; i++){
						var answer_array = majorDeptDegree_array[i].split(',');
						//console.log(answer_array);
						if(answer_array[1]==''){
							var deptIndex = depts.findIndex(function(c) { 
								return c.id == answer_array[0]; 
							});
							
							var commentIndex = array1.findIndex(function(c) { 
								return c.id == answer_array[0]; 
							});
							if(commentIndex != -1){
								array1[commentIndex].degreelevel.push(answer_array[2]);
							}else{
								array1.push({"id": answer_array[0],"value": depts[deptIndex].name,"type":"all","degreelevel":degreelevel});
							}
							
						}else{
							//console.log(answer_array[2]);
							//degreelevel = degreelevel.push(answer_array[2]);
							
							var deptIndex = depts.findIndex(function(c) { 
								return c.id == answer_array[0]; 
							});
							
							var mdeptIndex = mdepts.findIndex(function(c) { 
								return c.id == answer_array[1]; 
							});
							
							
							
							var commentIndex = array2.findIndex(function(c) { 
								return c.id == answer_array[1]; 
							});
							
							//console.log(commentIndex);
							if(commentIndex != -1){
								array2[commentIndex].degreelevel.push(answer_array[2]);
							}else{
								array2.push({"pid":answer_array[0], "id": answer_array[1],"value": mdepts[mdeptIndex].name,"type":"include","degreelevel":[answer_array[2]]});
							}
							
							var commentIndex1 = array1.findIndex(function(c) { 
								return c.id == answer_array[0]; 
							});
							//console.log(commentIndex1);
							if(commentIndex1 == -1){
								array1.push({"id": answer_array[0],"value": depts[deptIndex].name,"type":"include","degreelevel":[]});
							}
						}
					}
					
					
					//console.log(array1);
					//console.log(array2);
					
					this.state.major_filter_array = array1;
					this.state.majorsub_filter_array = array2;
					
					majorDeptDegreeLevel  = item.type;			
					//this.state.major_filter_array = majorDeptDegree_array;		
					//this.state.majorsub_filter_array
				}
				if(majorDeptDegreeLevel!= "all"){ majorDeptVal = "block"; }else{ majorDeptVal = "none"; }
				
				if(item.filter == "gpaMin_filter"){ 
					gpaMin_filter_array = item.gpaMin_filter;	
					this.state.gpaMin_filter = gpaMin_filter_array;
				}
				if(item.filter == "gpaMax_filter"){ 
					gpaMax_filter_array = item.gpaMax_filter;	
					this.state.gpaMax_filter = gpaMax_filter_array;			
				}
				if(item.filter == "satMin_filter"){ 
					satMin_filter_array = item.satMin_filter;
					this.state.satMin_filter = satMin_filter_array;			
				}
				if(item.filter == "satMax_filter"){ 
					satMax_filter_array = item.satMax_filter;
					this.state.satMax_filter = satMax_filter_array;	
				}
				if(item.filter == "actMin_filter"){ 
					actMin_filter_array = item.actMin_filter;
					this.state.actMin_filter = actMin_filter_array;					
				}
				if(item.filter == "actMax_filter"){ 
					actMax_filter_array = item.actMax_filter;
					this.state.actMax_filter = actMax_filter_array;
					
				}
				if(item.filter == "toeflMin_filter"){ 
					toeflMin_filter_array = item.toeflMin_filter;
					this.state.toeflMin_filter = toeflMin_filter_array;				
				}
				if(item.filter == "toeflMax_filter"){ 
					toeflMax_filter_array = item.toeflMax_filter;
					this.state.toeflMax_filter = toeflMax_filter_array;	
				}
				if(item.filter == "ieltsMin_filter"){ 
					ieltsMin_filter_array = item.ieltsMin_filter;
					this.state.ieltsMin_filter = ieltsMin_filter_array;	
				}
				if(item.filter == "ieltsMax_filter"){ 
					ieltsMax_filter_array = item.ieltsMax_filter;
					this.state.ieltsMax_filter = ieltsMax_filter_array;
				}
				if(item.filter == "uploads"){
					uploads_array = item.uploads;
				}
				
				if(item.filter == "ageMin_filter"){
					ageMin_filter_array = item.ageMin_filter;
					this.state.ageMin_filter = ageMin_filter_array[0];
				}
				
				if(item.filter == "ageMax_filter"){
					ageMax_filter_array = item.ageMax_filter;
					this.state.ageMax_filter = ageMax_filter_array[0];
				}
				
				if(item.filter == "gender"){ gender_array = item.gender; }
				
				if(item.filter == "include_eth_filter"){ 
					eth_filterLevel  = item.type; 
					if(item.include_eth_filter){
						eth_filter_array = item.include_eth_filter;
						this.state.ethnicity_filter_array = eth_filter_array
					}
					
				}
				if(eth_filterLevel!= "all"){ eth_filterVal = "block"; }else{ eth_filterVal = "none"; }
				if(item.filter == "include_rgs_filter"){ 
					if(item.include_rgs_filter){
						rgs_filter_array = item.include_rgs_filter;
						this.state.religion_filter_array = rgs_filter_array
					}
					rgs_filterLevel  = item.type;
					
				}
				if(rgs_filterLevel!= "all"){ rgs_filterVal = "block"; }else{ rgs_filterVal = "none"; }
				
				if(item.filter == "educationLevel"){
					if(item.educationLevel){
						educationLevel_array = item.educationLevel;
					}
				}
				if(item.filter == "inMilitary"){
					if(item.inMilitary){
						inMilitary_array = item.inMilitary;
					}
				}
				if(item.filter == "militaryAffiliation"){
					if(item.militaryAffiliation){
						militaryAffiliation_array = item.militaryAffiliation;
						this.state.military_affiliation_array = militaryAffiliation_array
					}
					
				}
				if(item.filter == "profileCompletion"){
					if(item.profileCompletion){
						profileCompletion_array = item.profileCompletion;
					}
				}
			})}
		}
				
		return(
			<div className="formtargetting">
				<div className="title2">Filter the results you receive in your student recommendations</div>
				<div className="row collapse tableRow">
					<div className="column small-12 large-3 show-for-large-up">
						<div className="adv-filtering-menu-container">
							<ul className="side-nav adv-filtering-menu">
								<li data-filter-tab="location" id="location" className="">
									<a onClick={() => {this.onChangetab('location')}} className="litext">Location</a>
									<div className="change-icon hide"></div>
								</li>
								<li data-filter-tab="startDateTerm" id="startDateTerm" className="">
									<a onClick={() => {this.onChangetab('startDateTerm')}} className="litext">Start Date</a>
									<div className="change-icon hide"></div>
								</li>
								<li data-filter-tab="financial" id="financial" className="">
									<a onClick={() => {this.onChangetab('financial')}} className="litext">Financials</a>
									<div className="change-icon hide"></div>
								</li>
								<li data-filter-tab="typeofschool" id="typeofschool" className="">
									<a onClick={() => {this.onChangetab('typeofschool')}} className="litext">Type of School</a>
									<div className="change-icon hide"></div>
								</li>
								<li data-filter-tab="majorDeptDegree" id="majorDeptDegree" className="">
									<a onClick={() => {this.onChangetab('majorDeptDegree')}} className="litext">Major</a>
									<div className="change-icon hide"></div>
								</li>
								<li data-filter-tab="scores" id="scores" className="">
									<a onClick={() => {this.onChangetab('scores')}} className="litext">Scores</a>
									<div className="change-icon hide"></div>
								</li>
								<li data-filter-tab="uploads" id="uploads" className="">
									<a onClick={() => {this.onChangetab('uploads')}} className="litext">Uploads</a>
									<div className="change-icon hide"></div>
								</li>
								<li data-filter-tab="demographic" id="demographic" className="">
									<a onClick={() => {this.onChangetab('demographic')}} className="litext">Demographic</a>
									<div className="change-icon hide"></div>
								</li>
								<li data-filter-tab="educationLevel" id="educationLevel" className="">
									<a onClick={() => {this.onChangetab('educationLevel')}} className="litext">Education Level</a>
									<div className="change-icon hide"></div>
								</li>
								<li data-filter-tab="militaryAffiliation" id="militaryAffiliation" className="">
									<a onClick={() => {this.onChangetab('militaryAffiliation')}} className="litext">Military Affiliation</a>
									<div className="change-icon hide"></div>
								</li>
								<li data-filter-tab="profileCompletion" id="profileCompletion" className="">
									<a onClick={() => {this.onChangetab('profileCompletion')}} className="litext">Profile Completion</a>
									<div className="change-icon hide"></div>
								</li>
							</ul>
						</div>
					</div>
					<div className="column small-12 large-9">
						<div className="adv-filtering-section-container adv-main-container">
							<div className="row filter-intro-container parentDiv" id="getResult1" data-equalizer="">
							{/* Default Div*/}
							<div className="defaultDiv" id="defaultDiv" style={{display:'block'}}>
								<div className="column small-12 medium-4 ">
									<div className="filter-intro-step" data-equalizer-watch="">
									  <div className="text-center">1</div>
									  <div className="text-center"> <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/step-1-filter.png" /> </div>
									  <div className="text-center textdec"> You receive student recommendations daily, but you're looking for certain kinds of students </div>
									</div>
								</div>
								<div className="column small-12 medium-4">
									<div className="filter-intro-step" data-equalizer-watch="">
									  <div className="text-center">2</div>
									  <div className="text-center"> <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/step-2-filter.png" /> </div>
									  <div className="text-center textdec">Choose what you'd like to filter by and save your changes (menu on the left)</div>
									</div>
								</div>
								<div className="column small-12 medium-4">
									<div className="filter-intro-step" data-equalizer-watch="">
									  <div className="text-center">3</div>
									  <div className="text-center"> <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/step-3-filter.png" /> </div>
									  <div className="text-center textdec">Based on your filters, you will receive recommendations that may be a better fit for your school </div>
									</div>
								</div>
							</div>
							{/* Default Div*/}
							{/* Location Div*/}
							<div className="locationDiv" id="locationDiv" style={{display:'none'}}>
								<div className="filter-crumbs-container">
									<ul className="inline-list filter-crumb-list">
										<li>
											<div className="clearfix">
												<div className="left section">Location: </div>
													{country_array.map((item, j) => {
															countryDs = item
															return <div className="left tag" key={"country"+j}>{item}<span className="remove-tag" onClick={this.handledel_country_Item.bind(this, item)}> x </span></div>
													})}
													
													{state_array.map((item, j) => {
														stateDs = item
														return <div className="left tag" key={"state"+j}>{item}<span className="remove-tag" onClick={this.handledel_state_Item.bind(this, item)}> x </span></div>
													})}
								
													{city_array.map((item, j) => {
														cityDs = item
														
														return <div className="left tag" key={"city"+j}>{item}<span className="remove-tag" onClick={this.handledel_city_Item.bind(this, item)}> x </span></div>
													})}
													
												</div>
											</li>
										</ul>
									</div>
								<div className="row filter-by-location-container filter-page-section" data-section="location">
									<div className="column small-12 large-6">
										<div className="for-usa-students-only-container">
											<div className="row contains-tags-row component clearfix" data-component="country" id="countryMainDiv">
												<div className="make_bold">Country:</div>
												<div className="xhide-for-large-up">
													<p>Choose if you would like to receive students from the USA and/or International students.</p>
													<input className="radio-filter filter-this country_fil" id="all_country_filter" name="country" value="all" type="radio" onClick={() => {this.onCheckOption('country','all')}} defaultChecked={ country_type == "all" ? true : false}/>
													  &nbsp;All
													&nbsp;<input className="radio-filter filter-this country_fil" id="include_country_filter" name="country" value="include" type="radio" onClick={() => {this.onCheckOption('country','include')}} defaultChecked={country_type == "include" ? true : false} /> &nbsp;Include
													&nbsp;<input className="radio-filter filter-this country_fil" id="exclude_country_filter" name="country" value="exclude" type="radio" onClick={() => {this.onCheckOption('country','exclude')}} defaultChecked={country_type == "exclude" ? true : false}/>&nbsp;Exclude
												</div>	
												<div className="selection-row" id="countryDiv" style={{display:countryLevel}}>
													<div>You can select multiple options, just click to add</div>
													<div className="select-option-error">
														<small>Must select at least one option</small>
													</div>
													<select className="select-filter filter-this" name="country" id="country_filter" onChange={this.onSelectCountry} value={(countryDs!="" && countryDs!='undefined') ? countryDs : 'undefined'}>
													<option value="">Select a country...</option>
													{countries.map((item, i) => {
														return <option key={"c"+i} value={item.country_name} data-code={item.country_code}>{item.country_name}</option>
													})}
													</select>
													<br />
													{country_filter_array.map((v) => {
														return <span key={v} className="advFilter-tag" data-tag-id={v} data-type-id="country">{v}<span className="remove-tag" onClick={this.handledel_country_Item.bind(this, v)}> x </span></span>
													})}
													<br />
													
												</div>
											</div>
											<br />
											<div className="row contains-tags-row component clearfix" data-component="state" id="stateMainDiv" style={{display:stateLevel}}>
												<div className="column small-12">
													<div className="make_bold">State:</div>
												</div>
												<div className="hide-for-large-up">
													<p>Choose if you would like to receive students from the USA and/or International students.</p>
												</div>
												<input className="radio-filter filter-this" id="all_state_filter" name="state" value="all" type="radio" onClick={() => {this.onCheckOption('state','all')}} defaultChecked={state_type === "all"}  />&nbsp;All
												&nbsp;<input className="radio-filter filter-this" id="include_state_filter" name="state" value="include" type="radio" onClick={() => {this.onCheckOption('state','include')}} defaultChecked={state_type == "include" ? true : false} />&nbsp;Include
												&nbsp;<input className="radio-filter filter-this" id="exclude_state_filter" name="state" value="exclude" type="radio" onClick={() => {this.onCheckOption('state','exclude')}} defaultChecked={state_type == "exclude" ? true : false} />&nbsp;Exclude
												
												<div className="selection-row" id="stateDiv" style={{display:statesubLevel}}>
													<div>You can select multiple options, just click to add</div>
													<div className="select-option-error">
														<small>Must select at least one option</small>
													</div>
													<select className="select-filter filter-this" name="location_state" id="state_filter" onChange={this.onState_change} value={(stateDs!="" && stateDs!='undefined') ? stateDs : 'undefined'}>
														<option value="">Select a state...</option>
														
													</select>
													<br />
													{state_filter_array.map((v) => {
														return <span key={v} className="advFilter-tag" data-tag-id={v} data-type-id="country">{v}<span className="remove-tag" onClick={this.handledel_state_Item.bind(this, v)}> x </span></span>
													})}
													<br />
												</div>
											</div>
											<br />
											<div className="row contains-tags-row component clearfix" data-component="city" id="cityMainDiv" style={{display:cityLevel}}>
												<div className="column small-12">
													<div className="make_bold">City:</div>
												</div>
												<div className="hide-for-large-up">
													<p>If you would like to include or exclude students from a certain State or City, select your desired location. You can select more than one.</p>
												</div>
												<input className="radio-filter filter-this" id="all_city_filter" name="city" value="all" type="radio" onClick={() => {this.onCheckOption('city','all')}} defaultChecked={city_type == "all" ? true : false} />&nbsp;All
												&nbsp;<input className="radio-filter filter-this" id="include_city_filter" name="city" value="include" type="radio" onClick={() => {this.onCheckOption('city','include')}} defaultChecked={city_type == "exclude" ? true : false} />&nbsp;Include
												&nbsp;<input className="radio-filter filter-this" id="exclude_city_filter" name="city" value="exclude" type="radio" onClick={() => {this.onCheckOption('city','exclude')}} defaultChecked={city_type == "exclude" ? true : false} />&nbsp;Exclude
												
												<div className="selection-row" id="cityDiv" style={{display:citysubLevel}}>
													<div>You can select multiple options, just click to add</div>
													<div className="select-option-error">
														<small>Must select at least one option</small>
													</div>
													<select className="select-filter filter-this" name="location_city" id="city_filter" onChange={this.onSelectCity} value={(cityDs!="" && cityDs!='undefined')  ? cityDs : 'undefined'}>
														if(stateDs!="" || stateDs!='undefined'){
															getCity(stateDs)
														}else{
															<option value="">Select a city...</option>
														}
														
													</select>
													<br />
													{city_filter_array.map((v) => {
														return <span key={v} className="advFilter-tag" data-tag-id={v} data-type-id="country">{v}<span className="remove-tag" onClick={this.handledel_city_Item.bind(this, v)}> x </span></span>
													})}
													<br />
												</div>
											</div>
										</div>
									</div>
									<div className="column small-12 large-6 location-filter-desc-col show-for-large-up">
										<div>Choose if you would like to receive students from the USA and/or International students.</div>
										<div>
											If you would like to include or exclude students from a certain State or City, select your desired location. You can select more than one.
										</div>
									</div>
								</div>
							</div>
							{/* Location Div*/}
							
							{/*startDateTerm Div*/}
							<div className="startDateTermDiv" id="startDateTermDiv" style={{display:'none'}} >
								<div className="filter-crumbs-container">
									<ul className="inline-list filter-crumb-list">
										<li>
											<div className="clearfix">
												<div className="left section">Start Date: </div>
												{startDate_array.map((item, j) => {
													startDateLevel = item;
													return <div className="left tag" key={"sdd"+j}>{startDateLevel}<span className="remove-tag" onClick={this.handledel_startdate_Item.bind(this, item)}> x </span></div>
												})}
											</div>
										</li>
									</ul>
								</div>
								<div className="row filter-by-startDateTerm-container filter-page-section" data-section="startDateTerm" >
									<div className="column small-12 large-6">
										<div className="component" data-component="startDateTerm">
											<label>You can select multiple options, just click to add.</label>
											
											<select className="select-filter filter-this" name="startDateTerm" id="startDateTerm_filter" onChange={this.onSelectStartDate} value={(startDateLevel!='' && startDateLevel!='undefined') ? startDateLevel : '0'}>
											<option value="">Select...</option><option value="Fall 2018">Fall 2018</option><option value="Spring 2018">Spring 2018</option><option value="Fall 2019">Fall 2019</option><option value="Spring 2019">Spring 2019</option><option value="Fall 2020">Fall 2020</option><option value="Spring 2020">Spring 2020</option><option value="Fall 2021">Fall 2021</option><option value="Spring 2021">Spring 2021</option><option value="Fall 2022">Fall 2022</option><option value="Spring 2022">Spring 2022</option><option value="Fall 2023">Fall 2023</option><option value="Spring 2023">Spring 2023</option><option value="Fall 2024">Fall 2024</option><option value="Spring 2024">Spring 2024</option>
											</select>
											
										</div>
									</div>
									<div className="column small-12 large-6">
										<div> Each student on Plexuss tell us when they intend to start school. Select the term(s) you want students you're targeting to apply for. </div>
									</div>
									<div className="column small-12 large-12">
										{start_date_array.map((v) => {
											return <span key={v} className="advFilter-tag" data-tag-id={v} data-type-id="startDateTerm">{v}<span className="remove-tag" onClick={this.handledel_startdate_Item.bind(this, v)}> x </span></span>
										})}
									<br />
									</div>
								</div>
							</div>
							{/*startDateTerm Div*/}
							
							{/*financial Div*/}
							<div className="financialDiv" id="financialDiv" style={{display:'none'}}>
								<div className="filter-crumbs-container">
									<ul className="inline-list filter-crumb-list">
										<li>
											<div className="clearfix">
												<div className="left section">Financial: </div>
												{financial_data.map((item, i) => {
													financial_val = item; 
													return <div className="left tag" key={"fin"+i}>{financial_val}<span className="remove-tag" onClick={this.handledelfinancialItem.bind(this, item)}> x </span></div>
												})}	
											</div>
										</li>
									</ul>
								</div>
								
								{interested_in_aid_array.map((item, i) => {
									interested_in_aid = item;
								})}
								
								<div className="row filter-by-financial-container filter-page-section" data-section="financial">
								
									<div className="column small-12 large-6">
										<div className="component" data-component="financial">
											<label>Select a minimum range.</label>
											<select className="select-filter filter-this" name="financial" id="financial_filter" onChange={this.onSelectFinancial} value={(financial_val!='' && financial_val!="undefined") ? financial_val : ''} >
												<option value="">Select...</option>
												<option value="0.00">0.00</option>
												<option value="0 - 5,000">0 - 5,000</option>
												<option value="5,000 - 10,000">5,000 - 10,000</option>
												<option value="10,000 - 20,000">10,000 - 20,000</option>
												<option value="20,000 - 30,000">20,000 - 30,000</option>
												<option value="30,000 - 50,000">30,000 - 50,000</option>
												<option value="50,000">50,000</option>
											</select>
										</div>
										{financial_fil_array.map((v) => {
											return <span key={v} className="advFilter-tag" data-tag-id={v} data-type-id="financial">{v}<span className="remove-tag" onClick={this.handledelfinancialItem.bind(this, v)}> x </span></span>
										})}
										<br />
										<br />
										<div className="component" data-component="financial">
											<label><input type="checkbox" name="interested_in_aid" id="interested_in_aid" defaultChecked={interested_in_aid}  value="interested_in_aid" className="checkbox-filter filter-this" />&nbsp;Filter by students who are NOT interested in financial aid, grants, and scholarships</label>
										</div>
									</div>
									<div className="column small-12 large-6">
										<div>
										If you would like to target students that are able to contribute financially to their college education, select the minimum amount that they might expect to contribute. These amounts are from the same list we give students to choose from on their profiles.
										</div>
									</div>
								</div>
							</div>
							{/*financial Div*/}
							{/*typeofschool Div*/}
							<div className="typeofschoolDiv" id="typeofschoolDiv" style={{display:'none'}}>
								{interested_school_type_array.map((item, i) => {
									if(item =="0"){
										interested_school_type = "campus_only";
									}else if(item =="1"){
										interested_school_type = "online_only";
									}else{
										interested_school_type = "both";
									}
								})}
								
								<div className="row filter-by-typeofschool-container filter-page-section" data-section="typeofschool">
									<div className="column small-12 large-6">
										<div className="component" data-component="typeofschool">
											<input className="radio-filter filter-this" id="both_typeofschool" name="typeofschool" value="both" type="radio" defaultChecked={interested_school_type == "both" ? true : false} />&nbsp;<label htmlFor="both_typeofschool">Both</label>
											&nbsp;<input className="radio-filter filter-this" id="online_only_typeofschool" name="typeofschool" value="online_only" type="radio" defaultChecked={interested_school_type == "online_only" ? true : false} /> &nbsp;<label htmlFor="online_only_typeofschool">Online Only</label>
											&nbsp;<input className="radio-filter filter-this" id="campus_only_typeofschool" name="typeofschool" value="campus_only" type="radio" defaultChecked={interested_school_type == "campus_only" ? true : false} />&nbsp;<label htmlFor="campus_only_typeofschool">Campus Only</label>
										</div>
									</div>
									<div className="column small-12 large-6">
										<div>
											By default, we will recommend students who are interested in both online and on-campus education. If you'd like to limit your recommendations to only online or on-campus, select one of these options.
										</div>
									</div>
								</div>
							</div>
							{/*typeofschool Div*/}
							{/*majorDeptDegree Div*/}
							<div className="majorDeptDegreeDiv" id="majorDeptDegreeDiv" style={{display:'none'}}>
								{majorDeptDegree_array.map((item, j) => {
									majorDeptArr[0]='';
									majorDeptArr = item.toString().split(',');
								})}
								<div className="row filter-by-major-container filter-page-section" data-section="major">
									<div className="column small-12 large-6">
										<div className="row contains-tags-row component" data-component="department">
											<div className="column small-12">
												<div className="hide-for-large-up">
													<p>If your school is targeting students within a specific major, select the desired majors you'd like to include or exclude. You can select more than one item. These majors are from the same list we give students to choose from on their profiles.</p>
												</div>
												<div className="make_bold">Department: </div>
												<div>Choose one option</div>
												<input className="radio-filter filter-this" id="all_department_filter" name="department" value="all" type="radio" onClick={() => {this.onCheckOption('department','all')}} defaultChecked={majorDeptDegreeLevel == "all" ? true : false} />&nbsp;All departments
												&nbsp;<input className="radio-filter filter-this" id="include_department_filter" name="department" value="include" type="radio" onClick={() => {this.onCheckOption('department','include')}} defaultChecked={majorDeptDegreeLevel == "include" ? true : false} /> &nbsp;Include
												&nbsp;<input className="radio-filter filter-this" id="exclude_department_filter" name="department" value="exclude" type="radio" onClick={() => {this.onCheckOption('department','exclude')}} defaultChecked={majorDeptDegreeLevel == "exclude" ? true : false} />&nbsp;Exclude
												
												<div className="selection-row" id="majorDeptDiv" style={{display:majorDeptVal}}>
													<div>You can select multiple options, just click to add</div>
													<div className="select-option-error">
														<small>Must select at least one option</small>
													</div>
													<select className="select-filter filter-this" name="specificDepartment_filter" id="specificDepartment_filter" onChange={this.onSelectmajor} >
														<option value="">Select...</option>
														
													</select>
												</div>
												
											</div>
										</div>
									</div>
									<div className="column small-12 large-6 show-for-large-up">
										If your school is targeting students within a specific major, select the desired majors you'd like to include or exclude. You can select more than one item. These majors are from the same list we give students to choose from on their profiles.
									</div>
								

									<div className="column small-12 dept-list" id="dept-list" style={major_filter_array.length >0 ? {display:'block'} : {display:'none'}}>
										<div className="dept-list-row">
											<div className="dept-head">Select degree level for all departments</div>
											<div className="opts-row">
												<div className="dept-degree-opts">
													<input defaultChecked={true}  className="toggle-controller" name="certificateProgram" type="checkbox" />
													<input name="dept_certificateProgram" type="hidden" />
												</div>
												<div className="dept-degree-opts">
													<input defaultChecked={true}  className="toggle-controller" name="associates" type="checkbox" />
													<input name="dept_associates" type="hidden" />
												</div>
												<div className="dept-degree-opts">
													<input defaultChecked={true}  className="toggle-controller" name="bachelors" type="checkbox" />
													<input name="dept_bachelors" type="hidden" />
												</div>
												<div className="dept-degree-opts">
													<input defaultChecked={true}  className="toggle-controller" name="masters" type="checkbox" />
													<input name="dept_masters" type="hidden" />
												</div>
												<div className="end dept-degree-opts">
													<input defaultChecked={true}  className="toggle-controller" name="doctorate" type="checkbox" />
													<input name="dept_doctorate" type="hidden" />
												</div>
											</div>
											<div className="opts-label-row">
												<div className="opts-label"><label>Filter is <span>including</span> students interested in:</label></div>
												<div className="opts-label"><label>Certificate Program</label></div>
												<div className="opts-label"><label>Associates</label></div>
												<div className="opts-label"><label>Bachelors</label></div>
												<div className="opts-label"><label>Masters</label></div>
												<div className="end opts-label"><label>Doctorate</label></div>
											</div>
										</div>
										
										<div id="sub-dept-list">
										{major_filter_array.map((v ,i) => {
											
											//console.log(v[i].value);
											//console.log(i);
							return (
								<div className="dept-item" key={i} data-dept={v.value} style={{display:'block'}}>
									<div className="subject">
										<div className="dept-action">
											<div className="remove-dept" onClick={this.handledel_major_Item.bind(this, v.id)}><div>✖</div></div>
											<div className="dept-name">{v.value}</div>
											<div className="show-major"><div className="arrow" id={"arrow"+i} onClick={() => this.handledel_major_hs(i,"block")}></div></div>
											<input name="is_department" value={v.value} data-childof="null" data-id={v.id} type="hidden" />
										</div>
									</div>
										<div className="subject">
										<input value="1" name={"chk_"+v.id+"[]"} id={"chk1_"+v.id} defaultChecked={true}  data-department-degreeof={v.id} className="deg-option" type="checkbox" onChange={() => this.change_parent_chkobjval(v.id)} />
										</div>
										<div className="subject">
											<input value="2" name={"chk_"+v.id+"[]"} id={"chk2_"+v.id} defaultChecked={true} data-department-degreeof={v.id} className="deg-option" type="checkbox" onChange={() => this.change_parent_chkobjval(v.id)} />
										</div>
										<div className="subject">
											<input value="3" name={"chk_"+v.id+"[]"} id={"chk3_"+v.id} defaultChecked={true} data-department-degreeof={v.id} className="deg-option" type="checkbox" onChange={() => this.change_parent_chkobjval(v.id)} />
										</div>
										<div className="subject">
											<input value="4" name={"chk_"+v.id+"[]"} id={"chk4_"+v.id} defaultChecked={true} data-department-degreeof={v.id} className="deg-option" type="checkbox" onChange={() => this.change_parent_chkobjval(v.id)} />
										</div>
										<div className="subject">
											<input value="5" name={"chk_"+v.id+"[]"} id={"chk5_"+v.id} defaultChecked={true} data-department-degreeof={v.id} className="deg-option" type="checkbox" onChange={() => this.change_parent_chkobjval(v.id)} />
										</div>
									
									<div className="major-pane component" data-component="major" id={"mj"+i}>
										<div className="ml15"><b>Major:</b></div>
										<div className="ml15">Choose one option</div>
										<input value="all" name={v.id} id={v+"_all_major"} defaultChecked={true} className="radio-filter filter-all filter-this ml15" type="radio" onClick={() => {this.change_major_type('all',v.id)}} />&nbsp; All &nbsp;
										<input value="include" name={v.id} id={v+"_include_major"} className="radio-filter filter-all filter-this ml15" type="radio" onClick={() => {this.change_major_type('include',v.id)}} />
										&nbsp; Include &nbsp;
										<input value="exclude" name={v.id} id={v+"_exclude_major"} className="radio-filter filter-all filter-this ml15" type="radio" onClick={() => {this.change_major_type('exclude',v.id)}} />
										&nbsp; Exclude &nbsp;
										<div className="hideOnLoad" id={"subdiv"+v.id} style={{display:"none"}}>
											<div className="ml15">You can select multiple options, just click to add.</div>
											<select name="major" className="major-pane-selector select-filter filter-this ml15" onChange={this.onSelectsubmajor} id={"subdept_"+v.id}>
												if(majorsub_filter_array.length>0){
													onChangeFunc(v.id)
												}
											</select>
											<div className="major-list">
											{
												majorsub_filter_array.map((p,j) => {
													if(p.pid == v.id){
														var answer_array = p.degreelevel;	 
												return (
													<div key={"msub"+j} className="dept-item" data-dept={p.value} style={{display:"block"}}>
												  <div className="subject">
													<div className="dept-action">
													  <div className="remove-dept" onClick={this.handledel_majorsub_Item.bind(this, p.id)}>
														<div>✖</div>
													  </div>
													  <div className="dept-name">{p.value}</div>
													  <input name="is_major" value={p.value} data-childof={p.id} data-id={j} type="hidden" />
													</div>
												  </div>
												  <div className="subject">
													<input value="1" name={"subchk_"+p.id+"[]"} id={"subchk_"+p.id} defaultChecked={(answer_array.includes(1) ==true) ? true : false} data-major-degreeof="" className="deg-option" type="checkbox" onChange={() => this.change_sub_chkobjval(p.id)} />
												  </div>
												  <div className="subject">
													<input value="2" name={"subchk_"+p.id+"[]"} id={"subchk_"+p.id} defaultChecked={(answer_array.includes(2) ==true) ? true : false} data-major-degreeof="" className="deg-option" type="checkbox" onChange={() => this.change_sub_chkobjval(p.id)} />
												  </div>
												  <div className="subject">
													<input value="3" name={"subchk_"+p.id+"[]"} id={"subchk_"+p.id} defaultChecked={(answer_array.includes(3) ==true) ? true : false} data-major-degreeof="" className="deg-option" type="checkbox" onChange={() => this.change_sub_chkobjval(p.id)} />
												  </div>
												  <div className="subject">
													<input value="4" name={"subchk_"+p.id+"[]"} id={"subchk_"+p.id} defaultChecked={(answer_array.includes(4) ==true) ? true : false} data-major-degreeof="" className="deg-option" type="checkbox" onChange={() => this.change_sub_chkobjval(p.id)} />
												  </div>
												  <div className="subject">
													<input value="5" name={"subchk_"+p.id+"[]"} id={"subchk_"+p.id} defaultChecked={(answer_array.includes(5) ==true) ? true : false} data-major-degreeof="" className="deg-option" type="checkbox" onChange={() => this.change_sub_chkobjval(p.id)} />
												  </div>
												</div>
												)}
											})}
											</div>
										</div>
									</div>
								</div>
								)
											
									})}	
										</div>
										
									</div>
									
								</div>
							</div>
							{/*majorDeptDegree Div*/}
							{/*Score Div*/}
							<div className="scoresDiv" id="scoresDiv" style={{display:'none'}}>
								{gpaMin_filter_array.map((item, j) => {
									gpaMinLevel = item;
								})}
								
								{gpaMax_filter_array.map((item, j) => {
									gpaMaxLevel = item;
								})}
								
								{satMin_filter_array.map((item, j) => {
									satMinLevel = item;
								})}
								{satMax_filter_array.map((item, j) => {
									satMaxLevel = item;
								})}
								{actMin_filter_array.map((item, j) => {
									actMinLevel = item;
								})}
								{actMax_filter_array.map((item, j) => {
									actMaxLevel = item;
								})}
								{toeflMin_filter_array.map((item, j) => {
									toeflMinLevel = item;
								})}
								{toeflMax_filter_array.map((item, j) => {
									toeflMaxLevel = item;
								})}
								{ieltsMin_filter_array.map((item, j) => {
									ieltsMinLevel = item;
								})}
								{ieltsMax_filter_array.map((item, j) => {
									ieltsMaxLevel = item;
								})}
									
								<div className="row filter-by-scores-container filter-page-section" data-section="scores">
									<div className="column small-12 large-6">
										<div className="row collapse">
											<div className="column small-12 filter-instructions">
												We will recommend students to you within the score ranges you set here.
											</div>
										</div>
										<br />
										<div className="row collapse component" data-component="Hs gpa">
											<div className="column small-3 medium-2 scores-desc">
												<label className="make_bold">GPA:</label>
											</div>
											<div className="column small-4 medium-1 large-4">
												<input type="text" name="gpa_min" id="gpaMin_filter" defaultValue={gpaMinLevel} className="text-filter scores filter-this" placeholder="Min: 0" onChange={(e) => setInput("gpaMin_filter", e.target.value)} />
											</div>
											<div className="column small-3 medium-2 text-center scores-desc">to</div>
											<div className="column small-3 medium-2 large-4 end">
												<input type="text" name="gpa_max" id="gpaMax_filter" defaultValue={gpaMaxLevel} className="text-filter scores filter-this" placeholder="Max: 4.0" onChange={(e) => setInput("gpaMax_filter", e.target.value)} />
											</div>
										</div>
										
										<div className="row collapse component" data-component="SAT">
											<div className="column small-3 medium-2 scores-desc">
												<label className="make_bold">SAT:</label>
											</div>
											<div className="column small-4 medium-1 large-4">
												<input type="text" name="sat_min" id="satMin_filter" defaultValue={satMinLevel} className="text-filter scores filter-this" placeholder="Min: 600" onChange={(e) => setInput("satMin_filter", e.target.value)} />
											</div>
											<div className="column small-3 medium-2 text-center scores-desc">to</div>
											<div className="column small-3 medium-2 large-4 end">
												<input type="text" name="sat_max" id="satMax_filter" defaultValue={satMaxLevel} className="text-filter scores filter-this" placeholder="Max: 2400" onChange={(e) => setInput("satMax_filter", e.target.value)} />
											</div>
										</div>
										
										<div className="row collapse component" data-component="ACT">
											<div className="column small-3 medium-2 scores-desc">
												<label className="make_bold">ACT:</label>
											</div>
											<div className="column small-4 medium-1 large-4">
												<input type="text" name="act_min" id="actMin_filter" defaultValue={actMinLevel} className="text-filter scores filter-this" placeholder="Min: 0" onChange={(e) => setInput("actMin_filter", e.target.value)} />
											</div>
											<div className="column small-2 medium-2 text-center scores-desc">to</div>
											<div className="column small-4 medium-2 large-4 end">
												<input type="text" name="act_max" id="actMax_filter" defaultValue={actMaxLevel} className="text-filter scores filter-this" placeholder="Max: 36" onChange={(e) => setInput("actMax_filter", e.target.value)} />
											</div>
										</div>
										
										<div className="row collapse component" data-component="TOEFL">
											<div className="column small-3 medium-2 scores-desc">
												<label className="make_bold">TOEFL:</label>
											</div>
											<div className="column small-4 medium-1 large-4">
												<input type="text" name="toefl_min" id="toeflMin_filter" defaultValue={toeflMinLevel} className="text-filter scores filter-this" placeholder="Min: 0" onChange={(e) => setInput("toeflMin_filter", e.target.value)} />
											</div>
											<div className="column small-2 medium-2 text-center scores-desc">to</div>
											<div className="column small-4 medium-2 large-4 end">
												<input type="text" name="toefl_max" id="toeflMax_filter" defaultValue={toeflMaxLevel} className="text-filter scores filter-this" placeholder="Max: 120" onChange={(e) => setInput("toeflMax_filter", e.target.value)} />
											</div>
										</div>
										
										<div className="row collapse component" data-component="IELTS">
											<div className="column small-3 medium-2 scores-desc">
												<label className="make_bold">IELTS:</label>
											</div>
											<div className="column small-4 medium-1 large-4">
												<input type="text" name="ielts_min" id="ieltsMin_filter" defaultValue={ieltsMinLevel} className="text-filter scores filter-this" placeholder="Min: 0" onChange={(e) => setInput("ieltsMin_filter", e.target.value)} />
											</div>
											<div className="column small-2 medium-2 text-center scores-desc">to</div>
											<div className="column small-4 medium-2 large-4 end">
												<input type="text" name="ielts_max" id="ieltsMax_filter" defaultValue={ieltsMaxLevel} className="text-filter scores filter-this" placeholder="Max: 9"  onChange={(e) => setInput("ieltsMax_filter", e.target.value)} />
											</div>
										</div>
									</div>
								</div>
							</div>
							{/*Score Div*/}
							
							{/*uploads Div*/}
							<div className="uploadsDiv" id="uploadsDiv" style={{display:'none'}}>
								<div className="filter-crumbs-container">
									<ul className="inline-list filter-crumb-list">
										<li>
											<div className="clearfix">
												<div className="left section">uploads: </div>
												{uploads_array.map((item, j) => {
													uploadLevel = item;
													if(uploadLevel=='transcript_filter'){
														transcript_filter ="transcript";
														return <div className="left tag" key={"up"+j}>transcript</div>
													}else{
														transcript_filter ="";
													}
													
													if(uploadLevel=='financialInfo_filter'){
														financialInfo_filter ="financialinfo";
														return <div className="left tag" key={"up"+j}>financialinfo</div>
													}else{
														financialInfo_filter ="";
													}
													
													if(uploadLevel=='ielts_fitler'){
														ielts_fitler ="ielts";
														return <div className="left tag" key={"up"+j}>ielts</div>
													}else{
														ielts_fitler ="";
													}
													
													if(uploadLevel=='toefl_filter'){
														toefl_filter ="toefl";
														return <div className="left tag" key={"up"+j}>toefl</div>
													}else{
														toefl_filter ="";
													}
													
													if(uploadLevel=='resume_filter'){
														resume_filter ="resume";
														return <div className="left tag" key={"up"+j}>resume</div>
													}else{
														resume_filter ="";
													}
													
													if(uploadLevel=='passport_filter'){
														passport_filter ="passport";
														return <div className="left tag" key={"up"+j}>passport</div>
													}else{
														passport_filter ="";
													}
													
													if(uploadLevel=='essay_filter'){
														essay_filter ="essay";
														return <div className="left tag" key={"up"+j}>essay</div>
													}else{
														essay_filter ="";
													}
													
													if(uploadLevel=='other_filter'){
														other_filter ="other";
														return <div className="left tag" key={"up"+j}>other</div>
													}else{
														other_filter ="";
													}
													
												})}
											</div>
										</li>
									</ul>
								</div>
								<div className="row filter-by-uploads-container filter-page-section" data-section="uploads">
									<div className="column small-12">
										<div className="row">
											<div className="column small-12 filter-instructions">
												In your recommended students, we will give priority to students that have uploaded their:			
											</div>
										</div>
										
										<div className="row">
											<div className="column small-12">
												<input type="checkbox" name="uploads" id="transcript_filter" value="transcript" className="checkbox-filter filter-this" defaultChecked={transcript_filter == "transcript" ? true : false}/>&nbsp;Transcript
											</div>
										</div>
										<div className="row">
											<div className="column small-12">
												<input type="checkbox" name="uploads" id="financialInfo_filter" value="financialInfo" className="checkbox-filter filter-this" defaultChecked={financialInfo_filter == "financialinfo" ? true : false} />&nbsp;Financial Info (Int'l Only)
											</div>
										</div>
										<div className="row">
											<div className="column small-12">
												<input type="checkbox" name="uploads" id="ielts_fitler" value="ielts" className="checkbox-filter filter-this" defaultChecked={ielts_fitler == "ielts" ? true : false} />&nbsp;Copy of IELTS score
											</div>
										</div>
										<div className="row">
											<div className="column small-12">
												<input type="checkbox" name="uploads" id="toefl_filter" value="toefl" className="checkbox-filter filter-this"  defaultChecked={toefl_filter == "toefl" ? true : false} />&nbsp;Copy of TOEFL score
											</div>
										</div>
										<div className="row">
											<div className="column small-12">
												<input type="checkbox" name="uploads" id="resume_filter" value="resume" className="checkbox-filter filter-this" defaultChecked={resume_filter == "resume" ? true : false} />&nbsp;Resume / CV
											</div>
										</div>
										<div className="row">
											<div className="column small-12">
												<input type="checkbox" name="uploads" id="passport_filter" value="passport" className="checkbox-filter filter-this" defaultChecked={passport_filter == "passport" ? true : false} />&nbsp;Passport
											</div>
										</div>
										<div className="row">
											<div className="column small-12">
												<input type="checkbox" name="uploads" id="essay_filter" value="essay" className="checkbox-filter filter-this" defaultChecked={essay_filter == "essay" ? true : false} />&nbsp;Essay
											</div>
										</div>
										<div className="row">
											<div className="column small-12">
												<input type="checkbox" name="uploads" id="other_filter" value="other" className="checkbox-filter filter-this" defaultChecked={other_filter == "other" ? true : false} />&nbsp;Other
											</div>
										</div>
									</div>
								</div>
							</div>
							{/*uploads Div*/}
							{/*demographic Div*/}
							<div className="demographicDiv" id="demographicDiv" style={{display:'none'}}>
								{ageMin_filter_array.map((item, j) => {
									ageMin_filter_n = item;
								})}
								{ageMax_filter_array.map((item, j) => {
									ageMax_filter_n = item;
								})}
								{gender_array.map((item, j) => {
									genderLevel = item;
								})}
								<div className="filter-crumbs-container">
									<ul className="inline-list filter-crumb-list">
										<li>
											<div className="clearfix">
												<div className="left section">demographic: </div>
												 <div className="left tag" key={"age"}>Age : {ageMin_filter_n} - {ageMax_filter_n}</div>
												
												{eth_filter_array.map((item, j) => {
													eth_filterSel = item;
													return <div className="left tag" key={"eth_"+j}>{eth_filterSel}<span className="remove-tag" onClick={this.handledel_ethnicity_Item.bind(this, item)}> x </span></div>
												})}
												{rgs_filter_array.map((item, j) => {
													rgs_filterSel = item;
													return <div className="left tag" key={"rgs_"+j}>{rgs_filterSel}<span className="remove-tag" onClick={this.handledel_religion_Item.bind(this, item)}> x </span></div>
												})}
												
											</div>
										</li>
									</ul>
								</div>
								<div className="row filter-by-demographic-container filter-page-section" data-section="demographic">
									<div className="column small-12 large-6">
										<div className="row component" data-component="Age">
											<div className="column small-3 medium-2 scores-desc">
												<label className="make_bold">Age:</label>
											</div>
											<div className="column small-3">
												<input className='full' name="ageMin_filter" placeholder="Min" defaultValue={ageMin_filter_n} onChange={(e) => setInput("ageMin_filter", e.target.value)}/>
											</div>
											<div className="column small-3 medium-1 text-center scores-desc">to</div>
											<div className="column small-3 end">
												<input className='full' name="ageMax_filter" placeholder="Max" defaultValue={ageMax_filter_n} onChange={(e) => setInput("ageMax_filter", e.target.value)}/>
											</div>
										</div>
										<div className="row contains-tags-row component" data-component="Gender">
											<div className="column small-12">
												<label className="make_bold">Gender:</label><br />
												<input type="radio" name="gender" value="all" id="all_gender_filter" className="radio-filter filter-this" defaultChecked={genderLevel!='undefined' && genderLevel == "all" ? true : false} onChange={()=>{}} />&nbsp;All
												&nbsp;<input type="radio" name="gender" value="include_gender" id="male_only_filter" className="radio-filter filter-this" defaultChecked={genderLevel!="undefined" && genderLevel == "male" ? true : false} onChange={()=>{}} />&nbsp;Males Only
												&nbsp;<input type="radio" name="gender" value="exclude_gender" id="female_only_filter" className="radio-filter filter-this" defaultChecked={genderLevel!="undefined" && genderLevel == "female" ? true : false} onChange={()=>{}} />&nbsp;Females Only
											</div>
										</div>
										<div className="row contains-tags-row component" data-component="Ethnicty">
											<div className="column small-12">
												<label className="make_bold">Gender:</label><br />
												<input type="radio" name="ethnicity" value="all" id="all_eth_filter" className="radio-filter filter-this" onClick={() => {this.onCheckOption('ethnicty','all')}} defaultChecked={eth_filterLevel == "all" ? true : false} />&nbsp;All Ethnicities
												&nbsp;<input type="radio" name="ethnicity" value="include" id="include_eth_filter" className="radio-filter filter-this" onClick={() => {this.onCheckOption('ethnicty','include')}} defaultChecked={eth_filterLevel == "include" ? true : false} />&nbsp;Include
												&nbsp;<input type="radio" name="ethnicity" value="exclude" id="exclude_eth_filter" className="radio-filter filter-this" onClick={() => {this.onCheckOption('ethnicty','exclude')}} defaultChecked={eth_filterLevel == "exclude" ? true : false} />&nbsp;Exclude
												
												<div className="selection-row" id="ethnicityDiv" style={{display:eth_filterVal}}>
													<div>You can select multiple options, just click to add</div>
													<select className="select-filter filter-this" name="ethnicity_filter" id="ethnicity_filter" value={(eth_filterSel!='' && eth_filterSel!='undefined') ? eth_filterSel : '0'} onChange={this.onSelectethnicity}>
														<option value="">Select...</option>
														
													</select>
													<br />
													
												</div>
											</div>
										</div>
										<div className="row contains-tags-row component" data-component="Religion">
											<div className="column small-12">
												<label className="make_bold">Religion:</label><br />
												<input type="radio" name="religion" value="all" id="all_rgs_filter" className="radio-filter filter-this" onClick={() => {this.onCheckOption('religion','all')}} checked={rgs_filterLevel==="all"} />&nbsp;All Religions
												&nbsp;<input type="radio" name="religion" value="include" id="include_rgs_filter" className="radio-filter filter-this" onClick={() => {this.onCheckOption('religion','include')}} defaultChecked={rgs_filterLevel == "include" ? true : false} />&nbsp;Include
												&nbsp;<input type="radio" name="religion" value="exclude" id="exclude_rgs_filter" className="radio-filter filter-this" onClick={() => {this.onCheckOption('religion','exclude')}} defaultChecked={rgs_filterLevel == "exclude" ? true : false} />&nbsp;Exclude
												
												<div className="selection-row" id="religionDiv" style={{display:rgs_filterVal}}>
													<div>You can select multiple options, just click to add</div>
													<select className="select-filter filter-this" name="religion_filter" id="religion_filter" value={(rgs_filterSel!='' && rgs_filterSel!='undefined') ? rgs_filterSel : '0'} onChange={this.onSelectreligion}>
														<option value="">Select...</option>
														
													</select>
													<br />
													{religion_filter_array.map((v) => {
														return <span key={v} className="advFilter-tag" data-tag-id={v} data-type-id="Religion">{v}<span className="remove-tag" onClick={this.handledel_religion_Item.bind(this, v)}> x </span></span>
													})}
												</div>
											</div>
										</div>
									</div>
									
									<div className="column small-12 large-6 show-for-large-up">
										<div>
										Choose an age range for students you are interested in. Students must be at least 13 years old to create an account on Plexuss.
										</div>
										<div>
										By default we will recommend you all ethnicities, but you can select which ethnicities you would like to give priority to include or exclude from your daily recommendations.
										</div>
									</div>

								</div>
							</div>
							{/*demographic Div*/}
							{/*educationLevel Div*/}
							<div className="educationLevelDiv" id="educationLevelDiv" style={{display:'none'}}>
								<div className="filter-crumbs-container">
									<ul className="inline-list filter-crumb-list">
										<li>
											<div className="clearfix">
												<div className="left section">educationLevel: </div>
												{educationLevel_array.map((item, j) => {
													educationLevel = item;
													if(educationLevel=='hsUsers_filter'){
														hs_users ="High school";
														return <div className="left tag" key={"hs"+j}>hsUsers</div>
													}else{
														hs_users ="";
													}
													
													if(educationLevel=='collegeUsers_filter'){
														college_users ="College";
														return <div className="left tag" key={"col"+j}>collegeUsers</div>
													}else{
														college_users ="";
													}
												})}
											</div>
										</li>
									</ul>
								</div>		
								<div className="row filter-by-educationLevel-container filter-page-section" data-section="educationLevel">
									<div className="column small-12 large-6">
										<div className="hide-for-large-up">
											By default, we show you students at all education levels, but if you are interested in students who have completed some college, you can select "College" here.
										</div>
										<div className="row">
											<div className="column small-12">
												<input type="checkbox" name="hs_users" id="hsUsers_filter" className="checkbox-filter filter-this" value="High school" defaultChecked={hs_users == "High school" ? true : false} />&nbsp;High school
											</div>
										</div>
										<div className="row">
											<div className="column small-12">
												<input type="checkbox" name="college_users" id="collegeUsers_filter" className="checkbox-filter filter-this"  defaultChecked={college_users == "College" ? true : false}  />&nbsp;College
												<br />
												<small>(Students who have completed some level of college)</small>
											</div>
										</div>
										<div className="row collapse minMaxError">
											<div className="column small-12">
												At least ONE checkbox must be checked.
											</div>
										</div>
									</div>
									<div className="column small-12 large-6 show-for-large-up">
										<div>
											By default, we show you students at all education levels, but if you are interested in students who have completed some college, you can select "College" here.
										</div>
									</div>
								</div>
							</div>
							{/*educationLevel Div*/}
							{/*militaryAffiliation Div*/}
							<div className="militaryAffiliationDiv" id="militaryAffiliationDiv" style={{display:'none'}}>
							<div className="filter-crumbs-container">
								<ul className="inline-list filter-crumb-list">
									<li>
										<div className="clearfix">
											<div className="left section">militaryAffiliation: </div>
											
											{inMilitary_array.map((item, j) => {
												if(item ==1){
													inMilitary =1;
													display_style ="block";
												}
												return <div className="left tag" key={"ma"+j}>{item==0 ? "No" : "Yes"}</div>
												
											})}
											
											{militaryAffiliation_array.map((item, i) => {
												militaryAffiliation = item;
												return <div className="left tag" key={"ma"+i}>{militaryAffiliation}<span className="remove-tag" onClick={this.handledel_military_Item.bind(this, item)}> x </span></div>
											})}
											
										</div>
									</li>
								</ul>
							</div>		
							<div className="row filter-by-militaryAffilation-container filter-page-section" data-section="militaryAffiliation">
									<div className="column small-12">Military Affiliation</div>
									<div className="column small-12">
										<br />
										<div className="row component" data-component="inMilitary">
											<div className="column small-12 medium-9">
												<label>In Military?</label>
												<select className="select-filter filter-this isProfComp" name="inMilitary" id="inMilitary_filter" onChange={() => {this.onSelectOption('military')}}  defaultValue={inMilitary}>
													<option value=''>Select...</option>
													<option value='0'>No</option>
													<option value='1'>Yes</option>
												</select>
											</div>
										</div>
										<div className="row component miliAffili" data-component="militaryAffiliation" id="militaryselect" style={{display:display_style}}>
											<div className="column small-12 medium-9">
												<label>Military Affiliation</label>
												<select className="select-filter filter-this" name="militaryAffiliation" id="militaryAffiliation_filter" value={(militaryAffiliation!='' && militaryAffiliation!='undefined') ? militaryAffiliation : '0'} onChange={this.onSelectMilitary}>
													<option value="">Select...</option>
													
												</select>
												<br />
												{military_affiliation_array.map((v) => {
													return <span key={v} className="advFilter-tag" data-tag-id={v} data-type-id="startDateTerm">{v}<span className="remove-tag" onClick={this.handledel_military_Item.bind(this, v)}> x </span></span>
												})}
												<br />
											
											</div>
										</div>
									</div>
								</div>
							</div>
							{/*militaryAffiliation Div*/}
							{/*profileCompletion Div*/}
							<div className="profileCompletionDiv" id="profileCompletionDiv" style={{display:'none'}}>
								<div className="filter-crumbs-container">
									<ul className="inline-list filter-crumb-list">
										<li>
											<div className="clearfix">
												<div className="left section">ProfileComplition: </div>
												 {profileCompletion_array.map((item, i) => {
														profileCompletion = item; 
														return <div className="left tag" key={"pc_"+i}>{profileCompletion} %</div>
												 })}
											</div>
										</li>
									</ul>
								</div>
								
								<div className="row filter-by-profileCompletion-container filter-page-section" data-section="profileCompletion">
									<div className="column small-12 large-6">
										<br />
										<div className="row component" data-component="profileCompletion">
											<div className="column small-12 medium-9">
												<label>Profile Completion</label>
												<select className="select-filter filter-this isProfComp" name="profileCompletion" id="profileCompletion_filter" value={(profileCompletion !='' && profileCompletion !='undefined') ? profileCompletion : '0'}>
													<option value=''>Select...</option>
													<option value='10'>10%</option>
													<option value='20'>20%</option>
													<option value='30'>30%</option>
													<option value='40'>40%</option>
													<option value='50'>50%</option>
													<option value='60'>60%</option>
													<option value='70'>70%</option>
													<option value='80'>80%</option>
													<option value='90'>90%</option>
													<option value='100'>100%</option>
												</select>
											</div>
										</div>
									</div>
									<div className="column small-12 large-6">
									<br />
									Select the minimum Profile Completion percentage that a student must reach to be considered a viable candidate for recruitment.
									</div>	
								</div>
							</div>
							{/*profileCompletion Div*/}
							
							</div>
						</div>
					</div>
					
					
					
				</div>
			</div>
		);
	}
}