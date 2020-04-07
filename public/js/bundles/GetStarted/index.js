import React from 'react'
import { render } from 'react-dom'
import { BrowserRouter as Router, Route, Switch, Redirect } from 'react-router-dom';
import { Provider } from 'react-redux'
import ReduxToastr from 'react-redux-toastr'
//import store
import store from './../stores/getStartedStore'
//import styles
import 'react-redux-toastr/src/styles/index.scss';
//import components
import Step from './components/Step/index'
import Header from './components/Header/index'

render((
		<Provider store={store}>
			<div>
				<Router>
				<div>
					<Route component={ Header }/>
					<Switch>
						<Route path="/get_started" exact component={ Step }/>
						<Route path="/get_started/:step" component={ Step }/>
					</Switch>
				</div>
			  </Router>
			  <ReduxToastr
				timeOut={4000}
				newestOnTop={false}
				preventDuplicates={false}
				position="top-right"
				transitionIn="fadeIn"
				transitionOut="fadeOut"
				progressBar
				closeOnToastrClick/>
			</div>
		</Provider>
), document.getElementById('_GetStarted_Component'));
