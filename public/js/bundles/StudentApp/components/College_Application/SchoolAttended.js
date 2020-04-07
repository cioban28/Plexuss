// /College_Application/SchoolAttended.js

import React from 'react'
import selectn from 'selectn'

import { updateProfile } from './../../actions/Profile'

export default class SchoolAttended extends React.Component {
	constructor(props) {
		super(props)
		this._addSchool = this._addSchool.bind(this)
	}
	_addSchool(){
		let { dispatch, _profile, school, added } = this.props,
			found = null,
			newList = null;

		// init courses list for added school
		// initing w/ empty obj to display empty form for courses
		school.courses = [{id: 1}]; 

		if( selectn('current_schools.length', _profile) ){
			let found = _.find(_profile.current_schools.slice(), {id: school.id});

			if( !found ){
				newList = [..._profile.current_schools, school];
				added(); // makes schooladded true
				dispatch( updateProfile({current_schools: newList}) );
			}else{
				added({schedulingAdded: true});
			}

		}else{
			newList = [school];
			added(); // makes schooladded true
			dispatch( updateProfile({current_schools: newList}) );
		}
	}

	render(){
		let { school } = this.props;

		return (
			<div className="school-attended" onClick={ this._addSchool }>
				{ school.name }
			</div>
		);
	}
}