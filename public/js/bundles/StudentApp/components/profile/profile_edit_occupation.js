import React, {Component} from 'react';
import { findIndex, find, remove, isEmpty } from 'lodash';
import {searchForMajors} from './../../actions/Profile';
import Edit_section from './profile_edit_section';
import ClickOutside from './../../../utilities/clickOutside';
import { DEGREE_TYPES } from './constants';
import Profile_edit_privacy_settings from './profile_edit_privacy_settings'

export default class Profile_edit_occupation extends Component{
	
	constructor(props){
		super(props);

		let {occupation_name, occupation_id} = this.props._profile;

		this.state = {
			occupation_name: occupation_name,
			occupation_id: null,
			submittable: true,
		}

		this._valid = this._valid.bind(this);
		this._save = this._save.bind(this);
		this._cancel = this._cancel.bind(this);
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

		// this.setState(obj);
		this.setState({submittable: valid});
	}
	_save(callback){
		let {save}  = this.props;
		let {occupation_name,occupation_id} = this.state;
		let data = {};

		data.occupation_name = occupation_name;
		data.occupation_id = occupation_id;


		// console.log(data);

		save(data, callback);
	}

	_cancel(){
		let { _profile } = this.props;
		if(this.state.occupation_name !== _profile.occupation_name){ this.setState({occupation_name: _profile.occupation_name}); }
		if(this.state.occupation_id !== _profile.occupation_id){ this.setState({occupation_id: _profile.occupation_id}); }
	}

	render(){
		let {occupation_name, occupation_id, showJobs, submittable } = this.state;
		let {_profile, searchForJobs} = this.props;

		return(
			<Edit_section autoOpenEdit={this.props.autoOpenEdit} editable={true} saveHandler={this._save} onCancelEditing={this._cancel} submittable={submittable} section={'occupation'}>
				<div>
					<div className="green-title">Current Occupation
						<Profile_edit_privacy_settings section="occupation" initial={!!_profile.public_profile_settings ? !!_profile.public_profile_settings.occupation ? _profile.public_profile_settings.occupation : null : null}/>
					</div>
					
					<div className="edit-detail lh-2 large">
						I currently work as a(n)  <span className="bold">{_profile.occupation_name || "Undecided"}</span>
					</div>
				</div>

				<div>
					<div className="green-title">Edit Current Occupation</div>

					<div className="clearfix mt30">
						<div className="left">
							<div className="edit-label" >Occupation</div>						
							<div className="abs-wrapper">
								<input name="occupation"
									   autoComplete="off"  
									   placeholder="Type in Occupation..." 
									   value={occupation_name || ""} 
									   onChange={(e) => { 
											this.setState({showJobs: true, occupation_name: e.target.value});
										    searchForJobs(e.target.value); 
											this._valid(e.target.value, 'name', 'submittable');}
										} 
								/>

								<div className={"jobs-results-container stylish-scrollbar occupation-jobs"} >
									<ClickOutside handler={()=> this.setState({showJobs: false})} elID="_jobsDrop">
									{showJobs && _profile.jobs_list &&

									 _profile.jobs_list.map((item, i) => {
										return (
											
												<div key={"j"+i} className="jobs-list-result" 
													 onClick={() => this.setState({occupation_name: item.profession_name, occupation_id: item.id, showJobs: false})}>
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