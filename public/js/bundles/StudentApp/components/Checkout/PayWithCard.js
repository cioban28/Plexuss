// /Checkout/PayWithCard.js

import React from 'react'
import selectn from 'selectn'
import ReactSpinner from 'react-spinjs'
import Tooltip from './../common/Tooltip'

import { spinjs_config } from './../../utils/spinjs_config'
import { createOrUpdateCustomer, getCountries, readyToCheckout, updateCreditCard } from './../../actions/Checkout'

const MONTHS = 12;
const YEARS = 20;

export default React.createClass({
	getInitialState(){
		return {
			month_options: [],
			year_options: [],
			country_options: [],
		};
	},

	componentWillMount(){
		let { dispatch, _checkout: _c } = this.props;

		this._buildMonthOptions();
		this._buildYearOptions();

		if( !_c.init_countries ) dispatch( getCountries() );
		else this._buildCountryOptions( _c.countries );
	},

	componentWillReceiveProps(np){
		let { _checkout: _c } = this.props;

		// if next state is different from this state AND next state saved is true, trigger toastr
		if( _c.init_countries !== np._checkout.init_countries && np._checkout.init_countries ){
			this._buildCountryOptions(np._checkout.countries);
		}
	},

	_buildCountryOptions(countries){
		var opts = [<option key="disabled" disabled="disabled" value="">Country...</option>];

		_.each(countries, (c) => opts = [...opts, <option key={c.id} value={c.id}>{c.country_name}</option>]);

		this.setState({country_options: opts});
	},

	_buildMonthOptions(){
		var opts = [<option key="disabled" disabled="disabled" value="">Month...</option>];

		for (var i = 1; i <= MONTHS; i++) {
			let month = i < 10 ? '0'+i : i;
			opts = [...opts, <option key={'month-'+month} value={month}>{month}</option>];
		}

		this.state.month_options = opts;
	},

	_buildYearOptions(){
		var opts = [<option key="disabled" disabled="disabled" value="">Year...</option>],
			date = new Date(),
			currentYr = date.getFullYear();

		for (var i = 0; i <= YEARS; i++) {
			opts = [...opts, <option key={'year-'+currentYr} value={currentYr}>{currentYr}</option>];
			currentYr++;
		}

		this.state.year_options = opts;
	},

	_update(e){
		let { dispatch } = this.props,
			name = e.target.getAttribute('name'),
			value = e.target.value;

		let card = {[name]: value},
			valid = this._preValidation(e);

		if( valid ) dispatch( updateCreditCard(card) );
	},

	_preValidation(e){
		let name = e.target.getAttribute('name'),
			value = e.target.value;

		let valid = this._validate(name, value, true);
		
		return valid;
	},

	_blurValidation(e){
		let name = e.target.getAttribute('name'),
			value = e.target.value;

		this._validate(name, value);
	},

	_formValid(){
		let { _checkout: _c } = this.props;

		if( !_c.active_credit_card ) return false;

		let c = _c.active_credit_card;

		return c.name && c.number && c.cvc && c.exp_month && c.exp_year;
	},

	_validate(name, value, pre){
		var valid = false, msg = '';

		switch( name ){
				// if( !value || ( value && !value.includes(' ') ) ) valid = true;
				// msg = 'Names cannot include a space.';
				// valid = true;
				// break;
			case 'cvc':
				if( value && value.length <= 4 && _.isFinite(+value) ) valid = true;
				if( pre && !value ) valid = true; // if is a prevalidation, it's ok to be empty in case user wants to clear field
				msg = 'Security code is a 3-4 digit code - no letters allowed.';
				break;

			case 'name':
			case 'exp_month':
			case 'exp_year':
			case 'country':
				if( value ) valid = true;
				if( pre && !value ) valid = true;
				msg = 'This field cannot be left empty.';
				break;

			case 'number':
				if( value && _.isFinite(+value) ) valid = true; // only allow numerical digits to be entered
				if( pre && !value ) valid = true; // if is a prevalidation, it's ok to be empty in case user wants to clear field
				msg = 'Credit/Debit card number cannot be empty.';
				break;

			// case 'address_zip':
			// case 'address_country':
			// 	if( value ) valid = true;
			// 	break;
			default: 
				valid = true;
				break;
		}

		this.setState({
			[name+'Valid']: valid,
			[name+'Validated']: true,
			[name+'ErrMsg']: msg,
		});
		
		return valid;
	},

	_continue(e){
		e.preventDefault();

		let { _checkout: _c } = this.props,
			form = {..._c.active_credit_card};

		Stripe.card.createToken(form, this._stripeResponseHandler);
	},

	_stripeResponseHandler(status, res){
		let { dispatch, _checkout: _c } = this.props,
			updated_card = {
				..._c.active_credit_card, 
				...res.card,
				stripeToken: res.id,
				type: res.type,
			};

		if( status === 200 ){
			this.setState({stripeErr: false});
			dispatch( createOrUpdateCustomer(updated_card) );
			
		}else{
			this.setState({
				stripeErr: true,
				stripeErrMsg: selectn('error.message', res) || 'There was an error processing your card with Stripe.',
			});
		}
	},

	render(){
		let { dispatch, _checkout: _c } = this.props,
			{ month_options, year_options, country_options,
				nameValid, nameValidated, nameErrMsg,
				numberValid, numberValidated, numberErrMsg,
				exp_monthValid, exp_monthValidated, exp_monthErrMsg,
				exp_yearValid, exp_yearValidated, exp_yearErrMsg,
				cvcValid, cvcValidated, cvcErrMsg,
				address_countryValid, address_countryValidated, address_countryErrMsg,
				address_zipValid, address_zipValidated, address_zipErrMsg,
				stripeErr, stripeErrMsg } = this.state,
				formValid = this._formValid();
		
		return (
			<div className="payment-method-form">

				<form onSubmit={ this._continue } className={ selectn('ready_to_checkout', _c) ? 'hide' : '' }>
					<hr />

					<div className="row collapse">
						<div className="column small-12">
							<label>
								Name as shown on credit/debit card
								<input 
									onChange={ this._update }
									onBlur={ this._blurValidation }
									value={ selectn('active_credit_card.name', _c) || '' }
									className={ nameValidated && !nameValid && 'err' }
									placeholder="Name as shown on credit/debit card"
									name="name"
									type="text" />
							</label>

							{ (nameValidated && !nameValid) && <div className={'err-msg'}>{ nameErrMsg }</div> }
						</div>
					</div>

					<div className="row collapse">
						<div className="column small-12 medium-6">
							<div className="buffer">
								<label>
									Credit or debit card number
									<input 
										onChange={ this._update }
										onBlur={ this._blurValidation }
										value={ selectn('active_credit_card.number', _c) || '' }
										className={ numberValidated && !numberValid && 'err' }
										placeholder="Credit/Debit card number"
										name="number"
										type="text" />
								</label>
								{ (numberValidated && !numberValid) && <div className={'err-msg'}>{ numberErrMsg }</div> }
							</div>
						</div>

						<div className="column small-12 medium-6">
							<div className="row collapse">
								<div className="column small-12 medium-7">
									<div className="buffer lt">
										<label className="exp-label">
											<div>Expiration Date</div>
											<select 
												onChange={ this._update }
												onBlur={ this._blurValidation }
												value={ selectn('active_credit_card.exp_month', _c) || '' }
												className={ exp_monthValidated && !exp_monthValid && 'err' }
												name="exp_month">
													{ month_options }
											</select>
											<select 
												onChange={ this._update }
												onBlur={ this._blurValidation }
												value={ selectn('active_credit_card.exp_year', _c) || '' }
												className={ exp_yearValidated && !exp_yearValid && 'err' }
												name="exp_year">
													{ year_options }
											</select>
										</label>
										{ (exp_monthValidated && !exp_monthValid || exp_yearValidated && !exp_yearValid) && 
											<div className={  'err-msg' }>{ exp_monthErrMsg || exp_yearErrMsg }</div> }
									</div>
								</div>

								<div className="column small-12 medium-5">
									<div className="buffer sc">
										<label>
											Security Code
											<div className="tip-relative">
												<input 
													onChange={ this._update }
													onBlur={ this._blurValidation }
													onFocus={ this._preValidation }
													value={ selectn('active_credit_card.cvc', _c) || '' }
													className={ cvcValidated && !cvcValid && 'err' }
													placeholder="Ex: 123"
													name="cvc"
													type="text" />

												<div className="tip-wrapper">
													<Tooltip customClass="checkout">
														<div><b>CVC</b></div>
														<div>The Card Security Code is located on the back of MasterCard, Visa and Discover credit or debit cards and is typically a separate group of 3 digits to the right of the signature strip.</div>
														<br />
														<img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/settings/creditcard.png' alt='CVC image' />
													</Tooltip>
												</div>
											</div>
										</label>

										{ (cvcValidated && !cvcValid) && <div className={'err-msg'}>{ cvcErrMsg }</div> }
									</div>
								</div>
							</div>
						</div>
					</div>

					<div className="row collapse">
						<div className="column small-12 medium-6">
							<div className="buffer">
								<label>
									Country
									<select 
										onChange={ this._update }
										value={ selectn('active_credit_card.address_country', _c) || '' }
										className={ address_countryValidated && !address_countryValid && 'err' }
										name="address_country">
											{ country_options }
									</select>
								</label>
								{ (address_countryValidated && !address_countryValid) && <div className={'err-msg'}>{ address_countryErrMsg }</div> }
							</div>
						</div>

						<div className="column small-12 medium-6">
							<div className="buffer lt">
								<label>
									Billing Postal Code
									<input 
										onChange={ this._update }
										value={ selectn('active_credit_card.address_zip', _c) || '' }
										className={ address_zipValidated && !address_zipValid && 'err' }
										placeholder="Billing Postal Code"
										name="address_zip"
										type="text" />
								</label>
								{ (address_zipValidated && !address_zipValid) && <div className={'err-msg'}>{ address_zipErrMsg }</div> }
							</div>
						</div>
					</div>

					<div className={"stripe-err "+(stripeErr ? '' : 'hide')}>{ stripeErrMsg }</div>

					<div className="form-actions">
						<button
							className="checkout-btn"
							disabled={ !formValid || _c.create_pending }>
								{ _c.create_pending ? <div className="spin-wrap continue"><ReactSpinner config={spinjs_config} /></div> : 'Continue to Checkout' }
						</button>

						<div className="with-icon stripe" />
					</div>

				</form>	

			</div>
		);
	}
});
