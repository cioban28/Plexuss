// portalUsers_list.js

import React from 'react'
import Tooltip from './../../utilities/tooltip'
import createReactClass from 'create-react-class'

export default createReactClass({
	render(){
		let { portal, users, removeUser } = this.props;

		return (
			<div className="clearfix" style={styles.list}>
				{
					users.map((user, i) => {
						if( user.email ){
							return <div className="left" key={i} style={styles.user}>
										<Tooltip customText={user.email} toolTipStyling={styles.tooltip}>
											<div>{user.super_admin ? 'Admin' : 'User'}</div>
										</Tooltip>

										{ users.length > 1 ?
											<div style={styles.removeuser} onClick={removeUser.bind(null, portal.hashedid , user.user_id)}> x </div>
											: null
										}
							   	   </div>
						}
					})
				}
			</div>
		);
	}
});

const styles = {
	removeuser: {
		// color: '#FB1313',
		color: 'firebrick',
		display: 'inline-block',
		cursor: 'pointer'
	},
	user: {
		margin: '0 15px 0 0'
	},
	list: {
		margin: '10px 0'
	},
	tooltip: {
		fontSize: '14px',
		color: '#333',
		border: 'none',
		margin: '0 5px 0 0',
		width: 'initial',
		height: 'initial',
		verticalAlign: 'initial',
	}
}
