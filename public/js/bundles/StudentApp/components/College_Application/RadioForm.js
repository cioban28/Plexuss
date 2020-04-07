// /College_Application/RadioForm.js

import React from 'react'
import TextField from './TextField'
import RadioField from './RadioField'
import SelectField from './SelectField'
import Tooltip from './../common/Tooltip'
import DatePickr from './../common/DatePickr'

import { updateProfile } from './../../actions/Profile'

export default class RadioForm extends React.Component{
	constructor(props) {
		super(props)
	}
	render(){
		let { _profile, radio } = this.props,
			trigger = radio.trigger || 'yes',
			field_val = _profile[radio.name.split('__')[1]],
			dependents = radio['dependents_'+field_val] || radio.dependents,
			invalid = !_profile[radio.name+'_valid'] && _.isBoolean(_profile[radio.name+'_valid']);

		if( trigger === 'any' && field_val ) trigger = _profile[radio.name.split('__')[1]];

		return (
			<div id={radio.name} className={ (radio.nested ? 'nested-'+radio.nested : '') + (invalid ? ' radioform invalid' : '') }>
				<div>
					{radio.label}

					{ radio.tip && 
						<Tooltip customClass="addtl_question">
							{ radio.tip.map(t => <div key={t}>{t}</div>) }
						</Tooltip>}
				</div>

				{ radio.fields.map(f => <RadioField key={f.name+f.id} field={f} {...this.props} />) }

				{ invalid && <div className={"field-err text"}>{ radio.err }</div> }

				{ (dependents && field_val === trigger) && 
					<div className="radio-dependent">
						{ dependents.map(d => {
							switch(d.field_type){
								case 'date': return <DatePickr 
														key={d.name}
														_label={d.label}
														_side="_left"
														_action={ updateProfile }
														_state={ _profile }
														_format={'YYYY-MM-DD'}
														_name={d.name} />;

								case 'text': return <TextField key={d.name} field={d} {...this.props} />;
								case 'select': return <SelectField key={d.name} field={d} {...this.props} />;
							}
						}) }
					</div> 
				}
			</div>
		);
	}
}