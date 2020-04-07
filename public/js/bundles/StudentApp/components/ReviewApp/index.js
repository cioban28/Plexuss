// /ReviewApp/index.js

import React, {Component} from 'react'
import selectn from 'selectn'
import Link from 'react-router'
import { connect } from 'react-redux'
import DocumentTitle from 'react-document-title'
import Loader from './../../../utilities/loader';

import { APP_ROUTES, _renderReviewSection, _getRequiredRoutes } from './../SIC/constants'
import { updateProfile } from './../../actions/Profile'
import { getStudentProfile, getProfileData } from '../../actions/Profile';

import './styles.scss'

class ReviewApp extends Component{
	
	constructor(props){
		super(props);
		this.state = {
			initialRoutes: _getRequiredRoutes(),
		}
	}

	componentDidMount(){
		let { _profile, dispatch } = this.props;
		if(!!this.props.adminView && this.props.studentID){
			console.log('here', this.props.studentID)
			dispatch(getStudentProfile(this.props.studentID));
			dispatch(getProfileData(this.props.studentID));
		}
		else if(Object.keys(this.props._profile).length < 10){
			this.props.dispatch(getStudentProfile());
			this.props.dispatch(getProfileData());
		}
		if ( _profile.page !== 'review' ) {
			dispatch( updateProfile({ page: 'review' }) );
		}
	}

	render(){
		let { _profile } = this.props,
			{ initialRoutes } = this.state,
			reqRoutes = _.get(_profile, 'req_app_routes'),
			reviewSections = initialRoutes;

		if( reqRoutes ) reviewSections = reqRoutes;
		return (
			<DocumentTitle title="Plexuss | College Application | Review">
				<div id="_appReviewer" className="review-app-container">

					{!!_profile.init_profile_pending && <Loader />}

					<div className="section bannr">
						<div>{ _profile.fname || '' } { _profile.lname || '' } Recruitment Application</div>
						{ _profile.app_last_updated && <div>Last Updated { _profile.app_last_updated }</div> }
					</div>

					{ reviewSections.map((r) => _renderReviewSection(r, {...this.props})) }

				</div>
			</DocumentTitle>
		)
	}
}

const mapStateToProps = (state, props) => {
	return {
		_user: state._user,
		_profile: state._profile,
		_superUser: state._superUser
	}
}

export default connect(mapStateToProps)(ReviewApp)
