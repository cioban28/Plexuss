import React from 'react';
import moment from 'moment';
import {connect} from 'react-redux';

import {addScholarship} from './../../../actions/scholarshipsActions';
import {nextFun} from './../../../actions/pickACollegeActions';

import AddEditModal_scholarship from './../Scholarships/addEditModal_scholarship';
import AddEditModal_filters from './../Scholarships/addEditModal_filters';
import AddEditModal_provider from './../Scholarships/addEditModal_provider';


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

			//step: 1,
		}

		this._getDescription = this._getDescription.bind(this);
		//this._next = this._next.bind(this);
		//this._prev = this._prev.bind(this);
		this._setInput = this._setInput.bind(this);
		this._typeDeadline = this._typeDeadline.bind(this);
		this._submit = this._submit.bind(this);
	}
	
	componentDidMount() { 
		let {setInput} = this.props;
		
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
			// case 'deadline':
			// 	if(value.match(dateReg) || value.trim() === ''){
			// 		tmp[name_valid] = true;
			// 	}else{
			// 		tmp[name_valid] = false;  //only set if not default
			// 	}
				// console.log("set: " , mValue, ": " , typeof mValue);
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
	}
	_submit(e){
		e.preventDefault();
		//let data = new FormData(e.target.getContent());
		
		let {close, dispatch} = this.props;
		let data = {...this.state};
		
		delete data.step;

		for(let i in data){
			if(data.hasOwnProperty(i)){
				if(i.includes('_valid')){
					delete data[i];
				}
			}	
		}
		
		data.date_submitted = moment().format('MM/DD/YYYY');
		data.deadline = data.deadline.format('MM/DD/YYYY');
		//dispatch to store and save to DB
		//console.log(data.toSource());
		//alert(data.toSource());
		dispatch(addScholarship(data, close));
		

	}
	
	render(){
		let {scholarship_name, website, amount, recurring, nextDue, 
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
		

		return(
			<div className="addModal">
				<form onSubmit={(e) => this._submit(e)}>
					<div className="close-btn" onClick={close}>&times;</div>
						{/*Step 1*/}
						<div id="step1">
							<div className="title">Step 1 of 2 Scholarship Information</div>
							<AddEditModal_scholarship data={this.state}
													  setInput={this._setInput}
													  typeDeadline={this._typeDeadline}/>
							<div className="clearfix mt20">
								{scholarship_name_valid && deadline_valid && website_valid && 
								amount_valid && numberof_valid && submission_id_valid && description !== '' ?
								<div className="add-sch-form-btn" onClick={nextFun('1','2')}>NEXT</div>
								: 
								<div className="add-sch-form-btn disabled">NEXT</div>}
								
									
							</div>					
						</div>
						{/*Step 1*/}
						
						{/*Step 2*/}
						<div id="step2" style={{display:'none'}}>	
							<div className="title">Step 2 of 2 Provider Information</div>
							<AddEditModal_provider data={this.state}
												   setInput={this._setInput}
												   countries={scholarships.countries}
												   providers={scholarships.providers} />
								<div className="clearfix mt20">
								{(provider_name_valid && contact_fname_valid && contact_lname_valid && 
								 provider_phone_valid && provider_email_valid && provider_address_valid && 
								 provider_zip_valid && provider_city_valid) || provider_id ?
								<input type="submit"  value={scholarships.add_sch_pending ? "Saving.." : "ADD"} className="add-sch-form-btn" />
								:
								<div className="add-sch-form-btn disabled">{scholarships.add_sch_pending ? "Saving.." : "ADD"}</div>}
								<div className="cancel-sch-form-btn" onClick={nextFun('1','2')}>BACK</div>
							</div> 
							</div>
						{/*Step 2*/}
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