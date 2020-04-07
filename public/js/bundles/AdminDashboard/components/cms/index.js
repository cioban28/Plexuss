// /cms/index.js

import React from 'react'
import selectn from 'selectn'
import { Link } from 'react-router'
import { connect } from 'react-redux'
import createReactClass from 'create-react-class'

import { getProfile } from './../../actions/profileActions'
import { getPins } from './../../actions/cmsRankingsActions'
import { getCollegeData } from './../../actions/collegeActions'
import { getTuitionCostData } from './../../actions/costActions'
import { getAppRequirements, getOverviewData } from './../../actions/overviewActions'
import { getAllIntlData, initMajorsData } from './../../actions/internationalActions'

// -- not too sure if I want to include this right now
import './styles.scss'

const ToolsContainer = createReactClass({
	componentWillMount(){
		let { dispatch, intl, _cost, overview, college, rankings, user } = this.props;

		if( !overview.init_done ) dispatch( getOverviewData() ); // init /tools/overview page data
		if( !intl.init_done ) dispatch( getAllIntlData() ); // init /tools/international page data
		if( !intl.major_data_init_done ) dispatch( initMajorsData() ); // init majors data for /tools/international/majors
		if( !_cost.init_done ) dispatch( getTuitionCostData() ); // init /tools/cost page data
		if( !college.initDone ) dispatch( getCollegeData() ); //init college data
		if( !overview.app_requirements_init_done ) dispatch( getAppRequirements() ); // init /tools/application page data
		if( !rankings.pins || _.isEmpty(rankings.pins) ) dispatch( getPins() ); //if pins (for whatever reason, is not set or it's empty, fetch pins)
		if( !user.initProfile ) dispatch( getProfile() ); //if we don't yet have profile info, get it
	},

    componentDidMount(){
        mixpanel.track('admin-content-management', { Location: 'Dashboard' });
    },

	_isGlobalAlertOpen(){
		var webinar = document.getElementById('_webinar_bar'),
			chat = document.getElementById('_chat_bar');

		if( webinar || chat ) return 'global_alert_on';
		return '';
	},

	render(){
		let { user, children, route } = this.props,
			alertClass = this._isGlobalAlertOpen();

		return (
			<div id="main-content-management-container" className={alertClass}>
				<ul className="cms-tab-list">
					<ToolsTab notLink={'SECTIONS:'} />
					{ selectn('childRoutes', route) && route.childRoutes.map((r) => <ToolsTab key={r.path} route={r} />) }
				</ul>

				{ children }
			</div>
		);
	}
});

const ToolsTab = createReactClass({
	render(){
		let { route, notLink, name } = this.props;

		return (
			<li className="tab">
				{ notLink || <Link to={route.path} className="tabLink" activeClassName="active">{route.name}</Link> }
			</li>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		user: state.user,
		intl: state.intl,
		_cost: state.cost,
		college: state.college,
		overview: state.overview,
		rankings: state.rankings,
	};
};

export default connect(mapStateToProps)(ToolsContainer);
