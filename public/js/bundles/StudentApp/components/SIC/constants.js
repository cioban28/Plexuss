// /SIC/constants.js

import React from 'react'
import { Route } from 'react-router'

/*
	To Add a new route:
	1. Create component and add under College Application Components
	2. Create route in APP_ROUTES (placement/order matters)
*/

// College Application Components
import ReviewApp from './../ReviewApp'
import GPA from './../College_Application/GPA'
import Clubs from './../College_Application/Clubs'
import Essay from './../College_Application/Essay'
import Sponsor from './../College_Application/Sponsor'
import Submit from './../College_Application/Submit'
import Scores from './../College_Application/Scores'
import Family from './../College_Application/Family'
import Awards from './../College_Application/Awards'
import Uploads from './../College_Application/Uploads'
import Courses from './../College_Application/Courses'
import Basic_Info from './../College_Application/Basic_Info'
import Financials from './../College_Application/Financials'
import Scholarships from './../College_Application/Scholarships'
import ScholarshipsSubmit from './../College_Application/Scholarships/submit'
import ScholarshipsThanks from './../College_Application/Scholarships/thanks'

import Citizenship from './../College_Application/Citizenship'
import Contact_Info from './../College_Application/Contact_Info'
import StartingWhen from './../College_Application/StartingWhen'
import Declaration from './../College_Application/Declarations'
import SelectColleges from './../College_Application/SelectColleges'
import StudyInCountry from './../College_Application/StudyInCountry'
import AdditionalInfo from './../College_Application/AdditionalInfo'
import NameConfirmation from './../College_Application/NameConfirmation'

// Review Application Components
import ReviewGPA from './../ReviewApp/GPA'
import ReviewBasic from './../ReviewApp/Basic'
import ReviewClubs from './../ReviewApp/Clubs'
import ReviewEssay from './../ReviewApp/Essay'
import ReviewAwards from './../ReviewApp/Awards'
import ReviewFamily from './../ReviewApp/Family'
import ReviewScores from './../ReviewApp/Scores'
import ReviewScholarships from './../ReviewApp/Scholarships'
import ReviewAdditionalInfo from './../ReviewApp/AdditionalInfo'
import ReviewContact from './../ReviewApp/Contact'
import ReviewCourses from './../ReviewApp/Courses'
import ReviewUploads from './../ReviewApp/Uploads'
import ReviewSponsor from './../ReviewApp/Sponsor'
import ReviewColleges from './../ReviewApp/Colleges'
import ReviewObjective from './../ReviewApp/Objective'
import ReviewFinancials from './../ReviewApp/Financials'
import ReviewCitizenship from './../ReviewApp/Citizenship'

