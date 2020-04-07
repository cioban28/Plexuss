// /Cost/index.js

import React from 'react'
import { Link } from 'react-router'
import { connect } from 'react-redux'
import { toastr } from 'react-redux-toastr'
import DocumentTitle from 'react-document-title'
import createReactClass from 'create-react-class'

import SideNav from './../SideNav'

import { CHANGES_ROUTE } from './constants'
import { spinjs_config } from './../../common/spinJsConfig'
import { resetSaved } from './../../../actions/costActions'

import './styles.scss'

const Cost = createReactClass({
	componentWillReceiveProps(np){
		let { dispatch, _cost } = this.props;

		// if next state is different from this state AND next state saved is true, trigger toastr
		if(_cost.saved !== np._cost.saved && np._cost.saved ){
			var OPTIONS = {
				timeOut: 7000, // by setting to 0 it will prevent the auto close
				component: (
					<div className="toastr-component">
						<div className="main">Your changes have been saved and published!</div>
						<div className="sub">You can view your changes <a href={ CHANGES_ROUTE } target="_blank">here</a></div>
					</div>
				),
			};

			toastr.success('', OPTIONS);
			dispatch( resetSaved() );
		}
	},

	render(){
		let { children, route, _cost, intl } = this.props;

		return (
			<DocumentTitle title="Admin Tools | Cost">
				<div className="tuition_cost_container tools-section">
					<SideNav
						items={ route.childRoutes }
						_state={ _cost }
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
		_cost: state.cost,
	};
};

export default connect(mapStateToProps)(Cost);
