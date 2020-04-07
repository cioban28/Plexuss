// OneApp/constants.js
import React from 'react'
import { Route } from 'react-router-dom'
import OneApp from './index'


// College Application Components
import ReviewApp from './../../../StudentApp/components/ReviewApp'
import GPA from './../../../StudentApp/components/College_Application/GPA'
import Clubs from './../../../StudentApp/components/College_Application/Clubs'
import Demographics from './../../../StudentApp/components/College_Application/Demographics'
import Essay from './../../../StudentApp/components/College_Application/Essay'
import Sponsor from './../../../StudentApp/components/College_Application/Sponsor'
import Submit from './../../../StudentApp/components/College_Application/Submit'
import Scores from './../../../StudentApp/components/College_Application/Scores'
import Family from './../../../StudentApp/components/College_Application/Family'
import Awards from './../../../StudentApp/components/College_Application/Awards'
import Uploads from './../../../StudentApp/components/College_Application/Uploads'
import Courses from './../../../StudentApp/components/College_Application/Courses'
import Basic_Info from './../../../StudentApp/components/College_Application/Basic_Info'
import Financials from './../../../StudentApp/components/College_Application/Financials'
import Scholarships from './../../../StudentApp/components/College_Application/Scholarships'
import ScholarshipsSubmit from './../../../StudentApp/components/College_Application/Scholarships/submit'
import ScholarshipsThanks from './../../../StudentApp/components/College_Application/Scholarships/thanks'

import Citizenship from './../../../StudentApp/components/College_Application/Citizenship'
import Contact_Info from './../../../StudentApp/components/College_Application/Contact_Info'
import StartingWhen from './../../../StudentApp/components/College_Application/StartingWhen'
import Declaration from './../../../StudentApp/components/College_Application/Declarations'
import SelectColleges from './../../../StudentApp/components/College_Application/SelectCollegesSocial'
import MyApplications from './../../../StudentApp/components/College_Application/MyApplicationsSocial'
import StudyInCountry from './../../../StudentApp/components/College_Application/StudyInCountry'
import AdditionalInfo from './../../../StudentApp/components/College_Application/AdditionalInfo'
import NameConfirmation from './../../../StudentApp/components/College_Application/NameConfirmation'

// Review Application Components
import ReviewGPA from './../../../StudentApp/components/ReviewApp/GPA'
import ReviewBasic from './../../../StudentApp/components/ReviewApp/Basic'
import ReviewClubs from './../../../StudentApp/components/ReviewApp/Clubs'
import ReviewEssay from './../../../StudentApp/components/ReviewApp/Essay'
import ReviewAwards from './../../../StudentApp/components/ReviewApp/Awards'
import ReviewFamily from './../../../StudentApp/components/ReviewApp/Family'
import ReviewScores from './../../../StudentApp/components/ReviewApp/Scores'
import ReviewScholarships from './../../../StudentApp/components/ReviewApp/Scholarships'
import ReviewAdditionalInfo from './../../../StudentApp/components/ReviewApp/AdditionalInfo'
import ReviewContact from './../../../StudentApp/components/ReviewApp/Contact'
import ReviewCourses from './../../../StudentApp/components/ReviewApp/Courses'
import ReviewUploads from './../../../StudentApp/components/ReviewApp/Uploads'
import ReviewSponsor from './../../../StudentApp/components/ReviewApp/Sponsor'
import ReviewColleges from './../../../StudentApp/components/ReviewApp/Colleges'
import ReviewObjective from './../../../StudentApp/components/ReviewApp/Objective'
import ReviewFinancials from './../../../StudentApp/components/ReviewApp/Financials'
import ReviewCitizenship from './../../../StudentApp/components/ReviewApp/Citizenship'
import VerifyCode from '../../../StudentApp/components/College_Application/VerifyCode';

