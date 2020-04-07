// profilePic.js

import React from 'react'
import { connect } from 'react-redux'
import { deleteUser } from './../actions/profileActions'
import createReactClass from 'create-react-class'

const ProfilePic = createReactClass({
	// componentWillReceiveProps(np){
	// 	if( np.user.temporaryAlternateProfile.deleted ) browserHistory.goBack();
	// },

	render(){
		let { dispatch, user, routeParams } = this.props, imgSrc = null, pic,
			isAlternate = !!user.temporaryAlternateProfile;

	        if( isAlternate && routeParams.id ) user = user.temporaryAlternateProfile;

	        pic = {
				backgroundImage: user.picObjectURL ? 'url('+user.picObjectURL+')' : 'url('+user.profile_pic+')',
				backgroundSize: 'cover',
				backgroundRepeat: 'no-repeat',
				backgroundPosition: 'center',
				width: '100px',
				height: '100px',
				backgroundColor: '#eee',
				borderRadius: '2px',
			};

		return (
			<div>
				<div style={pic}></div>
				<div style={styles.name}>{user.fname} {user.lname}</div>
				{/*{ isAlternate ? <div style={styles.deleteAcct} onClick={() => dispatch( deleteUser(user) )}>Delete Account</div> : null }*/}
			</div>
		);
	}
});

const styles = {
	deleteAcct: {
		fontSize: '12px',
		color: 'firebrick',
		textDecoration: 'underline',
		textAlign: 'center',
		cursor: 'pointer'
	},
	name: {
		color: '#797979',
		whiteSpace: 'nowrap',
		overflow: 'hidden',
		textOverflow: 'ellipsis',
		margin: '10px 0 30px'
	}
}

const mapStateToProps = (state, props) => {
	return {
		user: state.user,
	};
};

export default connect(mapStateToProps)(ProfilePic);
