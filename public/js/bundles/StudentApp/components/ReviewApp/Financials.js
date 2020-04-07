// /ReviewApp/Financials.js

import React from 'react'
import selectn from 'selectn'
import Link from 'react-router'

import SectionHeader from './SectionHeader'

class ReviewFinancials extends React.Component {
	constructor(props) {
		super(props)
	}
	render(){
		let { dispatch, _profile, _route, noEdit } = this.props;

		return (
			<div className="section">

				<div className="inner">
				
					<div className="arrow" />

					<SectionHeader route={_route} dispatch={dispatch} noEdit={noEdit} />

					<div className="notice"><small>All information is kept confidential and only shared with the Universities</small></div>

					{ _profile.financial_firstyr_affordibility &&
					<div className="item col">
						<div className="lbl">Financial Ability</div>
						<div className="val">{ _profile.financial_firstyr_affordibility }</div>
					</div>
					}

				</div>

			</div>
		);
	}
}

export default ReviewFinancials;