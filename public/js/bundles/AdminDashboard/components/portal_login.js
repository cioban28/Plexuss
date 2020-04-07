// portal_login.js

import React from 'react'
import Header from './header'
import { connect } from 'react-redux'
import PortalDoorway from './portal_doorway'
import Loader from './../../utilities/loader'
import { Link, browserHistory } from 'react-router'
import { resetCancelTokenList } from './../actions/dashboardActions'
import { getPortals, resetPortalEntered } from './../actions/managePortalsActions'
import createReactClass from 'create-react-class'

const PortalLogin = createReactClass({
	getInitialState(){
		return {
			bgColors: [
				'#2AC56C',
				'#26C9FF',
				'#000000',
				'#B973FF',
				'#FFC926',
				'#FF4D4D',
				'#26FFC9',
				'#2DFF96',
				'#AEE800',
			]
		};
	},

	componentWillMount(){
		let { portals, dispatch, dash } = this.props;
		if( !portals.initDone ) dispatch( getPortals() );


		//do not want the topnav -- non-react element to display on page
		//grab it and set display to none
		let uppernav = document.getElementById('react-hide-for-admin'),
			topnav = document.getElementById('react-hide-for-admin-2');

		if(uppernav && topnav){
			uppernav.style.display = 'none';
			topnav.style.display = 'none';
		}

		// on portal login mount, if cancelTokenList is not empty,
		// loop through each and run cancel to cancel all dashboard ajax requests
		if( _.get(dash, 'cancelTokenList.length', 0) > 0 ){
			dash.cancelTokenList.forEach(cancel => cancel());
			dispatch( resetCancelTokenList() ); // after, reset list to null
		}
	},

	componentWillReceiveProps(np){
		if( np.portals.portalEntered ) this._enterPortal();
	},

	componentWillUnmount(){
		let uppernav = document.getElementById('react-hide-for-admin'),
			lowernav = document.getElementById('react-hide-for-admin-2');
		// make portalEntered false on unmount
		this.props.dispatch(resetPortalEntered());

		if( uppernav && lowernav ){
			uppernav.style.display = 'block';
			lowernav.style.display = 'block';
		}
	},

	_enterPortal(){
		browserHistory.push('/admin/dashboard');
	},

	_getBgColor(i){
		var { bgColors } = this.state;

		if( i < bgColors.length ) return bgColors[i];
		return bgColors[Math.floor(Math.random() * 9)];
	},

	render(){
		let _this = this, { user, portals } = this.props, containerStyle = styles.container;
		containerStyle = portals.netflixBg ? Object.assign({}, containerStyle, {backgroundImage: 'url('+portals.netflixBg+')'}) : containerStyle;

		return (
			<div style={containerStyle}>
				<div style={styles.wrapper}>
					<Header title="Select Portal" logo={user.school_logo} />

					{/* only super admins are allowed to access manage users/portals */}
					{
						user && user.super_admin ?
						<div style={styles.lnkContainer}>
							<div style={styles.nav}><Link to="/admin/users" style={styles.lnk}>Manage Users</Link></div>
							<div style={styles.nav}><Link to="/admin/portals" style={styles.lnk}>Manage Portals</Link></div>
						</div>: null
					}


					<div>
						{
							portals && portals.active_portals.length > 0 ?
							portals.active_portals.map( (portal, i) => <PortalDoorway key={portal.name} portal={portal} color={_this._getBgColor(i)} /> )
							: null
						}
					</div>

					{ portals.saving ? <Loader /> : null }
				</div>
			</div>
		);
	}
});

const styles = {
	container: {
		backgroundImage: "url(https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/portal_login_bg.jpg)",
		backgroundSize: 'cover',
		backgroundRepeat: 'no-repeat',
		position: 'absolute',
		top: 0, right: 0, bottom: 0, left: 0,
		zIndex: 20,
		overflowY: 'auto',
	},
	nav: {
		display: 'inline-block',
		verticalAlign: 'center',
		margin: '0 10px 0 0'
	},
	wrapper: {
		maxWidth: '500px',
		backgroundColor: 'rgba(0,0,0,0.4)',
	    padding: '32px',
	    borderRadius: '5px',
	    margin: '75px auto'
	},
	lnkContainer: {
		padding: '0 0 40px 63px'
	},
	lnk: {
		color: '#fff',
		textDecoration: 'underline',
		fontSize: '14px'
	}
}

const mapStateToProps = (state, props) => {
	return {
		user: state.user,
		dash: state.dash,
		portals: state.portals
	};
};

export default connect(mapStateToProps)(PortalLogin);
