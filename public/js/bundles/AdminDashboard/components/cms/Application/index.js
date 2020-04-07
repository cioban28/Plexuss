//Application/index.js

import React from 'react'
import { Link } from 'react-router'
import { connect } from 'react-redux'
import DocumentTitle from 'react-document-title'
import createReactClass from 'create-react-class'

import Heading from './Heading'
import SideNav from './../SideNav'

import { getProfile } from './../../../actions/profileActions'
import { getCollegeData } from './../../../actions/collegeActions'
import { getAppRequirements } from './../../../actions/overviewActions'

import './styles.scss'

const Application = createReactClass({
	render(){
		let { route, children, overview, intl } = this.props;

		return (
			<DocumentTitle title="Admin Tools | Application">
				<div id="_application_container" className="tools-section">

					<SideNav
						items={ route.childRoutes }
						_state={ overview }
						program={ intl.activeProgram } />

					{ children || <Heading
									title='Add Section'
									descrip='Click on a section to the left then click the checkbox "include this in application" or select yes to require it.'/> }

				</div>
			</DocumentTitle>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		user: state.user,
		intl: state.intl,
		college: state.college,
		overview: state.overview,
	};
};

export default connect(mapStateToProps)(Application);
