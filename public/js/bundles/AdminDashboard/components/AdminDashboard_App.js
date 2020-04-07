// AdminDashboard.js

import React from 'react'
import { render } from 'react-dom'
import { Provider } from 'react-redux'
import { Router, Route, browserHistory, IndexRoute, IndexRedirect } from 'react-router'
import ReduxToastr from 'react-redux-toastr'

//store
import store from './../../stores/adminStore'

//components
import Step1 from './step1'
import Step2 from './step2'
import Step3 from './step3'
import Step4 from './step4'
import PortalLogin from './portal_login'
import SetupContainer from './setup_container'
import ProfileContainer from './profile_container'
import ManagePortalsPage from './manage_portal_page'
import DashboardContainer from './Dashboard'
import PremiumPlanRequest from './PremiumPlanRequest'
import ManageUsersContainer from './manage_users_container'
import HelpContainer from './HelpPages'

import PickACollegeContainer from './sales/pickACollege'
import SalesScholarshipsContainer from './sales/Scholarships'
import ApplicationOrder from './sales/applicationOrder'
import AgencyReporting from './sales/agencyReporting'
import Tracking from './sales/Tracking'
import SitePerformance from './sales/sitePerformance'
import StudentTracking from './sales/studentTracking'
import PixelTrackingTest from './sales/pixelTrackingTest'
import AllPosts from './sales/SocialNewsfeed/AllPosts/index.jsx';
import PlexussOnlyPosts from './sales/SocialNewsfeed/PlexussOnlyPosts/index.jsx';
import CreatePost from './sales/SocialNewsfeed/CreatePost/index.jsx';
import DeviceOSReporting from './sales/DeviceOSReporting';

import Tools from './cms'
import Tools_Logo from './cms/Logo'
import Tools_Cost from './cms/Cost'
import Tools_Overview from './cms/Overview'
import Tools_Rankings from './cms/Rankings'
import Tools_RepProfile from './cms/RepProfile'
import Tools_International from './cms/International'

import Cost_Tuition from './cms/Cost/Tuition'

import Overview_Header from './cms/Overview/header'
import Overview_Content from './cms/Overview/content'

import Tools_Scholarshipcms from './scholarshipcms/'
import Scholarshipcms_add from './scholarshipcms/add'
import Scholarshipcms_list from './scholarshipcms/list'

import Intl_Program from './cms/International/program'
import Intl_Header from './cms/International/header'
import Intl_Testimonials from './cms/International/testimonials'
import Intl_Admission from './cms/International/admission'
import Intl_Scholarship from './cms/International/scholarship'
import Intl_Notes from './cms/International/notes'
import Intl_Grades from './cms/International/grades'
import Intl_Requirements from './cms/International/requirements'
import Intl_Majors from './cms/International/majors'
import Intl_Alumni from './cms/International/alumni'

import Messages from './messages'

import Tools_Application from './cms/Application'
import Define_Program from './cms/Application/Define_Program'
import Shared from './cms/Application/Shared'
import Uploads from './cms/Application/Uploads'
import Courses from './cms/Application/Courses'
import Essay from './cms/Application/Essay'
import AdditionalQuestions from './cms/Application/AdditionalQuestions'
import CustomQuestions from './cms/Application/CustomQuestionsSequel'
import MandatoryQuestions from './cms/Application/MandatoryQuestions'
import Reporting from './Reporting'

const state = store.getState();
let indexComponent = <IndexRoute component={PortalLogin} onEnter={requireAuth} />;

