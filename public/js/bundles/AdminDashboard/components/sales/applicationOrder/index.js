// index.js

import React from 'react'
import { connect } from 'react-redux'

import ActionBar from './../pickACollege/components/actionBar'
import PriorityCollegeList from './../pickACollege/components/priorityCollegeList'
import createReactClass from 'create-react-class'

import { setPage } from './../../../actions/pickACollegeActions'

const ID = 'appOrder';

const AppOrder = createReactClass({
	componentWillMount(){
		this.props.dispatch( setPage({page: ID}) );
	},

	render(){
		return (
			<div>
				<ActionBar noDate={true} noExport={true} />
				<PriorityCollegeList id={ID} />
			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		pickACollege: state.pickACollege,
		dates: state.dates,
	};
}

export default connect(mapStateToProps)(AppOrder);
