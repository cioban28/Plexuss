// manageuser_user.js

import React from 'react'
import { connect } from 'react-redux'
import { Link, browserHistory } from 'react-router'

import Tooltip from './../../utilities/tooltip'

import { updateUser, setAlternateProfile } from './../actions/manageUsersActions'
import createReactClass from 'create-react-class'

const ManageuserUser = createReactClass({

	componentWillReceiveProps(np){
		let tempProfile = np.user.temporaryAlternateProfile;
		if( tempProfile ) browserHistory.push('/admin/profile/'+tempProfile.hasheduserid);
	},

	render(){
		var { dispatch, user, org_user } = this.props,
			tooltipText = '', portal_list = org_user.portal_info,
			pic = {
				width: '30px',
				height: '30px',
				backgroundColor: '#ddd',
				borderRadius: '100%',
				backgroundImage: 'url('+org_user.profile_pic+')',
				backgroundPosition: 'center',
				backgroundRepeat: 'no-repeat',
				backgroundSize: 'cover',
			};


			//if user has one or more portal
			if( portal_list && portal_list.length > 0 )
				tooltipText = portal_list[0].portal_name + ( portal_list.length > 1 ? ', +'+(portal_list.length - 1)+' others' : '');

		return (
			<tr style={ org_user.id === user.id ? styles.user_tr : styles.tr}>
				<td>
					<div style={pic}></div>
				</td>
				<td>
					<div style={styles.name}>{org_user.fname} {org_user.lname}</div>
				</td>
				<td>
					<select style={styles.select}
							value={org_user.super_admin}
							onChange={(e) => dispatch( updateUser(Object.assign({}, org_user, {super_admin: +e.target.value})) )}>
						<option value={1}>Admin</option>
						<option value={0}>User</option>
					</select>
				</td>
				<td>
					{
						portal_list && portal_list.length > 0 ?
						<span style={styles.portals}>
							<Tooltip customText={tooltipText} toolTipStyling={styles.tooltip}>
								{portal_list.map((p, i) => <div key={p.hashedid}>{p.portal_name}</div>)}
							</Tooltip>
						</span>
						: null
					}
				</td>
				<td>
					<select style={styles.select}
							value={org_user.show_on_front_page}
							onChange={(e) => dispatch( updateUser(Object.assign({}, org_user, {show_on_front_page: +e.target.value}), true) )}>
						<option value={1}>Yes</option>
						<option value={0}>No</option>
					</select>
				</td>
				<td>
					<select style={styles.select}
							value={org_user.show_on_college_page}
							onChange={(e) => dispatch( updateUser(Object.assign({}, org_user, {show_on_college_page: +e.target.value})) )}>
						<option value={1}>Yes</option>
						<option value={0}>No</option>
					</select>
				</td>
				<td className="text-center">
					{
						user.id === org_user.id ?
						<Link to="/admin/profile" style={styles.edit}>Edit</Link>
						: <span style={styles.edit} onClick={() => dispatch( setAlternateProfile(org_user) )}>Edit</span>
					}
				</td>
			</tr>
		);
	}
});

const styles = {
	select: {
		margin: 0,
	},
	tr: {
		background: '#fff',
		border: '1px solid #797979'
	},
	user_tr: {
		background: '#f2f2f2',
		border: '1px solid #797979'
	},
	name: {
		color: '#797979',
	},
	edit: {
		textAlign: 'center',
		color: '#2ba6cb',
		textDecoration: 'underline',
		cursor: 'pointer'
	},
	portals: {
		color: '#797979'
	},
	tooltip: {
		fontSize: '14px',
		color: '#797979',
		border: 'none',
		margin: '0 5px 0 0',
		width: 'initial',
		height: 'initial',
		verticalAlign: 'initial',
	}
}

const mapStateToProps = (state, props) => {
	return {
		user: state.user
	};
};

export default connect(mapStateToProps)(ManageuserUser);
