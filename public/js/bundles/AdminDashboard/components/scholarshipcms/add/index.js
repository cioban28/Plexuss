// index.js

import React from 'react'
import moment from 'moment'
import selectn from 'selectn'
import TinyMCE from 'react-tinymce'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs-fix'
import DocumentTitle from 'react-document-title'
import { spinjs_config } from './../../common/spinJsConfig'
import { isEmpty, findIndex } from 'lodash';

import {hideFun,nextFun} from './../../../actions/pickScholarshipActions';
import AddEditModal_scholarship from './addEditModal_scholarship';
import AddEditModal_filters from './addEditModal_filters';

import {sortCol, getScholarships, getAllCountries, getAllStates, getAllDepartments, getAllMilitaries, getAllEthnicities, getAllReligionsCustom, getAllProviders, deleteScholarship, getAllDates,getAllMajorDepartments,addScholarship} from './../../../actions/scholarshipscmsActions';


class ScholarshipcmsAdd extends React.Component{
	constructor(props){
		super(props);
		this.state = {
			id: '',
			scholarship_name: '',
			scholarshipsub_name: '',
			amount: '',
			description: '',
			submission_id: '',
			deadline: null,
			scholarship_name_valid: '',
			deadline_valid: true,
			amount_valid: '',
		}


		this.state1 = ['scholarship_name','scholarshipsub_name', 'amount', 'description','deadline','scholarship_name_valid','deadline_valid','amount_valid','id','created_at'];

		this._getDescription = this._getDescription.bind(this);
		this._setInput = this._setInput.bind(this);
		this._typeDeadline = this._typeDeadline.bind(this);
		this._submit = this._submit.bind(this);
	}

	componentWillMount(){
		let {dispatch,scholarships} = this.props;
		dispatch(getAllCountries());
		//dispatch(getScholarships());
	}

