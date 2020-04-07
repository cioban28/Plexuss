// portal_doorway.js

import React from 'react';
import { connect } from 'react-redux'
import Tooltip from './../../utilities/tooltip'
import { setPortal } from './../actions/managePortalsActions'
import createReactClass from 'create-react-class'

const PortalDoorway = createReactClass({
	getInitialState(){
		return{
			portalIcon: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/portal_doorway_icon.png',
			generalPortalIcon: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/general_portal_icon.png',
			hovering: false
		};
	},

	render(){
		let { dispatch, portals, portal, openDoor, color } = this.props,
			{ portalIcon, generalPortalIcon, hovering } = this.state, cp = portals.current_portal, isCurrentPortal = false,
			checkmark = [<div key={'checkmark-'+portal.hashedid} style={styles.checkmark}>&#10003;</div>],
			icon = {
				width: '120px',
				height: '120px',
				backgroundColor: color,
				backgroundImage: 'url('+(portal.name === 'General' ? generalPortalIcon : portalIcon)+')',
				backgroundRepeat: 'no-repeat',
				backgroundPosition: 'center',
				borderRadius: '5px',
				cursor: 'pointer',
				border: '3px solid transparent',
			};

			if(  (cp && cp.name) && (cp.name === portal.name) ) isCurrentPortal = true;
			icon = isCurrentPortal ? Object.assign({}, icon, {border: '3px solid #fff'}) : icon;
			icon = hovering ? Object.assign({}, icon, {boxShadow: '0px 0px 7px 2px rgba(0,0,0,0.6)'}) : icon;

		return (
			<div style={styles.container}
				onClick={() => dispatch(setPortal(portal))}
				onMouseOver={() => this.setState({hovering: true})}
				onMouseOut={() => this.setState({hovering: false})}>
					{ isCurrentPortal ?
						<Tooltip toolTipStyling={styles.tooltip} customText={checkmark}>
							Your current portal
						</Tooltip>
						: null
					}
					<div style={icon}></div>
					<div style={styles.name}>{portal.name}</div>
			</div>
		);
	}
});

const styles = {
	container: {
		display: 'inline-block',
		verticalAlign: 'top',
		maxWidth: '120px',
		margin: '0 25px 20px 0',
		textAlign: 'center',
		position: 'relative',
	},
	name: {
		color: '#fff',
		textAlign: 'center'
	},
	checkmark: {
		color: '#fff',
        fontSize: '23px',
	},
	tooltip: {
        position: 'absolute',
        top: '2px', right: '7px',
        border: 'none',
        borderRadius: 0,
       	margin: 0,
       	height: 'initial',
       	width: 'initial',
	}
}

const mapStateToProps = (state, props) => {
	return {
		portals: state.portals
	};
};

export default connect(mapStateToProps)(PortalDoorway);
