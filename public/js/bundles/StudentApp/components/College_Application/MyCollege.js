// /College_Application/MyCollege.js

import React from 'react'

import { updateProfile, changedFields, amplitudeRemoveCollege } from './../../actions/Profile'

class MyCollege extends React.Component {
	constructor(props) {
		super(props)
		this._dontApply = this._dontApply.bind(this)
	}

	_dontApply(){
		let { dispatch, _profile, college } = this.props;
		let newList = _.reject(_profile.applyTo_schools.slice(), college);

		dispatch( updateProfile({applyTo_schools: newList}) );
		dispatch(changedFields("removed college " + college.school_name + "(college id: " + college.college_id + ")"));

		// dispatch(amplitudeRemoveCollege(college));
	}

	render(){
		let { college, i, _profile, _user, classes } = this.props,
			already_submitted = _.get(college, 'submitted', 0),
            num_of_eligible_applied_colleges  = (_user && _user.num_of_eligible_applied_colleges) || null,
            not_available_for_selection = !_.isPlainObject(college) && num_of_eligible_applied_colleges < i;

        classes = classes ? classes : '';

        if (not_available_for_selection) {
            classes = classes + ' gray-out';
        }
		
		return (
			<div title={college.school_name} className={"my-college "+classes}>
				{ !_.isPlainObject(college) && <div>{i}</div> }
				{ (_.isPlainObject(college) && _profile && !already_submitted ) && <span className="remove" onClick={ this._dontApply }>x</span> }
				{ _.isPlainObject(college) && <div className="bg" style={{backgroundImage: 'url('+college.logo_url+')'}} /> }
			</div>
		);
	}
}
export default MyCollege;