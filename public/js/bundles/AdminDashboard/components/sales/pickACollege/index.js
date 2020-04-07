// index.js

import React from 'react'

import ActionBar from './components/actionBar'
import PriorityCollegeList from './components/priorityCollegeList'
import createReactClass from 'create-react-class'

import './styles.scss'

export default createReactClass({
	render(){
		return (
			<div>
				<ActionBar />
				<PriorityCollegeList />
			</div>
		);
	}
});
