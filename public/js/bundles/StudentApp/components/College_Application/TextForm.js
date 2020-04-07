// /College_Application/TextForm.js

import React from 'react'
import TextField from './TextField'

import { updateProfile } from './../../actions/Profile'

export default class extends React.Component{
	constructor(props) {
		super(props)
		this._setSchoolName = this._setSchoolName.bind(this)
	}

	_setSchoolName(){
		let { dispatch, school, field } = this.props;
		dispatch( updateProfile({[field.name]: school.name}) );
	}

	componentWillReceiveProps(np){
		let { dispatch, _profile, field } = this.props;

		if( np._profile[field.name] !== _profile[field.name] && np._profile[field.name] ){
			this.state.selected = false;
			dispatch( getSchoolsBasedOnSchoolType({
				search_for_school: np._profile[field.name],
				in_college: field.in_college,
			}) );
		}
	}

	render(){
		let { _profile, field } = this.props;

		return (
			<div className="school-name-container">
				<TextField field={field} {...this.props} />
				{ false && <div className="dropdown">
					<div onClick={ this._setSchoolName }>{'hi there'}</div>
				</div> }
			</div>
		);
	}
}