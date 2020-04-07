// profile_container.js

import React from 'react'
import Header from './header'
import Profile from './profile'
import { connect } from 'react-redux'
import ProfilePic from './profilePic'
import ProfileCard from './profileCard'
import { browserHistory } from 'react-router'
import Loader from './../../utilities/loader'
import ProfilePermissions from './profile_permissions'
import { validateEmail } from './../actions/validatorActions'
import { edit, removeAlternateProfile, saveProfile, resetSaved } from './../actions/profileActions'
import createReactClass from 'create-react-class'

const Profile_Container = createReactClass({
	componentWillMount(){
		document.getElementById('react-hide-for-admin').style.display = 'block';
		document.getElementById('react-hide-for-admin-2').style.display = 'block';
	},

	componentWillUnmount(){
		let { dispatch, user, routeParams } = this.props;
		if( user.temporaryAlternateProfile ) this._removeAlternateProfile();
	},

	componentWillReceiveProps(np){
		let { user } = np;

		//two things can happen when jumping from one profile to another...
		if( np.routeParams && this.props.routeParams.id !== np.routeParams.id ){
			// if user goes back to a point where the url contains user id, but no alternate profile is set, go back two pages to manage users
			//else if there is no user id in url but there is still an alternate profile set, unset it
			if( np.routeParams.id && !user.temporaryAlternateProfile ) browserHistory.goBack(); //will go back twice b/c back btn goes back then this goes back also
			else if( !np.routeParams.id && user.temporaryAlternateProfile ) this._removeAlternateProfile(); //unset alternate profile
		}

		// if alternate profile is set and it has just finished saving info, go back to prev page
		if( user.temporaryAlternateProfile && user.temporaryAlternateProfile.saved ) browserHistory.goBack();
		// else original user just saved their info, so just reset saved back to false - no need to go anywhere else
		else if( user.saved ) this._resetSaved();
	},

	_removeAlternateProfile(){
		let { dispatch } = this.props;
		dispatch( removeAlternateProfile() );
	},

	_resetSaved(){
		let _this = this;

		setTimeout(() => {
			_this.props.dispatch( resetSaved() );
		}, 7500);
	},

	_save(e){
		e.preventDefault();

		let { dispatch, user, invalidFields } = this.props, data = new FormData(e.target),
			isAlternate = !!user.temporaryAlternateProfile;

		console.log( 'valid or no: ', _.isBoolean(invalidFields.profileFormValid) && !invalidFields.profileFormValid );
		if( _.isBoolean(invalidFields.profileFormValid) && !invalidFields.profileFormValid ) return;

		if( isAlternate ) user = user.temporaryAlternateProfile;

		//if user doesn't have image file, avatar url, and profile pic OR user's profile pic is the default one, then open the profile pic modal
		//else save profile info
		if( (!user.picFile && !user.avatar_url && (!user.profile_pic || user.profile_pic.indexOf('default.png') > -1) ) ){
			dispatch(edit({pro: true, avatar: false, alternate: isAlternate}, 'toggleProfileAvatarModal'));
		}else{
			data.append('user_id', user.id);
			data.append('org_branch_id', user.org_branch_id);
			if( user.picFile ) data.append('profile_pic', user.picFile); //append image file if user uploaded own img
			if( user.useAvatar ) data.append('avatar_url', user.avatar_url); //if avatar chosen, append avatar position

			dispatch( saveProfile( data ) ); //make axios post to save form data
		}
	},

    render() {
    	var { user, invalidFields, routeParams, hideHeader } = this.props,
    		{ profileFormValid, profilePermissionsFormValid } = invalidFields,
    		disableBtn = false,

        disableBtn = (_.isBoolean(profileFormValid) && _.isBoolean(profilePermissionsFormValid) ) && (!profileFormValid || !profilePermissionsFormValid);

        if( user.temporaryAlternateProfile && routeParams.id ) user = user.temporaryAlternateProfile;

        return (
        	<div style={styles.container}>
        		<form onSubmit={this._save}>

	        		<div className="row" style={styles.max}>
	        			<div className="columns small-12">
				        	{ !hideHeader ? <Header title="Edit Profile" logo={user.school_logo} titleCustomStyle={styles.custom} goBack={true} /> : null }
			        	</div>

			        	<div className="column small-12 medium-2">
			        		<ProfilePic routeParams={{...routeParams}} />
			        	</div>

						<div className="column small-12 medium-4">
							<Profile tipStyle={styles.tip} routeParams={{...routeParams}} />
			        		<ProfilePermissions routeParams={{...routeParams}} />

							{ _.isBoolean(invalidFields.profileFormValid) && !invalidFields.profileFormValid ?
								<div style={styles.err}>There are one or more fields that are invalid above. Please fix before saving.</div> : null }

							<div className="text-right">
								{ user.saved ? <div style={styles.saveSuccess}>Successfully saved profile information!</div> : null }
								<button
									className="button radius"
									disabled={ disableBtn }
									style={ styles.btn }>
										Save
								</button>
							</div>

						</div>

						<div className="column small-12 medium-6">
							<ProfileCard user={user} routeParams={{...routeParams}} />
						</div>
					</div>

					{ user.pending ? <Loader /> : null }

	        	</form>
            </div>
        );
    }
});

const styles = {
	container: {
		// border: 'thin solid red'
	},
	btn: {
		padding: '10px 30px',
		background: '#24b26b',
	},
	custom: {
		color: '#797979'
	},
	max: {
		maxWidth: '1230px',
		padding: '30px 0',
		margin: '0 auto 100px'
	},
	tip: {
		color: '#797979'
	},
	saveSuccess: {
		color: '#24b26b',
	},
	err: {
		color: 'firebrick',
		fontSize: '11px',
		margin: '0 0 20px',
		textAlign: 'right',
	},
}

//the way i understand it is, what react-redux does is it creates a wrapper component around Profile_Container
//that (in the background) does the subscribe for you and 'maps' the new state of the wrapper component and passes
//that state down as props to the Profile_Container
const mapStateToProps = (state, props) => {
	return {
		user: state.user,
		invalidFields: state.invalidFields
	};
};

const Connected_Profile_Container = connect(mapStateToProps)(Profile_Container);

export default Connected_Profile_Container;
