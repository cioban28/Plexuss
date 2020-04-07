// /College_Application/Intermission

import React from 'react'
import { browserHistory } from 'react-router'

export default class Intermission extends React.Component{
	constructor(props) {
		super(props)
		this._saveAndContinue = this._saveAndContinue.bind(this)
	}
	_saveAndContinue(e){
		e.preventDefault();
		
		let { route, _profile } = this.props,
			required_route = _.find(_profile.req_app_routes.slice(), {id: route.id});

		
		if ( window.location.pathname.includes('social')  )
			{
				this.props.history.push('/social/one-app/'+required_route.next + window.location.search); 
		   }
		else
			{
				browserHistory.push('/college-application/'+required_route.next + window.location.search); 
			}	
	}

	render(){
		const { _openFacebookShare, _user, _profile } = this.props,
			num_of_eligible_colleges = _.get(_user, 'num_of_eligible_applied_colleges', 1),
			potential_num_of_applied_colleges = _profile.applyTo_schools ? _profile.applyTo_schools.length : 1,
			has_facebook_shared = _user.has_facebook_shared,
			premium_user_level_1 = _user.premium_user_level_1;

		return (
			<div className="application-container intermission-container">
				<form onSubmit={ this._saveAndContinue }>
					{ (has_facebook_shared || premium_user_level_1)
						?
						<div>
							<div className="page-head">You're Not Quite Done.</div>

							<div className="facet">The colleges you've selected require some more information, please proceed and continue the application process.</div>

							<br />

							<div>
								<button
									className="save"
									disabled={ false }>Continue</button>
							</div>
						</div>
						:
						<div className='facebook-share-container'>
							<div className="page-head">Click to share with friends and apply to an additional college for Free on Plexuss!</div>
							<img className='white-arrow-down' src="/images/application/white-arrow-down.png" />
							<div className='facebook-share-button' onClick={_openFacebookShare}>
								<img className='facebook-icon' src='/images/social/facebook_white.png' /><div className='facebook-share-text'>Share on Facebook!</div>
							</div>

							<div className='skip-button' onClick={ this._saveAndContinue }>Skip</div>
						</div> }
				</form>	

			</div>
		);
	}
}