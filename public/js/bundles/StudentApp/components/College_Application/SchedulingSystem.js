// /College_Application/SchedulingSystem.js

import React from 'react'
import selectn from 'selectn'

import { updateProfile } from './../../actions/Profile'

export default class SchedulingSystem extends React.Component{
	constructor(props) {
		super(props)
		this._addSchedule = this._addSchedule.bind(this)
	}
	_addSchedule(){
		let { dispatch, _profile, system, added } = this.props;

		// if this is the last elem in the arr, then add scheduling_system prop to it
		let updatedList = _profile.current_schools.map((school, i, arr) => i+1 === arr.length ? {...school, scheduling_system: system.name} : school );

		added();
		dispatch( updateProfile({current_schools: updatedList}) );
	}

	render(){
		let { system } = this.props;

		return (
			<div className="school-attended" onClick={ this._addSchedule }>
				{ system.name }
			</div>
		);
	}
};