// ***** TEMPORARY! - capturing click event of top nav items to render react components *****
// Temporary until all of admin pages are in react, then we won't need to add event listeners on non-react DOM elements
if( state.user.role === 'Admin' ){
	let login = document.getElementById('react_route_to_portal_login'),
		loginTwo = document.getElementById('react-route-to-portal-login-2'),
		profile = document.getElementById('react_route_to_profile'),
		users = document.getElementById('react_route_to_manage_users'),
		dash = document.getElementById('react_route_to_dashboard'),
		plexLogo = document.getElementById('react_route_to_dashboard_2'),
		portals = document.getElementById('react_route_to_manage_portals'),
		messages = document.getElementById('react_route_to_messages'),
		tools = document.getElementById('react_route_to_tools'),
    reporting = document.getElementById('react_route_to_reporting');

	if( login ) login.addEventListener('click', () => browserHistory.push('/admin'));
	if( loginTwo ) loginTwo.addEventListener('click', () =>  browserHistory.push('/admin'));
	if( users ) users.addEventListener('click', () => browserHistory.push('/admin/users'));
	if( dash ) dash.addEventListener('click', () => browserHistory.push('/admin/dashboard'));
	if( plexLogo ) plexLogo.addEventListener('click', () => browserHistory.push('/admin/dashboard'));
	if( profile ) profile.addEventListener('click', () => browserHistory.push('/admin/profile'));
	if( portals ) portals.addEventListener('click', () => browserHistory.push('/admin/portals'));
	if( messages ) messages.addEventListener('click', () => browserHistory.push('/admin/messaging'));
	if( tools ) tools.addEventListener('click', (e) => { e.preventDefault(); browserHistory.push('/admin/tools') });
    if( reporting ) reporting.addEventListener('click', (e) => { e.preventDefault(); browserHistory.push('/admin/reporting') });
}
// ***** TEMPORARY! - capturing click event of top nav items to render react components *****

// set index component based on url pathname
function getUrlPath(){
	let path = window.location.pathname;


	if( path.indexOf('profile') > -1 ) indexComponent = <IndexRoute component={ProfileContainer} />;
	else if( path.indexOf('portals') > -1 ) indexComponent = <IndexRoute component={ManagePortalsPage} />;
	else indexComponent = <IndexRoute component={PortalLogin} onEnter={requireAuth} />;
};

// authenticate user
function requireAuth(nextState, replace) {
	//if user is the organization's first ever user AND has not completed the signup, then render setup
	if ( !state.user.completed_signup && state.user.orgs_first_user ) {
		replace({
			pathname: '/admin/setup',
			state: { nextPathname: nextState.location.pathname }
		});
	}
};

