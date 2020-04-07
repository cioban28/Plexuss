import React from 'react';
import moment from 'moment';
import {connect} from 'react-redux';

import {editScholarship} from './../../../actions/scholarshipsActions';

import AddEditModal_scholarship from './addEditModal_scholarship';
import AddEditModal_filters from './addEditModal_filters';
import AddEditModal_provider from './addEditModal_provider';
import {hideFun} from './../../../actions/pickACollegeActions';

class AddModal extends React.Component{
	constructor(props){
		super(props);

		this.state = {
			scholarship_name: '',
			website: '',
			amount: '',
			numberof: '',
			recurring: '',
			description: '',
			submission_id: '',
			deadline: null,
			scholarship_name_valid: '',
			deadline_valid: true,
			website_valid: '',
			amount_valid: '',
			numberof_valid: '',
			submission_id_valid: true,
			
			provider_id: '',
			provider_name: '',
			contact_fname: '',
			contact_lname: '',
			provider_phone: '',
			provider_email: '',	
			provider_address: '',
			provider_city: '',	
			provider_state: '',	
			provider_zip: '',
			provider_country: '',
			provider_name_valid: '',
			contact_fname_valid: '',
			contact_lname_valid: '',
			provider_phone_valid: '',
			provider_email_valid: '',	
			provider_address_valid: '',
			provider_zip_valid: '',
			provider_city_valid: '',		

			step: 1,
		}

		this._getDescription = this._getDescription.bind(this);
		this._setInput = this._setInput.bind(this);
		this._typeDeadline = this._typeDeadline.bind(this);
		this._submit = this._submit.bind(this);
	}
	componentWillMount(){
		let {item} = this.props;
		let cp = {...item};
		cp.step = 1;

		cp.deadline = moment(cp.deadline, 'MM/DD/YYYY');

		this.setState(cp);
		// console.log('in edit: ', cp);
	}

