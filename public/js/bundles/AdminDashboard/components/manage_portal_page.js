// manage_portal_page.js

import React from 'react';
import Header from './header';
import { connect } from 'react-redux';
import ManagePortalContainer from './manage_portals_container';
import createReactClass from 'create-react-class'

const ManagePortalPage = createReactClass({
	render(){
		var { user } = this.props;
		return (
			<div>
				<ManagePortalContainer />
			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		user: state.user
	};
};

export default connect(mapStateToProps)(ManagePortalPage);
