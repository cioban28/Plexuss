// /View_Student_Application/index.js

import $ from 'jquery'
import React from 'react'
import { Link } from 'react-router'
import { connect } from 'react-redux'
import DocumentTitle from 'react-document-title'

import SIC from './../SIC'
import ReviewApp from './../ReviewApp'
import AppActionBar from './AppActionBar'

import { getProfileData, getCountries, getStates, getMajors, getAttendedSchools,  
	getLanguages, getCourseSubjects, getClassesBasedOnSubject, getReligions } from './../../actions/Profile'
import { getPrioritySchools } from './../../actions/Intl_Students'
import { getStudentData, getImposterData } from './../../actions/User'

import './styles.scss'

const View_Student_Application = React.createClass({
	componentWillMount(){
		let { dispatch, params, _profile: _p, _user: _u } = this.props;

		document.body.style.backgroundColor = '#555';
		document.body.style.margin = '0';

		if( _.get(params, 'id') ){
			if( !_p.init_done ) dispatch( getProfileData(params.id) );
			if( !_p.priority_schools_done ) dispatch( getPrioritySchools('?user_id='+params.id) );
			if( !_p.init_states_done ) dispatch( getStates() );
			if( !_p.init_majors_done ) dispatch( getMajors() );
			if( !_p.init_religion_done ) dispatch( getReligions() );
			if( !_p.init_countries_done ) dispatch( getCountries() );
			if( !_p.init_languages_done ) dispatch( getLanguages() );
			if( !_p.init_schools_done ) dispatch( getAttendedSchools() );
			if( !_p.init_course_subjects_done ) dispatch( getCourseSubjects() );
			if( !_p.init_priority_schools_done ) dispatch( getPrioritySchools() );
		}
	},

	componentWillReceiveProps(np){
		let { dispatch, _user: _u, params } = this.props,
			{ _user: _nu } = np;

		if( _u.init_done !== _nu.init_done && _nu.init_done ) dispatch( getImposterData(params.id, _nu) );
	},

	componentWillUnmount(){
		document.body.style.background = '#fff';
		document.body.style.margin = 'initial';
	},

	render(){
		let { dispatch, params, _user: _u, _superUser: _su } = this.props;
		return (
			<DocumentTitle title="Plexuss | View Student Application">
				<div id="_View_Student_Application">

					<AppActionBar su={_su} />

					<div className="reviewapp-wrapper">
						<ReviewApp noEdit={true} />
						<SIC inApp={true} disableRoutes={ !_su.is_plexuss && !_su.is_agency && !(_su.is_aor && _su.aor_id == 5) } />
					</div>

				</div>
			</DocumentTitle>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		_user: state._user,
		_profile: state._profile,
		_superUser: state._superUser,
	};
};

export default connect(mapStateToProps)(View_Student_Application);