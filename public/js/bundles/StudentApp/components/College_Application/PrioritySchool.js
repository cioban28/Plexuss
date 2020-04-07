// /College_Application/Submit.js

import React from 'react'
import selectn from 'selectn'
import Accounting from 'accounting-js'
import { isNil } from 'lodash'
import Tooltip from './../common/Tooltip'
import CustomModal from './../../../utilities/customModal'

import { updateProfile, changedFields, amplitudeAddCollege, amplitudeRemoveCollege, selectCollegeLearnMore } from './../../actions/Profile'

import './../../utils/_countryFlags.scss'

export default class PrioritySchool extends React.Component{
	constructor(props) {
		super(props)
		this.state = {
			show: false,
		}
		this._alreadyAdded = this._alreadyAdded.bind(this)
		this._apply = this._apply.bind(this)
		this._format = this._format.bind(this)
		this._getMapImgName = this._getMapImgName.bind(this)
		this._learnMoreClicked = this._learnMoreClicked.bind(this)
		this._reachedActualMaxApplyTo = this._reachedActualMaxApplyTo.bind(this)
		this._reachedMaxApplyTo = this._reachedMaxApplyTo.bind(this)
		this._dontApply = this._dontApply.bind(this)
	}

	_format(val){
		if( val === 0 ) return 'N/A';

		let { _profile } = this.props,
			formatted = Accounting.formatMoney(val, {
				symbol: 'USD',
				format: '%v %s',
			});

		// only converting amount if value of new_conversion_rate is a valid value
		// will be null if user selects 'USD' from the dropdown
		if( selectn('new_conversion_rate.value', _profile) ){
			let rate = _profile.new_conversion_rate;

			formatted = Accounting.formatMoney((+val * +rate.value), {
				symbol: rate.name,
				format: '%v %s',
			});
		}

		return formatted;
	}

	_apply(){
		let { dispatch, _profile, school } = this.props,
			newList = null;

		if( selectn('applyTo_schools.length', _profile) ){
			let found = _.find(_profile.applyTo_schools.slice(), {college_id: school.college_id});

			if( !found ) newList = [..._profile.applyTo_schools, school];
			else return;

		}else newList = [school];

		dispatch( updateProfile({applyTo_schools: newList}) );
		dispatch( changedFields("apply to college " + school.school_name + "(college id: " + school.college_id + ")"));
		// dispatch(amplitudeAddCollege(school));
	}

	_dontApply(){
		let { dispatch, _profile, school } = this.props;

		let newList = _.reject(_profile.applyTo_schools.slice(), school);

		dispatch( updateProfile({applyTo_schools: newList}) );
		dispatch( changedFields("remove college " + school.school_name + "(college id: " + school.college_id + ")"));
		// dispatch(amplitudeRemoveCollege(school));
	}

	_getMapImgName(){
		let { school } = this.props;
		let schoolState = school.state;

		if (schoolState) {
			if( schoolState.includes(' ') ) return schoolState.split(' ').join('_').toLowerCase();
			return schoolState.toLowerCase();
		}
		return schoolState;
	}

	_alreadyAdded(){
		let { _profile, school } = this.props,
			list = selectn('applyTo_schools', _profile),
			added = false;

		if( list ) added = _.find(list.slice(), school);

		return !!added;
	}

	_reachedMaxApplyTo(){
		let { _profile, school, _user } = this.props,
			max = _.get(_user, 'num_of_eligible_applied_colleges', 1);

		return _.get(_profile, 'applyTo_schools.length', 0) >= max;
	}

    _reachedActualMaxApplyTo(){
        const { _profile, _user } = this.props;
        let valid = false;

        if (_user.premium_user_level_1 && _user.country_id !== 1) {
            valid = _.get(_profile, 'applyTo_schools.length', 0) === 10;
        } else {
            valid = _.get(_profile, 'applyTo_schools.length', 0) >= 2;
        }

        return valid;
    }

