// /College_Application/index.js

import React, {Component} from 'react'
import selectn from 'selectn'
import { Link, Router } from 'react-router'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs-fix'
import DocumentTitle from 'react-document-title'

import UploadsModal from './../../../StudentApp/components/College_Application/UploadsModal'
import InviteModal from './../../../StudentApp/components/College_Application/InviteModal'

import { getPrioritySchools } from './../../../StudentApp/actions/Intl_Students'
import { getStudentProfile, updateProfile, pageChanged, getCountries, getStates, getMajors, getAttendedSchools,
	getLanguages, getCourseSubjects, verifyConfirmationCode, getClassesBasedOnSubject, getReligions, getProfileData, saveApplication, resetSaved } from './../../../StudentApp/actions/Profile'

import { addInConversationArray } from './../../actions/messages'

import './styles.scss'
import './newStyles.scss'
import SaveButton from '../../../StudentApp/components/College_Application/SaveButton';
import BottomBar from '../../../StudentApp/components/College_Application/BottomBar';
import * as _ from 'lodash'
var PAGE_DONE = '';
class OneApp extends Component {
    constructor (props) {
    	super(props);

        this.state = {
					uploadsModalShown: false,
					inviteModalShown: false,
					submitClickedFor: '',
					firstHalfDone: false,
					showMouseEnterModal: false
		}
		this.openChat = this.openChat.bind(this);
    }

	componentWillMount(){
		let { dispatch, _profile, _user, route } = this.props;
		PAGE_DONE = route.id+'_form_done';
		dispatch(getStudentProfile());
		dispatch(getProfileData());
		if( !_profile.init_states_done ) dispatch( getStates() );
		if( !_profile.init_majors_done ) dispatch( getMajors() );
		if( !_profile.init_religion_done ) dispatch( getReligions() );
		if( !_profile.init_countries_done ) dispatch( getCountries() );
		if( !_profile.init_languages_done ) dispatch( getLanguages() );
		if( !_profile.init_schools_done ) dispatch( getAttendedSchools() );
		if( !_profile.init_course_subjects_done ) dispatch( getCourseSubjects() );
		if( !_profile.init_priority_schools_done ) dispatch( getPrioritySchools() );

		document.body.style.background = '#555';
	}

	componentDidMount() {
		document.body.style.background = '#555';
	}

	componentWillUnmount(){
		document.body.style.background = '#f2f4f6';
	}

	componentWillReceiveProps(np){
		let { dispatch, _profile, route } = this.props;
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

		// if( np._profile.save_success !== _profile.save_success && np._profile.save_success && route.id === 'contact' ){
		// 	dispatch( resetSaved() );
		// 	// if opting in, show verifyCode, else skip that and go to next route
		// 	if( _profile.txt_opt_in === 1 ) this.state.firstHalfDone = true;
		// 	else { this.props.location.pathname.includes("social") ? this.props.history.push('/social/one-app/'+route.next + window.location.search) : browserHistory.push('/college-application/'+route.next + window.location.search)};
		// 	// else browserHistory.push('/college-application/'+route.next + window.location.search);
		// }

	}

	buildSponsorFormData() {
		const { _profile } = this.props;

		let option = _profile.sponsor_will_pay_option,
				number_of_entries = _profile.sponsor_number_of_entries,
				raw_form = _.pickBy( _profile, (val, key) => _profile[key] &&
															 key.includes('sponsor_will_pay') &&
															!key.includes('valid') &&
															!key.includes('optin') &&
															!key.includes('option')
				),
				result_obj = {},
				index = 0,
				found = true;

			result_obj.number_of_entries = number_of_entries;
			result_obj.page = _profile.page;
			result_obj.option = option;
			result_obj[option] = [];

			while (found) {
				let tmpObj = {};

				found = false;

				_.forIn(raw_form, (val, key) => {
					if (key.endsWith('_' + index)) {
						let newKey = key.replace('sponsor_will_pay_', '').replace('_' + index, '');
						tmpObj[newKey] = val;
						found = true;
						delete raw_form[key];
					} else if (key.endsWith('_' + index + '_code')) {
						tmpObj['phone_code'] = val;
						found = true;
						delete raw_form[key];
					}
				});

				if (found) {
					result_obj[option].push(tmpObj);
				}

				index++;
			}

			if (_profile['impersonateAs_id']) {
				result_obj.impersonateAs_id = _profile['impersonateAs_id'];
			}

			result_obj.optin = _profile.sponsor_will_pay_optin;
			return result_obj;
	}

