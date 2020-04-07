// conditionalRadioFields.js

import React from 'react'
import { editHeaderInfo } from './../../../../../actions/internationalActions'
import createReactClass from 'create-react-class'

const INPUTS = [
	{val: 'yes'},
	{val: 'no'},
	{val: 'unknown'}
];

export default createReactClass({
	_updateConditional(e){
		let { dispatch, customFieldName } = this.props,
			condition = {},
			dispatchMethod = editHeaderInfo,
			name = e.target.getAttribute('name');

		// use customFieldName if set
		if( customFieldName ) condition[customFieldName] = e.target.value;
		else condition[name] = e.target.value;

		dispatch( dispatchMethod(condition) );
	},

	render(){
		let { intl, field, inputs, customFieldName } = this.props,
			fieldName = customFieldName ? customFieldName : intl.activeProgram+'_'+field.name,
			_inputs = inputs || INPUTS;

		return (
			<div className="conditional-radio-container">
				<label className="conditional-radio">
					<div className="label-name">{ field.label }</div>

					{ _inputs.map((inp) => <label key={ inp.val }>
												<input
													type="radio"
													value={ inp.val }
													checked={ intl[fieldName] === inp.val }
													onChange={ this._updateConditional }
													name={ fieldName } />
												<span>{ inp.label || inp.val }</span>
											</label>) }

					{ field.directions && <div className="directions">{field.directions}</div> }

				</label>
			</div>
		);
	}
});
