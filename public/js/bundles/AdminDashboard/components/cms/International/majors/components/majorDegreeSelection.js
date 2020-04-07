// majorDegreeSelection.js

import React from 'react'
import createReactClass from 'create-react-class'

import PickDepartment from './pickDepartment'
import SelectionWithDegrees from './selectionWithDegrees'
import ConditionalRadioFields from './../../admission/components/conditionalRadioFields'

import { DEPT_OPTIONS, DEPT_OPT } from './../../constants'
// import { getThisDepartmentsMajors } from './../../../../../actions/internationalActions'

export default createReactClass({
	_getMajorOptionValue(){
		let { intl, dept } = this.props;
		return intl['option_for_dept_majors_'+dept.id];
	},

	render(){
		let { dispatch, intl, dept, open } = this.props,
			show = !open ? 'hide' : '',
			dept_majors_list = intl['selected_majors_for_dept_'+dept.id],
			major_option = this._getMajorOptionValue();

		return (
			<div className={"major_degree_selection_container "+show}>
				<PickDepartment
					major={{name: 'major', dept_id: dept.id}}
					{...this.props} />

				<div className={ major_option === 'all' ? 'hide' : '' }>
					{ dept_majors_list && dept_majors_list.map((m) => <SelectionWithDegrees key={m.id} major={m} {...this.props} />) }
				</div>

			</div>
		);
	}
});
