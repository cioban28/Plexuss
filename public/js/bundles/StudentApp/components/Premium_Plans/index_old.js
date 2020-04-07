// /Premium_Plans/index.js

import React from 'react'
import selectn from 'selectn'
import { Link } from 'react-router'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs'
import DocumentTitle from 'react-document-title'

import { PLANS } from './constants'
import { addToCart } from './../../actions/Checkout'

import './styles.scss'

const Premium_Plans = React.createClass({
	render(){
		let { children, route, dispatch } = this.props;
		
		return (
			<DocumentTitle title="Plexuss | Premium Plans">
				<div id="_Premium_Plans">

					<div className="header-container">
						<div className="h1">Your Path to a World Class Education Begins Here</div>
					</div>

					<div className="plan-container">
						<div>
							{ PLANS.map((p) => <Plan key={p.name} plan={p} {...this.props} />) }
						</div>
					</div>

				</div>
			</DocumentTitle>
		);
	}
});

const Plan = React.createClass({
	render(){
		let { plan } = this.props;

		return (
			<div className="plan">

				<div className={"name "+plan.name}>{ plan.name }</div>
				<div className="price">
					<div className="amt">
						<div className="dollar">$</div>
						{ plan.price }
						<div className="wp">with Plexuss</div>
					</div>
				</div>

				{ plan.features.map((f) => <div key={ f.name } className="feature">
												<div><b>{ f.name }</b> { f.details }</div>
												{ f.savings && <div className="savings">{ f.savings }</div> }
											</div>) }

				<div className="text-center">
					<Link to={ plan.choose_route } className="choose"><span>Choose Plan</span></Link>
				</div>

				<div className="savings total">{ plan.total_savings }</div>
				
			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {};
};

export default connect(mapStateToProps)(Premium_Plans);