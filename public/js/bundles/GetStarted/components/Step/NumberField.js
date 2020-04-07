// /College_Application/NumberField.js

import React, { Component } from 'react'
import selectn from 'selectn'
import _ from 'lodash'
import { updateProfile, changedFields } from './../../actions/Profile'
import store from '../../../stores/getStartedStore'

class NumberField extends Component{
	constructor(props) {
		super(props)
		this.state = {
			has_focus: false,
		}
		this._update = this._update.bind(this)
		this._validateNumber = this._validateNumber.bind(this)
	}

	_update(e){
		let { field } = this.props,
			val = parseFloat(e.target.value);
		this._validateNumber(val); // if field has err msg, then it needs to be validated
	}

	_validateNumber(val){
		let { field, readOnly, _profile } = this.props,
			name = field.name,
			valid = false;

		if ( (!field.min && !field.max) ) { 
			valid = true;
		} else if (val == null || val === '') {
			valid = false;
		} else {
			valid = ( val != null && (val >= field.min && val <= field.max) );
		}

		store.dispatch( updateProfile({
			[field.name]: val,
			[field.name+'_valid']: valid,
		}) );

		if(_profile[name] != val && typeof _profile[name] != 'undefined' && typeof field.name != 'Object')
			store.dispatch(changedFields(field.name));

	}
	componentWillReceiveProps(newProps){
		let { _profile, field, set_focus, readOnly } = this.props;

		// Manual validation on input clear
		if (!readOnly && newProps._profile[field.name] == null && newProps._profile[field.name] != _profile[field.name]){
			store.dispatch( updateProfile({ [field.name+'_valid']: false }) );
		}
	}

	componentDidMount(){
		// Force validation on mount
    	let event = { target: this.numberInput };
    	this._update(event);
	}

	componentDidUpdate(prevProps){
		let { _profile, field } = this.props,
			{ _profile: _prevProfile } = prevProps; 

		// Force validation on update as well since it may be updated indirectly
		if ( _profile[field.name] && _profile[field.name] != _prevProfile[field.name]) {
	    	let event = { target: this.numberInput };
	    	this._update(event);
    	}
	}

	render(){
		let { _profile, field, disableFocus, set_focus, readOnly } = this.props,
			{ has_focus } = this.state,
			valid = selectn(field.name+'_valid', _profile),
			err_msg = selectn(field.name+'_err', _profile),
			val = _.get(_profile, field.name, '');

		if ( readOnly ) valid = true; // No need to validate if input is ready only.

		if( disableFocus && !set_focus ) has_focus = disableFocus;

		if ( set_focus && !disableFocus ) has_focus = set_focus;

		// convert to string as long as it's not null/undefined and a number
		if( !_.isUndefined(val) && !_.isNull(val) && _.isFinite(+val) ) val = ''+val;

		return (
			<label htmlFor={ field.name }>
				{ field.label }

				<input
					id={ field.name }
					type={'number'}
					className={ _.isBoolean(valid) && (!valid ? 'err' : '') }
					name={ field.name || '' }
					placeholder={ field.placeholder || '' }
					ref={ (input) => this.numberInput = input }
					pattern={ field.pattern || '' }
					value={ val || '' }
					readOnly={ readOnly }
					step={ field.step }
					onFocus={ e => this.setState({has_focus: true}) }
					onBlur={ e => this.setState({has_focus: false}) }
					onChange={ this._update } />

				{ (_.isBoolean(valid) && !valid && has_focus) && <div className="err-msg">{ field.err }</div> }
			</label>
		);
	}
}

export default NumberField;