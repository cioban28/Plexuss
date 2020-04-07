// costField.js

import React from 'react'
import createReactClass from 'create-react-class'

import { editHeaderInfo } from './../../../../../actions/internationalActions'

export default createReactClass({
	getInitialState(){
		return {
			valid: false,
			haveTested: false,
		};
	},

	_edit(e){
		let { dispatch, cost, customDispatch } = this.props,
			value = {}, is_valid = false, action = null;

		is_valid = this._determineValidationMethod(e);

		//if valid entry or it's empty, dispatch to update value, else preventDefault
		if( is_valid || !e.target.value ){
			// only need to run _modify func if using _validateNumber
			value[e.target.id] = !cost.validateType ? this._modifyIfAmountStartsWithZero(e.target.value) : e.target.value;

			action = customDispatch ? customDispatch(value) : editHeaderInfo(value);
			dispatch( action );
		}
	},

	_modifyIfAmountStartsWithZero(val){
		//if val length is larger than 1 and starts with a zero (ex: 099 -> 99), then remove 0, else just return val
		if( val.length > 1 && val.indexOf('0') === 0 ) return +val;
		return val;
	},

	_determineValidationMethod(e){
		let { cost } = this.props;

		switch( cost.validateType ){
			case 'text': return this._validateText(e.target.value);
			case 'required_text': return this._validateText(e.target.value, true);
			case 'name': return this._validateName(e.target.value);
			case 'required_name': return this._validateName(e.target.value, true);
			case 'gpa': return this._validateGPA(e.target.value);
			case 'link': return this._validateLink(e.target.value);
			default: return this._validateNumber(e.target.value);
		}
	},

	_validateName(name, required){
		var valid = false,
			err_msg = 'Only letters, hyphen, and spaces are allowed';

		/*
			** alphabet letters and - and space only **
			if required, use the opposite of the required value, so if
			required = true, then making it false forces name to have to be true in order for the whole condition to be true
			else if required = false, then making it true with the || makes it so name doesn't have to be valid
		*/
		if( (/^[A-Za-z -]+$/.test(name) || !name) && (!required || name) ) valid = true;

		if( required ) err_msg += ' and cannot be left empty.';

		this._updateState(valid, err_msg);
		return valid;
	},

	_validateText(text, required){
		// no validation so far for text - field is optional so checking if field is empty doesn't make sense
		var valid = false,
			err_msg = '';

		// if required, text cannot be empty, else can be empty
		if( !required || text ) valid = true;

		if( required ) err_msg = 'Cannot be left empty.';

		this._updateState(valid, err_msg);
		return valid;
	},

	_validateGPA(gpa){
		var valid = false;

		// if gpa is a number between 0 and 4
		if( _.isFinite(+gpa) && +gpa >= 0 && +gpa <= 4.0 && gpa.length <= 4 ) valid = true;

		this._updateState(valid, 'GPA values accepted: 0 - 4.00 with 2 decimal places max.');
		return valid;
	},

	_validateLink(link){
		var valid = false;

		// not required so empty field is valid or link include http, then also valid
		if( !link || link.includes('http') || link.includes('.com') ) valid = true;

		this._updateState(valid, 'Invalid link. Must include http(s) and/or .com in the link.');
		return true;
	},

	_validateNumber(val){
		var { cost } = this.props,
			valid = false,
			min = cost.min || 0,
			max = cost.max || 999999;

		//if val is a number and between 0 and 1 million, then it's valid
		if( _.isFinite(+val) && +val >= min && +val <= max ) valid = true;

		this._updateState(valid, 'Number inputs only. Range is '+min+' - '+max+'.');
		return valid;
	},

	_updateState(valid, err){
		this.setState({
			valid: valid,
			err_msg: err,
			haveTested: true,
		});
	},

	render(){
		let { dispatch, intl, cost, custom_class, customDispatch, rootObj, useProgram, customProgram } = this.props,
			{ valid, haveTested, err_msg } = this.state,
			cost_id = '';

		intl = rootObj ? rootObj : intl;
		cost_id = customDispatch ? cost.name : intl.activeProgram+'_'+cost.name;

		if( customDispatch && useProgram ){
			let program = customProgram || intl.activeProgram;
			cost_id = program+'_'+cost.name;
		}

		return (
			<div className={(custom_class || '')+' parent'}>
				<label htmlFor={ cost_id } className={custom_class}>

					{ cost.sub_label && <div className="sub-label">{cost.sub_label}</div> }

					<div className="cost-field-label">{ cost.label }</div>

					<input
						id={ cost_id }
						type="text"
						name={ cost_id }
						className={ !valid && haveTested ? 'cost-input error' : 'cost-input' }
						onChange={ this._edit }
						onFocus={ this._edit }
						onBlur={ this._edit }
						placeholder={ cost.placeholder || "..." }
						value={ intl[cost_id] || '' } />

					{ ( cost.showPercentage && intl.undergrad_num_of_applicants && intl[cost_id] ) ?
						<div className="perc-view">{ cost.showPercentage } <span>{ Math.ceil((+intl[cost_id] / +intl[intl.activeProgram+'_num_of_applicants'])*100)+'%' }</span></div>
						: null }

					{ !valid && haveTested ? <div className="has-error">{err_msg}</div> : null }
				</label>
			</div>
		);
	}
});
