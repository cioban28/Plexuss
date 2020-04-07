// index.js

import React from 'react'
import selectn from 'selectn'
import { Link } from 'react-router'
import { connect } from 'react-redux'
import { toastr } from 'react-redux-toastr'
import DocumentTitle from 'react-document-title'
import createReactClass from 'create-react-class'

import SideNav from './../SideNav'

import { getOverviewData, resetSaved } from './../../../actions/overviewActions'

import './styles.scss'

const Overview = createReactClass({
	componentWillReceiveProps(np){
		let { dispatch, overview } = this.props;

		// if next state is different from this state AND next state saved is true, trigger toastr
		if( overview.saved !== np.overview.saved && np.overview.saved ){
			var OPTIONS = {
				timeOut: 7000, // by setting to 0 it will prevent the auto close
				component: (
					<div className="toastr-component">
						<div className="main">Your changes have been saved and published!</div>
						<div className="sub">You can view your changes <a href={overview.route} target="_blank">here</a></div>
					</div>
				),
			};

			toastr.success('', OPTIONS);
			dispatch( resetSaved() );
		}
	},

	render(){
		let { children, route, overview, intl } = this.props;

		return (
			<DocumentTitle title="Admin Tools | Overview">

				<div className="intl-container tools-section">
					<SideNav
						items={ route.childRoutes }
						_state={ overview }
						program={ intl.activeProgram } />

					<div className="row content-start">
						<div className="column small-12 medium-10 content">
							{ children }
						</div>
					</div>
				</div>

			</DocumentTitle>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		intl: state.intl,
		overview: state.overview,
	};
};

export default connect(mapStateToProps)(Overview);
