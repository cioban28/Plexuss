import React, { Component } from 'react'
import ReactDom from 'react-dom'
import { connect } from 'react-redux'
import Cards from './Cards'
import FilterByCountryButton from './FilterByCountryButton'
import CountryDropDown from './CountryDropDown'
import axios from 'axios'
import Collapsible from 'react-collapsible';
import {BrowserView, MobileView} from 'react-device-detect';
import '../styles.scss'
import { getUpcomingEvents, getNearestEvents, getPastEvents } from './../../../api/events'
import AnimateHeight from 'react-animate-height';
import { Helmet } from 'react-helmet';

class CollegeFairEvents extends Component {
	is_mount = false;
	constructor(props) {
		super(props);
		this.state = {
			tab: 0,
			country_selected: '',
			is_dropdown_active: false,
			countries: [],
			collapseOpened: 0
		}
		this.countryBtn
		this.countryList
		this.countryMobileBtn
		this.countryMobileList
	}

	componentDidMount() {
		this.is_mount = true;
		document.addEventListener('click', this.handleOutsideCountry, false);
    axios.get('/ajax/getCountryNames')
    	.then(res => {
				if(this.is_mount)
  	    	this.setState({ countries: res.data });
	    }).catch(error => {
    })

		getUpcomingEvents();
	}

	componentWillUnmount() {
		this.is_mount = false;
		document.removeEventListener('click', this.handleOutsideCountry, false);
	}

	handleOutsideCountry = (e) => {
		if (this.countryList) {
			if (this.countryBtn.contains(e.target) || this.countryList.contains(e.target)) {return;}
		}
		if (this.countryMobileList) {
			if (this.countryMobileBtn.contains(e.target) || this.countryMobileList.contains(e.target)) {return;}
		}
		if(this.state.is_dropdown_active === true){
			this.setState({is_dropdown_active: false})
		}
	}

	buttonClickHandler = (type) => {
		this.setState({collapseOpened: 0})

		switch(type) {
			case "upcoming":
				this.setState({ tab: 0 })
				getUpcomingEvents(this.state.country_selected)
				break;
			case 'nearest':
				this.setState({ tab: 1 })
				getNearestEvents()
				break;
			case 'past':
				this.setState({ tab: 2 })
				getPastEvents(this.state.country_selected)
				break;
		}
	}

	handleSelectCountry(country) {
		this.setState({ country_selected: country });
  		this.setState({ is_dropdown_active: false });

		if (this.state.tab == 0) {
			this.setState({ tab: 0 })
			getUpcomingEvents(country)
		} else if (this.state.tab == 1) {
			this.setState({ tab: 1 })
			getNearestEvents()
		} else if (this.state.tab == 2) {
			this.setState({ tab: 2 })
			getPastEvents(country)
    }
  }

  listClickHandler = () => {
    this.setState( prevState => ({ is_dropdown_active: !prevState.is_dropdown_active }) );
  }

  handleClearClick = () => {
    this.setState({ country_selected: '' });

    if (this.state.tab == 0) {
      getUpcomingEvents()
    } else if (this.state.tab == 1) {
      getNearestEvents()
    } else if (this.state.tab == 2) {
      getPastEvents()
    }
  }

	adjustHeight = () => {
		this.setState((prevState) => ({collapseOpened: prevState.collapseOpened === 0 ? 'auto' : 0}))
	}

