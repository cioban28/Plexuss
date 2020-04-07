// deactivatedPortalItem.js

import React from 'react';
import createReactClass from 'create-react-class'

export default createReactClass({
	render(){
		let { portal, removePortal, reactivatePortal } = this.props;

		return (
			<div style={styles.item} data-hashedid={portal.hashedid}>
				<div className="clearfix">
					<div className="left" style={styles.name}>{portal.name}</div>
					<div className="right">
						<div style={styles.deactivate} onClick={removePortal.bind(null, portal)}>Remove</div>
					</div>
					<div className="right">
						<div style={styles.edit} onClick={reactivatePortal.bind(null, portal)}>Reactivate</div>
						<div style={styles.divider}>|</div>
					</div>
				</div>
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
		margin: '0 0 10px'
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
}
