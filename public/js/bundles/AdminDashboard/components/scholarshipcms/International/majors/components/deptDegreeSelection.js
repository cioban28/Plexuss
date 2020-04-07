// deptDegreeSelection.js

import React from 'react'

import SelectionWithDegrees from './selectionWithDegrees'
import MajorDegreeSelection from './majorDegreeSelection'
import ConditionalRadioFields from './../../admission/components/conditionalRadioFields'
import createReactClass from 'create-react-class'

import { DEPT_OPTIONS, DEPT_OPT } from './../../constants'

/*
	Created for every Dept selected
	- creates radio buttons to include all or selected majors
	- generates list of selected majors
*/

export default createReactClass({
	getInitialState(){
		return {
			open: false,
		};
	},

	_toggleMajorContainer(){
		this.setState((prevState) => ({
			open: !prevState.open
		}));
	},

	render(){
		let { dispatch, dept } = this.props;

		return (
			<div className="dept_degree_selection_container">
				<SelectionWithDegrees
					_toggle={ this._toggleMajorContainer }
					{...this.state}
					{...this.props} />

				<MajorDegreeSelection
					{...this.state}
					{...this.props} />
			</div>
		);
	}
});
