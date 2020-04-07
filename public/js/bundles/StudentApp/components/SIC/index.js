// /SIC/index.js

import $ from 'jquery'
import React from 'react'
import selectn from 'selectn'
import { findIndex } from 'lodash'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs'
import { Link, browserHistory } from 'react-router'
import CircularProgressbar from 'react-circular-progressbar'

import YouCanBookMe from './../common/YouCanBookMe'

import { APP_ROUTES, OPTIONAL_ROUTE_IDS, _getRequiredRoutes } from './constants'
import { getStudentData } from './../../actions/User'
import { getProfileData, updateReqAppRoutes } from './../../actions/Profile'
import { getPrioritySchools } from './../../actions/Intl_Students'

import AppSection from './AppSection'

import './styles.scss'

var REQUIRED_ROUTES = _getRequiredRoutes();

const SIC = React.createClass({
	getInitialState(){
		return {
			scrollClass: '',
			barTop: null,
			completeOpen: false,
			incompleteOpen: false,
            optionalOpen: false,
			openScheduler: false,
		};
	},

	componentWillMount(){
		let { dispatch, _user: _u, _profile: _p } = this.props;
		
		document.addEventListener('scroll', this._scrollListener);

		if( !_u.init_done && !_u.init_student_data_pending && !_.isBoolean(_u.init_student_data_pending) && !_u.is_imposter ){
			dispatch( getStudentData() );
		}

		if( !_p.init_done && !_p.init_profile_pending && !_.isBoolean(_p.init_profile_pending) && !_u.is_imposter ){
			dispatch( getProfileData() );
		}
	},

	componentWillUnmount(){
		document.removeEventListener('scroll', this._scrollListener);
	},

	componentWillReceiveProps(np){
		let { dispatch, _profile: _p, _user: _u } = this.props,
			{ _profile: _np, _user: _nu } = np;

		// once profile init is done and we haven't already gotten priority schools and it's not currently pending
		if( (_p.init_done !== _np.init_done && _np.init_done) && 
			!_np.priority_schools_done && 
			(!_p.priority_schools_pending && !_np.priority_schools_pending) ){
				dispatch( getPrioritySchools( this._elsCheck() || '' ) );
		}

		// if user init done and user is signed out, fetch priority schools
		if( (_u.init_done !== _nu.init_done && _nu.init_done) && (!_nu.signed_in && parseInt(_nu.signed_in) === 0) ){
			dispatch( getPrioritySchools() );
		}

		// check for changes in applyTo_schools array
		if( !_np.get_priority_schools_err && (_p.init_priority_schools_done !== _np.init_priority_schools_done && _np.init_priority_schools_done) || 
			(_.get(_p, 'applyTo_schools.length', 0) !== _.get(_np, 'applyTo_schools.length', 0)) || 
			( _np.priority_schools_pending == false && _np.priority_schools_pending !== _p.priority_schools_pending ) ){
			
			REQUIRED_ROUTES = _getRequiredRoutes(np._profile.applyTo_schools);
			dispatch( updateReqAppRoutes(REQUIRED_ROUTES) ); // dispatch required routes to store b/c other pages need it

		}
	},	

	_elsCheck(){
		var path = window.location.href;

		if( path.includes('?aid') && path.includes('type') ) return '?'+path.split('?')[1];
		return false;
	},

	_scrollListener(e){
		// adds a class to the header to make it fixed to the top when scrolled past a certain point
		let { scrollClass, barTop } = this.state,
			classname = '',
			doc = e.srcElement.body.scrollTop,
			bar = $('#_SIC').offset().top;

		if( barTop === null ) this.state.barTop = bar;

		// only setting state to if scrollClass isn't already set
		if( barTop && doc > barTop ){
			// only setting state to if scrollClass isn't already set
			if( !scrollClass ) this.setState({scrollClass: 'scrolledToTop'});
		}else {
			// only setting scrollClass to empty if it is set
			if( scrollClass ) this.setState({scrollClass: ''});
		}
	},

	_filterSections(){
		let { _profile } = this.props,
			done = [], notDone = [], optional = [];

		// filter out the routes that are not meant to be a nav item of the SIC
		let navItems = _.filter(REQUIRED_ROUTES, (r) => !r.notNavItem);

		_.each(navItems, (r) => {
			let section = r.id;
            let isOptional = findIndex(OPTIONAL_ROUTE_IDS, (id) => r.id === id);

            if (isOptional !== -1) optional = [...optional, r];
			else if( _profile[section+'_form_done'] ) done = [...done, r];
			else notDone = [...notDone, r];
		});

		var continueFrom = '/college-application/';	

		// if there are any routes from notDone, grab the first one
		if( notDone.length > 0 ) continueFrom = notDone[0].path;

		return { done, notDone, optional, continueFrom };
	},

	_getReviewRoute(){
		let { _profile } = this.props;
		return _.find(APP_ROUTES, {id: 'review'});
	},

	_sendTo(){
		let { _user, _profile } = this.props,
			_routes = this._filterSections();

		if( !_user.completed_signup ) window.location.href = '/college-application';
		else browserHistory.push(_routes.continueFrom);
	},

	render(){
		let { dispatch, _user, _profile, toggle, inApp, disableRoutes } = this.props,
			{ scrollClass, completeOpen, incompleteOpen, optionalOpen, openScheduler } = this.state,
			_routes = this._filterSections(),
			_reviewRoute = this._getReviewRoute();

		return (
			<div id="_SIC" className={scrollClass}>
	
				<div className="close-sic-btn" title="close" onClick={ e => $('#_SIC').hide() }>&times;</div>

				{ !inApp && 
					<div className="prem-member">
						<div className="title">Premium Membership</div>

						<div className="membership">
							{ _user.premium_user_plan ? 
								<div className="plan">
									<div className={"badge "+(_.get(_user, 'premium_user_plan', ''))} />
								</div>
								:
								<div>Non Premium Plan</div> 
							}
						</div>
					</div> }

				{ !inApp && 
					<div className={"diff apps " + (_user.premium_user_type ? 'is_prem' : '')} onClick={ this._sendTo }>
						<div className="inner">
							Applications Remaining
							<div className="num">{ _user.num_of_eligible_applied_colleges || 1 }</div>
						</div>
					</div> }

				{ !inApp && 
					<div className="diff essays" onClick={ e => window.location.href="/news/catalog/college-essays"}>
						<div className="inner">
							Essay Views Remaining
							<div className="num">{ _user.num_of_eligible_premium_essays || 0 }</div>
						</div>
					</div> }

				{ !inApp && 
					<div className="diff schedule">
						<div className="schedule-btn" onClick={ e => this.setState({openScheduler: true}) }>Schedule Interview</div>

						<div id="_skyper">
							<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/skype_icon_sic.png" />
							<a id="_skypeCall" href="skype:live:premium_156?call">Call</a>
							<a id="_skypeChat" href="skype:live:premium_156?chat">Chat</a>
						</div>
					</div> }

				<div className="app-status">
					<div className="title">Application Status</div>

					<div className="profile">
						<div className="meter">
							<CircularProgressbar percentage={ _.get(_profile, 'profile_percent', 0)  } />
						</div>

						{ !inApp ? 
							<Link className="edit" to={_routes.continueFrom}>
								{ _routes.notDone.length > 0 ? 'Continue?' : 'Edit' }
							</Link> 
							: 
							<div className="edit">Application Completion</div> 
						}
					</div>

					{ _.get(_routes, 'done.length', 0) > 0 && 
						<div 
							className="section-name"
							onClick={ e => toggle && this.setState({completeOpen: !completeOpen}) }>
								Complete Sections { toggle && <span>{ completeOpen ? '-' : '+' }</span> }
						</div> }

					<AppSection 
						open={ !toggle || completeOpen } 
                        _profile={_profile}
						type="complete" 
						routes={ _routes.done } />

					{ _.get(_routes, 'notDone.length', 0) > 0 && 
						<div 
							className="section-name"
							onClick={ e => toggle && this.setState({incompleteOpen: !incompleteOpen}) }>
								Incomplete Sections { toggle && <span>{ incompleteOpen ? '-' : '+' }</span> }
						</div> }

					<AppSection 
						open={ !toggle || incompleteOpen } 
                        _profile={_profile}
                        type="incomplete" 
						routes={ _routes.notDone } />


                    { _.get(_routes, 'optional.length', 0) > 0 && 
                        <div 
                            className="section-name"
                            onClick={ e => toggle && this.setState({optionalOpen: !optionalOpen}) }>
                                Optional Sections { toggle && <span>{ optionalOpen ? '-' : '+' }</span> }
                        </div> }

                    <AppSection 
                        open={ !toggle || optionalOpen } 
                        _profile={_profile}
                        type="optional" 
                        routes={ _routes.optional } />

					{ _reviewRoute && <Link to={ _reviewRoute.path } className="review-route">{ _reviewRoute.name }</Link> }
					
				</div>
			
				{ !inApp && 
					<div className="submitted-apps">
						<div className="title">Submitted Applications</div>

						{ _.get(_profile, 'applyTo_schools.length', 0) > 0 && 
							_profile.applyTo_schools.map((s) => s.userHasApplied > 0 && <SubmittedSchools key={s.college_id} school={s} />) }	

						{ _.get(_profile, 'applyTo_schools.length', 0) === 0 && <div><small>No submissions yet</small></div> }
					</div> }

				{ openScheduler && <YouCanBookMe closeMe={ e => this.setState({openScheduler: false}) } /> }

				{ disableRoutes && <div className="delbasid" /> }

			</div>	
		);
	}
});

const SubmittedSchools = React.createClass({
	render(){
		let { school: s } = this.props;

		return (
			<div className="subm-school">
				<div className="logo" style={{backgroundImage: 'url('+s.logo_url+')'}} />
				{ s.school_name }
			</div>
		);
	}
});


const mapStateToProps = (state, props) => {
	return {
		_user: state._user,
		_profile: state._profile,
	};
};

export default connect(mapStateToProps)(SIC);