// index.js

import React from 'react'
import selectn from 'selectn'
import { Link } from 'react-router'
import { connect } from 'react-redux'
import { toastr } from 'react-redux-toastr'
import DocumentTitle from 'react-document-title'

import SideNav from './SideNav'
import './styles.scss'

class Scholarshipcms extends React.Component{
	render(){
		let { children, route, scholarships, intl } = this.props;
		
		return (
			<DocumentTitle title="Admin Tools | Scholarship Management">
				<div id="main-content-management-container">
					<div className="intl-container tools-section">
						<SideNav 
							items={ route.childRoutes }
							_state={ scholarships }
							program={ intl.activeProgram } />

						<div className="row content-start">
							<div className="column small-12 medium-10 content">
								{ children }
							</div>
						</div>
					</div>
				</div>
			</DocumentTitle>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		intl: state.intl,
		scholarships: state.scholarships,
	}
}

export default connect(mapStateToProps)(Scholarshipcms);