// manage_users_container.js

import React from 'react'
import Header from './header'
import { connect } from 'react-redux'

import Loader from './../../utilities/loader'
import ManageuserUser from './manageuser_user'
import AddUserToUsersForm from './addUserToUsersForm'

import { getProfile } from './../actions/profileActions'
import { getUsers, addUser, resetNewUser } from './../actions/manageUsersActions'
import createReactClass from 'create-react-class'

const ManageUsersContainer = createReactClass({
	getInitialState(){
		return {
			err: false,
			errmsg: 'Email field cannot be empty and you must select at least one portal to add this new user to.',
			_emailValid: false,
			_emailValidated: false,
			focused: '',
		};
	},

	componentWillMount(){
		document.getElementById('react-hide-for-admin').style.display = 'block';
		document.getElementById('react-hide-for-admin-2').style.display = 'block';

		let { user, dispatch } = this.props;
		if( !user.id ) dispatch( getProfile() ); //get profile info if don't already have it
	},

	componentDidMount(){
		let { dispatch, user, users } = this.props;
		if( !users.initDone ) dispatch( getUsers() );
	},

    render() {
    	var { user, users, invalidFields } = this.props;

        return (
        	<div style={styles.container}>
	        	<Header title="Manage Users" logo={user.school_logo} titleCustomStyle={styles.custom} goBack={true} />

	        	{ users.fp_unset_err_msg ? <div style={styles.err}>{users.fp_unset_err_msg}</div> : null }
	        	{ users.update_err ? <div style={styles.err}>{'There was an error updating that user. Refresh the page and try again.'}</div> : null }
				{ user.display_setting_err && user.display_setting_err.has_err ? <div style={styles.display_err}>{user.display_setting_err.msg}</div> : null }

				<table style={styles.table}>
					<tbody>
						<tr>
							<th width="20" style={styles.th}></th>
							<th width="200" style={styles.th_l}>Name</th>
							<th width="120" style={styles.th_l}>Role</th>
							<th width="300" style={styles.th_l}>Portals</th>
							<th width="120" style={styles.th}>Front page</th>
							<th width="120" style={styles.th}>College page</th>
							<th width="100" style={styles.th}>Edit Profile</th>
						</tr>

						<ManageuserUser key={user.id} org_user={user} />
		    			{ users.list && users.list.length > 0 ?
		    				users.list.map( (obj, i) => <ManageuserUser key={obj.hasheduserid} org_user={obj} /> ) :
		    				<tr><td colSpan="6" style={styles.empty}>{'There are no other users in your organization.'}</td></tr>
		    			}
	    			</tbody>
				</table>

				<AddUserToUsersForm />

				{ users.pending ? <Loader /> : null }
			</div>
        );
    }
});

const styles = {
	container: {
		maxWidth: '1000px',
		margin: '0 auto 100px',
		padding: '20px 10px',
	},
	table: {
		width: '100%',
		border: 'none',
	},
	th: {
		textAlign: 'center',
		color: '#797979'
	},
	th_l: {
		textAlign: 'left',
		color: '#797979'
	},
	custom: {
		color: '#797979'
	},
	empty: {
		color: '#797979',
		textAlign: 'center',
		fontSize: '18px',
		padding: '20px 0'
	},
	err: {
		color: '#FF5C26',
		textAlign: 'center',
		fontSize: '14px'
	},
	display_err: {
		color: '#FF5C26',
		textAlign: 'center',
		fontSize: '14px'
	},
};

const mapStateToProps = (state, props) => {
	return {
		user: state.user,
		users: state.users,
		portals: state.portals,
		invalidFields: state.invalidFields,
	};
};

export default connect(mapStateToProps)(ManageUsersContainer);