    _learnMoreClicked() {
        const { school, dispatch, _profile } = this.props;
        const data = {};

        if (_.get(_profile, 'applyTo_schools.length', 0) !== 10) {
            this._apply();
        }

        // Requires ro_id and college_id
        if (isNil(school.ro_id) || isNil(school.college_id)) return;

        data['ro_id']      = school.ro_id;
        data['college_id'] = school.college_id;

        dispatch( selectCollegeLearnMore(data) );
        // window.open('https://www.elearners.com/a/Plexuss', '_blank');
    }

	render(){
		let { school, _skipToIntermission } = this.props,
			{ premiumModalOpen, show } = this.state,
			epp = this._format( school.epp_column_cost || 0 ),
			undergrad = this._format( school.undergrad_column_cost || 0 ),
			grad = this._format( school.grad_column_cost || 0 ),
			mapName = this._getMapImgName(),
			_state = school.country_name === 'United States' ? school.state : school.country_name,
			alreadyAdded = this._alreadyAdded(),
			at_max = this._reachedMaxApplyTo(),
			already_submitted = _.get(school, 'userHasApplied'),
            true_max = this._reachedActualMaxApplyTo(),
            has_learn_more = !isNil(school.ro_id);

            // if (has_learn_more) { console.log(school.school_name, school.ro_type, 'ro_id:' ,school.ro_id, 'college_id', school.college_id); }

		return (
			<div className="school-item">

				<div className="rank col col-1">{ +school.rank || 'N/A' }</div>

				<div className="school-name col col-3">
					<div className="name">
						<div className="logo" style={{backgroundImage: 'url('+school.logo_url+')'}} />
						<a href={school.slug} target="_blank">{school.school_name}</a>
						{ _.get(school, 'why_recommended.length') > 0 &&
							<div><div className="why-apply" onClick={ () => this.setState({show: !show}) }>Why should I apply? <div /></div></div> }
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

				<div className="assist col col-2">
					{ epp === 'N/A' ? epp : <a href={'/college/'+school.slug+'/epp'}>{epp}</a> }
					{ (school.company_logo && school.quick_tip) &&
						<Tooltip customClass="quicktip">
							<img src={ school.company_logo } />
							<div className="qt">{ school.quick_tip }</div>
						</Tooltip> }
				</div>
				<div className="assist col col-1-5">{ undergrad === 'N/A' ? undergrad : <a href={'/college/'+school.slug+'/undergrad'}>{undergrad}</a> }</div>
				<div className="annual col col-1-5">{ grad === 'N/A' ? grad : <a href={'/college/'+school.slug+'/grad'}>{grad}</a> }</div>

				<div className="apply_now col col-2">
					{ (!already_submitted && alreadyAdded) && <div onClick={ this._dontApply } className="already-added">Added</div> }
					{ !has_learn_more && (!already_submitted && !alreadyAdded && !at_max) && <div onClick={ this._apply } className="apply-btn">+</div> }
                    { (already_submitted || ((at_max && true_max) && !has_learn_more && !alreadyAdded)) && <div className="reached-max">&nbsp;</div> }
					{ !has_learn_more && (!already_submitted && ((at_max && !true_max) && !alreadyAdded)) && <div onClick={ _skipToIntermission } className="apply-btn">+</div> }
                    { has_learn_more && !alreadyAdded && !already_submitted && <div className='learn-more-link' onClick={ this._learnMoreClicked }>Learn More</div> }
				</div>

				{ show && <div className="apply-reason">
							<div><b>You are receiving this recommendation because</b></div>
							<div>
								{ _.get(school, 'why_recommended', []).map((t, i) => <ApplyReason key={t} reason={t} i={i+1} />) }
							</div>
						</div> }

			</div>
		);
	}
}

class ApplyReason extends React.Component{
	constructor(props) {
		super(props)
	}
	
	render(){
		let { reason, i } = this.props;
		return (
			<div><b>{ i }.</b> { reason }</div>
		);
	}
}
