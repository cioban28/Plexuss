//\ getStartedStore.js \/
// Please Note: Redux is not connected with every step. If you need redux, please wrap the redux provider with the respective step.
// Example is in GetStarted_Step2_Component.jsx at the bottom.

import thunk from 'redux-thunk'
import logger from 'redux-logger'
import promise from 'redux-promise-middleware'
import { reducer as toastrReducer } from 'react-redux-toastr'
import { createStore, applyMiddleware, combineReducers } from 'redux'

// App Reducers
// import dates from './../AdminDashboard/reducers/datesReducer'
import _user from './../reducers/user'

//create and combine middleware
// -- dev 
// const middleware = applyMiddleware(thunk, promise(), logger());

// -- production
const middleware = applyMiddleware(thunk, promise());

//combine all reducers
const reducers = combineReducers({
    _user,
});

// Create Store
const store = createStore(reducers, middleware);

export default store;