// all college-application routes - order matters here
export const APP_ROUTES = [
	{path: '/college-application/basic', id: 'basic', next: 'identity', name: 'Basic Info', _component: Basic_Info, required: true},
	{path: '/college-application/identity', id: 'identity', next: 'planned_start', name: 'Identity', notNavItem: true, _component: NameConfirmation, required: true},
	{path: '/college-application/planned_start', id: 'start', next: 'contact', name: 'Planned Start', _component: StartingWhen, required: true},
	{path: '/college-application/contact', id: 'contact', next: 'study', name: 'Contact Info', _component: Contact_Info, required: true},
	{path: '/college-application/study', id: 'study', next: 'citizenship', name: 'Study in Country', notNavItem: true, _component: StudyInCountry, required: true},
	{path: '/college-application/citizenship', id: 'citizenship', next: 'financials', name: 'Citizenship', _component: Citizenship, required: true},
	{path: '/college-application/financials', id: 'financials', next: 'gpa', name: 'Financials', _component: Financials, required: true},
	{path: '/college-application/gpa', id: 'gpa', next: 'scores', name: 'GPA', _component: GPA, required: true},
	{path: '/college-application/scores', id: 'scores', next: 'scholarships', name: 'Scores', _component: Scores, required: true},
    {path: '/college-application/scholarships', id: 'scholarships', next: 'colleges', name: 'Select Scholarships', _component: Scholarships, required: true},
	{path: '/college-application/colleges', id: 'colleges', next: 'uploads', name: 'Select Colleges', _component: SelectColleges, required: true},
	{path: '/college-application/family', id: 'family', next: 'awards', name: 'Family', _component: Family, required: false},
	{path: '/college-application/awards', id: 'awards', next: 'clubs', name: 'Honors & Awards', _component: Awards, required: false},
	{path: '/college-application/clubs', id: 'clubs', next: 'courses', name: 'Orgs & Clubs', _component: Clubs, required: false},
	{path: '/college-application/courses', id: 'courses', next: 'essay', name: 'Current Courses', _component: Courses, required: false},
	{path: '/college-application/essay', id: 'essay', next: 'additional_info', name: 'Essay', _component: Essay, required: true},
	{path: '/college-application/additional_info', id: 'additional_info', next: 'uploads', name: 'Additional Info', _component: AdditionalInfo, required: false}, //change back to false for required
	{path: '/college-application/uploads', id: 'uploads', next: 'declaration', name: 'Uploads', _component: Uploads, required: true},
	{path: '/college-application/declaration', id: 'declaration', next: 'sponsor', name: 'Declaration', _component: Declaration, required: false},
	{path: '/college-application/sponsor', id: 'sponsor', next: 'submit', name: 'Sponsor', _component: Sponsor, required: true},
	{path: '/college-application/submit', id: 'submit', next: 'review', name: 'Submit', _component: Submit, required: true},
	{path: '/college-application/review', id: 'review', next: '', name: 'Edit Application', notNavItem: true, _component: ReviewApp, required: true},
];

export const OPTIONAL_ROUTE_IDS = [
    'uploads',
    'essay',
    'additional_info',
    'sponsor',
    'select_scholarships'
];

//required routes for sic are based off of this
export const SCH_ROUTES = [
	{path: '/college-application/basic', id: 'basic', next: 'identity', name: 'Basic Info', _component: Basic_Info, required: true},
	{path: '/college-application/identity', id: 'identity', next: 'planned_start', name: 'Identity', notNavItem: true, _component: NameConfirmation, required: true},
	{path: '/college-application/planned_start', id: 'start', next: 'contact', name: 'Planned Start', _component: StartingWhen, required: true},
	{path: '/college-application/contact', id: 'contact', next: 'study', name: 'Contact Info', _component: Contact_Info, required: true},
	{path: '/college-application/study', id: 'study', next: 'citizenship', name: 'Study in Country', notNavItem: true, _component: StudyInCountry, required: true},
	{path: '/college-application/citizenship', id: 'citizenship', next: 'financials', name: 'Citizenship', _component: Citizenship, required: true},
	{path: '/college-application/financials', id: 'financials', next: 'gpa', name: 'Financials', _component: Financials, required: true},
	{path: '/college-application/gpa', id: 'gpa', next: 'scores', name: 'GPA', _component: GPA, required: true},
	{path: '/college-application/scores', id: 'scores', next: 'scholarships', name: 'Scores', _component: Scores, required: true},
	{path: '/college-application/scholarships', id: 'scholarships', next: 'scholarships-thanks', name: 'Submit Scholarships', _component: ScholarshipsSubmit, required: true},
	{path: '/college-application/review', id: 'review', next: '', name: 'Edit Application', notNavItem: true, _component: ReviewApp, required: true},
	{path: '/college-application/scholarships-thanks', id: 'scholarships-thanks', next: '', name: 'Scholarships Thank You', _component: ScholarshipsThanks, required: false}
	
];

var APP_ROUTES_COPY = [...APP_ROUTES];

var SCH_ROUTES_COPY = [...SCH_ROUTES];

