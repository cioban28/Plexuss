// /College_Application/VerifyCode.js

import React from 'react'
import selectn from 'selectn'
import DocumentTitle from 'react-document-title'
import { Link, browserHistory } from 'react-router'
import { connect } from 'react-redux'

import TextField from './TextField'

import { V_CODE } from './constants'
import { updateProfile, saveApplication, resetSaved, sendConfirmationCode, verifyConfirmationCode } from './../../actions/Profile'
import { APP_ROUTES } from '../../../SocialApp/components/OneApp/constants';

class VerifyCode extends React.Component{
	constructor(props) {
		super(props)
		this._saveAndContinue = this._saveAndContinue.bind(this)
		this._skip = this._skip.bind(this)
		this._resend = this._resend.bind(this)
	}

	_saveAndContinue(e){
		e.preventDefault();

		let { dispatch, _profile } = this.props;
		dispatch( verifyConfirmationCode(_profile.verification_code) );
	}

	_resend(){
		let { dispatch, _profile } = this.props;
		dispatch( sendConfirmationCode({..._profile}) )
	}

	_skip(){
		let { dispatch, route } = this.props;
		dispatch( resetSaved() );
		this.props.location.pathname.includes("social") ? this.props.history.push('/social/one-app/'+route.next + window.location.search) : browserHistory.push('/college-application/'+route.next);
	}

	componentDidMount() {
		let { dispatch, _profile } = this.props;
		_profile.phone && dispatch(sendConfirmationCode({..._profile}))
	}

	componentWillReceiveProps(np){
		let { dispatch, _profile, route } = this.props;

		// after saving, reset saved and go to next route
		if( np._profile.verify_confirmation_code_success !== _profile.verify_confirmation_code_success && np._profile.verify_confirmation_code_success ){
			dispatch( resetSaved() );
			if(this.props.location.pathname.includes('social')) {
				let nextIncompleteStep = '';
				let formDonePropertyName = '';
				const routeIndex = APP_ROUTES.findIndex(r => r.id === route.id);
				if(routeIndex !== -1) {
					const nextStepsRoutes =  APP_ROUTES.slice(routeIndex+1);
					for(let nextRoute of nextStepsRoutes) {
						formDonePropertyName = `${nextRoute.id}_form_done`;
						if(_profile.hasOwnProperty(formDonePropertyName) && !_profile[formDonePropertyName]) {
							nextIncompleteStep = nextRoute;
							break;
						}
					}
				}
				const nextRoute = !!nextIncompleteStep ? nextIncompleteStep.id : route.next;
				this.props.history.push('/social/one-app/'+nextRoute + window.location.search)
			} else {
				browserHistory.push('/college-application/'+route.next + window.location.search)
			}
		}
	}
	render(){
		let { _profile, goBack } = this.props,
			_phone = selectn('alternate_phone', _profile) || selectn('phone', _profile),
			_code = selectn('alternate_phone_code', _profile) || selectn('phone_code', _profile);

		return(
				<div className="application-container">

					<form onSubmit={ this._saveAndContinue }>
						<div className="app-form-container">
							<div className="page-head">{"We’ve sent you an SMS code to"}</div>

							<div className="sent-to">
								+{ _code || '1' } { _phone }
								<span onClick={ goBack }>Change Number</span>
							</div>

							{ selectn('confirmation_sent_error', _profile) && <div className="error-msg">{_profile.confirmation_sent_err_msg}</div> }

							<div className="dir">To complete your phone number verification, please enter your 4 digit code below.</div>

							<div className="verify-code-container">
								<TextField field={ V_CODE } {...this.props} />
								<div className="resend" onClick={ this._resend }>Resend Code</div>
							</div>


							<div className="show-for-small-only">
								<button
									className="save"
									disabled={ false }>Save & Continue</button>

								<span className="skip" onClick={ this._skip }>Skip</span>
							</div>
						</div>
					</form>

				</div>)
	}
}

const mapStateToProps = (state) => {
	return {
		_profile: state._profile
	}
}

export default connect(mapStateToProps)(VerifyCode);
