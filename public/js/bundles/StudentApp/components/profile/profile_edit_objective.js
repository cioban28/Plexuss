import React, {Component} from 'react';

import { findIndex, find, remove, isEmpty } from 'lodash';

import {searchForMajors} from './../../actions/Profile';

import Edit_section from './profile_edit_section';

import ClickOutside from './../../../utilities/clickOutside';

import { DEGREE_TYPES } from './constants';

import Profile_edit_privacy_settings from './profile_edit_privacy_settings'

export default class Profile_edit_details2 extends Component{
	
	constructor(props){
		super(props);

		let {degree_name, degree_id, majors, planned_start_term, planned_start_yr, profession_name, profession_id} = this.props._profile;


		//used to store temporary state of form edits
		this.state = {
			degree_name: degree_name,
			degree_id: degree_id,
			majors: [...majors],
			planned_start_term: planned_start_term,
			planned_start_yr: planned_start_yr,
			profession_name: profession_name,
			profession_id: null,
			majorTmp: '',
			showMajors: false,
			submittable: true,
		}

		this._rmMajor = this._rmMajor.bind(this);
		this._addMajor = this._addMajor.bind(this);
		this._valid = this._valid.bind(this);
		this._save = this._save.bind(this);
		this._cancel = this._cancel.bind(this);

	}
	_addMajor(name, id){
		let mMajors = this.state.majors;

		let found = findIndex(mMajors, (item) => {
			return item.name == name;
		})

		if(found != -1){
			return;
		}

		mMajors.push({id: id, name: name});
		this.setState({majors: mMajors, showMajors: false});
	}
	_rmMajor(id){
		let mMajors = this.state.majors;
		
		remove(mMajors, (item) => {
			return item.id == id;
		})

		this.setState({majors: mMajors});
	}
	_valid(input, type, fieldV){
		let valid = true;
		let obj = {};
		obj[fieldV] = true;


		switch(type){
			case 'name':
				if( !(/^[a-zA-Z0-9\.\,\s\-\']+$/g.test(input)) || input === ''){
					valid = false;
					obj[fieldV] = false;
				}
				break;
			case 'number':
				if(!/$[0-9]{4}^/g.test(input)){
					valid = false;
					obj[fieldV] = false;
				}
				break;
			default:
				valid = true;
				obj[fieldV] = true;
				break;
		}

		this.setState(obj);
		this.setState({submittable: valid});
	}
	_save(callback){
		let {save}  = this.props;
		let {degree_name, degree_id, majors, planned_start_term, planned_start_yr, profession_name,profession_id} = this.state;
		let data = {};

        data.degree_id = degree_id;

        const foundDegree = find(DEGREE_TYPES, (degree) => degree.id == degree_id);
        
        if (!isEmpty(foundDegree)) {
            data.degree_name = foundDegree.label;
        }

		data.majors = [...majors];
		data.planned_start_term = planned_start_term;
		data.planned_start_yr = planned_start_yr;
		data.profession_name = profession_name;
		data.profession_id = profession_id;


		// console.log(data);

		save(data, callback);
	}

	_cancel(){
		let { _profile } = this.props;
		if(this.state.degree_name !== _profile.degree_name){ this.setState({degree_name: _profile.degree_name}); }
		if(this.state.degree_id !== _profile.degree_id){ this.setState({degree_id: _profile.degree_id}); }
		if(this.state.majors !== _profile.majors){ this.setState({majors: [..._profile.majors]}); }
		if(this.state.planned_start_term !== _profile.planned_start_term){ this.setState({planned_start_term: _profile.planned_start_term}); }
		if(this.state.planned_start_yr !== _profile.planned_start_yr){ this.setState({planned_start_yr: _profile.planned_start_yr}); }
		if(this.state.profession_name !== _profile.profession_name){ this.setState({profession_name: _profile.profession_name}); }
		if(this.state.profession_id !== _profile.profession_id){ this.setState({profession_id: _profile.profession_id}); }
	}

	_renderYears(){
		let date = new Date();
		let cYear = date.getFullYear();
		let yrList = [];

		for(let i = cYear, n = 0; i < cYear + 15; i++, n++){
			yrList.push(cYear+n);
		}

		return yrList;

	}
	render(){
		let {degree_name, degree_id, majors, planned_start_term, planned_start_yr, profession_name, profession_id, majorTmp, showMajors, showJobs, submittable,
		} = this.state;
		let {_profile, searchForMajors, searchForJobs} = this.props;

		let years = this._renderYears();
		// let l = _profile.majors ? _profile.majors.length -1 : 0;

		return(
			<Edit_section autoOpenEdit={this.props.autoOpenEdit} editable={true} saveHandler={this._save} onCancelEditing={this._cancel} submittable={submittable} section={'objective'}>
				<div>
					<div className="green-title">Objective
						<Profile_edit_privacy_settings section="objective" initial={!!_profile.public_profile_settings ? !!_profile.public_profile_settings.objective ? _profile.public_profile_settings.objective : null : null}/>
					</div>
					<div className="edit-detail lh-2 large">
						I would like to get a/an <span className="bold">{_profile.degree_name || "Undecided"}</span> in&nbsp;
						<span className="bold">
						{_profile.majors ?   
							_profile.majors.map((item, i ) => {
								return <span key={"m"+i}>{ i < _profile.majors.length -1  ?  item.name + ", " : item.name } </span>
							})
							: "Undecided"} 
						</span>
					</div> 
					
					<div className="edit-detail lh-2 large">
						My dream would be to one day work as a(n)  <span className="bold">{_profile.profession_name || "Undecided"}</span>
					</div>

					{/* <div className="edit-detail lh-2  large mt30">
						I would like to begin college in the &nbsp;
						<span className="bold">{_profile.planned_start_term || "Undecided term"} of {_profile.planned_start_yr || "Undecided year"}</span>
					</div> */}
				</div>

				<div>
					<div className="green-title">Edit Objective</div>

					<div className="clearfix mt30">
						<div className="left">
							<div className="edit-label" >Degree</div>
							<select name="degree_name" value={degree_id} onChange={(e) => this.setState({degree_id: e.target.value})} placeholder="Degree">
								<option value={1}>Certificate</option>
								<option value={2}>Associate's</option>
								<option value={3}>Bachelor's</option>
								<option value={4}>Master's</option>
								<option value={5}>Doctorate</option>
								<option value={6}>Undecided</option>
							</select>



							<div className="edit-label" >Major</div>
							<div className="abs-wrapper">
								<input name="major"
									   autoComplete="off"  
									   placeholder="Type in Major..."  
									   onChange={(e) => { 
											this.setState({showMajors: true});
										    searchForMajors(e.target.value); }} />

								<div className={"major-results-container stylish-scrollbar " + (showMajors && 'visible')} >
									<ClickOutside  handler={()=> this.setState({showMajors: false})} elID="_majorsDrop">
									{showMajors && _profile.majors_list && _profile.majors_list.other_majors && _profile.majors_list.other_majors.map((item, i) => {
										return (
											
												<div key={"ml"+i} className="major-list-result" data-id={item.id} data-deptID={item.department_id} 
													 onClick={() => this._addMajor(item.name, item.id)}>
													{item.name}
												</div>
											
										);
									})}
									</ClickOutside>
								</div>
							</div>
							<div className="major-results  stylish-scrollbar">
								{majors.map((item, i)=> {
									return (<div key={"maj"+i} className="major-result">
										<div className="major-name">{item.name}</div>
										<div className="rmMajor" onClick={() => this._rmMajor(item.id)}> &times; </div>
									</div>)
								})}
							</div>



						</div>

						<div className="right">
							<div className="edit-label" >Start Term</div>
							<select name="planned_start_term" value={planned_start_term} onChange={(e) => this.setState({planned_start_term: e.target.value})} placeholder="Start Term">
								<option value="fall">Fall</option>
								<option value="winter">Winter</option>
								<option value="spring">Spring</option>
								<option value="summer">Summer</option>
							</select>


							<div className="edit-label" >Start Year</div>
							<select name="planned_start_yr" value={planned_start_yr} onChange={(e) => this.setState({planned_start_yr: e.target.value})} placeholder="Start Year">
								{years.map((item, i) => {
									return (
										<option key={"yo"+i} value={item}>{item}</option>	
									);
								})}
							</select>



							<div className="edit-label" >Occupation</div>						
							<div className="abs-wrapper">
								<input name="occupation"
									   autoComplete="off"  
									   placeholder="Type in Occupation..." 
									   value={profession_name || ""} 
									   onChange={(e) => { 
											this.setState({showJobs: true, profession_name: e.target.value});
										    searchForJobs(e.target.value); }} />

								<div className={"jobs-results-container stylish-scrollbar " + (showJobs && 'visible')} >
									<ClickOutside handler={()=> this.setState({showJobs: false})} elID="_jobsDrop">
									{showJobs && _profile.jobs_list &&

									 _profile.jobs_list.map((item, i) => {
										return (
											
												<div key={"j"+i} className="jobs-list-result" 
													 onClick={() => this.setState({profession_name: item.profession_name, profession_id: item.id, showJobs: false})}>
													{item.profession_name}
												</div>
											
										);
									})}
									</ClickOutside>
								</div>
							</div>


						</div>
					</div>
				</div>

				
			</Edit_section>
		);

	}
} 