render((
	<Provider store={store}>

		<div>
			<Router history={browserHistory}>

				<Route path="/admin">

					{indexComponent}

					<Route path="/admin/dashboard" component={DashboardContainer} />

          <Route path="/admin/premium-plan-request" component={PremiumPlanRequest} />

          <Route path="/admin/reporting" component={Reporting} />

					<Route path="/admin/setup" component={SetupContainer}>
						<IndexRoute component={Step1} />
						<Route path="/admin/setup/step1" component={Step1} />
						<Route path="/admin/setup/step2" component={Step2} />
						<Route path="/admin/setup/step3" component={Step3} />
						<Route path="/admin/setup/step4" component={Step4} />
					</Route>

					<Route path="/admin/profile" component={ProfileContainer} />
					<Route path="/admin/profile/:id" component={ProfileContainer} />
					<Route path="/admin/portals" component={ManagePortalsPage} />
					<Route path="/admin/users" component={ManageUsersContainer} />

					<Route path="/admin/messages" component={ Messages }>
						<Route path="/admin/messages/:id" component={ Messages } />
						<Route path="/admin/messages/:id/:type" component={ Messages } />
					</Route>

					<Route path="/agency/messages" component={ Messages }>
						<Route path="/agency/messages/:id" component={ Messages } />
						<Route path="/agency/messages/:id/:type" component={ Messages } />
					</Route>

					<Route path="/admin/tools" component={ Tools }>

						<IndexRedirect to="/admin/tools/overview" />

						<Route path="/admin/tools/overview" name="Overview" component={ Tools_Overview }>
							<IndexRedirect to="/admin/tools/overview/header" />
							<Route path="/admin/tools/overview/header" name="Header Info" id="overview:header" component={ Overview_Header } />
							<Route path="/admin/tools/overview/content" name="Main Content" id="overview:content" component={ Overview_Content } />
						</Route>

						<Route path="/admin/tools/international" name="International Students" component={ Tools_International }>
							<IndexRedirect to="/admin/tools/international/program" />
							<Route path="/admin/tools/international/program" name="Define Program" id="program" component={ Intl_Program } />
							<Route path="/admin/tools/international/header" name="Header Info" id="header_info" component={ Intl_Header } />
							<Route path="/admin/tools/international/testimonials" name="Video Testimonials" id="testimonials" component={ Intl_Testimonials } />
							<Route path="/admin/tools/international/admission" name="Admission Info" id="admission" component={ Intl_Admission } />
							<Route path="/admin/tools/international/scholarship" name="Scholarship Info" id="scholarship" component={ Intl_Scholarship } />
							<Route path="/admin/tools/international/notes" name="Additional Notes" id="notes" component={ Intl_Notes } />
							<Route path="/admin/tools/international/grades" name="Grades & Exams" id="grades" component={ Intl_Grades } />
							<Route path="/admin/tools/international/requirements" name="Requirements" id="requirements" component={ Intl_Requirements } />
							<Route path="/admin/tools/international/majors" name="Majors & Degrees" id="majors" component={ Intl_Majors } />
							<Route path="/admin/tools/international/alumni" name="International Alumni" id="alumni" component={ Intl_Alumni } />
						</Route>

						<Route path="/admin/tools/application" name="Application" component={ Tools_Application }>
							<Route path="/admin/tools/application/program" name="Define Program" id="program" component={ Intl_Program } />
							<Route path="/admin/tools/application/family" name="Family" id="family" component={ Shared } />
							<Route path="/admin/tools/application/awards" name="Honors & Awards" id="awards" component={ Shared } />
							<Route path="/admin/tools/application/clubs" name="Organizations & Clubs" id="clubs" component={ Shared } />
							<Route path="/admin/tools/application/uploads" name="Uploads" id="uploads" component={ Uploads } />
							<Route path="/admin/tools/application/courses" name="Current Courses" id="courses" component={ Courses } />
							<Route path="/admin/tools/application/essay" name="Essay" id="essay" component={ Essay } />
							<Route path="/admin/tools/application/additional" name="Additional Questions" id="additional" atypical={true} component={ AdditionalQuestions } />
							<Route path="/admin/tools/application/custom" name="Custom Questions" id="custom" atypical={true} component={ CustomQuestions } />
							<Route path="/admin/tools/application/mandatory" name="Mandatory Questions" id="mandatory" atypical={true} component={ MandatoryQuestions } />
						</Route>

						<Route path="/admin/tools/cost" name="Tuition & Cost" component={ Tools_Cost }>
							<IndexRedirect to="/admin/tools/cost/program" />
							<Route path="/admin/tools/cost/program" name="Define Program" id="program" component={ Intl_Program } />
							<Route path="/admin/tools/cost/tuition" name="Tuition" id="tuition" component={ Cost_Tuition } />
						</Route>

						<Route path="/admin/tools/rep" name="Rep Profile" component={ Tools_RepProfile } />
						<Route path="/admin/tools/rankings" name="Rankings" component={ Tools_Rankings } />
						<Route path="/admin/tools/logo" name="Logo" component={ Tools_Logo } />

					</Route>

					<Route path="/admin/tools/scholarshipcms" component={ Tools_Scholarshipcms }>
						<IndexRedirect to="/admin/tools/scholarshipcms/add" />
						<Route path="/admin/tools/scholarshipcms/list" name="Scholarships" id="scholarshipcms:list" component={ Scholarshipcms_list } />
						<Route path="/admin/tools/scholarshipcms/add" name="Add Scholarship" id="scholarshipcms:add" component={ Scholarshipcms_add } />

					</Route>

				</Route>

				<Route path="/sales">
					<IndexRoute component={ PickACollegeContainer } />
					<Route path="/sales/pickACollege" component={ PickACollegeContainer } />
          <Route path="/sales/pixelTrackingTest" component={ PixelTrackingTest } />
					<Route path="/sales/application-order" component={ ApplicationOrder } />
					<Route path="/sales/agency-reporting" component={ AgencyReporting } />
					<Route path="/sales/tracking" component={ Tracking } />
					<Route path="/sales/scholarships" component={ SalesScholarshipsContainer } />
					<Route path="/sales/site-performance" component={ SitePerformance } />
					<Route path="/sales/social-newsfeed" component={ AllPosts } />
					<Route path="/sales/social-newsfeed/plexuss-only" component={ PlexussOnlyPosts } />
					<Route path='/sales/social-newsfeed/new' component={ CreatePost } />
					<Route path='/sales/social-newsfeed/edit' component={ CreatePost } />
					<Route path="/sales/overview-tracking" component={ StudentTracking } />
					<Route path="/sales/site-performance" component={ StudentTracking } />
					<Route path="/sales/device-os-reporting" component={ DeviceOSReporting } />
				</Route>

		    </Router>

			<ReduxToastr
				timeOut={4000}
				newestOnTop={false}
				preventDuplicates={false}
				position="top-right"
				transitionIn="fadeIn"
				transitionOut="fadeOut"
				progressBar
				closeOnToastrClick
			/>

		</div>

    </Provider>
), document.getElementById('AdminDashboard_Component'));
