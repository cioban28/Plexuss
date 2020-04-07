import React, {Component} from 'react';
import { connect } from 'react-redux';
import { isEmpty, omit, findIndex, remove } from 'lodash';
import ClickOutside from './../../../utilities/clickOutside';
import Profile_edit_privacy_settings from './profile_edit_privacy_settings'

export default class EditEducation extends Component{
	constructor(props){
		super(props);

		let {edu_level, school_name, school_id, degree_type, grad_year, majors, is_valid, id} = this.props.education;

		this.state = {
			id: id,
			edu_level: edu_level,
			school_name: school_name,
			school_id: null,
			grad_year: grad_year,
			degree_type: degree_type,
			majors: [...majors],
			is_valid: is_valid,

			schoolNameV: true,
			gradYearV: true,
			showSchools: false,
			showMajors: false,
			showMinors: false,
			major_val: '',
			minor_val: '',
		}

		this._addMajor = this._addMajor.bind(this);
		this._rmMajor = this._rmMajor.bind(this);

		this._validField = this._validField.bind(this);
		this._validAll = this._validAll.bind(this);
	}

	componentDidUpdate(prevProps, prevState){			
		if(this.state !== prevState){
			let newState = omit(this.state, ['schoolNameV', 'gradYearV', 'showSchools', 'showMajors', 'major_val', 'showMinors', 'minor_val',]);
			this.props.updateEdu(newState, this.state.id);
			this._validAll();
		}
	}

	_addMajor(name, id, is_minor = 0){
		let mMajors = this.state.majors;

		let found = findIndex(mMajors, (item) => {
			return item.major_name == name;
		})

		if(found != -1){
			return;
		}

		mMajors.push({major_id: id, major_name: name, is_minor: is_minor});
		this.setState({majors: mMajors});
		if(is_minor === 1){
			this.setState({showMinors: false, minor_val: ''});
		}else {
			this.setState({showMajors: false, major_val: ''});
		}
	}
	_rmMajor(id,  is_minor = 0){
		let mMajors = this.state.majors;
		
		remove(mMajors, (item) => {
			if(is_minor === 1){
				return (item.major_id == id && item.is_minor === 1);
			}
			return item.major_id == id;
		})

		this.setState({majors: mMajors});
	}

	_validField(input, type, fieldV){
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
				if(!/^[0-9]{4}$/g.test(input)){
					valid = false;
					obj[fieldV] = false;
				}
				break;
			default:
				valid = true;
				break;
		}

