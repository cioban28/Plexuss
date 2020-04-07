// /ReviewApp/Basic.js

import React from 'react'
import selectn from 'selectn'

import SectionHeader from './SectionHeader'

const EDU = {
	'0': 'High School',
	'1': 'College'
};

class ReviewBasic extends React.Component {
	constructor(props) {
		super(props)
		this._getEduLevel = this._getEduLevel.bind(this)
	}

	_getEduLevel(){
		let { _profile } = this.props;

		if( _profile.in_college >= 0 ) return EDU[_profile.in_college];
		return 'N/A';
	}

	render(){
		let { dispatch, _profile, _route, noEdit } = this.props,
			edu = this._getEduLevel();

		return (
			<div className="section">

				<div className="inner">

					<div className="arrow" />
					
					<SectionHeader route={_route} dispatch={dispatch} noEdit={noEdit} />

					<div className="item col">
						<div className="lbl">Name</div>
						<div className="val">{ _profile.fname || '' } { _profile.lname || '' }</div>
					</div>

					{ _profile.birth_date && 
					<div className="item col">
						<div className="lbl">Birthday</div>
						<div className="val">{ _profile.birth_date }</div>
					</div>
					}

					{ _profile.country_name &&
					<div className="item col">
						<div className="lbl">Country</div>
						<div className="val">{ _profile.country_name }</div>
					</div>
					}

					{ _profile.gender &&
					<div className="item col">
						<div className="lbl">Gender</div>
						<div className="val">{ _profile.gender }</div>
					</div>
					}

					{ edu != 'N/A' &&
					<div className="item fill">
						<div className="lbl">Education Level</div>
						<div className="val">{ edu }</div>
					</div>
					}

					{ _profile.schoolName &&
					<div className="item fill">
						<div className="lbl">School Name</div>
						<div className="val">{ _profile.schoolName }</div>
					</div>
					}

					{ _profile.grad_year &&
					<div className="item fill">
						<div className="lbl">Graduation Year</div>
						<div className="val">{ _profile.grad_year }</div>
					</div>
					}

					{ _profile.planned_start_term &&
					<div className="item fill">
						<div className="lbl">Intended Start Date</div>
						<div className="val">{ _profile.planned_start_term } { _profile.planned_start_yr || '' }</div>
					</div>
					}

				</div>
				
			</div>
		);
	}
}

export default ReviewBasic;