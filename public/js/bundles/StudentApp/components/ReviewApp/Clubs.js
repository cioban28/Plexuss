// /ReviewApp/Basic.js

import React from 'react'
import selectn from 'selectn'

import SectionHeader from './SectionHeader'

class ReviewClub extends React.Component {
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

					{ _.get(_profile, 'my_clubs.length', 0) > 0 && 
						_profile.my_clubs.map((a) => <ClubReview key={a.club_id} item={a} />) }

					{ _.get(_profile, 'my_clubs.length', 0) === 0 && <div>No Organizations/Clubs added</div> }

				</div>

			</div>
		);
	}
}

class ClubReview extends React.Component{
	constructor(props) {
		super(props)
	}
	render(){
		let { item } = this.props;

		return (
			<div className="award">
				<div className="title">
					<div>{ item.club_name || 'N/A' }</div>
					<div>{ item.club_active_start_month || '' } { item.club_active_start_year || '' } - { item.club_active_end_month || '' } { item.club_active_end_year || '' }</div>
				</div>
				<div>{ item.club_role || '' }</div>
				<div className="descrip">{ item.club_notes }</div>
			</div>
		);
	}
}

export default ReviewClub;