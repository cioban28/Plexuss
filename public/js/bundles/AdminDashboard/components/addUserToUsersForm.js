// addUserToUsersForm.js

import _ from 'lodash'
import React from 'react'
import { Link } from 'react-router'
import { connect } from 'react-redux'

import AddUserToPortalDropdown from './addUserToPortalDropdown'
import { addUser, resetNewUser } from './../actions/manageUsersActions'
import createReactClass from 'create-react-class'

const AddUserToUsersForm = createReactClass({
	getInitialState(){
		return {
			err: false,
			errmsg: 'Email field cannot be empty and you must select at least one portal to add this new user to.',
			open: false,
			role: 'Admin',
			_emailValid: false,
			_emailValidated: false,
			focused: '',
		};
	},

	componentWillReceiveProps(np){
		let { users } = np;
		if( users.newUserAdded ) this._resetNewUserDelayed();
	},

	_resetNewUserDelayed(){
		//reset new user info after 5 seconds of the new user being added
		let _this = this;

		setTimeout(() => {
			_this.props.dispatch( resetNewUser() );
		}, 7500);
	},

	_addUser(e){
		e.preventDefault();

		let { dispatch, users } = this.props,
			email = this.refs.emailInput.value,
			role = this.state.role,
			newUser = {users_name: email, 'users-access': role};

		_.each(users.newUsersPortals, (portal) => {newUser['portal_name_'+portal.hashedid] = portal.name});

		if( email && users.newUsersPortals.length > 0 ){
			this.setState({err: false, open: false, _emailValid: false, _emailValidated: false});
			dispatch( addUser(newUser) );
		}else this.setState({err: true});
	},

	_validate(e){
		var valid = !!e.target.value && /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(e.target.value);
		this.setState({_emailValid: valid, _emailValidated: true, focused: e.target.id});
	},

	render(){
		var { user, users, invalidFields } = this.props,
    		{ open, role, err, errmsg, _emailValid, _emailValidated, focused } = this.state;

		return ( !open ) ?
			<div className="text-right">
				{ users.newUserAdded ? <div style={styles.addedSuccess}>{users.newUserAddedMsg}</div> : null }
				<Link to="/admin/portals" style={styles.route}>
					<div style={styles.btnPortals} className="button radius">
						Manage Portals
					</div>
				</Link>
				<div style={styles.btnAdd} className="button radius" onClick={() => this.setState({open: true})}>
					Add User
				</div>
			</div>
			:
			<div className="row collapse">

				<div className="column small-10">
					<input type="text"
						id="add_user_email"
						ref="emailInput"
						placeholder="Enter email here..."
						name="add_user_email"
						style={ focused === 'add_user_email' ? (_emailValid ? styles.good : styles.bad) : (_emailValidated && !_emailValid ? styles.bad : {}) }
						onFocus={ this._validate }
						onBlur={ () => this.setState({focused: ''}) }
						onChange={ this._validate } />
				</div>

				<div className="column small-2">
					<button
						className="button radius"
						style={ styles.add }
						disabled={ !_emailValid && !err }
						onClick={ this._addUser }>
							Add User
					</button>
				</div>

				{
					_.isBoolean(invalidFields.addUserValid) && !invalidFields.addUserValid ?
					<div style={styles.err} className="column small-12">Invalid email input. Please enter a valid email. Ex: username@domain.com</div> : null
				}

				<div className="column small-10">
					<div style={styles.invite}>Invite as: </div>

					<input id="role_admin"
						   type="radio"
						   name="role"
						   style={ styles.radio }
						   value={'Admin'}
						   onChange={ () => this.setState({role: 'Admin'}) }
						   checked={ role === 'Admin' } />
					<label htmlFor="role_admin">Admin</label>

					<input id="role_user"
						   type="radio"
						   name="role"
						   style={ styles.radio }
						   value={'User'}
						   onChange={ () => this.setState({role: 'User'}) }
						   checked={ role === 'User' } />
					<label htmlFor="role_user">User</label>

					<AddUserToPortalDropdown err={err} />

				</div>

				<div className="column small-2">
					<div
						className="button radius"
						style={ styles.cancel }
						onClick={ () => this.setState({open: false, _emailValid: false, _emailValidated: false, err: false}) }>
							Cancel
					</div>
				</div>

				{ err ? <div className="column small-12" style={styles.err}>{errmsg}</div> : null }
			</div>
	}
});

const styles = {
	addedSuccess: {
		color: '#24b26b',
		textAlign: 'center',
		padding: '0 0 20px'
	},
	add: {
		padding: '10px 31px 9px 32px',
		width: '100%',
	},
	cancel: {
		padding: '10px 40px',
		backgroundColor: '#797979',
		width: '100%',
	},
	radio: {
		margin: 0
	},
	invite: {
		display: 'inline-block',
		margin: '0 10px 0 0',
		color: '#797979',
		verticalAlign: 'middle',
	},
	btnPortals: {
		backgroundColor: '#797979',
		color: '#fff',
		padding: '10px 30px',
		display: 'inline-block',
	},
	btnAdd: {
		backgroundColor: '#24b26b',
		color: '#fff',
		padding: '10px 30px',
		display: 'inline-block',
		margin: '0 0 0 10px'
	},
	empty: {
		color: '#797979',
		textAlign: 'center',
		fontSize: '18px',
		padding: '20px 0'
	},
	route: {
		color: '#fff'
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
	bad: {
		border: '1px solid firebrick',
	},
	good: {
		border: '1px solid #24b26b',
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

export default connect(mapStateToProps)(AddUserToUsersForm);
