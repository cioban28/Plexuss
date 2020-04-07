// /Premium_Plans/index.js

import React from 'react'
import selectn from 'selectn'
import { Link } from 'react-router'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs'
import DocumentTitle from 'react-document-title'

import Tooltip from './../../../utilities/tooltip'
import LiveChatBtn from './../../../utilities/liveChatBtn/liveChatBtn' 

import { PLANS, CUSTOM , premTip} from './constants'
import { addToCart } from './../../actions/Checkout'

import './styles.scss'

const Premium_Plans = React.createClass({
	
	getInitialState(){
		return {
			plans : 'choose'
		};

	},
	_togglePlans(e){

		let that = $(e.target);

		$('.plan-toggle-btn').removeClass('active');

		if(that.is('.choose-btn')){
			this.setState({plans: 'choose'})
			$('.choose-btn').addClass('active');
		}
		else{
			this.setState({plans: 'custom'});
			$('.custom-btn').addClass('active');
		}
	},

	render(){
		let { children, route, dispatch } = this.props;

		return (
			<DocumentTitle title="Plexuss | Premium Plans">
				<div id="_Premium_Plans">
					<div className="clearfix background">
						<div className="header-container">
							<div className="h1">Plexuss Premium Membership</div>
							<div className="h3">Your International Common App</div>
							<div className="mt30">
								<span className="choose-btn plan-toggle-btn active" onClick={ this._togglePlans }>Choose a plan</span>
								<span className="custom-btn plan-toggle-btn" onClick={ this._togglePlans }>Customize your plan</span>
							</div>
						</div>


						{ this.state.plans && this.state.plans === 'choose' ?
							
							<div className="plan-container">
								<div className="left-image-foot"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/general/students-left.png" /></div>
								<div className="clearfix w66 text-center">
									<div>
										{ PLANS.map((p) => <Plan key={p.name} plan={p} num={ PLANS.length } {...this.props} />) }
									</div>
								</div>
								<div className="right-image-foot">
									<LiveChatBtn />
									<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/general/students-right.png" />
								</div>
							</div>

						:

							<div className="plan-container">
								<div className="left-image-foot"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/general/students-left.png" /></div>
								<div className="clearfix w66 text-center">
									<div>
									{ CUSTOM.map((p) => <Plan key={p.name} plan={p} num={ CUSTOM.length } {...this.props} />) }
									</div>
								</div>
								<div className="right-image-foot">
									<LiveChatBtn />
									<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/general/students-right.png" />
								</div>
							</div>
						}


					</div>
				</div>
			</DocumentTitle>
		);
	}
});





const Plan = React.createClass({
	render(){

		let { plan, num} = this.props;
		let price = '';
		let dollar = false;
		let per = false;

		let name = (plan.name + ' membership').toUpperCase();

		if(plan.name == 'Custom'){

			price =  plan.price + '/';
			dollar = true;
			per = true;
		}
		else if(plan.price != 'FREE'){
			price =  plan.price + '/yr';
			dollar = true;
		}else{
			price = plan.price;
		}

		return (
			<div className={"plan  c-"+ this.props.num}>
		
				<div className={"name "+plan.name}>{ name }</div>
				<div className="price">
					<div className={plan.price_sm ? "amt smaller" : "amt"}>
						{ dollar ? <div className="dollar">$</div> : null}
						{ price }
						{ per && <span className="fs25" >onetime</span> } 
					</div>
				</div>

				{ plan.features.map((f) => <div key={ f.name } className="feature">
												<span>{ f.name } { f.details }</span>
												{/* f.savings && <div className="savings">{ f.savings }</div> */ }
												{ f.tip && 
													<Tooltip tipStyling={premTip} >
														<div className="tipArrow"></div>
														{ f.tip }
													</Tooltip>
												}
											</div>) }


				
				<div className="rel-wrapper">
				{/* static/relative wrapper to keep bottom content below other content 
				-- inner content can be positioned absolute */}

					<div className="bottom-wrapper">
						<div className="text-center">
							{typeof plan.current !== 'undefined' && plan.current ?
								<div className="current-btn">Current plan</div>
							:
								<Link to={ plan.choose_route } className="choose"><span>Choose Plan</span></Link>
								
							}
						</div>

						<div className="savings total">{ plan.total_savings ||  <span>&mdash;&mdash;</span>}</div>
					</div>
				</div>
			
			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {};
};

export default connect(mapStateToProps)(Premium_Plans);