  render() {
    return (
    	<div>
    		<Helmet>
    			<title>College Fairs | University Events | Plexuss</title>
       		<meta name="description" content="Looking to attend a college fair or a university admissions events? Visit Plexuss to discover and RSVP to a college event near you." />
    		</Helmet>
	      <div id="college-fair-events-main-div">
					<div className="content-wrapper">
						<div id="eventcontent" className="row collapse ranking-c-wrapper">
							<div className="column small-12 large-12" id="eventcontent_left">
								<div data-reactid=".0">
									<div className="event-container" data-reactid=".0.0">
												<div className="header_sec" data-reactid=".0.0.0.0.0">
													<h1 data-reactid=".0.0.0.0.0.0" style={{color: "black"}}>RSVP to a college fair or university event below!
													</h1>
												</div>
										<div className="banner_sec" data-reactid=".0.0.1">
											<img src="/images/college-fairs-and-university-events.jpg" alt="College Fairs and University Events" data-reactid=".0.0.1.0" style={{borderRadius: '10px 10px 1px 1px'}}/>
										</div>
										<div className="container" data-reactid=".0.0.2">
											<div className="top_sec" data-reactid=".0.0.2.0">
												<div className="college_fairs_media_mobile_div">
												<div className={`column small-12 side-bar-college-fairs show-for-small`} style={{backgroundColor: 'black', color: '#fff'}} onClick={this.adjustHeight}>Events <i className='fa fa-caret-down'></i></div>
													<AnimateHeight duration={500} height={this.state.collapseOpened}>
														<div className={`column small-12 side-bar-college-fairs show-for-small`} style={{backgroundColor: 'black'}}>

																<div ><a style={{color: 'white'}} className={`colapsible-item ${this.state.tab == 0 ? 'active' : ''}`} onClick={() => this.buttonClickHandler('upcoming')} >Upcoming Events</a></div>
																<div ><a style={{color: 'white'}} className={`colapsible-item ${this.state.tab == 1 ? 'active' : ''}`} onClick={() => this.buttonClickHandler('nearest')} >Events around me</a></div>
																<div ><a style={{color: 'white'}} className={`colapsible-item ${this.state.tab == 2 ? 'active' : ''}`} onClick={() => this.buttonClickHandler('past')} >Past Events</a></div>


														</div>
													</AnimateHeight>



													<div className="dropdown" data-reactid=".0.0.2.0.2">
														{
															this.state.tab != 1 &&
															<span ref={(ref) => {this.countryBtn = ref;}} className="" onClick={() => this.listClickHandler()} data-reactid=".0.0.2.0.2.0">
																		{
																			this.state.country_selected &&
																			<span>

																					<img className="flags-mini" src={`/images/flags-mini/${this.state.country_selected}.png`} data-reactid=".0.0.2.0.2.1.0.0"/>
																					<span data-reactid=".0.0.2.0.2.1.0.1">
																					&nbsp;&nbsp;
																					</span>
																					<span className='country-name'>{this.state.country_selected}</span>
																			</span>

																		}
																		{

																				!this.state.country_selected &&
																				<span className='country-name'>

																						Filter by country
																				</span>

																		}
																<span className="" data-reactid=".0.0.2.0.2.0.1">
																</span>
															</span>
														}

														{
															this.state.is_dropdown_active && this.state.countries && this.state.countries.length != 0 &&
															<div ref={(ref) => {this.countryList = ref;}} id="myDropdown" className="dropdown-content" data-reactid=".0.0.2.0.2.1" style={{display: 'block'}}>
																{
																	this.state.countries.map((country, index) => (
																		<a href='#' onClick={this.handleSelectCountry.bind(this, country.event_country)} data-reactid=".0.0.2.0.2.1.0" key={index}>
																			<img className="flags-mini" src={`/images/flags-mini/${country.event_country}.png`} data-reactid=".0.0.2.0.2.1.0.0"/>
																			<span data-reactid=".0.0.2.0.2.1.0.1">
																			</span>
																			<span data-reactid=".0.0.2.0.2.1.0.2">
																				&nbsp;&nbsp;{country.event_country}
																			</span>
																		</a>
																	))
																}
															</div>
														}
													</div>
												</div>
												<div className="college_fairs_media_browser_div">
													<ul className="nav nav-tabs" data-reactid=".0.0.2.0.0" style={{borderRadius: '1px 1px 10px 10px'}}>

														<li id="onlineEvents" data-reactid=".0.0.2.0.0.0" className={this.state.tab == 0 ? 'active' : ''} onClick={() => this.buttonClickHandler('upcoming')}>
															<a data-toggle="tab" href="#" data-reactid='.0.0.2.0.0.0.0'>
																Upcoming Events
															</a>
														</li>
														<li id="nearestEvents" data-reactid=".0.0.2.0.0.1" className={this.state.tab == 1 ? 'active' : ''} onClick={() => this.buttonClickHandler('nearest')}>
															<a data-toggle="tab" href="#" data-reactid='.0.0.2.0.0.1.0'>
																Events around me
															</a>
														</li>
														<li id="offlineEvents" data-reactid=".0.0.2.0.0.2" className={this.state.tab == 2 ? 'active' : ''} onClick={() => this.buttonClickHandler('past')}>
															<a data-toggle="tab" href="#" data-reactid='.0.0.2.0.0.2.0'>
																Past Events
															</a>
														</li>
														<li style={{float: 'right'}}>

																{
																	this.state.country_selected &&
																		<div className="clearfilter" >
																			<a href="#">
																				<span className="resetingFil showclearfilter" onClick={this.handleClearClick}>
																					Clear All  x
																				</span>
																			</a>
																		</div>
																}
															<div className="dropdown" data-reactid=".0.0.2.0.2">
																{
																	this.state.tab != 1 &&
																	<button ref={(ref) => {this.countryMobileBtn = ref;}} className="dropbtn dropcountry" onClick={() => this.listClickHandler()} data-reactid=".0.0.2.0.2.0">
																				{
																					this.state.country_selected &&
																					<span className="fill-country" data-reactid=".0.0.2.0.2.0.0">

																							<img className="flags-mini" src={`/images/flags-mini/${this.state.country_selected}.png`} data-reactid=".0.0.2.0.2.1.0.0"/>
																							<span data-reactid=".0.0.2.0.2.1.0.1">
																							</span>
																							<span>&nbsp;&nbsp;{this.state.country_selected}</span>
																					</span>

																				}
																				{

																						!this.state.country_selected &&
																						<span className="fill-country" data-reactid=".0.0.2.0.2.0.0">

																								Filter by country
																						</span>

																				}
																		<span className="navigation-arrow-down11" data-reactid=".0.0.2.0.2.0.1">
																		</span>
																	</button>
																}

																{
																	this.state.is_dropdown_active && this.state.countries && this.state.countries.length != 0 &&
																	<div ref={(ref) => {this.countryMobileList = ref;}} id="myDropdown" className="dropdown-content" data-reactid=".0.0.2.0.2.1" style={{display: 'block'}}>
																		{
																			this.state.countries.map((country, index) => (
																				<a href='#' onClick={this.handleSelectCountry.bind(this, country.event_country)} data-reactid=".0.0.2.0.2.1.0" key={index}>
																					<img className="flags-mini" src={`/images/flags-mini/${country.event_country}.png`} data-reactid=".0.0.2.0.2.1.0.0"/>
																					<span data-reactid=".0.0.2.0.2.1.0.1">
																					</span>
																					<span data-reactid=".0.0.2.0.2.1.0.2">
																						&nbsp;&nbsp;{country.event_country}
																					</span>
																				</a>
																			))
																		}
																	</div>
																}

															</div>


														</li>
													</ul>

												</div>

												<div className="tab-content" data-reactid=".0.0.2.0.3">
													<div className="my-center">
														<span>At these FREE QS MBA events you can meet with a variety of top business schools and get your all admissions questions answered</span>
													</div>
													{
														this.props.events && this.props.events.length != 0 &&
														<Cards events={this.props.events}/>
													}
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
    	</div>
    )
  }
}

function mapStateToProps(state) {
  return {
    events: state.events.events,
  }
}

export default connect(mapStateToProps, null)(CollegeFairEvents);
