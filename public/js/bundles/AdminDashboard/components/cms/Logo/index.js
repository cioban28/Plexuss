// index.js

import React from 'react'
import { connect } from 'react-redux'
import DocumentTitle from 'react-document-title'
import createReactClass from 'create-react-class'

import Upload from './components/upload'
import Preview from './components/preview'
import Loader from './../../../utils/loader'

import { getCollegeData } from './../../../actions/collegeActions'

const UpdateLogoContainer = createReactClass({
	render(){
		let { logo } = this.props;

		return (
			<DocumentTitle title="Admin Tools | Logo">
				<div className="clearfix tools-section">
					<Upload />
					<Preview />
					{ logo.pending ? <Loader /> : null }
				</div>
			</DocumentTitle>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		logo: state.logo,
		college: state.college,
	};
};

export default connect(mapStateToProps)(UpdateLogoContainer);
