// profileCard_Back.js

import React from 'react';
import { connect } from 'react-redux';
import { edit } from './../actions/profileActions';
import createReactClass from 'create-react-class'

const ProfileCard_Back = createReactClass({
	render(){
		let { user, routeParams } = this.props, logo = null;

	        if( user.temporaryAlternateProfile && routeParams.id ) user = user.temporaryAlternateProfile;

			logo = {
				backgroundImage: user.school_logo ? 'url('+user.school_logo+')' : '',
				backgroundSize: 'cover',
				backgroundRepeat: 'no-repeat',
				backgroundPosition: 'center',
				width: '80px',
				height: '80px',
				borderRadius: '100%',
				margin: '0 auto 15px',
				border: '2px solid #eee',
			};

		return (
			<div style={styles.card}>
				<div style={logo}></div>
				<div style={styles.name}>{user.fname || '[First]'} {user.lname || '[Last]'}</div>
				<div style={styles.title}>{user.title || '[Title]'}</div>
				<div style={styles.title}>Since {user.working_since || '[Year]'}</div>
				<div style={styles.blurb}>{user.blurb || '[Small blurb about yourself]'}</div>
			</div>
		);
	}
});

const styles = {
	card: {
		textAlign: 'center',
		width: '250px',
		height: '310px',
		backgroundColor: '#fff',
		padding: '20px 10px 0',
		boxShadow: '1px 1px 2px 1px rgba(0,0,0,0.5)',
		borderRadius: '1px',
	},
	name: {
		fontWeight: '600',
		fontSize: '16px',
		color: '#24b26b',
		whiteSpace: 'nowrap',
		overflow: 'hidden',
		textOverflow: 'ellipsis'
	},
	title: {
		fontSize: '16px',
		color: '#24b26b',
		whiteSpace: 'nowrap',
		overflow: 'hidden',
		textOverflow: 'ellipsis'
	},
	blurb: {
		color: '#000',
		marginTop: '10px',
		fontSize: '13px'
	}
};

const mapStateToProps = (state, props) => {
	return {
		user: state.user,
		invalidFields: state.invalidFields,
		currentStep: state.setup.currentStep
	};
};

export default connect(mapStateToProps)(ProfileCard_Back);