		this.setState({[fieldV]: valid});
	}
	_validAll(){
		let {edu_level, school_name, school_id, grad_year, majors, degree_type, gradYearV, schoolNameV } = this.state;
		if( (school_name !== '' && schoolNameV) && (grad_year !== '' && gradYearV) ){
			if ( Number(edu_level) === 0 ){
				if(this.state.is_valid === false) this.setState({is_valid: true});
			}
			else if( (Number(edu_level) === 1) && (degree_type !== 0) ){
				if(this.state.is_valid === false) this.setState({is_valid: true});
			}
			else{
				if(this.state.is_valid === true) this.setState({is_valid: false});
			}			
		}else{
			if(this.state.is_valid === true) this.setState({is_valid: false});
		}
	}

	render(){
		let {id, edu_level, school_name, school_id, grad_year, majors, degree_type,
			showSchools, showMajors, showMinors, is_valid, schoolNameV, gradYearV } = this.state;
		let {_profile, findSchools, searchForMajors} = this.props;

		return(
			<div>	
					<div className="clearfix">
						<div className="left">
							<div className="edit-label">Education Level</div>
							<select name="edu_level" value={edu_level} onChange={(e) => {this.setState({edu_level: Number(e.target.value), school_name: ''}); this._validField('', 'name', 'schoolNameV');}} placeholder="Education Level...">
								<option value={-1} disabled>Select...</option>
								<option value={0}>High School</option>
								<option value={1}>College</option>
							</select>
			            	
			                <div className="edit-label">Graduation Year</div>
							<input className={gradYearV ? '': 'error'} autocomplete="off" name="gradDate" value={grad_year} onChange={(e) => { this.setState({grad_year: e.target.value}); this._validField(e.target.value, 'number', 'gradYearV');   }} placeholder="YYYY"/>

							{edu_level === 1 &&
								<span>
									<div className="edit-label">Degree</div>
									<select name="degree_name" value={degree_type} onChange={(e) => this.setState({degree_type: Number(e.target.value)})} placeholder="Degree">
										<option value={0} disabled>Select...</option>
										<option value={1}>Certificate</option>
										<option value={2}>Associate's</option>
										<option value={3}>Bachelor's</option>
										<option value={4}>Master's</option>
										<option value={5}>Doctorate</option>
										<option value={6}>Undecided</option>
									</select>
								</span>
							}
							
						</div>

						<div className="right">
							<div className="edit-label">School Name</div>
			                <div className="abs-wrapper">
			                    <input className={schoolNameV ? '': 'error'} autocomplete="off" name="school" value={school_name} onChange={(e) => { findSchools(e.target.value, edu_level); this.setState({school_name: e.target.value, showSchools: true});  this._validField(e.target.value, 'name', 'schoolNameV');} } placeholder="School Name"/>
			                    <div className="school-results-container stylish-scrollbar">
			                        <ClickOutside  handler={()=> this.setState({showSchools: false})} elID={"_schoolsDrop"+id}>
			                        {showSchools && _profile.school_names && _profile.school_names.map((item, i) => {
			                            return (
			                                
			                                    <div key={"sch"+i} className="school-list-result" 
			                                         onClick={() => this.setState({school_name: item.school_name, school_id: item.id, showSchools: false})}>
			                                        {item.school_name}
			                                    </div>
			                                
			                            );
			                        })}
			                        </ClickOutside>
			                    </div>
			                </div>
		                

						{edu_level === 1 &&
		            		<span>
				                <div className="edit-label" >Major</div>
								<div className="abs-wrapper">
									<input name="major"
										   autoComplete="off"
										   placeholder={this.state.majors.length === 0 ? "Type in Major...": 'Add additional Major...'}
										   value={this.state.major_val}
										   onChange={(e) => { 
												this.setState({showMajors: true});
											    searchForMajors(e.target.value);
												this.setState({major_val: event.target.value});}} />

									<div className={"major-results-container stylish-scrollbar"} >
									<ClickOutside  handler={()=> this.setState({showMajors: false})} elID={"_majorsDrop"+id}>
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
									{majors.filter(maj => maj.is_minor !== 1).map((item, i)=> {
										return (<div key={"maj"+i} className="major-result">
											<div className="major-name">{item.major_name}</div>
											<div className="rmMajor" onClick={() => this._rmMajor(item.major_id)}> &times; </div>
										</div>)
									})}
								</div>
							</span>
						}
						{edu_level === 1 &&
							<span>
				                <div className="edit-label" >Minors</div>
								<div className="abs-wrapper">
									<input name="major"
										   autoComplete="off"
										   placeholder={this.state.majors.some(maj => maj.is_minor === 1) ? "Add additional Minor...": 'Type in Minor...'}
										   value={this.state.minor_val}
										   onChange={(e) => { 
												this.setState({showMinors: true});
											    searchForMajors(e.target.value);
												this.setState({minor_val: event.target.value});}} />

									<div className={"major-results-container stylish-scrollbar"} >
									<ClickOutside  handler={()=> this.setState({showMinors: false})} elID={"_majorsDrop"+id}>
										{showMinors && _profile.majors_list && _profile.majors_list.other_majors && _profile.majors_list.other_majors.map((item, i) => {
											return (
												
													<div key={"ml"+i} className="major-list-result" data-id={item.id} data-deptID={item.department_id} 
														 onClick={() => this._addMajor(item.name, item.id, 1)}>
														{item.name}
													</div>
												
											);
										})}
										</ClickOutside>
									</div>
								</div>
								<div className="major-results  stylish-scrollbar">
									{majors.filter(maj => maj.is_minor === 1).map((item, i)=> {
										return (<div key={"maj"+i} className="major-result">
											<div className="major-name">{item.major_name}</div>
											<div className="rmMajor" onClick={() => this._rmMajor(item.major_id, 1)}> &times; </div>
										</div>)
									})}
								</div>
							</span>
						}
						</div>
					</div>

            </div>
        );
    }
}

// export default connect(mapStateToProps)(Profile_edit_education);