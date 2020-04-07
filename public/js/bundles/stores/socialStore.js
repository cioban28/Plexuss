// socialStore.js

import thunk from 'redux-thunk'
import { createStore, applyMiddleware, compose } from 'redux'

import initialState from '../SocialApp/reducers/initialState';
import rootReducer from '../SocialApp/reducers/rootReducer';

const store = createStore(
  rootReducer,
  initialState,
  compose(
    applyMiddleware(thunk),
    window.devToolsExtension ? window.devToolsExtension() : f => f
  )
);

export default store;
