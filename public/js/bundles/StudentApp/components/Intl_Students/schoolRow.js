// /Intl_Student/schoolRow.js

import React from 'react'
import selectn from 'selectn'
import { Link, browserHistory } from 'react-router'
import Accounting from 'accounting-js'
import Tooltip from './../common/Tooltip'
import CustomModal from './../../../utilities/customModal'

import { updateProfile } from './../../actions/Profile'

import './../../utils/_countryFlags.scss'

export default React.createClass({
	getInitialState(){
		return {
			premiumModalOpen: false,
			signup_route: '/signup?utm_source=SEO&utm_term=topnav&utm_medium=international-students&redirect=international-students',
		};
	},

	componentWillMount(){
		this._utmCheck(); //checks url for utm params
	},

	_utmCheck(){
		var path = window.location.href;
		if( path.includes('?') ) this.state.signup_route = '/signup?'+path.split('?')[1]+'&redirect=international-students';
	},

	_format(val){
		if( val === 0 ) return 'N/A';

		let { _intl } = this.props,
			formatted = Accounting.formatMoney(val, {
				symbol: 'USD',
				format: '%v %s',
			});

		// only converting amount if value of new_conversion_rate is a valid value
		// will be null if user selects 'USD' from the dropdown
		if( selectn('new_conversion_rate.value', _intl) ){
			let rate = _intl.new_conversion_rate;

			formatted = Accounting.formatMoney((+val * +rate.value), {
				symbol: rate.name,
				format: '%v %s',
			});
		}

		return formatted;
	},

	_applyNow(){
		let { school, _user, _profile } = this.props,
			{ signup_route } = this.state;

		if( !_user.signed_in ) window.location.href = signup_route; // if not signed in
		else{
			// this._addToApplyList(); // add this school to applyTo_school array
			browserHistory.push('/college-application'); //window.open(school.app_url); // if premium member
		}
	},

	_addToApplyList(){
		let { dispatch, _profile, school } = this.props,
			newList = null;

		if( selectn('applyTo_schools.length', _profile) ){
			let found = _.find(_profile.applyTo_schools.slice(), {college_id: school.college_id});

			if( !found ) newList = [..._profile.applyTo_schools, school];
			else return;

		}else newList = [school];

		dispatch( updateProfile({applyTo_schools: newList}) );
	},

	_getMapImgName(){
		let { school } = this.props;
		let schoolState = school.state;

		if (schoolState) {
			if( schoolState.includes(' ') ) return schoolState.split(' ').join('_').toLowerCase();
			return schoolState.toLowerCase();
		}
		return schoolState;
	},

	render(){
		let { school } = this.props,
			{ premiumModalOpen } = this.state,
			epp = this._format( school.epp_column_cost || 0 ),
			undergrad = this._format( school.undergrad_column_cost || 0 ),
			grad = this._format( school.grad_column_cost || 0 ),
			mapName = this._getMapImgName(),
			_state = school.country_name === 'United States' ? school.state : school.country_name;

		return (
			<div className="school-item">

				<div className="rank col col-1">{ +school.rank || 'N/A' }</div>

				<div className="school-name col col-3">
					<div className="name">
						<div className="logo" style={{backgroundImage: 'url('+school.logo_url+')'}} />
						<a href={school.slug} target="_blank">{school.school_name}</a>
					</div>
				</div>

				<div className="st col col-1">
					{ school.country_code !== 'us' && <div className={"country_flag "+school.country_code} /> }
					<Tooltip
						customText={ _state }
						customClass="state">
							<div>Located in { school.city }, { _state }</div>
							<div className="text-center">
								<img src={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/map_of_'+mapName+'.gif'} alt={"US Map - "+_state} />
							</div>
					</Tooltip>
				</div>

				<div className="cost col col-2">
					{ epp === 'N/A' ? epp : <a href={school.slug+'/epp'}>{epp}</a> }
					{ (school.company_logo && school.quick_tip) &&
						<Tooltip customClass="quicktip">
							<img src={ school.company_logo } />
							<div className="qt">{ school.quick_tip }</div>
						</Tooltip> }
				</div>
				<div className="assist col col-1-5">{ undergrad === 'N/A' ? undergrad : <a href={school.slug+'/undergrad'}>{undergrad}</a> }</div>
				<div className="annual col col-1-5">{ grad === 'N/A' ? grad : <a href={school.slug+'/grad'}>{grad}</a> }</div>

				<div className="apply_now col col-2">
					<div onClick={ this._applyNow } className="apply-btn">Apply Now!</div>
				</div>

			</div>
		);
	}
});
