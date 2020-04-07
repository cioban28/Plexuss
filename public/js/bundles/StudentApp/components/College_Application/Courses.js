// /College_Application/Courses.js

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import { browserHistory } from 'react-router'
import DocumentTitle from 'react-document-title'

import MySchool from './MySchool'
import SaveButton from './SaveButton'
import SchoolAttended from './SchoolAttended'
import SchedulingSystem from './SchedulingSystem'
import SchoolSearchResult from './SchoolSearchResult'

import { SCHEDULING } from './constants'
import { findSchools, updateProfile, saveApplication, resetSaved, clearChangedFields } from './../../actions/Profile'

var PAGE_DONE = '';

class Courses extends React.Component {
	constructor(props){
		super(props)
		this.state = {
			schools: [],
			schoolAdded: false,
			schedulingAdded: false,
			open: false,
		}
		this._saveAndContinue = this._saveAndContinue.bind(this)
		this._searchForSchool = this._searchForSchool.bind(this)
	}

	_saveAndContinue(e){
		e.preventDefault();
		
		let { dispatch, _profile } = this.props;
		let form = _.omitBy( _profile, (v, k) => k.includes('list') );

		dispatch( saveApplication(form, 'courses', _profile.oneApp_step) );
	}

	_searchForSchool(e){
		let { dispatch } = this.props;
		dispatch( findSchools({[e.target.id]: e.target.value}) );
	}

	componentWillMount(){
		let { dispatch, _profile, route } = this.props;

		PAGE_DONE = route.id+'_form_done';

		if( _profile.init_schools_done ) this.state.schools = _profile.schools_list.slice();
		dispatch( updateProfile({page: route.id}) );
		dispatch(clearChangedFields());
	}

	componentWillReceiveProps(np){
		let { dispatch, route, _profile } = this.props;	

		// after saving, reset saved and go to next route
		if( np._profile.save_success !== _profile.save_success && np._profile.save_success ){
			dispatch( resetSaved() );
			
			if( np._profile.coming_from ) browserHistory.goBack();
			else{
				let required_route = _.find(_profile.req_app_routes.slice(), {id: route.id});
				if(required_route) browserHistory.push('/college-application/'+required_route.next + window.location.search);
			}
		}
	}

	render(){
		let { _profile, route } = this.props,
			{ schools, schoolAdded, open, schedulingAdded } = this.state;

		return (
			<DocumentTitle title={"Plexuss | College Application | "+route.name}>
				<div className={"application-container "+(schoolAdded && schedulingAdded ? 'full' : '')}>

					<form>

						<div className="page-head">Current Courses</div>

						{ (!schoolAdded && !schedulingAdded) &&
							<div className="course-piece sm">
								<div>Add a course at</div>

								<div className="custom-select-container">
									<div onClick={ () => this.setState({open: !open}) }>
										Select the school you have attended
										<div className="arrow" />	
									</div>
									<div className={"select-schools-list crs " + (open ? 'show' : '')}>

										<div className="drp-labl"><b>Schools you've attended:</b></div>
										{ selectn('schools_attended_list.length', _profile) > 0 && 
											_profile.schools_attended_list.map((sa) => <SchoolAttended 
																							key={sa.name} 
																							school={sa} 
																							{...this.props} 
																							added={ (schedulingAdded) => this.setState({schoolAdded: true, open: false, ...schedulingAdded}) } />) }

										<div className="drp-labl"><b>Add another school</b></div>
										<input
											id="search_for_school"
											type="text"
											name="search_for_school"
											placeholder="Type school name here..."
											value={ selectn('search_for_school', _profile) || '' }
											onChange={ this._searchForSchool } />

										<div className="search-schools-container">
											{ selectn('schools_searched.length', _profile) > 0 && 
												_profile.schools_searched.map((sr) => <SchoolSearchResult 
																							key={sr.id} 
																							school={sr} 
																							added={ () => this.setState({schoolAdded: true, open: false}) }
																							{...this.props} />) }
										</div>
									</div>
								</div>

								<div>
									<button
										className="save"
										onClick={ () => this.setState({schoolAdded: true, open: false}) }
										disabled={ false }>Next</button>
								</div>
							</div>
						}

						{ (schoolAdded && !schedulingAdded) && 
							<div className="course-piece sm">

								<div>Please select the course scheduling system your school is using</div>

								<div className="custom-select-container">
									<div onClick={ () => this.setState({open: !open}) }>
										Select...
										<div className="arrow" />	
									</div>

									<div className={"select-schools-list crs " + (open ? 'show' : '')}>

										<div className="drp-labl"><b>Schools you've attended:</b></div>
											{ SCHEDULING.map((sc) => <SchedulingSystem
																		key={sc.name} 
																		system={sc} 
																		{...this.props} 
																		added={ () => this.setState({schedulingAdded: true, open: false}) } />) }

									</div>
								</div>

								<div>
									<button
										className="save"
										onClick={ () => this.setState({schedulingAdded: true}) }
										disabled={ false }>Next</button>
								</div>
							</div> 
						}

						{ (schoolAdded && schedulingAdded) && 
							<div className="course-piece">
								<div className="course-head">
									<div>My Schools <span onClick={ () => this.setState({schoolAdded: false, schedulingAdded: false}) }>+ Add Another School</span></div>
									<div>Total Courses</div>
									<div>Total Credits</div>
								</div>

								{ selectn('current_schools.length', _profile) > 0 && 
									_profile.current_schools.map((sch) => <MySchool key={sch.id} school={sch} {...this.props} />) }

								<div>
									<button
										className="save"
										onClick={ this._saveAndContinue }
										disabled={ !_profile[PAGE_DONE] || _profile.save_pending }>
											{ _profile.save_pending ? 'Saving...' : 'Save & Continue' }
									</button>
								</div>
							</div> 
						}

					</form>	

				</div>
			</DocumentTitle>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		_profile: state._profile,
	};
};

export default connect(mapStateToProps)(Courses);