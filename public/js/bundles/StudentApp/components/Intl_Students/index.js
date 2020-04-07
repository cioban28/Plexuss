// /Intl_Students/index.js

import React from 'react'
import selectn from 'selectn'
import { Link } from 'react-router'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs'
import DocumentTitle from 'react-document-title'

import SIC from './../SIC'
import SchoolRow from './schoolRow'
import Banner from './../common/Banner'
import SortBar from './../common/SortBar'
import FilterOption from './../common/SortBar/filterOption'
import ConversionOption from './../common/SortBar/conversionOption'

import { spinjs_config } from './../../utils/spinjs_config'
import { SORT_COLS, FILTER_BY_COST, FILTER_BY_DEGREE, FILTER_BY_MAJOR, EX_RATES } from './constants'

import { getStudentData } from './../../actions/User'
import { getPrioritySchools, getExchangeRates, getAllMajors, convertExRate,
		sortCols, filterCost, filterMajors, filterDegrees } from './../../actions/Intl_Students'

import './styles.scss'

var filter_by_major = {...FILTER_BY_MAJOR};
var ex_rates = {...EX_RATES};

const Intl_Students = React.createClass({
	getInitialState(){
		return {
			signup_route: '/signup?utm_source=SEO&utm_term=topnav&utm_medium=international-students&redirect=international-students',
		};
	},

	componentWillMount(){
		let { dispatch, _intl, _user } = this.props;

		if( !_user.init_done ) dispatch( getStudentData() );
		if( !_intl.init_majors_done ) dispatch( getAllMajors() );
		if( !_intl.init_ex_rates_done ) dispatch( getExchangeRates() );
		if( !_intl.init_done ) dispatch( getPrioritySchools( this._elsCheck() || '' ) );

		this._utmCheck(); // checks url for utm params
	},

	componentWillReceiveProps(np){
		let { _intl } = this.props;

		// once init majors is done, set 
		if( _intl.init_majors_done !== np._intl.init_majors_done && np._intl.init_majors_done ){
			filter_by_major.options = np._intl.all_majors;
		}

		// once rates api is returned, update ex_rates options with return data
		if( _intl.init_ex_rates_done !== np._intl.init_ex_rates_done && np._intl.init_ex_rates_done ){
			ex_rates.options = np._intl.ex_rates;
		}
	},

	_utmCheck(){
		var path = window.location.href;
		if( path.includes('?') ) this.state.signup_route = '/signup?'+path.split('?')[1]+'&redirect=international-students';
	},

	_elsCheck(){
		var path = window.location.href;

		if( path.includes('?aid') && path.includes('type') ) return '?'+path.split('?')[1];
		return false;
	},

	render(){
		let { children, route, dispatch, _intl, _user } = this.props,
			{ signup_route } = this.state,
			is_prem = true; // !!_.get(_user, 'premium_user_level_1');
		
		return (
			<DocumentTitle title="Plexuss | International Students">
				<div id="_intl_students_container" className={is_prem ? 'sic_on' : ''}>

					<Banner
						bg="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/intl_students_bg_light.png">
							<div className={"intl-banner "+(is_prem ? 'empty' : '')}>
								<div className={"join-box "+(is_prem ? 'hide' : '')}>
									<div className="head">Join Plexuss Premium!</div>
									<br />
									<p>Apply to Colleges for Free!</p>
									<p>Read Accepted Student Essays!</p>
									<p>Get Expert Support!</p>
									{ 
										_user.signed_in ? 
										<Link to={'/college-application'} className="learn-btn">Learn More!</Link> 
										: <a href={signup_route} className="learn-btn">Learn More!</a> 
									}
								</div>
							</div>
					</Banner>	

					<SortBar
						storeObj={ _intl }
						sortAction={ sortCols }
						list={ selectn('list', _intl) }
						columns={ SORT_COLS }>

							<FilterOption 
								filter={ FILTER_BY_DEGREE } 
								filterAction={ filterDegrees } 
								applied={ selectn('filters_applied.degree', _intl) }
								{...this.props} />

							<FilterOption 
								filter={ filter_by_major } 
								filterAction={ filterMajors } 
								applied={ selectn('filters_applied.majors', _intl) } 
								{...this.props} />

							<FilterOption 
								filter={ FILTER_BY_COST } 
								filterAction={ filterCost } 
								applied={ selectn('filters_applied.cost', _intl) }
								{...this.props} />

							<ConversionOption 
								conversion={ ex_rates }
								current_conversion_obj={ selectn('new_conversion_rate', _intl) || {} }
								convertAction={ convertExRate } 
								convertables={ selectn('ex_rates', _intl) } 
								{...this.props} />

					</SortBar>

					<div className="school-expense-list">
						{ _intl.init_pending && <div className="spin-container"><ReactSpinner color="#24b26b" /></div> }

						{ (!_.get(_intl, 'list.length') && _.isArray(_intl.list)) && <NoResults /> }
						{ _.get(_intl, 'list.length', 0) > 0 && _intl.list.map((s) => <SchoolRow key={s.college_id} school={s} {...this.props} />) }
					</div>

					<SIC toggle={true} />

				</div>
			</DocumentTitle>
		);
	}
});

const NoResults = React.createClass({
	render(){
		return (
			<div className="no-results">
				No Results
			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		_user: state._user,
		_intl: state._intl,
		_profile: state._profile,
	};
};

export default connect(mapStateToProps)(Intl_Students);