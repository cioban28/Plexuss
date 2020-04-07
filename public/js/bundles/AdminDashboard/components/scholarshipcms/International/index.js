// index.js

import React from 'react'
import selectn from 'selectn'
import { Link } from 'react-router'
import { connect } from 'react-redux'
import { toastr } from 'react-redux-toastr'
import DocumentTitle from 'react-document-title'
import createReactClass from 'create-react-class'

import SideNav from './../SideNav'

import * as constants from './constants'
import { getAllIntlData, initMajorsData, resetSaved } from './../../../actions/internationalActions'

import './styles.scss'

const InternationalContainer = createReactClass({
	componentWillReceiveProps(np){
		let { dispatch, intl } = this.props;

		// if next state is different from this state AND next state saved is true, trigger toastr
		if( intl.saved !== np.intl.saved && np.intl.saved ){
			var route = intl.route[intl.activeProgram];
			var OPTIONS = {
				timeOut: 7000, // by setting to 0 it will prevent the auto close
				component: (
					<div className="toastr-component">
						<div className="main">Your changes have been saved and published!</div>
						<div className="sub">You can view your changes <a href={route} target="_blank">here</a></div>
					</div>
				),
			};

			toastr.success('', OPTIONS);
			dispatch( resetSaved() );
		}
	},

	render(){
		let { children, route, intl } = this.props;

		return (
			<DocumentTitle title="Admin Tools | International Students">
				<div className="intl-container tools-section">
					<SideNav
						items={ route.childRoutes }
						_state={ intl }
						program={ intl.activeProgram } />

					<div className="row content-start">
						<div className="column small-12 medium-10 content">{ children }</div>
					</div>
				</div>
			</DocumentTitle>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		intl: state.intl,
	};
};

export default connect(mapStateToProps)(InternationalContainer);
