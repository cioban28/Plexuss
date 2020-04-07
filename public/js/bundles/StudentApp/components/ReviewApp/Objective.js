// /ReviewApp/Objective.js

import React from 'react'

import SectionHeader from './SectionHeader'
import { DEGREES } from './../College_Application/constants'

class ReviewObjective extends React.Component {
	constructor(props) {
		super(props)
		this._getMajor = this._getMajor.bind(this)
	}

	_getMajor(){
		let { _profile } = this.props,
			majors = [];

		if( _profile.majors_list && _profile.majors ){
			let list = _profile.majors_list.slice();

			_.each(_profile.majors.slice(), (id, i, arr) => {
				let mjr = _.find(list, {id}),
					last = i+1 === arr.length ? 'or ' : '';

				if( mjr ) majors.push(last + mjr.name);
			});
		}

		return majors.join(', ');
	}

	render(){
		let { dispatch,  _profile, _route, noEdit } = this.props,
			major = this._getMajor();

		return (
			<div className="section">

				<div className="inner">
				
					<div className="arrow" />

					<SectionHeader route={_route} dispatch={dispatch} noEdit={noEdit} customName={'Objective'} />

					<div className="obj">
						{ _profile.degree_name && <div>I would like to get a/an <b>{ _profile.degree_name }</b> in <b>{ major || 'N/A' }</b></div> }
						{ _profile.planned_start_term && <div>I would like to begin college in <b>{ _profile.planned_start_term } { _profile.planned_start_yr || 'N/A' }</b></div>}
						{ _profile.career_name && <div>My dream would be to one day work as a(n) <b>{ _profile.career_name }</b></div> }
						{ _profile.campus_type && <div>I'm interested in colleges that offer education <b>{ _profile.campus_type }</b></div> }
					
					</div>

				</div>

			</div>
		);
	}
}

export default ReviewObjective;