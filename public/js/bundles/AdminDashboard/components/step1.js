// step1.js

import React from 'react'
import Profile from './profile'
import { connect } from 'react-redux'
import ProfileCard from './profileCard'
import Loader from './../../utilities/loader'
import { browserHistory } from 'react-router'
import { edit } from './../actions/profileActions'
import { nextStep } from './../actions/setupActions'
import { saveProfile } from './../actions/profileActions'
import { validateEmail } from './../actions/validatorActions'
import createReactClass from 'create-react-class'

const Step1 = createReactClass({
	componentWillReceiveProps(nextprops){
		//go to next step once done saving
		if( this.props.user.saved !== nextprops.user.saved ) this.toNextStep();
	},

	toNextStep(){
		var { dispatch, currentStep } = this.props, step = 2;
		if( currentStep === 1 ) dispatch( nextStep(step) ); //only need to dispatch once
		browserHistory.push('/admin/setup/step'+step);
	},

	save(e){
		//post all of user's info
		e.preventDefault();
		let { dispatch, user } = this.props, data = new FormData(e.target);

		//if user hasn't chosen a profile pic, open the profile pic modal
		//else save profile info
		if( (!user.picFile && !user.avatar_url && (!user.profile_pic || user.profile_pic.indexOf('default.png') > -1) ) ){
			dispatch(edit({pro: true, avatar: false}, 'toggleProfileAvatarModal'));
		}else{
			data.append('user_id', user.id);
			data.append('org_branch_id', user.org_branch_id);
			if( user.picFile ) data.append('profile_pic', user.picFile); //append image file if user uploaded own img
			if( user.useAvatar ) data.append('avatar_url', user.avatar_url); //if avatar chosen, append avatar position

			dispatch( saveProfile( data ) ); //make axios post to save form data
		}
	},

	render(){
		var { user, invalidFields } = this.props;

		return (
			<div>
				{ user.pending ? <Loader /> : null }

				<form onSubmit={this.save} className="text-center">
					<div className="row">
						<div className="column small-12 medium-5">
							<Profile {...styles} />
							<div className="text-right">
								<button
									className="button radius"
									disabled={ _.isBoolean(invalidFields.profileFormValid) && !invalidFields.profileFormValid }
									style={styles.btn}>
										Next
								</button>
							</div>
						</div>

						<div className="column small-12 medium-7">
							<ProfileCard user={user} {...styles} />
						</div>
					</div>
				</form>

			</div>
		);
	}
});

const styles = {
	btn: {
		backgroundColor: '#FF5C26',
		padding: '10px 75px'
	},
	customLabel: {
		color: '#fff'
	},
	customTip: {
		color: '#fff',
		border: '1px solid #fff'
	}
};

const mapStateToProps = (state, props) => {
	return {
		user: state.user,
		invalidFields: state.invalidFields,
		currentStep: state.setup.currentStep
	};
};

export default connect(mapStateToProps)(Step1);
