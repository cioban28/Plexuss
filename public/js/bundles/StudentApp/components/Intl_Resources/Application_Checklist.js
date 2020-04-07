// /Intl_Resources/Application_Checklist.js

import React from 'react'
import {connect} from 'react-redux';
import ListGenerator from './ListGenerator'
import ResourceHeader from './ResourceHeader'

import { RESOURCES, SUBMITTED_BY_YOU, SUBMITTED_BY_PROF, CHECKLIST_DETAILS } from './constants'
import UpgradeModal from './../upgradeToPremiumModal/upgradeToPremiumModal'

const HEADER = _.find(RESOURCES, {icon: 'list'});

class Application_checklist extends React.Component {
	constructor(props){
		super(props);
		this.state ={
			showUpgrade: false
		}
	}

	_openUpgrade(){
		// this.setState({showUpgrade: true});
		// already exist on page in topnav...
		var modal = $('._upgradePremiumModal');
		$(window).scrollTop(0);
		
		if( !modal.is(':visible') ){
			// modal.show();
			modal.fadeIn(200);
		}
		else{	
			modal.fadeOut(200);
			// modal.hide();
		}
	}

	_openSignup(){

		var modalBack = $('.signupModal-back');
		var modal = $('.signupModal-wrapper');
		$(window).scrollTop(0);
		
		if( !modal.is(':visible') ){
			// modal.show();
			modalBack.fadeIn(100);
			modal.fadeIn(200);
		}
		else{	
			modalBack.fadeOut(100);
			modal.fadeOut(200);
			// modal.hide();
		}
	}

	_closeUpgrade(){
		// this.setState({showUpgrade: false});
	}

	_upgradeOrVisit(){
		var {_user} = this.props;
		// if premium
		if(_user.premium_user_plan != null)
			window.location.href  = "/college-application";
		else if(_user.signed_in  == 1)
			this._openUpgrade();
		else
			//show sign up modal
			this._openSignup();
	}

	render(){
		let {showUpgrade} = this.state;

		return (
			<section>

				<ResourceHeader header={HEADER} />

				{ CHECKLIST_DETAILS.map((d) => <div className="content details" key={d}>{d}</div>) }

				<h5 className="title">Submitted by you</h5>
				{ SUBMITTED_BY_YOU.map((y) => <ListGenerator key={y.title} chklist={y} openUpgrade={this._openUpgrade}  upgradeOrVisit={this._upgradeOrVisit}/>) }

				<h5 className="title">Submitted by your guidance counselor or teachers</h5>
				{ SUBMITTED_BY_PROF.map((p) => <ListGenerator key={p.title} chklist={p}  openUpgrade={this._openUpgrade} upgradeOrVisit={this._upgradeOrVisit}/>) }

				<h5 className="title">After You Have Been Admitted</h5>

				<div className="content details">Before you can receive your F-1 student visa to study in the United States, you will need to provide proof of financial capability for your tuition. You will not be issued an I-20 form from the university until you are able to provide this information. Each university will have their own method for submitting this financial verification.</div>

				<div className="content details"><b>*Important to note:</b> Although many components of your application are submitted by others, it is still your responsibility to make absolute certain that every element of your application is sent to the universities you apply to and arrives on time. That means you should always check with your teachers and counselors to ensure that they are aware of what components they are sending, which colleges they are sending them to, and when they need to send them by. You will also be able to check the online portal of the colleges you are applying to in order to make sure they have received your materials.</div>


				
			</section>
		);
	}
}


const mapStateToProps = (state, props) => {
	return {
		_user: state._user,
	};
};

export default connect(mapStateToProps)(Application_checklist);