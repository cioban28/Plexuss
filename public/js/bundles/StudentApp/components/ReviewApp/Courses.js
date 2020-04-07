// /ReviewApp/Courses.js

import React from 'react'
import selectn from 'selectn'

import SectionHeader from './SectionHeader'

class ReviewCourses extends React.Component {
	constructor(props) {
		super(props)
	}

	render(){
		let { dispatch,  _profile, _route, noEdit } = this.props;

		return (
			<div className="section">

				<div className="inner">
				
					<div className="arrow" />

					<SectionHeader route={_route} dispatch={dispatch} noEdit={noEdit} />

					<div>
						{ _.get(_profile, 'current_schools.length', 0) > 0 && 
							_profile.current_schools.map((s) => <SingleSchool key={s.id} school={s} {...this.props} />) }

						{ _.get(_profile, 'current_schools.length', 0) === 0 && <div>No courses added</div> }
					</div>

					<br />

				</div>

			</div>
		);
	}
}

class SingleSchool extends React.Component{
	constructor(props) {
		super(props)
		this._getUnits = this._getUnits.bind(this)
	}
	_getUnits(){
		let { school } = this.props,
			units = 0;

		if( _.get(school, 'courses.length', 0) > 0 )
			_.each(school.courses.slice(), (crs) => crs.credits ? units += (+crs.credits) : null );

		return units;
	}

	render(){
		let { school } = this.props,
			units = this._getUnits();
		return (
			<div className="school">
				<div className="head">
					<div>{ school.name || 'N/A' }</div>
					<div>Total Courses: <span>{ _.get(school, 'courses.length', 0) }</span></div>
					<div>Total Units: <span>{ units }</span></div>
				</div>
				<div className="courses">
					{ school.courses.map((c) => <SingleCourse key={c.id} course={c} {...this.props} />) }	
				</div>
			</div>
		);
	}
}

class SingleCourse extends React.Component{
	constructor(props) {
		super(props)
		this._getCourse = this._getCourse.bind(this)
		this._getSubject = this._getSubject.bind(this)
	}
	_getSubject(){
		let { _profile, course } = this.props,
			all_subjects = _.get(_profile, 'subjects_list'),
			subj = null;

		if( all_subjects ) subj = _.find(all_subjects, {id: +course.subject});

		return _.get(subj, 'name', '');
	}

	_getCourse(){
		let { _profile, course } = this.props,
			all_courses = _.get(_profile, 'course_list_for_subject_'+course.subject),
			crs = null;

		if( all_courses ) crs = _.find(all_courses, {id: +course.course_id});

		return _.get(crs, 'name', '');
	}

	render(){
		let { course } = this.props,
			courseName = this._getCourse(),
			subject = this._getSubject();

		return (
			<div className="course">
				<div>{ subject || 'N/A' }</div>
				<div>{ courseName || 'N/A' }</div>
				<div>{ course.designation || 'N/A' }</div>
				<div>{ course.credits || 'N/A' }</div>
			</div>
		);
	}
}

export default ReviewCourses;