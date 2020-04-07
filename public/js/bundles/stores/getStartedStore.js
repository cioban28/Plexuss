// getStartedStore.js

import thunk from 'redux-thunk'
import { createStore, applyMiddleware, compose } from 'redux'

import initialState from '../GetStarted/reducers/initialState';
import rootReducer from '../GetStarted/reducers/rootReducer';

let store = createStore(
  rootReducer,
  initialState,
  compose(
    applyMiddleware(thunk),
    window.devToolsExtension ? window.devToolsExtension() : f => f
  )
);

export default store;
