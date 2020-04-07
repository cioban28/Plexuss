// /College_Application/TextField.js

import React from 'react'
import selectn from 'selectn'

import { updateProfile, changedFields } from './../../actions/Profile'

class TextField extends React.Component {
	constructor(props) {
		super(props)
		this.state = {
			has_focus: false,
		}
		this._update = this._update.bind(this)
		this._validate = this._validate.bind(this)
		this._validateNumber = this._validateNumber.bind(this)
		this._validateText = this._validateText.bind(this)
	}

	_update(e){
		let { dispatch, field, _profile } = this.props,
			valid = true, // set valid to true by default in case field doesn't need to be validated
			name = field.name,
			val = e.target.value;

		if( field.field_type === 'email' ) valid = this.validateEmailAddress(val); // if field has err msg, then it needs to be validated
		else valid = this._validateText(val);

		if(_profile[name] != val && typeof _profile[name] != 'undefined' && typeof field.name != 'Object')
			dispatch(changedFields(field.name));


		dispatch( updateProfile({
			[field.name]: val,
			[field.name+'_valid']: valid,
		}) );
	}

	_validateNumber(val){
		let { field } = this.props;

		if( val >= field.min && val <= field.max ) return true;
		return false;
	}

	_validateText(val){
		return !!val;
	}

	validateEmailAddress(email) {
	    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	    return re.test(String(email).toLowerCase());
	}

	_validate(e){
		let { dispatch, field } = this.props,
			{ value } = e.target;

		let valid = field.field_type === 'email' ? this.validateEmailAddress(value) : this._validateText(value);

		this.state.has_focus = false;
		dispatch( updateProfile({[field.name+'_valid']: valid}) );
	}

	componentWillReceiveProps(newProps){
		let { _profile, field, dispatch } = this.props;

		// Manual validation on input clear
		if (newProps._profile[field.name] == null && newProps._profile[field.name] != _profile[field.name]){
			dispatch( updateProfile({ [field.name+'_valid']: true }) );
		}
	}

	render(){
		let { _profile, field, readOnly } = this.props,
			{ has_focus } = this.state,
			valid = selectn(field.name+'_valid', _profile),
			err_msg = selectn(field.name+'_err', _profile),
			val = _.get(_profile, field.name, ''),
			not_valid_and_required = _profile[field.name+'_valid'] != null && !_profile[field.name+'_valid'] && field.err;
		return (
			<label htmlFor={ field.name }>
				{ field.label }

				<input
					id={ field.name }
					type={ field.type || 'text' }
					className={ not_valid_and_required ? 'err' : '' }
					name={ field.name || '' }
					placeholder={ field.placeholder || '' }
					value={ val || '' }
					onFocus={ () => this.setState({has_focus: true}) }
					onBlur={ this._validate }
					onChange={ this._update }
					readOnly={ readOnly } />

				{ field.field_type !== 'email' && not_valid_and_required && <div className={"field-err "+(field.type || 'text')}>{ field.err }</div> }
        { field.field_type === 'email' && not_valid_and_required && <div className={"field-err "+(field.type || 'text')}>{ !!val ? 'Not a valid email address' : field.err }</div> }
			</label>
		);
	}
}

export default TextField;