const _updateRequiredRoutes = (nextApplyTo_Schools) => {
	const flowtype = window.location.search.includes('?isScholarship') ? 'sch' : 'app';

	var app_routes = [...APP_ROUTES];
	if(flowtype === 'sch'){
	
		app_routes = [...SCH_ROUTES];
	}
	// only if nextApplyTo_Schools is an array and has more than one school, do we want to check for allowed_sections
	if( _.isArray(nextApplyTo_Schools) && nextApplyTo_Schools.length ){
		// 1. loop through each school
		// 2. check if school has any allowed_sections
		// 3. if above is false, just move on to next school, else loop through allowed_sections
		// 4. find section in NOT_REQUIRED_ROUTES, remove from that list and to REQUIRED_ROUTES
		_.each(nextApplyTo_Schools.slice(), school => {

			// if school has some required sections, find the section and add to APP_ROUTES_COPY
			if( _.get(school, 'allowed_sections.length') ){
				_.each(school.allowed_sections, id => {
					let nr_route = _.find([...app_routes], {id}); //it'll find the section/route that can be either required or not

					if( nr_route ){
						// in order to maintain order, I am taking the original APP_ROUTES, updating the route equal to the nr_route
						// updating its required prop to true
						app_routes = app_routes.map(rt => rt.id === nr_route.id ? {...rt, required: true} : rt);
					}
				});
			}

			// check if school has declarations, if false, don't do anything
			// if true, check if declarations route required prop is false, if true, do nothing
			// else if false, make true
			if( _.get(school, 'declarations.length') ){
				let id = 'declaration',
					decl_route = _.find(app_routes, {id});

				if( !_.isEmpty(decl_route) && !decl_route.required ) app_routes = app_routes.map(rt => rt.id === id ? {...rt, required: true} : rt);
			}

			// check if school has custom_questions
			if( _.get(school, 'custom_questions') && !_.isEmpty(school.custom_questions) ){
				let id = 'additional_info',
					addtl_route = _.find(app_routes, {id});

				if( !_.isEmpty(addtl_route) && !addtl_route.required ) app_routes = app_routes.map(rt => rt.id === id ? {...rt, required: true} : rt);
			}
		});
	}

	return _.filter(app_routes, r => r.required);
}

export const _getRequiredRoutes = (nextApplyTo_Schools) => {
	const flowtype = window.location.search.includes('?isScholarship') ? 'sch' : 'app';

	if(flowtype === 'app')
		return nextApplyTo_Schools ? _updateRequiredRoutes(nextApplyTo_Schools) : _.filter(APP_ROUTES_COPY, r => r.required);
	else
		return nextApplyTo_Schools ? _updateRequiredRoutes(nextApplyTo_Schools) : _.filter(SCH_ROUTES_COPY, r => r.required);

}


// used in StudentApp/index.js - so that there's only on place we have to add a new route
export const _renderAppRoute = (route) => <Route key={route.id} path={route.path} id={route.id} next={route.next} name={route.name} component={ route._component } />

// used in ReviewApp/index.js - add a new review component based on a route
export const _renderReviewSection = (route, props) => {
	switch( route.id ){
		case 'clubs': return <ReviewClubs key={route.id} _route={route} {...props} />;
		case 'essay': return <ReviewEssay key={route.id} _route={route} {...props} />;
		case 'basic': return <ReviewBasic key={route.id} _route={route} {...props} />;
		case 'scores': return <ReviewScores key={route.id} _route={route} {...props} />;
		case 'awards': return <ReviewAwards key={route.id} _route={route} {...props} />;
		case 'courses': return <ReviewCourses key={route.id} _route={route} {...props} />;
		case 'contact': return <ReviewContact key={route.id} _route={route} {...props} />;
		case 'uploads': return <ReviewUploads key={route.id} _route={route} {...props} />;
		case 'sscholarships': return <ReviewScholarships key={route.id} _route={route} {...props} />;
		case 'sponsor': return <ReviewSponsor key={route.id} _route={route} {...props} />;
		case 'start': return <ReviewObjective key={route.id} _route={route} {...props} />;
		case 'colleges': return <ReviewColleges key={route.id} _route={route} {...props} />;
		case 'financials': return <ReviewFinancials key={route.id} _route={route} {...props} />;
		case 'citizenship': return <ReviewCitizenship key={route.id} _route={route} {...props} />;
		case 'additional_info': return <ReviewAdditionalInfo key={route.id} _route={route} {...props} />;
		default: return null;
	}
};