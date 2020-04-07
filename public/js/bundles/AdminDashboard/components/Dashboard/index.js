// dashboard_container.js

import _ from 'lodash'
import React from 'react'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs-fix'

import DashBanner from './components/dashBanner'
import DashMainBody from './components/dashMainBody'
import createReactClass from 'create-react-class';
import './styles.scss'

const Dashboard_Container = createReactClass({
	getInitialState(){
		return {
			currentPortal: ''
		};
	},

	componentWillMount() {
		let { dispatch, portals } = this.props,
			portalIdentifier = document.getElementById('react-route-to-portal-login-2');

		// if current portal is set and portal name identifier is currently on the DOM, update topnav portal name
		if( portals.current_portal && portals.current_portal.name && portalIdentifier ){
			portalIdentifier.innerHTML = portals.current_portal.name;

		}else if( portalIdentifier ){
			this.state.currentPortal = portalIdentifier.innerHTML.trim();
		}
	},

	render(){
		var { dispatch, dash, portals, premiumPlans, user } = this.props,
			{ currentPortal } = this.state,
			currPortal = portals.current_portal.name || currentPortal,


            loading = dash.sendInterestedServicesPending;


		return (
			<div id="_admin_dash" className='admin-main-dash-container clearfix'>

				<DashBanner />

				<div className="dashboard-main-container clearfix">
					<DashMainBody {...dash} is_admin_premium={user.is_admin_premium} dispatch={dispatch} currentPortal={currPortal} />
				</div>

                { loading &&
                    <div className="spin-container"><ReactSpinner color="#24b26b" /></div> }

			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
        user: state.user,
		dash: state.dash,
		portals: state.portals,
	};
};

export default connect(mapStateToProps)(Dashboard_Container);
