// step4.js

import React from 'react';
import Profile from './profile';
import { connect } from 'react-redux';
import { edit } from './../actions/profileActions';
import { validateEmail } from './../actions/validatorActions';
import createReactClass from 'create-react-class'

const Step4 = createReactClass({
	render(){
		return (
			<div>
				targeting page here
			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		user: state.user,
		invalidFields: state.invalidFields
	};
};

export default connect(mapStateToProps)(Step4);
