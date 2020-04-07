
import React from 'react'

import PremiumPlan from './premiumPlan'
import PremiumFeature from './premiumFeature'
import RecruitBlock from './recruitBlock'
import VerifiedBlock from './verifiedBlock'
import Tooltip from './../../../../utilities/tooltip'
import PicButton from './../../Base/PicButton/picButton'
import ImportantMsgModal from './importantMsgModal'
import PremiumRequestSuccessModal from './premiumRequestSuccessModal'
import createReactClass from 'create-react-class';

import { toggleInterestedPremiumService, postInterestedPremiumServices } from './../../../actions/dashboardActions'

import {
    PREMIUM_PLANS,
    PREMIUM_FEATURES,
    FREE_SERVICES,
	verifiedTooltip,
	veriInnerTooltip,
	verifiedTip,
	verifiedInnerTip,
	greenTooltip,
	downloadContainer } from './../constants'
/***********************************************************************
************************************************************************
*	main body of Dashboard
*   created mostly for organization not as a reusable component
*	but potentially replaceable component -- lets say we only want to change main body, not topnav/banner one day
***********************************************************************/

export default createReactClass({

	getInitialState(){
		return{
			openImportantMsgModal: false,
            importantMsgSent: false,
            showPremiumRequestSuccessModal: false,
		}
	},

    componentWillReceiveProps(newProps) {
        const { sendInterestedServicesPending } = this.props,
              { sendInterestedServicesPending: newSendInterestedServicesPending,
                sendInterestedServicesResponse: newSendInterestedServicesResponse } = newProps;

        if (sendInterestedServicesPending != newSendInterestedServicesPending && !newSendInterestedServicesPending && newSendInterestedServicesResponse == 'success') {
            this.setState({ showPremiumRequestSuccessModal: true });
        }
    },

    _onSelectedPremiumService(feature) {
        const { dispatch } = this.props;

        mixpanel.track('admin-choose-feature-interest', { Feature: feature.toLowerCase() }) ;
        dispatch( toggleInterestedPremiumService(feature) );
    },

    _postInterestedServices(){
        const { dispatch, interestedPremiumServices } = this.props,
            services = interestedPremiumServices;

        mixpanel.track('admin-submit-feature-interest', {
            'Targeting': services.indexOf('Targeting') !== -1 ? 1 : 0,
            'Daily Recommendations': services.indexOf('Daily Recommendations') !== -1 ? 1 : 0,
            'Advanced Search': services.indexOf('Advanced Search') !== -1 ? 1 : 0,
            'Texting': services.indexOf('Texting') !== -1 ? 1 : 0,
            'Chat Bot': services.indexOf('Chat Bot') !== -1 ? 1 : 0,
        });

        dispatch( postInterestedPremiumServices(interestedPremiumServices) );
    },

    _postSingleInterestedService(service){
        const { dispatch } = this.props;

        dispatch( postInterestedPremiumServices([service]) );
    },

    _sendImportantMessage(e){
        e.preventDefault();

        var _this = this;
        var data = {msg: $('#important_msg_body').val()};

        $('.important-message-notices').html('Sending...');

        $.ajax({
            url:  '/admin/ajax/sendAdminUrgentMatterMsg',
            type: 'POST',
            data: data,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).done(function(response){
            _this.setState({importantMsgSent: true});
        });
    },

	render(){
		let { verifiedBlocks, recruitmentBlocks, communicationBlocks, currentPortal, interestedPremiumServices, is_admin_premium } = this.props,
            { openImportantMsgModal, importantMsgSent } = this.state,
            interestedServicesLength = interestedPremiumServices ? interestedPremiumServices.length : null;

		return (
			<div>
				{/* recruitment title and Advanced Search, Export, Targeting buttons */}
				<div className="row dash-centered-row morepadding clearfix">

					{/* top button on right side of Recruitment header */}
					<div className="column large-12 medium-12 small-12 clearfix">
						<div className=" top-btn-container">
                            { is_admin_premium === 1 &&
    							<PicButton
    								colorProf="white-btn"
    								btnSizing="button-inner"
    								iconImg="adv-search"
    								btnText="ADVANCED SEARCH"
    								link="/admin/studentsearch"
    								funcid="dash_adv_search" /> }

							{/* is_admin_premium === 1 &&
                                <PicButton
    								colorProf="white-btn"
    								btnSizing="button-inner"
    								iconImg="export-icon"
    								btnText="EXPORT STUDENTS"
    								revealid="exp-student-modal"
    								funcid="dash_export_stu" /> */}

							{ currentPortal !== 'General' && <PicButton
																	colorProf="white-btn"
																	btnSizing="button-inner"
																	iconImg="target-icon"
																	btnText="TARGETING"
																	link="/admin/filter"
																	funcid="dash_targeting" />  }
						</div>
					</div>

				</div>

                {/* is_admin_premium ? */}

                    {/* <div>
        				{/* Verified Title =}
        				<div className="row dash-centered-row noMarginTop">
        					<span className="verified-check">&#10003;</span>
        					<span className="title3 ">Verified</span>
        					<Tooltip toolTipStyling={verifiedTooltip} tipStyling={verifiedTip}>
        						<div className="veri-arrow"></div>
        						<div className="veri-tooltip-title">Verified</div>
        						These are students who have had their information verified by Plexuss.
        					</Tooltip>
        				</div>

        				{/* Verified Boxes =}
        				<div className="row dash-centered-row veri-row">
        					{ verifiedBlocks.map(v => <VerifiedBlock key={ v.name } block={v} />) }
        				</div>


        				{/* Recruitment Title =}
        				<div className="row dash-centered-row">
        					<span className="column large-12 small-12 title3 ">Recruitment</span>
        				</div>

        		 		{/* recruitment options boxes =}
        				<div className="row dash-centered-row clearfix">
        					{ recruitmentBlocks.map(r => <RecruitBlock key={r.name} block={r} />) }
        				</div>


        				{/* Communication title =}
        				<div className="row dash-centered-row">
        					<span className="column large-12 small-12 title3 ">Communication</span>
        				</div>

        				{/* communication options boxes =}
        				<div className="row dash-centered-row clearfix">
        					{ communicationBlocks.map(c => <RecruitBlock key={c.name} block={c} />) }
        				</div>

                    </div> */}

                    <div>
                        {/* Free Services Title */}
                        <div className="row dash-centered-row">
                            <span className="premium-services-title title3">Free Services</span>
                            <Tooltip toolTipStyling={verifiedTooltip} tipStyling={verifiedTip}>
                                <div className="veri-arrow"></div>
                                <div className="veri-tooltip-title">Free Services</div>
                                These are services offered for free.
                            </Tooltip>
                        </div>

                        {/* recruitment options boxes */}
                        <div className="row dash-centered-row clearfix">
                            { FREE_SERVICES.map(r => <RecruitBlock key={r.name} isFreeService={true} block={r} />) }
                            <div className='free-service-tool-links'>
                                {/* Tools title */}
                                <div className="row dash-centered-row morepadding">
                                    <span className=" column large-12 small-12 title3">Tools</span>
                                </div>

                                {/* Tools links below */}
                                <div id="tools" className="row dash-centered-row">
                                    <div className="column">

                                        <PicButton
                                            colorProf="text-btn"
                                            btnSizing="tool-btn"
                                            iconImg="content-mng"
                                            btnText="Content Management"
                                            is_router="/admin/tools"
                                            link="/admin/content" />

                                        <br/>

                                        <PicButton
                                            onPressFunc={ () => mixpanel.track('admin-crm-integration-doc', {}) }
                                            colorProf="text-btn"
                                            btnSizing="tool-btn"
                                            iconImg="crm-integration"
                                            btnText="CRM Integration"
                                            link="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/CRM_Integration/Plexuss+CRM+Integration.zip"
                                            funcid="dash_adv_search" />

                                        <br/>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                {/* Premium Features title */}
                <div className="row dash-centered-row">
                    <span className="premium-features-title title3">Premium Features</span>
                    <Tooltip toolTipStyling={verifiedTooltip} tipStyling={verifiedTip}>
                        <div className="veri-arrow"></div>
                        <div className="veri-tooltip-title">Premium Features</div>
                        Clicking the grey circle on the left side of an item indicates you are interested in this service.<br /><br />You may select multiple services. Once an item is selected you will see a green button to submit.
                    </Tooltip>
                    <span className="column large-12 small-12 premium-features-desc-txt">Select features you would like to add to your services (you may select more than one)</span>
                </div>

                {/* Premium Features Container */}
                <div className="dash-centered-row premium-features-container">
                    { PREMIUM_FEATURES.map((feature, index) =>
                        <PremiumFeature
                            key={index}
                            videoLink={feature.videoLink}
                            name={feature.name}
                            label={feature.label}
                            pricing={feature.pricing}
                            isSelected={interestedPremiumServices && interestedPremiumServices.indexOf(feature.label) !== -1}
                            description={feature.description}
                            _onSelect={() => this._onSelectedPremiumService(feature.label)} />) }
                </div>

                <div className='dash-centered-row premium-features-button-container'>
                    { interestedServicesLength > 0 &&
                        <div onClick={this._postInterestedServices} className='premium-features-submit-button'>
                            I'm interested in { interestedServicesLength > 1 ? ('these ' + interestedServicesLength) : 'this' } service{ interestedServicesLength > 1 ? 's' : '' }
                        </div> }
                </div>

                {/* Commented out PremiumPlans
                    <div className="row dash-centered-row">
                        <span className="column large-12 small-12 title3 ">Premium Plans</span>
                    </div>

                    <div className='dash-centered-row premium-plans-container'>
                        { PREMIUM_PLANS.map((plan, index) =>
                            <PremiumPlan
                                key={index}
                                label={plan.label}
                                description={plan.description}
                                _onRequest={() => this._postSingleInterestedService(plan.label)} />) }
                    </div>
                */}

                { is_admin_premium === 1 &&
                    <div>
        				{/* Tools title */}
        				<div className="row dash-centered-row morepadding">
        					<span className=" column large-12 small-12 title3">Tools</span>
        				</div>

        				{/* Tools links below */}
        				<div id="tools" className="row dash-centered-row">
        					<div className="column">

        						<PicButton
        							colorProf="text-btn"
        							btnSizing="tool-btn"
        							iconImg="content-mng"
        							btnText="Content Management"
        							is_router="/admin/tools"
        							link="/admin/content" />

        						<br/>

        						<PicButton
        							colorProf="text-btn"
        							btnSizing="tool-btn"
        							iconImg="adv-search-b"
        							btnText="Advanced Search"
        							link="/admin/studentsearch"
        							funcid="dash_adv_search" />

        						<br/>

        					</div>
        				</div>
                    </div>
                }

				<div className="morepadding" />

				<div className="dash-centered-row error-or-suggestions-message">
					If you find an error or have suggestions, please <span className='contact-us-button' onClick={ e => this.setState({ openImportantMsgModal: true })}>contact us</span>.
				</div>

				<div className="morepadding" />

                { this.state.showPremiumRequestSuccessModal === true &&
                    <PremiumRequestSuccessModal
                        closeMe={() => this.setState({ showPremiumRequestSuccessModal: false })} /> }

                {/* message box for important messages */}
                { openImportantMsgModal && <ImportantMsgModal
                            msgSent={ importantMsgSent }
                            sendMsg={ this._sendImportantMessage }
                            close={ e => this.setState({ openImportantMsgModal : false }) } /> }

			</div>
		);
	}
});
