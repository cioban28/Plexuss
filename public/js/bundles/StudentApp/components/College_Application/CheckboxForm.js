// /College_Application/RadioForm.js

import React from 'react'
import CheckboxField from './CheckboxField'

class CheckboxForm extends React.Component {
	render(){
		let { _profile, checkb } = this.props,
			invalid = !_profile[checkb.name+'_valid'] && _.isBoolean(_profile[checkb.name+'_valid']);

		return (
			<div id={checkb.name} className={"checkboxform "+(invalid ? 'invalid' : '')}>
				<div>{checkb.label}</div>

				{ checkb.fields.map(f => <CheckboxField key={f.name+f.id} field={f} {...this.props} />) }

				{ invalid && <div className={"field-err "+(checkb.field_type || 'text')}>{ checkb.err }</div> }
			</div>
		);
	}
}

export default CheckboxForm;