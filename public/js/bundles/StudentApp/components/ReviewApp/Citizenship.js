// /ReviewApp/Citizenship.js

import React from 'react'
import selectn from 'selectn'

import SectionHeader from './SectionHeader'
import { CTZEN_STATUS } from './../College_Application/constants'

const _map = {
	'0': 'No',
	'1': 'Yes',
};

class ReviewCitizenShip extends React.Component {
	constructor(props) {
		super(props)
		this._getCountry = this._getCountry.bind(this)
		this._getLangs = this._getLangs.bind(this)
		this._getStatus = this._getStatus.bind(this)
	}

	_getLangs(){
		let { _profile } = this.props,
			languages = [];

		if( _profile.init_languages_done && _.get(_profile, 'languages.length', 0) ){
			let list = _profile.languages_list.slice(),
				lang = null;

			_.each(_profile.languages.slice(), (lan) => {
				lang = _.find(list, {id: lan});
				languages.push(lang.name);
			});

			return languages.join(', ');
		}

		return '';
	}

	_getStatus(){
		let { _profile } = this.props;
		let status = _.find(CTZEN_STATUS, {id: +_profile.citizenship_status});

		if( status ) return status.name;
		return 'N/A';
	}

	_getCountry(){
		let { _profile } = this.props;

		if( _profile.init_countries_done && _profile.country_of_birth ){
			let country = _.find(_profile.countries_list.slice(), {id: +_profile.country_of_birth});
			return country.name || 'N/A';
		}

		return 'N/A';
	}

	render(){
		let { dispatch, _profile, _route, noEdit } = this.props,
			langs = this._getLangs(),
			status = this._getStatus(),
			country = this._getCountry();

		return (
			<div className="section">

				<div className="inner">
				
					<div className="arrow" />

					<SectionHeader route={_route} dispatch={dispatch} noEdit={noEdit} />

					{ country != 'N/A' &&
					<div className="item col">
						<div className="lbl">Country of Birth</div>
						<div className="val">{ country }</div>
					</div>
					}

					{ _profile.city_of_birth &&
					<div className="item col">
						<div className="lbl">City of Birth</div>
						<div className="val">{ _profile.city_of_birth }</div>
					</div>
					}
					
					{ status != 'N/A' &&
					<div className="item col">
						<div className="lbl">Citizenship Status</div>
						<div className="val">{ status }</div>
					</div>
					}

					{ langs != 'N/A' &&
					<div className="item col">
						<div className="lbl">Language(s)</div>
						<div className="val">{ langs }</div>
					</div>
					}

				</div>

			</div>
		);
	}
}

export default ReviewCitizenShip;