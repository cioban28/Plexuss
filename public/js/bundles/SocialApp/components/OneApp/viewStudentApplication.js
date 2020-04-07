// /OneApp/viewStudentApplication.js

import React from 'react'
import { connect } from 'react-redux'

import ReviewApp from './../../../StudentApp/components/ReviewApp'

import { getStudentData, getImposterData } from './../../../StudentApp/actions/User'
import { getProfileData } from './../../../StudentApp/actions/Profile'

import './styles.scss'

class ViewStudentApplication extends React.Component {
	render(){
		// console.log(this.props)
		return (
			<div className="social_view_student_app">
				<ReviewApp noEdit={true} adminView={true} studentID={this.props.match.params.id}/>
			</div>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		_user: state._user,
		_profile: state._profile,
		_superUser: state._superUser,
	};
};

export default connect(mapStateToProps)(ViewStudentApplication);
