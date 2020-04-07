// /ReviewApp/Awards.js

import React from 'react'
import selectn from 'selectn'

import SectionHeader from './SectionHeader'
class ReviewAward extends React.Component {
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

					{ _.get(_profile, 'my_awards.length', 0) > 0 && 
						_profile.my_awards.map((a) => <AwardReview key={a.award_name+a.award_received_year} item={a} />) }

					{ _.get(_profile, 'my_awards.length', 0) === 0 && <div>No Honors/Awards added</div> }

				</div>

			</div>
		);
	}
}

class AwardReview extends React.Component{
	constructor(props) {
		super(props)
	}
	render(){
		let { item } = this.props;

		return (
			<div className="award">
				<div className="title">
					<div>{ item.award_name || 'N/A' }</div>
					<div>{ item.award_received_month || '' } { item.award_received_year || 'N/A' }</div>
				</div>
				<div>{ item.award_accord || '' }</div>
				<div className="descrip">{ item.award_notes }</div>
			</div>
		);
	}
}

export default ReviewAward;