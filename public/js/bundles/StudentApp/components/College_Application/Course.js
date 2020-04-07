// /College_Application/Courses.js

import React from 'react'
import selectn from 'selectn'

import { GRADE_LVL, CREDITS, LEVEL } from './constants'
import { updateProfile, getClassesBasedOnSubject, removeCourse, clearChangedFields } from './../../actions/Profile'

class Course extends React.Component {
	constructor(props) {
		super(props)
		this._updateCourse = this._updateCourse.bind(this)
		this._removeCourse = this._removeCourse.bind(this)
	}

	_updateCourse(e){
		let { dispatch, _profile, school, course } = this.props,
			name = e.target.getAttribute('name'),
			val = e.target.value;

		let updatedList = _profile.current_schools.map((_school) => {
			if( _school.name === school.name ){
				// return with new obj, updated course data by looping/finding current course
				// if current course, return updated course obj, else return old course untouched
				return {
					..._school, 
					courses: _school.courses.map((_course) => _course.id === course.id ? {..._course, [name]: val} : _course),
				};
			}

			return _school;
		});

		dispatch( updateProfile({current_schools: updatedList}) );
	}

	_removeCourse(){
		let { dispatch, _profile, school, course } = this.props;

		let updatedList = _profile.current_schools.map((_school) => 
			_school.name === school.name ? {..._school, courses: _.reject(school.courses.slice(), {id: course.id})} : _school);

		if( course.course_table_id ) dispatch( removeCourse(course.course_table_id, {current_schools: updatedList}) );
		else dispatch( updateProfile({current_schools: updatedList}) );
	}

	componentWillMount(){
		let{dispatch} = this.props;

		dispatch(clearChangedFields());
	}

	render(){
		let { _profile, course } = this.props,
			course_list_name = course.subject ? 'course_list_for_subject_'+course.subject : '';

		return (
			<div className="course c-cols">
				<div>
					<select name="subject" value={course.subject || ''} onChange={this._updateCourse}>
						<option value='' disabled="disabled">Select subject</option>
						{ _profile.subjects_list.map((l) => <option key={l.id+l.name} value={l.id}>{l.name}</option>) }
					</select>
				</div>

				<div>
					<select name="designation" value={course.designation || ''} onChange={this._updateCourse}>
						{ LEVEL.map((l) => <option key={l.disabled || l.name} value={l.name || ''} disabled={!!l.disabled}>{l.disabled || l.name}</option>) }
					</select>
				</div>

				<div>
					<select name="course_id" value={course.course_id || ''} onChange={this._updateCourse}>
						<option value='' disabled="disabled">Select course</option>
						{ (course_list_name && _.get(_profile, course_list_name+'.length')) && 
							_profile[course_list_name].map((c) => <option key={c.id+c.name} value={c.id}>{c.name}</option>) }
					</select>
				</div>

				<div>
					<select name="credits" value={course.credits || ''} onChange={this._updateCourse}>
						{ CREDITS.map((c) => <option key={c.disabled || c.name} value={c.name || ''} disabled={!!c.disabled}>{c.disabled || c.name}</option>) }
					</select>
				</div>

				<div>
					<select name="edu_level" value={course.edu_level || ''} onChange={this._updateCourse}>
						{ GRADE_LVL.map((g) => <option key={g.disabled || g.name} value={g.name || ''} disabled={!!g.disabled}>{g.disabled || g.name}</option>) }
					</select>
				</div>
				<span className="remove" onClick={ this._removeCourse }>&times;</span>
			</div>
		);
	}
}
export default Course;