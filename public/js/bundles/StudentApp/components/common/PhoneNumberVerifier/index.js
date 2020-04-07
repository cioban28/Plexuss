// /common/PhoneNumberVerifier/index.js

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import $ from 'jquery' 

import { updateProfile, verifyPhone, getCountries } from './../../../actions/Profile'

import './styles.scss'

class PhoneNumberVerifier extends React.Component {
	constructor(props) {
		super(props)
		this.state = {
			countries: [],
			open: false,
		}
		this._closeList = this._closeList.bind(this)
		this._verify = this._verify.bind(this)
		this._getPhoneNumName = this._getPhoneNumName.bind(this)
	}

	_closeList(e){
		let target = $(e.target),
			phoneName = this._getPhoneNumName();
		if( !target.hasClass('_pv'+phoneName) ) this.setState({open: false});
	}

	_verify(e, fromNP){
		let { dispatch, _profile } = this.props,
		pn = this._getPhoneNumName(),
		
		pcode = pn+'_code',
		val = !!e ? e.target.value : (_profile[pn] || ''),
		code_val = _profile[pcode] || '';
		
		if( fromNP ){
			val = fromNP[pn];
			code_val = fromNP[pcode];
		}	

		let phone = {[pn]: val},
			code = {[pcode]: code_val};
		dispatch( verifyPhone(phone, code) );
	}

	_getPhoneNumName(){
		let { alternate } = this.props;
		return alternate || 'phone';
	}

	componentWillMount(){
		let { dispatch, _profile } = this.props;

		if( !_profile.init_countries_done ) dispatch( getCountries() );
		else this.state.countries = _profile.countries_list.slice();

		if( _profile.init_done ) this._verify(null);

		document.addEventListener('click', this._closeList);
	}

	componentWillUnmount(){
		document.removeEventListener('click', this._closeList);
	}

	componentWillReceiveProps(np){
		let { _profile } = this.props;

		if( _profile.init_countries_done !== np._profile.init_countries_done && np._profile.init_countries_done ){
			this.state.countries = np._profile.countries_list.slice();
		}
	
		// as soon as profile data init is done, verify phone number
		// if( _profile.init_done !== np._profile.init_done && np._profile.init_done ){
		// 	let phoneName = this._getPhoneNumName(),
		// 		codeName = phoneName+'_code',
		// 		phone = np._profile[phoneName],
		// 		code = np._profile[codeName];

		// 	this._verify(null, {[phoneName]: phone, [codeName]: code});
		// }
	}

	render(){
		let { _profile, alternate } = this.props,
			{ countries, open } = this.state,
			phoneName = this._getPhoneNumName(),
			phoneClass = '',
			invalid = !_profile[phoneName+'_valid'] && _.isBoolean(_profile[phoneName+'_valid']),
			phoneError = selectn(phoneName+'_error', _profile);

		if( phoneError || invalid ) phoneClass = 'err';

		return (
			<div id="_PhoneNumberVerifier">
				<div className={"code _pv"+phoneName} onClick={ () => this.setState({open: !open}) }>
					<div className={"_pv"+phoneName}>{ '+'+(_profile[phoneName+'_code'] || '') } <div className={"arrow _pv"+phoneName} /></div>
				</div>

				{ open && <div className={"country-list _pv"+phoneName}>
							{ countries.map((c) => <CountryCode key={c.id} country={c} pn={phoneName} {...this.props} />) }
						</div> }

				<input
					id="_phoneVerifier"
					type="text"
					className={ phoneClass }
					name={ phoneName }
					value={ selectn(phoneName, _profile) || '' }
					placeholder="Enter phone number..."
					onChange={ this._verify } />

				{ phoneError && <div className="err">Not a valid phone number. Make sure your country code is correct.</div> }

				{ (invalid && !_profile[phoneName]) && <div className={"field-err text"}>Cannot leave phone empty</div> }

			</div>
		);
	}
}

class CountryCode extends React.Component {
	constructor(props) {
		super(props)
		this._updateCode = this._updateCode.bind(this)
	}
	_updateCode(){
		let { dispatch, _profile, country, pn } = this.props,
			phone = {[pn]: _profile[pn]},
			code = {[pn+'_code']: country.code};

		dispatch( verifyPhone(phone, code) );
	}

	render(){
		let { country, pn } = this.props;

		return (
			<div onClick={ this._updateCode }>
				<div className={'country_flag '+country.abbr.toLowerCase()} />
				{ country.name } (+{country.code})
			</div>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		_profile: state._profile,
	};
};

export default connect(mapStateToProps)(PhoneNumberVerifier);