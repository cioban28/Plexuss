// /College_Application/ExamForm.js

import React from 'react'
import selectn from 'selectn'

import TextField from './TextField'
import NumberField from './NumberField'
import CheckboxField from './CheckboxField'

import { updateProfile } from './../../actions/Profile'

export default class extends React.Component {
	constructor(props){
		super(props);

		this._clearExamFields = this._clearExamFields.bind(this);
	}

	_clearExamFields(event){
		let { _profile, type, dispatch } = this.props,
			{ name, fields } = _profile[type + '_active_exam'],
			_fields = fields,
			tmpObj = {};

		if( selectn('is_pre_2016_sat', _profile) && name === 'SAT' ) _fields = _profile[type + '_active_exam'].alternateFields;
		else if( selectn('is_pre_2016_psat', _profile) && name === 'PSAT' ) _fields = _profile[type + '_active_exam'].alternateFields;

		_.each(_fields, field => {
			tmpObj[field.name] = null;
		});

		dispatch( updateProfile({ ...tmpObj }) );
	}

	render(){
		let { _profile, type } = this.props;

		let { name, fields } = _profile[type + '_active_exam'],
			_fields = fields,
			already_set_focus = false;

		if( selectn('is_pre_2016_sat', _profile) && name === 'SAT' ) _fields = _profile[type + '_active_exam'].alternateFields;
		else if( selectn('is_pre_2016_psat', _profile) && name === 'PSAT' ) _fields = _profile[type + '_active_exam'].alternateFields;

		return (
			<div className="exam-form">
				{ selectn(type + '_active_exam.conditionalQuestion', _profile) && <CheckboxField field={ _profile[type + '_active_exam'].conditionalQuestion } {...this.props} /> }
				<div className="sub-head">{ name }</div>
				<div>
					{ _fields.map((f, index) => {
						// Logic to cycle focus on invalid entries
						let set_focus = !already_set_focus && _profile[f.name + '_valid'] != null && !_profile[f.name + '_valid'];
						if (set_focus) { already_set_focus = true; }

						switch( f.type ){
							case 'number': return <NumberField key={f.name} set_focus={set_focus} field={f} {...this.props} />;
							default: return <TextField key={f.name} field={f} {...this.props} />;
						}
					}) }
					<span className='clear-exam-fields-btn' onClick={ this._clearExamFields }>clear</span>
				</div>
			</div>
		);
	}
}