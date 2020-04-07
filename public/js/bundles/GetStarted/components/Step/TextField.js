// /College_Application/TextField.js

import React, {Component} from 'react'
import selectn from 'selectn'
import _ from 'lodash'

import { updateProfile, changedFields } from './../../actions/Profile'
import store from '../../../stores/getStartedStore'
class TextField extends Component{
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
		let { field, _profile } = this.props,
			valid = true, // set valid to true by default in case field doesn't need to be validated
			name = field.name,
			val = e.target.value;

		if( field.type === 'number' ) valid = this._validateNumber(+val); // if field has err msg, then it needs to be validated
		else valid = this._validateText(val);
		
		if(_profile[name] != val && typeof _profile[name] != 'undefined' && typeof field.name != 'Object')
			store.dispatch(changedFields(field.name));


		store.dispatch( updateProfile({
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

	_validate(e){
		let { field } = this.props,
			{ value } = e.target;

		let valid = field.type === 'number' ? this._validateNumber(+value) : this._validateText(value);

		this.state.has_focus = false;
		store.dispatch( updateProfile({[field.name+'_valid']: valid}) );
	}

	componentWillReceiveProps(newProps){
		let { _profile, field } = this.props;

		// Manual validation on input clear
		if (newProps._profile[field.name] == null && newProps._profile[field.name] != _profile[field.name]){
			store.dispatch( updateProfile({ [field.name+'_valid']: true }) );
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

				{ not_valid_and_required && <div className={"field-err "+(field.type || 'text')}>{ field.err }</div> }
			</label>
		);
	}
}

export default TextField;