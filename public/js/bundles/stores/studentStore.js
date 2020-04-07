// studentStore.js

import thunk from 'redux-thunk'
import logger from 'redux-logger'
import promise from 'redux-promise-middleware'
import { reducer as toastrReducer } from 'react-redux-toastr'
import { createStore, applyMiddleware, combineReducers } from 'redux'

// App Reducers
// import dates from './../AdminDashboard/reducers/datesReducer'
import _user from './../StudentApp/reducers/User'
import _profile from './../StudentApp/reducers/Profile'
import _checkout from './../StudentApp/reducers/Checkout'
import _intl from './../StudentApp/reducers/Intl_Students'
import _superUser from './../StudentApp/reducers/SuperUser'
import user from './../SocialApp/reducers/user'


//create and combine middleware
// -- dev
// const middleware = applyMiddleware(thunk, promise(), logger());

// -- production
const middleware = applyMiddleware(thunk, promise());

//combine all reducers
const reducers = combineReducers({
	_user,
	_intl,
	_profile,
	_checkout,
	_superUser,
	user,
	toastr: toastrReducer,
});

// Create Store
const store = createStore(reducers, middleware);

export default store;
