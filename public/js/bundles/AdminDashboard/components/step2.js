// step2.js

import React from 'react'
import { connect } from 'react-redux'
import Loader from './../../utilities/loader'
import { browserHistory } from 'react-router'
import { nextStep } from './../actions/setupActions'
import ProfilePermissions from './profile_permissions'
import { validateEmail } from './../actions/validatorActions'
import { edit, saveProfile } from './../actions/profileActions'
import createReactClass from 'create-react-class'

const Step2 = createReactClass({
	componentWillReceiveProps(nextprops){
		//go to next step once done saving
		if( this.props.user.saved !== nextprops.user.saved ) this.toNextStep();
	},

	toNextStep(){
		var { dispatch, currentStep } = this.props, step = 3;
		if( currentStep === 2 ) dispatch( nextStep(step) ); //only need to dispatch once
		browserHistory.push('/admin/setup/step'+step);
	},

	save(e){
		e.preventDefault();
		let { dispatch, user } = this.props, data = new FormData(e.target);

		data.append('user_id', user.id);
		data.append('org_branch_id', user.org_branch_id);

		dispatch( saveProfile( data ) ); //make axios post to save form data
	},

	render(){
		var { user, invalidFields } = this.props;

		return (
			<div>
				{ user.pending ? <Loader/> : null }
				<form onSubmit={this.save}>
					<ProfilePermissions {...styles} />

					<div style={styles.btnrow}>
	                    <button
	                    	className="button radius"
	                    	disabled={ _.isBoolean(invalidFields.profilePermissionsFormValid) && !invalidFields.profilePermissionsFormValid }
	                    	style={styles.btn}>
	                    		Next
	                    </button>
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
	},
	btnrow: {
		maxWidth: '500px',
		textAlign: 'right',
		margin: 'auto',
	}
}

const mapStateToProps = (state, props) => {
	return {
		user: state.user,
		invalidFields: state.invalidFields,
		currentStep: state.setup.currentStep
	};
};

export default connect(mapStateToProps)(Step2);
