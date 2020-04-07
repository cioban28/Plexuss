// adminStore.js

import thunk from 'redux-thunk'
import logger from 'redux-logger'
import promise from 'redux-promise-middleware'
import { createStore, applyMiddleware, combineReducers, compose } from 'redux'
import { reducer as toastrReducer } from 'react-redux-toastr'

// App Reducers
import cost from './../AdminDashboard/reducers/costReducer'
import dates from './../AdminDashboard/reducers/datesReducer'
import setup from './../AdminDashboard/reducers/setupReducer'
import user from './../AdminDashboard/reducers/profileReducer'
import logo from './../AdminDashboard/reducers/cmsLogoReducer'
import dash from './../AdminDashboard/reducers/dashboardReducer'
import college from './../AdminDashboard/reducers/collegeReducer'
import overview from './../AdminDashboard/reducers/overviewReducer'
import users from './../AdminDashboard/reducers/manageUsersReducer'
import intl from './../AdminDashboard/reducers/internationalReducer'
import messages from './../AdminDashboard/reducers/messagesReducer'
import rankings from './../AdminDashboard/reducers/cmsRankingsReducer'
import portals from './../AdminDashboard/reducers/managePortalsReducer'
import invalidFields from './../AdminDashboard/reducers/validatorReducer'
import pickACollege from './../AdminDashboard/reducers/pickACollegeReducer'
import agencyReporting from './../AdminDashboard/reducers/agencyReportingReducer'
import reporting from './../AdminDashboard/reducers/reportingReducer'
import scholarships from './../AdminDashboard/reducers/scholarshipsReducer'
import pixelTracking from './../AdminDashboard/reducers/pixelTrackingReducer'
import userData from './../AdminDashboard/reducers/userReducer'
import formSubmission from './../AdminDashboard/reducers/formSubmission'
import modal from './../AdminDashboard/reducers/modal'
import newsfeed from './../AdminDashboard/reducers/newsfeed'
import sitePerformance from './../AdminDashboard/reducers/salesPerformanceReducer'
import studentTracking from './../AdminDashboard/reducers/studentTrackingReducer'
import deviceOSReporting from './../AdminDashboard/reducers/deviceOSReportingReducer'

//create and combine middleware
// -- dev
// const middleware = applyMiddleware(thunk, promise(), logger());

// -- production
const middleware = applyMiddleware(thunk, promise());

//combine all reducers
const reducers = combineReducers({
	cost,
	user,
	logo,
	dash,
	intl,
	dates,
	setup,
	users,
	portals,
	college,
	messages,
	overview,
	rankings,
	invalidFields,
	pickACollege,
	agencyReporting,
  reporting,
  scholarships,
  pixelTracking,
	toastr: toastrReducer,
  userData,
  formSubmission,
  modal,
  newsfeed,
	sitePerformance,
	studentTracking,
	deviceOSReporting,
});

// Create Store
const store = createStore(reducers, compose(
      middleware,
      window.devToolsExtension ? window.devToolsExtension() : f => f
    ));

export default store;
