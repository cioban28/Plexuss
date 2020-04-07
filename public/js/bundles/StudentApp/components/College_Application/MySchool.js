// /College_Application/MySchool.js

import React from 'react'
import selectn from 'selectn'

import Course from './Course'

import { updateProfile, clearChangedFields } from './../../actions/Profile'

class MySchool extends React.Component {
	constructor(props) {
		super(props)
		this._addCourse = this._addCourse.bind(this)
		this._getCreditCount = this._getCreditCount.bind(this)
	}

	_addCourse(){
		let { dispatch, _profile, school } = this.props;

		let updatedList = _profile.current_schools.map((_school) => 
			school.name === _school.name ? {..._school, courses: [..._school.courses, {id: _school.courses.length + 1}]} : _school );

		dispatch( updateProfile({current_schools: updatedList}) );
		// dispatch(clearChangedFields());
	}

	_getCreditCount(){
		let { school } = this.props,
			count = 0;

		_.each(school.courses.slice(), (crs) => crs.credits ? count += (+crs.credits) : null );

		return count;
	}

	render(){
		let { school } = this.props,
			credit_count = this._getCreditCount();

		return (
			<div className="my-school">

				<div className="school-selected">
					<div>{ school.name }</div>
					<div>{ selectn('courses.length', school) || 0 }</div>
					<div>{ credit_count || 0 }</div>
				</div>

				<div className="course-section">
					<div className="courses-title">My Courses</div>

					<div className="c-cols">
						<div>Subject</div>
						<div>Designation</div>
						<div>Course Name</div>
						<div>Credits</div>
						<div>Education Level</div>
					</div>

					{ school.courses.map((co, i) => <Course key={co.name || i} course={co} school={school} {...this.props} />) }
					<div className="add-course" onClick={ this._addCourse }>Add Course</div>
				</div>

			</div>
		);
	}
}

export default MySchool;