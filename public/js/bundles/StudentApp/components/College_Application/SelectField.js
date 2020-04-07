// /College_Application/Contact_Info

import React from 'react'
import selectn from 'selectn'

import { updateProfile, changedFields } from './../../actions/Profile'

class SelectField extends React.Component {
	constructor(props) {
		super(props)
		this._update = this._update.bind(this)
		this._validate = this._validate.bind(this)
	}

	_update(e){
		let { dispatch, _profile, field } = this.props,
			name = field.name,
			val = e.target.value;

		// if allowed to select multiple, val should be new array of values
		if( field.select_multiple ){
			if( _.isFinite(+val) ) val = +val; // convert to number if val is a finite value
			val = _.get(_profile, name) ? _.uniq([..._profile[name], val]) : [val]; // don't allow duplicates to be added
		}

		if(_profile[name] != val && typeof _profile[name] != 'undefined' && typeof field.name != 'Object')
			dispatch(changedFields(field.name));

		if(field.name === 'in_college'){
			//if current edu level changes, reset school
			dispatch( updateProfile({
				schoolName: '',
				schoolName_valid: false,
			}) );
		}

		dispatch( updateProfile({
			[name]: val,
			[name+'_valid']: !!val,
		}) );
	}

	_validate(e){
		let { dispatch, _profile, field } = this.props,
			name = field.name,
			val = e.target.value;

		dispatch( updateProfile({[name+'_valid']: !!val}) );
	}

	componentDidMount(){
		if (this.selectField.value) {
	    	let event = { target: this.selectField };
	    	this._update(event);
    	}
	}

	componentDidUpdate(prevProps){
		let { _profile, field } = this.props,
			{ _profile: _oldProfile } = prevProps;

		// Special case for sponsor option for now, causes unwanted behavior 
		if ( field.name == 'sponsor_will_pay_option' && _profile[field.name] && _oldProfile[field.name] != _profile[field.name]) {
	    	let event = { target: this.selectField };
	    	this._update(event);
    	}
	}

	render(){
		let { _profile, field } = this.props,
			val = selectn(field.name, _profile),
			not_valid_and_required = !_profile[field.name+'_valid'] && field.err;

		// if val is falsy and is either null or undefined make val empty string b/c react components cannot have a value = null/undefined
		// else if val is a digit/number, convert to int b/c 0 can be a valid value
		// else if val is not a number and not a truthy value, then just set it to empty string
		if( !val && (_.isNull(val) || _.isUndefined(val)) ) val = '';
		else if( _.isFinite(+val) && val !== '0.00' ) val = +val;
		else if( _.isArray(val) ) val = val.slice().pop(); // if array (for fields that can have multiple selected) us last index as val

		return (
			<label htmlFor={ field.name }>
				{ field.label }

				<select
					ref={ (field) => this.selectField = field }
					id={ field.name }
					className={ not_valid_and_required ? 'err' : '' }
					name={ field.name }
					value={ val }
					onBlur={ this._validate }
					onChange={ this._update } >

						<option key={field.name+'_default'} value='' disabled="disabled">{ field.placeholder || 'Select from list...' }</option>
						{ field.options.map(o => <option key={o.id} value={o.id}>{o.name}</option>) }

				</select>

				{ not_valid_and_required && <div className="field-err select">{field.err}</div> }
			</label>
		);
	}
}
export default SelectField;