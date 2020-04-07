// pickDepartment.js

import React from 'react'
import selectn from 'selectn'
import createReactClass from 'create-react-class'

import ConditionalRadioFields from './../../admission/components/conditionalRadioFields'

import { DEPT_OPTIONS, MAJOR_OPTIONS, DEPT_OPT, MAJOR_OPT, DEGREE_TYPES } from './../../constants'
import { getAllDepts, getMajorsFor, addRemoveIntlDept, addRemoveIntlMajor, setMajorOption } from './../../../../../actions/internationalActions'

const DEFAULT_NAME = 'department';

export default createReactClass({
	getInitialState(){
		return {
			options_ui: null
		}
	},

	componentWillMount(){
		let { dispatch, intl, major } = this.props;

		/*
			if major is passed, get majors for selected dept
			else if we already have all_depts (coming from alumni component) just build options
			else get all depts and then build
		*/
		if( major ){
			let majors_list_name = 'all_majors_for_dept_'+major.dept_id;

			// if majors list has already been created, just proceed to building options for select field
			if( intl[majors_list_name] ){
				this._buildOptions(intl[majors_list_name]);
			}else{
				// else get majors for specific dept
				dispatch( getMajorsFor(major.dept_id) );
			}

		}else if( intl.all_depts ) this._buildOptions(intl.all_depts);
		else dispatch( getAllDepts() );
	},

	componentWillReceiveProps(np){
		let { intl, major } = this.props;

		/*
			if major is passed, check if we now have majors for said dept
			else if check if we now have depts
		*/
		if( major ){
			let majors_init = 'init_done_for_dept_'+major.dept_id,
				majors_list_name = 'all_majors_for_dept_'+major.dept_id;

			if( intl[majors_init] !== np.intl[majors_init] ) this._buildOptions(np.intl[majors_list_name]);

		}else if( intl.all_depts !== np.intl.all_depts ) this._buildOptions(np.intl.all_depts);
	},

	_buildOptions(list){
		var { major } = this.props,
			text = major ? 'major' : 'department',
			ui = [<option key={'disabled'} value="" disabled="disabled">{'Select '+text+'...'}</option>];

		_.each(list, (item) => ui = [...ui, <option key={item.id+'_'+item.name} value={item.id}>{item.name}</option>] );

		this.setState({options_ui: ui});
	},

	_addItem(e){
		let { dispatch, intl, major } = this.props,
			id = e.target.value;

		if( major ){
			let major_with_dept = this._getMajorObj(id, major.dept_id);
			dispatch( addRemoveIntlMajor(major_with_dept) );
		}else{
			dispatch( addRemoveIntlDept( this._getDeptObj(id) ) );
		}
	},

	_getDeptObj(id){
		let { intl } = this.props,
			dept = _.find(intl.all_depts, {id: +id});

		dept = this._initDegreeTypes(dept);

		return dept;
	},

	_getMajorObj(major, dept){
		let { intl } = this.props,
			all_majors_list = intl['all_majors_for_dept_'+dept],
			_major = _.find(all_majors_list, {id: +major});

		_major = this._initDegreeTypes(_major);
		_major.dept_id = dept;

		return _major;
	},

	_initDegreeTypes(obj){
		_.each(DEGREE_TYPES, (deg) => {
			obj[deg.name] = true;
		});

		return obj;
	},

	_getMajorOptionName(){
		let { intl, major } = this.props;
		return 'option_for_dept_majors_'+major.dept_id;
	},

	render(){
		let { dispatch, intl, major } = this.props,
			{ options_ui } = this.state,
			fields = major ? MAJOR_OPT : DEPT_OPT,
			options = major ? MAJOR_OPTIONS : DEPT_OPTIONS,
			_customDispatch = major ? setMajorOption : null,
			major_field = major ? '_'+major.dept_id : '',
			program_field = major ? intl[this._getMajorOptionName()] : intl[fields.name+major_field],
			custName = major ? this._getMajorOptionName() : null;

		return (
			<div className="pickDeptMajor_container">

				<div className="pick_dropdown_name">{ selectn('name', major) || DEFAULT_NAME }:</div>

				<ConditionalRadioFields
					field={ fields }
					inputs={ options }
					customFieldName={ custName }
					{...this.props} />

				<div className={ program_field === 'all' ? 'hide' : '' }>
					<div className="select-multiple-label">You can select multiple options, just click to add</div>
					<select
						onChange={ this._addItem }
						name={ selectn('name', major) || DEFAULT_NAME }
						value={''}>
							{ options_ui }
					</select>
				</div>

			</div>
		);
	}
});