	_saveAndContinue = (e=undefined, callback=undefined) => {
		let {_profile, dispatch} = this.props
			let currentPage = _profile.oneApp_step;
			let form = {};
			if(this.props.route.id === 'sponsor') {
				form = this.buildSponsorFormData();
			} else {
					form = _.omitBy(_profile, (v, k) => !k.includes('listening') && k.includes('list'));
			}

			dispatch( saveApplication(form, this.props.route.id, currentPage) );
	}

    _hasInviteModalParam() {
        const url = window.location.href;

        return url.includes('invitemodal=true');
    }

	setFirstHalfDone = () => {
		this.setState({firstHalfDone: true})
	}

	unsetFirstHalfDone = () => {
		this.setState({firstHalfDone: false})
	}

	_skip = () => {
		let { dispatch, route } = this.props;
		dispatch( resetSaved() );
		this.props.location.pathname.includes("social") ? this.props.history.push('/social/one-app/'+route.next + window.location.search) : browserHistory.push('/college-application/'+route.next);
	}

	_verifySaveAndContinue = () => {
		let { dispatch, _profile } = this.props;
		dispatch( verifyConfirmationCode(_profile.verification_code) );
	}

	handleMouseEnterLeave = () => {
		this.setState((prevState) => ({
			showMouseEnterModal: !prevState.showMouseEnterModal
		}))
	}
	openChat(){
		let { dispatch, messageThreads } = this.props;
		dispatch(addInConversationArray(messageThreads[0]));
	}

	render(){
		let { _profile, children, route, dispatch, _user } = this.props,
			showInviteModalParam = this._hasInviteModalParam();
		let routesForSkip = ['review', 'uploads', 'essay', 'sponsor', 'verify']
		return (
			<DocumentTitle title="Plexuss | College Application">
				<div className="pd-bottom-16">
					<div id="_College_Application">
						{/* (!_profile.init_priority_schools_done || _user.inviteContactsPending) && <div className="spin-container"><ReactSpinner color="#24b26b" /></div> }

						{/* if _user is imposter (admin temporarily signed in as student) then show appActionBar w/empty div to push content down */}
						{/* _user.is_imposter && <AppActionBar /> }
						{ _user.is_imposter && <div className="spacer" /> */}

						{ React.cloneElement(children, {showMouseEnterModal: this.state.showMouseEnterModal,handleMouseEnterLeave: this.handleMouseEnterLeave ,submitClickedFor: this.state.submitClickedFor,firstHalfDone: route.id === 'contact' ? this.state.firstHalfDone : this.props.firstHalfDone, setFirstHalfDone: this.setFirstHalfDone, unsetSubmit: ()=>{this.setState({submitClickedFor: ''})}}) }

		                {/* this.state.uploadsModalShown === false && !showInviteModalParam && !_profile.is_imposter && _profile.init_priority_schools_done &&
		                    <UploadsModal
		                        closeMe={ () => this.setState({ uploadsModalShown: true }) } /> */}

		                { this.state.inviteModalShown === false && showInviteModalParam && !_profile.is_imposter && _profile.init_priority_schools_done &&
		                    <InviteModal
		                        closeMe={ () => this.setState({ inviteModalShown: true }) }/>   }

					</div>

					{	(route.id !== 'review' && route.id !== 'verify' && route.id !== 'uploads' && route.id !== 'applications') &&
						<div className="show-for-small-only save-btn-styling-for-mobile" style={{marginBottom: "0px"}}>
							<SaveButton
								_profile={_profile}
								page_done={PAGE_DONE}
								onClick={this._saveAndContinue}
							/>
						</div>
					}
					{	route.id !== 'review' &&
						<div id="common_bottom_bar" className="hide-for-small-only">
							<BottomBar _profile={_profile}
								myApplicationsLength={route.id === 'colleges' ? this.props._profile.MyApplicationList.length : 0 }
								PAGE_DONE={PAGE_DONE}
								routeId={route.id}
								onClick={route.id === 'verify' ? this._verifySaveAndContinue :  this._saveAndContinue}
								skip={routesForSkip.includes(route.id)}
								verifySkipHandler={this._skip}
								route={route.id}
								onClickModal={this.handleMouseEnterLeave}
								onMouseLeave={this.handleMouseEnterLeave}
								openChat={this.openChat}
							/>
						</div>
					}
				</div>
			</DocumentTitle>
		);
	}
}


const mapStateToProps = (state, props) => {
	return {
		_user: state.user.data,
		_profile: state._profile,
        messageThreads: state.messages && state.messages.messageThreads && state.messages.messageThreads.topicUsr,
	}
}
export default connect(mapStateToProps, null)(OneApp);