export const APP_ROUTES = [
	{path: '/social/one-app/basic', id: 'basic', next: 'start', name: 'Basic Info', _component: Basic_Info, required: true},
	//{path: '/social/one-app/identity', id: 'identity', next: 'planned_start', name: 'Identity', notNavItem: true, _component: NameConfirmation, required: true},
	{path: '/social/one-app/start', id: 'start', next: 'contact', name: 'Planned Start', _component: StartingWhen, required: true},
	{path: '/social/one-app/contact', id: 'contact', next: 'verify', name: 'Contact Info', _component: Contact_Info, required: true},
	{path: '/social/one-app/verify', id: 'verify', next: 'citizenship', name: 'Verify Phone Number', _component: VerifyCode, required: true},
	//{path: '/social/one-app/study', id: 'study', next: 'citizenship', name: 'Study in Country', notNavItem: true, _component: StudyInCountry, required: true},
	{path: '/social/one-app/citizenship', id: 'citizenship', next: 'financials', name: 'Citizenship', _component: Citizenship, required: true},
	{path: '/social/one-app/financials', id: 'financials', next: 'gpa', name: 'Financials', _component: Financials, required: true},
	{path: '/social/one-app/gpa', id: 'gpa', next: 'scores', name: 'GPA', _component: GPA, required: true},
	{path: '/social/one-app/scores', id: 'scores', next: 'demographics', name: 'Scores', _component: Scores, required: true},
    {path: '/social/one-app/scholarships', id: 'scholarships', next: 'colleges', name: 'Select Scholarships', _component: Scholarships, required: true},
	{path: '/social/one-app/colleges', id: 'colleges', next: 'applications', name: 'Select Colleges', _component: SelectColleges, required: true},
	{path: '/social/one-app/applications', id: 'applications', next: 'uploads', name: 'My Applications', _component: MyApplications, required: true},
	// {path: '/social/one-app/submit', id: 'submit', next: 'review', name: 'Submit', _component: Submit, required: true},
	{path: '/social/one-app/review', id: 'review', next: '', name: 'Edit Application', notNavItem: true, _component: ReviewApp, required: true},
	//{path: '/social/one-app/family', id: 'family', next: 'awards', name: 'Family', _component: Family, required: false},
	//{path: '/social/one-app/awards', id: 'awards', next: 'clubs', name: 'Honors & Awards', _component: Awards, required: false},
	//{path: '/social/one-app/clubs', id: 'clubs', next: 'courses', name: 'Orgs & Clubs', _component: Clubs, required: false},
	//{path: '/social/one-app/courses', id: 'courses', next: 'essay', name: 'Current Courses', _component: Courses, required: false},

	{path: '/social/one-app/essay', id: 'essay', next: 'uploads', name: 'Essay', _component: Essay, required: true},
	{path: '/social/one-app/demographics', id: 'demographics', next: 'scholarships', name: 'Demographics', _component: Demographics, required: true},
	//{path: '/social/one-app/additional_info', id: 'additional_info', next: 'uploads', name: 'Additional Info', _component: AdditionalInfo, required: false}, //change back to false for required
	{path: '/social/one-app/uploads', id: 'uploads', next: 'sponsor', name: 'Uploads', _component: Uploads, required: true},
	//{path: '/social/one-app/declaration', id: 'declaration', next: 'sponsor', name: 'Declaration', _component: Declaration, required: false},
	{path: '/social/one-app/sponsor', id: 'sponsor', next: 'review', name: 'Sponsor', _component: Sponsor, required: true},
];

const _renderAppRoute = (route, index) => {
	switch( route.id ){
		case 'basic': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}  route={APP_ROUTES[index]} ><Basic_Info {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'identity': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><NameConfirmation {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'start': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><StartingWhen {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'contact': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><Contact_Info {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'verify': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><VerifyCode {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'study': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><StudyInCountry {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'citizenship': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><Citizenship {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'financials': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><Financials {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'gpa': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><GPA {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'scores': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><Scores {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'scholarships': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><Scholarships {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'colleges': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><SelectColleges {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'applications': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><MyApplications {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'family': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><Family {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'awards': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><Awards {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'clubs': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><Clubs {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'courses': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><Courses {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'demographics': return <Route key={index} path={route.path} exact render={(routeProps) =>  <OneApp route={APP_ROUTES[index]} {...routeProps}><Demographics {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'essay': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><Essay {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'additional_info': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><AdditionalInfo {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'uploads': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><Uploads {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'declaration': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><Declaration {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'sponsor': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><Sponsor {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		// case 'submit': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><Submit {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		case 'review': return <Route key={index} path={route.path} exact render={(routeProps) => <OneApp route={APP_ROUTES[index]} {...routeProps}><ReviewApp {...routeProps} route={APP_ROUTES[index]} /></OneApp>}/>
		default: return null;
	}
}

export const OneAppRoutes = APP_ROUTES.map((r,i) => _renderAppRoute(r,i));
