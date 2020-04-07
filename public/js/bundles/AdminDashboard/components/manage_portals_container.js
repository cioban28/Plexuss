// manage_portals_container.js

import React from 'react'
import Header from './header'
import { connect } from 'react-redux'

import Portal_List from './portal_list'
import AddPortalForm from './addPortalForm'
import Loader from './../../utilities/loader'

import { getProfile } from './../actions/profileActions'
import * as portalActions from './../actions/managePortalsActions'
import createReactClass from 'create-react-class'

const Manage_Portal_Container = createReactClass({
	getInitialState(){
		return {};
	},

	componentWillMount(){
		let { setup } = this.props;

		if( setup.completed ){
			document.getElementById('react-hide-for-admin').style.display = 'block';
			document.getElementById('react-hide-for-admin-2').style.display = 'block';
		}

		let { user, dispatch } = this.props;
		if( !user.id ) dispatch( getProfile() ); //get profile info if don't already have it
	},

	componentDidMount(){
		let { dispatch, portals } = this.props;
		if( !portals.initDone ) dispatch( portalActions.getPortals() ); //get portals if we don't already have it
	},

	deactivate(portal, e){
		e.preventDefault();
		this._updatePortal(portal, 'deactivate');
	},

	removePortal(portal, e){
		e.preventDefault();
		this._updatePortal(portal, 'remove');
	},

	reactivatePortal(portal, e) {
		e.preventDefault();
		this._updatePortal(portal, 'reactivate');
	},

	_updatePortal(portal, action){
		this.props.dispatch( portalActions.updatePortal(portal.name, action, portal.hashedid) );
	},

	addUser(portal, node, e, users_access){
		var e = e || node, code = e.keyCode || e.which;

		//if triggered by input typing
		//else triggered by add user button
		if( e.target.id === 'add_user_input' ){
			if( code === 13 && e.target.value ){
				e.preventDefault();
				this.props.dispatch( portalActions.addUser(portal.hashedid, e.target.value, users_access) );
			}
		}else{
			if( node && node.value ) this.props.dispatch( portalActions.addUser(portal.hashedid, node.value, users_access) );
		}
	},

	removeUser(portal, user, e){
		e.preventDefault();
		this.props.dispatch( portalActions.removeUser(portal, user) );
	},

    render() {
    	var { user, setup, portals } = this.props, _this = this;

        return (
        	<div style={styles.container} >

        		{ portals.fetching || portals.saving ? <Loader /> : null }

        		{ setup.completed ? <Header title="Manage Portals" logo={user.school_logo} titleCustomStyle={styles.custom} goBack={true} /> : null }

        		<AddPortalForm />

	        	{/* active portal list */}
	        	<div style={styles.title}>Active Portals</div>
	        	<Portal_List
	        		is_active={true}
    				user={user}
    				portals={portals.active_portals}
    				addUser={this.addUser}
    				deactivate={this.deactivate}
	        		removeUser={this.removeUser} />

	        	{/* deactivated portal list */}
	        	<div style={styles.title}>Deactivated Portals</div>
	        	<Portal_List
	        		is_active={false}
    				portals={portals.deactivated_portals}
    				removePortal={this.removePortal}
    				reactivatePortal={this.reactivatePortal} />

			</div>
        );
    }
});

const styles = {
	container: {
		maxWidth: '800px',
		margin: 'auto',
		padding: '20px 10px'
	},
    title: {
    	color: '#333',
    	fontWeight: '600',
    	margin: '0 0 10px'
    },
    custom: {
		color: '#797979'
	},
}

const mapStateToProps = (state, props) => {
	return {
		user: state.user,
		setup: state.setup,
		portals: state.portals,
	};
};

export default connect(mapStateToProps)(Manage_Portal_Container);