	_setInput(name, value){
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
	}
	_typeDeadline(val){
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
		// console.log(val);
	}
	_submit(e){
		e.preventDefault();

		let {close, dispatch} = this.props;
		let data = {...this.state};
		delete data.step;

		for(let i in data){
			if(data.hasOwnProperty(i)){
				if(i.includes('_valid')){  // || data[i] == ""
					delete data[i];
				}
			}	
		}
		
		if(document.getElementById("all_country_filter").checked == true){ data['all_country_filter'] = document.getElementById('all_country_filter').value; }
		if(document.getElementById("include_country_filter").checked == true){ data['include_country_filter'] = document.getElementById('include_country_filter').value; }
		if(document.getElementById("exclude_country_filter").checked == true){ data['exclude_country_filter'] = document.getElementById('exclude_country_filter').value; }
		data['country'] = document.getElementById('country_filter').value;
		
		if(document.getElementById("all_state_filter").checked == true){ data['all_state_filter'] = document.getElementById('all_state_filter').value; }
		if(document.getElementById("include_state_filter").checked == true){ data['include_state_filter'] = document.getElementById('include_state_filter').value; }
		if(document.getElementById("exclude_state_filter").checked == true){ data['exclude_state_filter'] = document.getElementById('exclude_state_filter').value; }
		data['state'] = document.getElementById('state_filter').value;
		
		if(document.getElementById("all_city_filter").checked == true){ data['all_city_filter'] = document.getElementById('all_city_filter').value; }
		if(document.getElementById("include_city_filter").checked == true){ data['include_city_filter'] = document.getElementById('include_city_filter').value; }
		if(document.getElementById("exclude_city_filter").checked == true){ data['exclude_city_filter'] = document.getElementById('exclude_city_filter').value; }
		data['city'] = document.getElementById('city_filter').value;
		
		data['startDateTerm'] = document.getElementById('startDateTerm_filter').value;
		data['financial'] = document.getElementById('financial_filter').value;
		if(document.getElementById("interested_in_aid").checked == true){ data['interested_in_aid'] = 'true'; }
		data['typeofschool'] = document.getElementById('typeofschool').value;
		
		if(document.getElementById("all_department_filter").checked == true){ data['all_department_filter'] = document.getElementById('all_department_filter').value; }
		if(document.getElementById("include_department_filter").checked == true){ data['include_department_filter'] = document.getElementById('include_department_filter').value; }
		if(document.getElementById("exclude_department_filter").checked == true){ data['exclude_department_filter'] = document.getElementById('exclude_department_filter').value; }
		
		if(document.getElementById("both_typeofschool").checked == true){ data['both_typeofschool'] = document.getElementById('both_typeofschool').value; }
		if(document.getElementById("online_only_typeofschool").checked == true){ data['online_only_typeofschool'] = document.getElementById('online_only_typeofschool').value; }
		if(document.getElementById("campus_only_typeofschool").checked == true){ data['campus_only_typeofschool'] = document.getElementById('campus_only_typeofschool').value; }
		
		if(document.getElementById("gpaMin_filter").value != ''){ data['gpa_min'] = document.getElementById('gpaMin_filter').value;}
		if(document.getElementById("gpaMax_filter").value != ''){ data['gpa_max'] = document.getElementById('gpaMax_filter').value;}
		if(document.getElementById("satMin_filter").value != ''){ data['sat_min'] = document.getElementById('satMin_filter').value;}
		if(document.getElementById("satMax_filter").value != ''){ data['sat_max'] = document.getElementById('satMax_filter').value;}
		if(document.getElementById("actMin_filter").value != ''){ data['act_min'] = document.getElementById('actMin_filter').value;}
		if(document.getElementById("actMax_filter").value != ''){ data['act_max'] = document.getElementById('actMax_filter').value;}
		if(document.getElementById("toeflMin_filter").value != ''){ data['toefl_min'] = document.getElementById('toeflMin_filter').value;}
		if(document.getElementById("toeflMax_filter").value != ''){ data['toefl_max'] = document.getElementById('toeflMax_filter').value;}
		if(document.getElementById("ieltsMin_filter").value != ''){ data['ielts_min'] = document.getElementById('ieltsMin_filter').value;}
		if(document.getElementById("ieltsMax_filter").value != ''){ data['ielts_max'] = document.getElementById('ieltsMax_filter').value;}

		if(document.getElementById("transcript_filter").checked == true){ data['transcript_filter'] = document.getElementById('transcript_filter').value; }
		if(document.getElementById("financialInfo_filter").checked == true){ data['financialInfo_filter'] = document.getElementById('financialInfo_filter').value; }
		if(document.getElementById("ielts_fitler").checked == true){ data['ielts_fitler'] = document.getElementById('ielts_fitler').value; }
		if(document.getElementById("toefl_filter").checked == true){ data['toefl_filter'] = document.getElementById('toefl_filter').value; }
		if(document.getElementById("resume_filter").checked == true){ data['resume_filter'] = document.getElementById('resume_filter').value; }
		if(document.getElementById("passport_filter").checked == true){ data['passport_filter'] = document.getElementById('passport_filter').value; }
		if(document.getElementById("essay_filter").checked == true){ data['essay_filter'] = document.getElementById('essay_filter').value; }
		if(document.getElementById("other_filter").checked == true){ data['other_filter'] = document.getElementById('other_filter').value; }
		
		data['ageMin_filter'] = document.getElementById('ageMin_filter').value;
		data['ageMax_filter'] = document.getElementById('ageMax_filter').value;
		if(document.getElementById("all_gender_filter").checked == true){ data['all_gender_filter'] = document.getElementById('all_gender_filter').value; }
		if(document.getElementById("male_only_filter").checked == true){ data['male_only_filter'] = document.getElementById('male_only_filter').value; }
		if(document.getElementById("female_only_filter").checked == true){ data['female_only_filter'] = document.getElementById('female_only_filter').value; }
		
		if(document.getElementById("all_eth_filter").checked == true){ data['all_eth_filter'] = document.getElementById('all_eth_filter').value; }
		if(document.getElementById("include_eth_filter").checked == true){ data['include_eth_filter'] = document.getElementById('include_eth_filter').value; }
		if(document.getElementById("exclude_eth_filter").checked == true){ data['exclude_eth_filter'] = document.getElementById('exclude_eth_filter').value; }
		data['ethnicity'] = document.getElementById('ethnicity_filter').value;
		if(document.getElementById("all_rgs_filter").checked == true){ data['all_rgs_filter'] = document.getElementById('all_rgs_filter').value; }
		if(document.getElementById("include_rgs_filter").checked == true){ data['include_rgs_filter'] = document.getElementById('include_rgs_filter').value; }
		if(document.getElementById("exclude_rgs_filter").checked == true){ data['exclude_rgs_filter'] = document.getElementById('exclude_rgs_filter').value; }
		data['religion'] = document.getElementById('religion_filter').value;
		
		if(document.getElementById("hsUsers_filter").checked == true){ data['hs_users'] = document.getElementById('hsUsers_filter').value; }
		if(document.getElementById("collegeUsers_filter").checked == true){ data['college_users'] = document.getElementById('collegeUsers_filter').value; }
		
		data['inMilitary'] = document.getElementById('inMilitary_filter').value;
		data['militaryAffiliation'] = document.getElementById('militaryAffiliation_filter').value;
		data['profileCompletion'] = document.getElementById('profileCompletion_filter').value;
		
		if(data.dealine){
				data.deadline = data.deadline.format('MM/DD/YYYY');
		}
		// console.log(data);

		//dispatch to store and save to DB
		dispatch(editScholarship(data, close));

	}
	render(){
		let { step, scholarship_name, website, amount, recurring, nextDue, 
			provider_id, provider_name, contact_fname, contact_lname, provider_phone,
			provider_email, provider_address, provider_city, provider_state, description,
			provider_country, deadline,
			scholarship_name_valid,
			deadline_valid,
			website_valid,
			amount_valid,
			numberof_valid,
			submission_id_valid,
			provider_name_valid,
			contact_fname_valid,
			contact_lname_valid,
			provider_phone_valid,
			provider_email_valid,	
			provider_address_valid,
			provider_zip_valid,
			provider_city_valid,

		} = this.state;
		let {close, open, scholarships} = this.props;


		// console.log(this.state);
		return(
			<div className="addModal">
				<form onSubmit={(e) => this._submit(e)}>
					<div className="close-btn" onClick={close}>&times;</div>
					
					<div className="edit-title">Edit Scholarship </div>
					<div className="setStep-container">
						<div className={step == 1 ? "active step-nav": "step-nav"} onClick={hideFun('1')}>Scholarship Information</div>
						{<div className={step == 2 ? "active step-nav": "step-nav"} onClick={hideFun('2')}>Targeting</div>}
						<div className={step == 3 ? "active step-nav": "step-nav"} onClick={hideFun('3')}>Provider Information</div>
					</div>
					<div className="mt50"></div>
					

					
						<div id="step1">
							<AddEditModal_scholarship data={this.state}
													  setInput={this._setInput}
													  typeDeadline={this._typeDeadline}/>
						</div>

					
						<div id="step2" style={{display:'none'}}>
							<AddEditModal_filters data={this.state} 
												  getDescription={this._getDescription}
												  setInput={this._setInput} />
						</div>



					<div id="step3" style={{display:'none'}}>	
							<AddEditModal_provider data={this.state}
												   editProvider={true}
												   setInput={this._setInput}
												   countries={scholarships.countries}
												   providers={scholarships.providers} />
						</div>

					<div className="clearfix mt20">	
						<div className="add-sch-form-btn"  onClick={this._submit} onKeyUp={(e) => { if(e.keyCode == 13) this._submit();}} tabIndex="0">
							{scholarships.edit_sch_pending ? "Saving.." : "SAVE" }
						</div>
					</div>
				</form>
			</div>
		);
	}
}

const mapStateToProps = (state, props) => {
	return{
		scholarships: state.scholarships
	}
}

export default connect(mapStateToProps)(AddModal);