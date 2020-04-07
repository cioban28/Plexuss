// /College_Application/index.js

import React from 'react'
import selectn from 'selectn'
import { Link, Router } from 'react-router'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs'
import DocumentTitle from 'react-document-title'

import SIC from './../SIC'
import Footer from './Footer'
import AppActionBar from './../View_Student_Application/AppActionBar'

import { updateProfile, pageChanged, getCountries, getStates, getMajors, getAttendedSchools,  
	getLanguages, getCourseSubjects, getClassesBasedOnSubject, getReligions } from './../../actions/Profile'

import { getPrioritySchools } from './../../actions/Intl_Students'
import UploadsModal from './UploadsModal'
import InviteModal from './InviteModal'

import './styles.scss'

class College_Application extends React.Component{
	constructor(props) {
		super(props)
		this.state = {
			uploadsModalShown: false,
            inviteModalShown: false,     
		}
		this._hasInviteModalParam = this._hasInviteModalParam.bind(this)
	}

	_hasInviteModalParam() {
        const url = window.location.href;

        return url.includes('invitemodal=true');
    }

	componentWillMount(){
		let { dispatch, _profile } = this.props;

		if( !_profile.init_states_done ) dispatch( getStates() );
		if( !_profile.init_majors_done ) dispatch( getMajors() );
		if( !_profile.init_religion_done ) dispatch( getReligions() );
		if( !_profile.init_countries_done ) dispatch( getCountries() );
		if( !_profile.init_languages_done ) dispatch( getLanguages() );
		if( !_profile.init_schools_done ) dispatch( getAttendedSchools() );
		if( !_profile.init_course_subjects_done ) dispatch( getCourseSubjects() );
		if( !_profile.init_priority_schools_done ) dispatch( getPrioritySchools() );

		document.body.style.backgroundColor = '#555';		
	}

	componentWillUnmount(){
		document.body.style.background = '#fff';
	}

    componentDidMount(){
        
    }

	componentWillReceiveProps(np){
		let { dispatch, _profile } = this.props;
		// once course subjects are returned, make call to get classes for each one of the subjects
		if( _profile.init_course_subjects_done !== np._profile.init_course_subjects_done && np._profile.init_course_subjects_done ){
			let all_subj = np._profile.subjects_list.slice();
			_.each(all_subj, (sub) => dispatch( getClassesBasedOnSubject(sub.id) ) );
		}

		// Page has changed, will verify if any routes have been skipped.
		if( ( !np._profile.init_profile_pending && np._profile.init_priority_schools_done && np._profile.init_priority_schools_done !== _profile.init_priority_schools_done ) ||
			( np._profile.init_priority_schools_done && !np._profile.init_profile_pending && np._profile.init_profile_pending !== _profile.init_profile_pending ) ) {
			dispatch( pageChanged(np._profile.page) );
		}

		// Redirect to proper route if skipped
		if ( np._profile.skipped_page && ( _profile.skipped_page !== np._profile.skipped_page || _profile.page == 'submit' ) ) {
			dispatch( updateProfile({ skipped_page: null }) );
			this.context.router.push(np._profile.skipped_page);
		}

	}

	render(){
		let { _profile, children, route, dispatch, _user } = this.props,
            showInviteModalParam = this._hasInviteModalParam();

		return (
			<DocumentTitle title="Plexuss | College Application">
				<div id="_College_Application">
					{ (!_profile.init_priority_schools_done || _user.inviteContactsPending) && <div className="spin-container"><ReactSpinner color="#24b26b" /></div> }

					{/* if _user is imposter (admin temporarily signed in as student) then show appActionBar w/empty div to push content down */}
					{ _user.is_imposter && <AppActionBar /> }
					{ _user.is_imposter && <div className="spacer" /> }

					{ children }

					<SIC inApp={true} />

					<Footer />

                    {/* this.state.uploadsModalShown === false && !showInviteModalParam && !_profile.is_imposter && _profile.init_priority_schools_done &&
                        <UploadsModal 
                            closeMe={ () => this.setState({ uploadsModalShown: true }) } /> */}

                    { this.state.inviteModalShown === false && showInviteModalParam && !_profile.is_imposter && _profile.init_priority_schools_done && 
                        <InviteModal
                            closeMe={ () => this.setState({ inviteModalShown: true }) }/>   }

				</div>
			</DocumentTitle>
		);
	}
}

College_Application.contextTypes = {
  router: React.PropTypes.object.isRequired
};

const mapStateToProps = (state, props) => {
	return {
		_user: state._user,
		_profile: state._profile,
	};
};

export default connect(mapStateToProps)(College_Application);