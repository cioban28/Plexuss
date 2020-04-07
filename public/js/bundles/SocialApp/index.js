import React from 'react'
import { render } from 'react-dom'
import { BrowserRouter as Router, Route, Switch, Redirect } from 'react-router-dom';
import { Provider } from 'react-redux'
import ReduxToastr from 'react-redux-toastr'
import store from './../stores/socialStore'
import 'react-redux-toastr/src/styles/index.scss';
//import "@babel/polyfill";
import App from './App';


render((
		<Provider store={store}>
			<div>
				<Router>
					<App />
			  </Router>
			  <ReduxToastr
					timeOut={4000}
					newestOnTop={false}
					preventDuplicates={false}
					position="top-right"
					transitionIn="fadeIn"
					transitionOut="fadeOut"
					progressBar
					closeOnToastrClick />
			</div>
		</Provider>
), document.getElementById('_SocialApp_Component'));
