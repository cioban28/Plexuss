// /College_Application/SchoolSearchResult.js

import React from 'react'
import selectn from 'selectn'

import { updateProfile } from './../../actions/Profile'

export default class SchoolSearchResult extends React.Component{
	constructor(props) {
		super(props)
		this._addSchool = this._addSchool.bind(this)
	}
	_addSchool(){
		let { dispatch, _profile, school, added } = this.props,
			found = null,
			newList = null;

		school.courses = [{id: 1}]; // init courses list for added school

		if( selectn('current_schools.length', _profile) ){
			let found = _.find(_profile.current_schools.slice(), {id: school.id});

			if( !found ){
				newList = [..._profile.current_schools, school];
				added();
				dispatch( updateProfile({current_schools: newList}) );
			}

		}else{
			newList = [school];
			added();
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
};