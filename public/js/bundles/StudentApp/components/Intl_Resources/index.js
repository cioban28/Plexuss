// /Intl_Resources/index.js

import React from 'react'
import selectn from 'selectn'
import { Link } from 'react-router'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs'
import DocumentTitle from 'react-document-title'

import SIC from './../SIC'
import Banner from './../common/Banner'

import { getStudentData } from './../../actions/User'

import './styles.scss'

class Intl_Resources extends React.Component{
	constructor(props){
		super(props);
		this.state =  {
			scrollClass: '',
			signup_route: '/signup?utm_source=SEO&utm_term=topnav&utm_medium=international-resources&fromintl=true',
		}
	}

	componentWillMount(){
		let { dispatch, _user } = this.props;

		document.addEventListener('scroll', this._scrollListener);

		if( !_user.init_done ) dispatch( getStudentData() );

		this._utmCheck(); //checks url for utm params
	}

	componentWillUnmount(){
		document.removeEventListener('scroll', this._scrollListener);
	}

	_utmCheck(){
		var path = window.location.href;
		if( path.includes('?') ) this.state.signup_route = '/signup?'+path.split('?')[1]+'&fromintl=true';
	}

	_scrollListener(e){
		// adds a class to the header to make it fixed to the top when scrolled past a certain point
		let { scrollClass } = this.state,
			classname = '';

		// only setting state to if scrollClass isn't already set
		if( e.srcElement.body.scrollTop > 233 ){
			// only setting state to if scrollClass isn't already set
			if( !scrollClass ) this.setState({scrollClass: 'scrolledToTop'});
		}else {
			// only setting scrollClass to empty if it is set
			if( scrollClass ) this.setState({scrollClass: ''});
		}
	}


	_openRoutes(e){

		let drop = $('.routes-dropdown-list');


		if(drop.hasClass('opened')){
			drop.slideUp();
			drop.removeClass('opened');
		}
		else{
			drop.slideDown();
			drop.addClass('opened');

			$(document).one('click', function(e){

				if($(e.target).closest('.routes-dropdown').length)
					return;
				drop.slideUp();
				drop.removeClass('opened');

			})
		}



	}

	render(){
		let { children, route, dispatch, _user } = this.props,
			{ scrollClass, signup_route } = this.state,
			redirect = _user.signed_in ? '/premium-plans' : signup_route,
			is_prem = true;

		return (
			<DocumentTitle title="Plexuss | International Resources">
				<div id="_intl_resources_container" className='sic_on'>


					<div className="intl-res-cont">
						<Banner
							customClass="resources"
							bg="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/intl_students_bg.png">
								<div className="resources-banner">
									<h3 className="main-title">Here are some topics you might find helpful!</h3>
									<div className="topics">
										<Link to="/international-students">College Expense Breakdown</Link>
										<a href="/help">Help & FAQ Home</a>
									</div>
								</div>
						</Banner>

						<div className={"side-bar "+scrollClass}>

							<div className="med-routes-drop-btn">International Resources</div>
							<ul className="routes">
								<li><h6>International Student Resources</h6></li>
								{ route.childRoutes.map((rt) => <ResourcesTab key={rt.name} _route={rt} />) }
								<li><a className="tan-color" href="/premium-plans-info">Get Help with the Process</a></li>
							</ul>


							<div className="route-prem-cont">
									<div className="routes-dropdown-cont">
										<div className="topics-txt">Resource Topics</div>
										<ul className="routes-dropdown"  onClick={ (e) => { e.stopPropagation();  this._openRoutes() } }>
											<li><span  className="chosen-topic"> {route.childRoutes[0].name || ' '}</span>
												<span className="topic-arrow"></span>
												<ul className="routes-dropdown-list">
													{ route.childRoutes.map((rt) => <ResourcesTab key={rt.name} _route={rt} />) }
												</ul>
											</li>
										</ul>
									</div>

									<div className={"join-prem "+(is_prem ? 'hide' : '')}>
										<div>Sign up for Premium Services and apply to colleges for free!</div>
										<div><a href={redirect}>Join Premium!</a></div>
										<div><small>Limited to colleges in the Plexuss network</small></div>
									</div>

							</div>
						</div>

						<article className="resource-article">
							{ children }
						</article>

					</div>

					<SIC toggle={true} />

				</div>
			</DocumentTitle>
		);
	}
}

class ResourcesTab extends React.Component{

	_switchTopic(e){
		let el = $(e.target);
		let topic = el.find('.link').text();

		el.closest('.routes-dropdown').find('.chosen-topic').text(this.props._route.name);

		el.closest('.routes-dropdown-list').slideUp();
		el.closest('.routes-dropdown-list').removeClass('opened');
	}

	render(){
		let { _route } = this.props;

		return (

			<li onClick={ (e) => { e.stopPropagation(); this._switchTopic(e)} }>
				<Link to={ _route.path } className="link" activeClassName="active-tab">
					{ _route.name }
				</Link>
			</li>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		_user: state._user,
	};
};

export default connect(mapStateToProps)(Intl_Resources);
