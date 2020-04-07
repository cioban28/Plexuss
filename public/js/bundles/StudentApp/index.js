// StudentApp/index.js -> entry point to whole student app

import React from 'react'
import { render } from 'react-dom'
import { Provider } from 'react-redux'
import { StripeProvider } from 'react-stripe-elements'
import ReduxToastr from 'react-redux-toastr'
import { Router, Route, browserHistory, IndexRoute, IndexRedirect } from 'react-router'

//store
import store from './../stores/studentStore'

//components
import Intl_Students from './components/Intl_Students'

// Intl_Resource components
import Aid from './components/Intl_Resources/Aid'
import Prep from './components/Intl_Resources/Prep'
import Main from './components/Intl_Resources/Main'
import Intl_Resources from './components/Intl_Resources'
import Work_In_US from './components/Intl_Resources/Work_In_US'
import Find_Schools from './components/Intl_Resources/Find_School'
import Student_Visa from './components/Intl_Resources/Student_Visa'
import Application_Checklist from './components/Intl_Resources/Application_Checklist'

// Premium Plans
import Premium_Plans from './components/Premium_Plans'

//Profile 
import Profile from './components/profile'
import Profile_edit from './components/profile/profile_edit'
import Profile_documents from './components/profile/profile_documents'

// Checkout
import Checkout from './components/Checkout'
import PaymentFailed from './components/Checkout/PaymentFailed'
import PaymentSuccess from './components/Checkout/PaymentSuccess'
import IndianCheckout from './components/Indian_Checkout'

// College Application Components
import College_Application from './components/College_Application'
import View_Student_Application from './components/View_Student_Application'

// NCSA components
import NCSA from './components/NCSA'

import GetStartedTCPA from './components/GetStarted_tcpa'

// constants
import { APP_ROUTES, SCH_ROUTES, _renderAppRoute } from './components/SIC/constants'

// authenticate user is premium member
// function requiresPremiumAuth(nextState, replace) {
// 	// store state
// 	const state = store.getState();
	
// 	//if user is the organization's first ever user AND has not completed the signup, then render setup
// 	if ( !state._user.premium_user_type ) {
// 		replace({
// 			pathname: '/premium-plans',
// 			state: { nextPathname: nextState.location.pathname }
// 		});
// 	}
// };

function _getRoutes() {
	
	if(window.location.search.includes('?isScholarship')){
		return SCH_ROUTES;
	}	

	return APP_ROUTES;
}


render((

	<StripeProvider apiKey="pk_live_8To1rx24ZEQgXTUQEUMeRO6S">
		<Provider store={store}>

			<div>
				<Router history={browserHistory}>

					<Route path="/">

						{/* Default for now is Intl_Students page */}
						<IndexRoute component={ Intl_Students } />

						{/* Internation Students page */}
						<Route path="/international-students" component={ Intl_Students } />

						{/* Internation Resources pages */}
						<Route path="/international-resources" name="International Students" component={ Intl_Resources }>
							<IndexRedirect to="/international-resources/main" />
							<Route path="/international-resources/main" name="Main" component={ Main } />
							<Route path="/international-resources/application-checklist" name="Application Checklist" component={ Application_Checklist } />
							<Route path="/international-resources/finding-schools" name="Finding the Right School" component={ Find_Schools } />
							<Route path="/international-resources/aid" name="Scholarships and Financial Aid" component={ Aid } />
							<Route path="/international-resources/prep" name="English Proficiency Test Preparation" component={ Prep } />
							<Route path="/international-resources/working-in-us" name="International Student Working in the US" component={ Work_In_US } />
							<Route path="/international-resources/student-visa" name="Student Visa and Immigration Center" component={ Student_Visa } />
						</Route>

						{/* Premium Plans page */}
						<Route path="/premium-plans" component={ Premium_Plans } />


						{/* Profile page -- called "Me" page */}
						<Route path="/profile" component={Profile} />
						<Route path="/profile/edit_public" component={Profile_edit} />
						<Route path="/profile/documents" component={Profile_documents} />

						{/* Checkout page for ALL products - hopefully it stays this way */}
						<Route path="/checkout/:product" component={ IndianCheckout } />
						<Route path="/payment-success" component={ PaymentSuccess } />
						<Route path="/payment-failed" component={ PaymentFailed } />
						<Route path="/payment-success/:plan" component={ PaymentSuccess } />
						{/* Checkout page for ALL products - Indian version */}
						<Route path="/indian-checkout/:product" component={ IndianCheckout } />

						{/* College Application page */}
						<Route path="/college-application" name="College Application" component={ College_Application }>
							<IndexRedirect to="/college-application/basic"  />
							{ _getRoutes().map((r) => _renderAppRoute(r)) }
						</Route>


						// <Route path="/scholarships-application" name="Scholarships Application" component={ College_Application }>
						// 	<IndexRedirect to="/scholarships-application/basic" />
						// 	{ SCH_ROUTES.map((r) => _renderAppRoute(r)) }
						// </Route>

						{/* College Application Preview page - for when colleges/plexuss are viewing a student's app */}
						<Route path="/view-student-application/:id" component={ View_Student_Application } />


						{/* NCSA info page */}
						<Route path="/ncsa" component={NCSA} />

						<Route path="/get_started/7" component={GetStartedTCPA} />

					</Route>
			    </Router>

				<ReduxToastr
				      timeOut={4000}
				      newestOnTop={true}
				      preventDuplicates={false}
				      position="top-right"
				      transitionIn="bounceIn"
				      transitionOut="bounceOut"
				      progressBar />		

			</div>

	    </Provider>
	</StripeProvider>
), document.getElementById('_StudentApp_Component'));
