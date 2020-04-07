// /ReviewApp/GPA.js

import React from 'react'
import selectn from 'selectn'

import SectionHeader from './SectionHeader'

class ReviewGPA extends React.Component {
	constructor(props) {
		super(props)
	}

	render(){
		let { dispatch, _profile, _route, noEdit } = this.props;

		return (
			<div className="section">

				<SectionHeader route={_route} dispatch={dispatch} noEdit={noEdit} />

				<div className="_row">
					<div><b>Name</b></div>
					<div>{ _profile.fname || '' } { _profile.lname || '' }</div>
					<div><b>Birthday</b></div>
					<div>{ _profile.birth_date || '' }</div>
				</div>
				
			</div>
		);
	}
}

export default ReviewGPA;