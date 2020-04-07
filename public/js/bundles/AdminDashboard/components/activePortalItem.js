// activePortalItem.js

import React from 'react'
import _ from 'underscore'
import Display from './../../utilities/display'
import PortalUsersList from './portalUsers_list'
import createReactClass from 'create-react-class'

export default createReactClass({
	getInitialState(){
		return {
			val: '',
			admin_access: false,
			user_access: false,
			open: false,
			usertype: 'Admin',
			_emailValid: false,
			_emailValidated: false,
			focused: '',
			userExists: false,
			userExistsMsg: 'This user is has already been added to this portal.',
		};
	},

	_keyPressed(e){
		var code = e.keyCode || e.which, { addUser, portal } = this.props;
		if( code === 13 ) this.setState({val: ''});
		else this.setState({val: e.target.value});
	},

	_addUserClick(e){
		var { addUser, portal } = this.props;

		this.setState({val: ''});
		if(this.refs.Admin.checked) addUser(portal, this.refs.add_user, e, 'Admin');
		else addUser(portal, this.refs.add_user, e, 'User');
	},

	_validate(e){
		var { portal } = this.props,
			user_exists = null,
			valid = !!e.target.value && /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(e.target.value);

		if( portal && portal.users && portal.users.length > 0 ){
			user_exists = _.findWhere(portal.users, {email: e.target.value});
			if( user_exists ) valid = false;
		}

		this.setState({
			val: e.target.value,
    		_emailValid: valid,
    		_emailValidated: true,
    		focused: e.target.id,
    		userExists: !!user_exists,
    	});
	},

	render(){
		let { user, portal, deactivate, addUser, removeUser, openPortal } = this.props,
			{ open, val, admin_access, user_access, usertype, focused,
				_emailValid, _emailValidated, userExists, userExistsMsg } = this.state,
			itemStyle = null, editStyle,
			good = Object.assign({}, styles.field, styles.good),
			bad = Object.assign({}, styles.field, styles.bad);

			itemStyle = open ? Object.assign({}, styles.item, {backgroundColor: '#eee', color: '#333'}) : styles.item;
			editStyle = open ? Object.assign({}, styles.edit, {color: '#2ba6cb'}) : styles.edit;

		return (
			<div style={itemStyle}>

				<div className="clearfix">
					<div className="left" style={styles.name}>{portal.name}</div>

					<Display if={ user.super_admin && portal.deactivatable }>
						<div className="right">
							<a style={styles.deactivate} onClick={deactivate.bind(null, portal)}>Deactivate</a>
						</div>

						<div className="right">
							<div onClick={() => this.setState({open: !open})}
								style={editStyle}>
									Edit/Add Users
							</div>
							<div style={styles.divider}>|</div>
						</div>

						{/*<div className="right">
							<div style={editStyle}>Rename</div>
							<div style={styles.divider}>|</div>
						</div>*/}
					</Display>

				</div>

				<Display if={ open }>

		    		{/* display list of users */}
	    			{ portal.users && portal.users.length > 0 ? <PortalUsersList portal={portal} users={portal.users} removeUser={removeUser} /> : null }

	    			<form>
		    			{ userExists ? <div style={styles.err}>{userExistsMsg}</div> : null }

			    		{/* add user input */}
		    			<input
		    				name="add_user"
		    				type="text"
		    				placeholder="Enter the email of user..."
		    				ref="add_user"
		    				id={'add_user_to_this_portal_'+portal.hashedid}
		    				onKeyPress={ this._keyPressed }
		    				onFocus={ this._validate }
		    				onChange={ this._validate }
		    				onBlur={ () => this.setState({focused: ''}) }
		    				style={ focused === 'add_user_to_this_portal_'+portal.hashedid ? (_emailValid ? good : bad) : (_emailValidated && !_emailValid ? bad : styles.field) }
		    				value={val} />


			    		{/* invite as admin or user options */}
		    			<div className="clearfix">
		    				<div className="left" style={styles.invite}>Invite as</div>

		    				<div className="left">
		    					<input id={"invite_as_admin_"+portal.hashedid}
		    						type="radio"
		    						name="usertype"
		    						defaultChecked={true}
		    						ref="Admin" />
		    					<label htmlFor={"invite_as_admin_"+portal.hashedid}>Admin</label>
		    				</div>

		    				<div className="left">
		    					<input id={"invite_as_user_"+portal.hashedid}
		    						type="radio"
		    						name="usertype"
		    						ref="User" />
		    					<label htmlFor={"invite_as_user_"+portal.hashedid}>User</label>
		    				</div>

		    				<div className="right">
		    					<button
		    						className="button radius"
		    						style={styles.add}
		    						disabled={ !_emailValid }
		    						onClick={this._addUserClick}>
		    							Add User
		    					</button>
		    				</div>

		    				<div className="right">
		    					<div className="button radius"
		    						style={styles.cancel}
		    						onClick={() => this.setState({open: false, val: '', admin_access: false, user_access: false,
		    							_emailValid: false, _emailValidated: false, focused: '', userExists: false})}>
		    								Cancel
		    					</div>
		    				</div>
		    			</div>
		    		</form>

	    		</Display>

    		</div>
		);
	}
});

const styles = {
	item: {
		backgroundColor: '#333',
		padding: '5px 10px',
		fontSize: '14px',
		color: '#ddd',
		margin: '0 0 10px',
		borderRadius: '2px',
	},
	name: {
		fontWeight: '600'
	},
	deactivate: {
		color: 'firebrick',
		cursor: 'pointer'
	},
	edit: {
		display: 'inline-block',
		color: '#fff',
		cursor: 'pointer'
	},
	divider: {
		display: 'inline-block',
		padding: '0 7px'
	},
	add: {
		padding: '7px 25px'
	},
	cancel: {
		backgroundColor: '#797979',
		padding: '7px 25px',
		margin: '0 10px 0 0'
	},
	field: {
		margin: '0 0 10px'
	},
	invite: {
		margin: '0 10px 0 0'
	},
	bad: {
		border: '1px solid firebrick',
	},
	good: {
		border: '1px solid #24b26b',
	},
	err: {
		color: 'firebrick',
		fontSize: '12px',
	}
}
