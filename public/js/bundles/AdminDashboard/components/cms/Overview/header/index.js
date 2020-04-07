// index.js

import React from 'react'
import { connect } from 'react-redux'
import DocumentTitle from 'react-document-title'
import createReactClass from 'create-react-class'

import OverviewImageForm from './components/overviewImageForm'
import OverviewVideoForm from './components/overviewVideoForm'

const OverviewHeader = createReactClass({
	render(){
		return (
			<DocumentTitle title="Admin Tools | Overview Header">
				<div>
					<OverviewImageForm {...this.props} />
					<hr />
					<OverviewVideoForm {...this.props} />
				</div>
			</DocumentTitle>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		overview: state.overview,
	};
};

export default connect(mapStateToProps)(OverviewHeader);
