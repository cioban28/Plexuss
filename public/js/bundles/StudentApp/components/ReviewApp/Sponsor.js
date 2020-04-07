// /ReviewApp/Sponsor.js

import React from 'react'
import selectn from 'selectn'
import Link from 'react-router'
import SectionHeader from './SectionHeader'
import { SPONSOR_SELECT } from '../College_Application/questionConstants'

export default class Sponsor extends React.Component {
	_buildSponsorData() {
		let { _profile } = this.props,
			number_of_entries = _profile.sponsor_number_of_entries,
			option = _profile.sponsor_will_pay_option,
			field = SPONSOR_SELECT[0];

		if (!option || !number_of_entries) { return null; }

		const dependents = field['dependents_' + option],
			  render = [];

		for (let entry_index = 0; entry_index < number_of_entries; entry_index++) {
			render.push(
				<table key={entry_index} className='sponsor-table'>
					<tbody>
						{ dependents.map((dependent) => this._buildDependentField(dependent, entry_index)) }
					</tbody>
				</table>
			);
		}

		return render;
	}

	_buildDependentField(dependent, entry_index) {
		let { _profile } = this.props,
			value = _profile[dependent.name + '_' + entry_index],
			phone_code = null;

		if (dependent.name.includes('phone')) {
			phone_code = _profile[dependent.name + '_' + entry_index + '_code'];
			value = '+' + phone_code + ' ' + value;
		}

		return (
			<tr key={ dependent.name }>
				<th>{ dependent.label }</th>
				<th>{ value }</th>
			</tr>
		)
	}

	render() {
		let { dispatch, _profile, _route, noEdit } = this.props,
			number_of_entries = _profile.sponsor_number_of_entries;

		return (
			<div className="section">
				<div className="inner">
					<div className="arrow" />
					<SectionHeader customName='Sponsors' route={_route} dispatch={dispatch} noEdit={noEdit} />
					{ _profile.financial_firstyr_affordibility &&
						<div className="notice">
							<small>Financial Ability: { _profile.financial_firstyr_affordibility }</small>
						</div> }
					
					<br />
					
					{ number_of_entries > 0 && 
						<div className="notice">The applicant has indicated that the following sponsor(s) will provide financial assistance.</div> }
					
					{ number_of_entries == 0 &&
						<div className="notice">No sponsor information has been provided.</div> }

					<div className="review-sponsor-data">
						{ this._buildSponsorData() }
					</div>
				</div>
			</div>
		);
	}

}