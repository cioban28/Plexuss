// selectionWithDegrees.js

import React from 'react'
import createReactClass from 'create-react-class'

import Tooltip from './../../../../../../utilities/tooltip'

import { DEGREE_TYPES } from './../../constants'
import { addRemoveIntlDept, addRemoveIntlMajor, editCampusType } from './../../../../../actions/internationalActions'

export default createReactClass({
	_removeItem(){
		let { dispatch, dept, major } = this.props,
			deptOrMajor = major || dept,
			_remove = major ? addRemoveIntlMajor : addRemoveIntlDept;

		dispatch( _remove(deptOrMajor) );
	},

	_doesDeptHaveMajor(){
		let { intl, dept, major } = this.props,
			selected_list_name = 'selected_majors_for_dept_'+dept.id;

		// if it's a dept (not major) rendering this component AND this dept has majors selected, hide the degree options for this dept
		if( (dept && !major) && intl[selected_list_name] && intl[selected_list_name].length > 0 ) return 'hidden';

		return '';
	},

	render(){
		let { dispatch, dept, major, _toggle, open } = this.props,
			deptOrMajor = major || dept,
			toggleClass = open ? 'open' : '',
			doesDeptHaveMajorClass = this._doesDeptHaveMajor();

		return (
			<div className="dept-dropdown-container">

				<div className="dept-dropdown">
					<div>
						<div className="close" onClick={ this._removeItem }><div>&#10006;</div></div>
						<div className="name">
							<Tooltip
								tipStyling={ styles.tip }
								toolTipStyling={ styles.tooltip }
								customText={deptOrMajor.name || ''}>
									<div>{deptOrMajor.name || ''}</div>
							</Tooltip>
						</div>
						{ _toggle && <div className={"arrow-wrapper "+toggleClass} onClick={ _toggle }>
											<div className="arrow" />
										</div> }
					</div>
				</div>

				<div className={"degree-container "+doesDeptHaveMajorClass}>
					{ DEGREE_TYPES.map((cam) => <DegreeCheckbox key={cam.name} degree={cam} {...this.props} />) }
				</div>

			</div>
		);
	}
});

const DegreeCheckbox = createReactClass({
	_editCampusType(e){
		let { dispatch, dept, major } = this.props,
			deptOrMajor = major || dept,
			name = e.target.getAttribute('name');

		deptOrMajor[name] = !deptOrMajor[name]; // save the opposite of what this campus type originally was

		dispatch( editCampusType(deptOrMajor) );
	},

	render(){
		let { dispatch, intl, degree, dept, major } = this.props,
			deptOrMajor = major || dept,
			ident = 'dept_'+deptOrMajor.id+'_degree_'+degree.value+'_is_'+(major ? 'major' : 'dept');

		return (
			<div className="degree-type-box">
				<label htmlFor={ ident }>{ degree.label || '' }</label>
				<input
					id={ ident }
					type="checkbox"
					name={ degree.name || '' }
					value={ ''+degree.value || '' }
					onChange={ this._editCampusType }
					checked={ !!deptOrMajor[degree.name] } />
			</div>
		);
	}
});

const styles = {
	tooltip: {
		fontSize: '14px',
		color: '#797979',
		border: 'none',
		margin: '0px',
		width: 'initial',
		height: 'initial',
		verticalAlign: 'initial',
		borderRadius: 'initial',
		textAlign: 'left',
		textOverflow: 'ellipsis',
	    whiteSpace: 'nowrap',
	    overflow: 'hidden',
	    display: 'block',
	},
	tip: {
		padding: '5px 10px',
	}
};