	_setInput(name, value){
		//console.log(name+"+++"+value);
		let tmp = this.state;
		let mValue = value;
		let name_valid = name + "_valid";

		//could make them const outside
		//but so much simpler to see and manip right where we use them
		//only place currently that needs them too
		let strReg = /^[a-zA-Z0-9_'"#.,\-/: ]*$/g;
		let emailReg = /^[a-zA-Z0-9!#$%&'*+-/=?^_`{|}~\-.]+[@]{1}[a-zA-Z]+[.][a-zA-Z]+$/g;
		let numReg = /^[0-9]+$/g;
		let amountReg = /^[0-9]*[.]*[0-9]*$/g;
		let phoneReg = /^[a-z0-9+\-_()#. ]+$/g;
		let dateReg = /^([0]*[1-9]|[1][0-2])[/]([0][1-9]|[1-2][0-9]|[3][0-1])[/][0-9]{4}$/g;

		tmp[name] = mValue;

		//test to see if valid
		switch(name){
			case 'scholarship_name':
			case 'provider_name':
			case 'contact_fname':
			case 'contact_lname':
			case 'provider_address':
			case 'provider_city':
			case 'website':
				if(mValue.match(strReg) && mValue !== ''){
					tmp[name_valid] = true;
				}else{
					tmp[name_valid] = false;  //only set if not default
				}
				break;
			case 'provider_email':
				if(mValue.match(emailReg) && mValue !== ''){
					tmp[name_valid] = true;
				}else{
					tmp[name_valid] = false;  //only set if not default
				}
				break;
			case 'amount':
				if(mValue.match(amountReg) && mValue !== ''){
					tmp[name_valid] = true;
				}else{
					tmp[name_valid] = false;  //only set if not default
				}
				break;
			case 'numberof':
			case 'provider_zip':
				if(mValue.match(numReg) && mValue !== '' && mValue !== '0'){
					tmp[name_valid] = true;
				}else{
					tmp[name_valid] = false;  //only set if not default
				}
				break;
			case 'provider_phone':
				if(mValue.match(phoneReg) && mValue !== ''){
					tmp[name_valid] = true;
				}else{
					tmp[name_valid] = false;  //only set if not default
				}
				break;
			case 'submission_id':
				if(mValue.match(numReg) || mValue.trim() === ''){
					tmp[name_valid] = true;
				}else{
					tmp[name_valid] = false;  //only set if not default
				}
				break;
			default:

				break;
		}

		this.setState(tmp);
		//console.log(this.state);
	}

	_typeDeadline(val){
		//console.log(val)
		let tmp = this.state;
		let mValue = val;
		let name_valid = name + "_valid";
		let dateReg = /^([0]*[1-9]|[1][0-2])[/]([0][1-9]|[1-2][0-9]|[3][0-1])[/][0-9]{4}$/g;

		//react-datepicker expects type Date versus a string
		//to handle input for datepicker, I used a ref and bound a custom event to catch users typing
		//this delivers a string in e.target.value
		mValue = moment(val, 'MM/DD/YYYY');

		tmp[name] = mValue;
		if(val.match(dateReg) || val.trim() === ''){
				tmp[name_valid] = true;
			}else{
				tmp[name_valid] = false;  //only set if not default
			}

		this.setState(tmp);

	}

	_getDescription(e){
		let val = e.target.getContent();
		this.state.description = val;
	}

	_submit(e){
		e.preventDefault();

		var crrt = document.getElementById('activetab').innerHTML;
		let {close, dispatch,scholarships} = this.props;
		let data = {...this.state};

		//console.log(this.state);

		let data1 = {};
		if(crrt ==1){
			//console.log("ggg");
			for(let i in data){
				if(data.hasOwnProperty(i)){
					if(this.state1.indexOf(i)!=-1 ){
						if(data[i]!=''){data1[i] = data[i];}
						if(i.includes('_valid')){
							delete data1[i];
						}
					}
				}
			}
			if(data1.deadline!=null){ data1.deadline = data1.deadline.format('MM/DD/YYYY'); }
			data1['step'] = 1;
		}
		if(crrt ==2){
			var tabname = document.getElementById('activemenu').innerHTML;
			//console.log("++"+tabname);
			if(tabname=="location"){
				if(document.getElementById("all_country_filter").checked == true){ data1['all_country_filter'] = document.getElementById('all_country_filter').value; }
				if(document.getElementById("include_country_filter").checked == true){ data1['include_country_filter'] = document.getElementById('include_country_filter').value; }
				if(document.getElementById("exclude_country_filter").checked == true){ data1['exclude_country_filter'] = document.getElementById('exclude_country_filter').value; }
				data1['country'] = data['country_filter_array'];

				if(document.getElementById("all_state_filter").checked == true){ data1['all_state_filter'] = document.getElementById('all_state_filter').value; }
				if(document.getElementById("include_state_filter").checked == true){ data1['include_state_filter'] = document.getElementById('include_state_filter').value; }
				if(document.getElementById("exclude_state_filter").checked == true){ data1['exclude_state_filter'] = document.getElementById('exclude_state_filter').value; }
				data1['state'] = data['state_filter_array'];

				if(document.getElementById("all_city_filter").checked == true){ data1['all_city_filter'] = document.getElementById('all_city_filter').value; }
				if(document.getElementById("include_city_filter").checked == true){ data1['include_city_filter'] = document.getElementById('include_city_filter').value; }
				if(document.getElementById("exclude_city_filter").checked == true){ data1['exclude_city_filter'] = document.getElementById('exclude_city_filter').value; }
				data1['city'] = data['city_filter_array'];

			}else if(tabname=="startDateTerm"){
				data1['startDateTerm'] = data['start_date_array'];
			}else if(tabname=="financial"){
				data1['financial'] = data['financial_fil_array'];
				if(document.getElementById("interested_in_aid").checked == true){ data1['interested_in_aid'] = 'true'; }

			}else if(tabname=="typeofschool"){
				if(document.getElementById("both_typeofschool").checked == true){ data1['both_typeofschool'] = document.getElementById('both_typeofschool').value; }
				if(document.getElementById("online_only_typeofschool").checked == true){ data1['online_only_typeofschool'] = document.getElementById('online_only_typeofschool').value; }
				if(document.getElementById("campus_only_typeofschool").checked == true){ data1['campus_only_typeofschool'] = document.getElementById('campus_only_typeofschool').value; }

			}else if(tabname=="majorDeptDegree"){
				/*if(document.getElementById("all_department_filter").checked == true){ data1['all_department_filter'] = document.getElementById('all_department_filter').value; }
				if(document.getElementById("include_department_filter").checked == true){ data1['include_department_filter'] = document.getElementById('include_department_filter').value; }
				if(document.getElementById("exclude_department_filter").checked == true){ data1['exclude_department_filter'] = document.getElementById('exclude_department_filter').value; }
				data1['department'] = document.getElementById('specificDepartment_filter').value;*/

				data1['major_filter_array'] = data['major_filter_array'];
				data1['majorsub_filter_array'] = data['majorsub_filter_array'];

			}else if(tabname=="scores"){
				if(data['gpaMin_filter']){ data1['gpaMin_filter'] = data['gpaMin_filter'];}
				if(data['gpaMax_filter']){ data1['gpaMax_filter'] = data['gpaMax_filter'];}
				if(data['satMin_filter']){ data1['satMin_filter'] = data['satMin_filter'];}
				if(data['satMax_filter']){ data1['satMax_filter'] = data['satMax_filter'];}
				if(data['actMin_filter']){ data1['actMin_filter'] = data['actMin_filter'];}
				if(data['actMax_filter']){ data1['actMax_filter'] = data['actMax_filter'];}
				if(data['toeflMin_filter']){ data1['toeflMin_filter'] = data['toeflMin_filter'];}
				if(data['toeflMax_filter']){ data1['toeflMax_filter'] = data['toeflMax_filter'];}
				if(data['ieltsMin_filter']){ data1['ieltsMin_filter'] = data['ieltsMin_filter'];}
				if(data['ieltsMax_filter']){ data1['ieltsMax_filter'] = data['ieltsMax_filter'];}

			}else if(tabname=="uploads"){
				if(document.getElementById("transcript_filter").checked == true){ data1['transcript_filter'] = document.getElementById('transcript_filter').value; }
				if(document.getElementById("financialInfo_filter").checked == true){ data1['financialInfo_filter'] = document.getElementById('financialInfo_filter').value; }
				if(document.getElementById("ielts_fitler").checked == true){ data1['ielts_fitler'] = document.getElementById('ielts_fitler').value; }
				if(document.getElementById("toefl_filter").checked == true){ data1['toefl_filter'] = document.getElementById('toefl_filter').value; }
				if(document.getElementById("resume_filter").checked == true){ data1['resume_filter'] = document.getElementById('resume_filter').value; }
				if(document.getElementById("passport_filter").checked == true){ data1['passport_filter'] = document.getElementById('passport_filter').value; }
				if(document.getElementById("essay_filter").checked == true){ data1['essay_filter'] = document.getElementById('essay_filter').value; }
				if(document.getElementById("other_filter").checked == true){ data1['other_filter'] = document.getElementById('other_filter').value; }

			}else if(tabname=="demographic"){
				data1['ageMin_filter'] = data["ageMin_filter"];
				data1['ageMax_filter'] = data["ageMax_filter"];
				if(document.getElementById("male_only_filter").checked == true){ data1['male_only_filter'] = document.getElementById('male_only_filter').value; }
				if(document.getElementById("female_only_filter").checked == true){ data1['female_only_filter'] = document.getElementById('female_only_filter').value; }


				if(document.getElementById("include_eth_filter").checked == true){ data1['include_eth_filter'] = document.getElementById('include_eth_filter').value; }
				if(document.getElementById("exclude_eth_filter").checked == true){ data1['exclude_eth_filter'] = document.getElementById('exclude_eth_filter').value; }
				data1['ethnicity'] = data['ethnicity_filter_array'];

				if(document.getElementById("include_rgs_filter").checked == true){ data1['include_rgs_filter'] = document.getElementById('include_rgs_filter').value; }
				if(document.getElementById("exclude_rgs_filter").checked == true){ data1['exclude_rgs_filter'] = document.getElementById('exclude_rgs_filter').value; }
				data1['religion'] = data['religion_filter_array'];

			}else if(tabname=="educationLevel"){
				if(document.getElementById("hsUsers_filter").checked == true){ data1['hsUsers_filter'] = document.getElementById('hsUsers_filter').value; }
				if(document.getElementById("collegeUsers_filter").checked == true){ data1['collegeUsers_filter'] = document.getElementById('collegeUsers_filter').value; }

			}else if(tabname=="militaryAffiliation"){
				if(document.getElementById('inMilitary_filter')!= null){ data1['inMilitary'] = document.getElementById('inMilitary_filter').value; }
				if(document.getElementById('inMilitary_filter')!= null){ data1['militaryAffiliation'] = data['military_affiliation_array']; }


			}else if(tabname=="profileCompletion"){
				data1['profileCompletion'] = document.getElementById('profileCompletion_filter').value;
			}

			data1['step'] = 2;
			data1['tab_name'] = tabname;
			data1["id"] = this.state.id;

		}

		//console.log(data1.toSource());
		dispatch(addScholarship(data1));


	}

	componentWillReceiveProps(np){
		let { dispatch, scholarships } = this.props;
		//console.log("+++"+scholarships.added);
		/*if(scholarships.scholarshipsList){
			let newid = scholarships.scholarshipsList;
			if(this.state.id=='' && newid[0]){
				let tmp = this.state;
				tmp["id"] = '';
				tmp["id"] = newid[0];
				this.setState(tmp);
			}
		}*/
	}


	render(){
		let {id,scholarship_name, scholarshipsub_name, amount,description,deadline,scholarship_name_valid,deadline_valid,amount_valid,editItem} = this.state;
		let {dispatch, scholarships} = this.props;
		//const selectedLength = isEmpty(scholarships.scholarshipsList) ? 0 : scholarships.scholarshipsList.length;

		return (
			<DocumentTitle title="Admin Tools | Add Scholarship">
				<div className="addModal">

					{scholarships.get_sch_pending ?
							<div className="spinner-wrapper">
								<ReactSpinner config={spinjs_config} />
							</div>
						:
					<div className="row i-container overview_container">

						<span id="activetab" style={{display:'none'}}>1</span>
						{/*Tab 1*/}
						<div id="step1">
							<form id="form1" onSubmit={(e) => this._submit(e,1)}>
								<div className="setStep-container">
									<div id="tab1" className="active step-nav" onClick={hideFun('1')}>Scholarship Information</div>
									<div id="tab2" className="step-nav" onClick={hideFun('2')}>Targeting</div>
								</div>
								<div className="mt50"></div>
								<AddEditModal_scholarship  data={this.state}
													mystate={this.state}
													setInput={this._setInput}
													 getDescription={this._getDescription}
													  typeDeadline={this._typeDeadline} />
								<div className="clearfix mt20">
									{(scholarship_name_valid && deadline_valid && amount_valid   && description !== '') ?
									<div className="add-sch-form-btn"  onClick={this._submit} onKeyUp={(e) => { if(e.keyCode == 13) this._submit();}} tabIndex="0">
										{scholarships.add_sch_pending ? "Saving.." : "SAVE" }
									</div>
									:
									<div className="add-sch-form-btn disabled">SAVE</div>}
								</div>
							</form>
						</div>
						{/*Tab 1*/}
						{/*Tab 2*/}
						<div id="step2" style={{display:'none'}}>
							<form id="form2" onSubmit={(e) => this._submit(e,2)}>
								<div className="setStep-container">
									<div id="tab1" className=" step-nav" onClick={hideFun('1')}>Scholarship Information</div>
									<div id="tab2" className="step-nav active" onClick={hideFun('2')}>Targeting</div>
								</div>
								<div className="mt50"></div>
								<span id="activemenu" style={{display:'none'}}>default</span>
								<span id="isedit" style={{display:'none'}}>0</span>
								<AddEditModal_filters data={this.state}
														setInput={this._setInput}
														countries={scholarships.countries}
								/>
								<div className="clearfix mt20">

									{(scholarships.added) ?
									<div className="add-sch-form-btn"  onClick={this._submit} onKeyUp={(e) => { if(e.keyCode == 13) this._submit();}} tabIndex="0">
										{scholarships.add_sch_pending ? "Saving.." : "SAVE" }
									</div>
									:
									<div className="add-sch-form-btn disabled">SAVE</div>}

								</div>
							</form>
						</div>
						{/*Tab 2*/}

					</div>}
				</div>
			</DocumentTitle>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		scholarships: state.scholarships
	}
}

export default connect(mapStateToProps)(ScholarshipcmsAdd);
