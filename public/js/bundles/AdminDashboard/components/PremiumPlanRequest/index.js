// dashboard_container.js

import _ from 'lodash'
import React from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import ReactSpinner from 'react-spinjs-fix'
import PremiumPlan from './../Dashboard/components/premiumPlan'
import Tooltip from './../../../utilities/tooltip'
import { postInterestedPremiumServices } from './../../actions/dashboardActions'
import PremiumRequestSuccessModal from './../Dashboard/components/premiumRequestSuccessModal'

import {
    PREMIUM_PLANS,
    verifiedTooltip,
    verifiedTip
} from './../Dashboard/constants'

import './styles.scss'

class PremiumPlanRequest extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            showRequestSuccessModal: false,
        };
    }

    componentWillReceiveProps(newProps) {
        const { dash } = this.props,
              { dash: newDash } = newProps;

        if (dash.sendInterestedServicesPending != newDash.sendInterestedServicesPending && !newDash.sendInterestedServicesPending && newDash.sendInterestedServicesResponse == 'success') {
            this.setState({ showRequestSuccessModal: true });
        }
    }

    _postSingleInterestedService = (service) => {
        const { postInterestedPremiumServices } = this.props;

        postInterestedPremiumServices([service]);
    }

    render() {
        const { dash } = this.props,
            loading = dash.sendInterestedServicesPending;

        return (
            <div className='admin-premium-plan-request-container clearfix'>
                <div className='upgrade-to-premium-header'>
                    <span>Please Upgrade to Premium to access</span>
                    <Tooltip toolTipStyling={verifiedTooltip} tipStyling={verifiedTip}>
                        <div className="veri-arrow"></div>
                        <div className="veri-tooltip-title">Premium Plans</div>
                        Inquire about a plan by clicking Request Proposal below. We will then contact you with more information.
                    </Tooltip>
                </div>

                <div className='premium-plans-container'>
                    { PREMIUM_PLANS.map((plan, index) =>
                        <PremiumPlan
                            key={index}
                            label={plan.label}
                            description={plan.description}
                            _onRequest={() => this._postSingleInterestedService(plan.label)} />) }
                </div>

                { loading &&
                    <div className="spin-container"><ReactSpinner color="#24b26b" /></div> }

                { this.state.showRequestSuccessModal === true &&
                    <PremiumRequestSuccessModal
                        closeMe={() => this.setState({ showRequestSuccessModal: false })} /> }
            </div>
        );
    }
};

const mapStateToProps = (state, props) => {
    return {
        dash: state.dash,
    };
};

const mapDispatchToProps = (dispatch) => {
    return bindActionCreators({ postInterestedPremiumServices }, dispatch);
}

export default connect(mapStateToProps, mapDispatchToProps)(PremiumPlanRequest);
