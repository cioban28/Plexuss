// GetStarted_Step6_Component.jsx
var percentage;

function wasRedirected(){
    return JSON.parse(sessionStorage.getItem('college_id'));
};

function currentPercentage(pct){
    if(pct) percentage = pct;
    return percentage;
};

var styles = {
	bad: {
		border: '1px solid firebrick',
	},
	good: {
		border: '1px solid skyblue',
	},
	bad_code: {
		borderTopColor: 'firebrick',
		borderLeftColor: 'firebrick',
		borderBottomColor: 'firebrick',
	},
}

var GetStarted_Step6_Component = React.createClass({
	getInitialState: function(){
		return {
			save_route: '/get_started/save',
			get_route: '/get_started/getDataFor/step',
			confirm_code_route: '/get_started/checkPhoneConfirmation',
			resend_code_route: '/get_started/sendPhoneConfirmation',
			new_phone_num_route: '/get_started/saveNewPhone',
			validate_with_twilio_route: '/get_started/validatePhone',
			step_num: null,
			is_valid: false,
			is_sending: false,
			back_route: null,
			next_route: null,
			save_btn_classes: 'right btn submit-btn text-center',
			save_has_been_clicked: !1,
			needConfirmation: false,
			_zipValid: true,
			confirmationCode: '',
			_formValid: false,
			country_list: [],
			unique_countries: [],
			states: [],
			openDialingCodes: false,
		};
	},

	componentWillMount: function(){	
		var classes = this.state.save_btn_classes, prev, next, num,
			Carousel = null, carou = [], _this = this;

		// Facebook event tracking
        fbq('track', 'GetStarted_Step6_PhoneNumber_Page');

		//get current step num
		this.state.step_num = $('.gs_step').data('step');
		this.state.get_route += this.state.step_num;

		//build prev step route
		num = parseInt(this.state.step_num);
		prev = num - 1;
		next = num + 1;

		this.state.back_route = '/get_started/'+prev;
		this.state.next_route = '/get_started/'+next;

		this._getData();
		this._getCountries();
		this._getStates();
	},

	_getData: function(){
		var _this = this;

		$.ajax({
			url: '/get_started/getDataFor/step6',
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(data) {
			if( data && _.isObject(data) && !_.isEmpty(data) ){
				_this._initData(data);
			}
		});
	},

	_initData: function(data){
		var valid = false, dataForValidation = {};

		this.setState( _.extend({}, data) );

		for( var prop in data ){
			if( data.hasOwnProperty(prop) && prop !== 'txt_opt_in' && prop !== 'dialing_code' ){
				dataForValidation.id = '_'+prop;
				dataForValidation.name = prop;
				dataForValidation.value = data[prop];
				this._validate(dataForValidation);
			}
		}
	},

	_getStates: function(){
		var _this = this;

		$.ajax({
			url: '/get_started/getDataFor/states',
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(data) {
			_this._initStates(data);
		});
	},

	_initStates: function(data){
		this.setState({states: data});
	},

	_getCountries: function(){
		var _this = this;

		$.ajax({
			url: '/get_started/getDataFor/country',
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(data) {
			_this._initCountries(data);
		});
	},

	_initCountries: function(data){
		// var unique_countries_by_country_codes = _.uniq(data, function(obj){
		// 	return obj.country_phone_code;
		// });

		this.setState({
			unique_countries: data,
			country_list: data
		});
	},

	_save: function(e){
		e.preventDefault();

		var _this = this, s = this.state, formData = new FormData( $('form')[0] );

		if( s.enableConfirmationCode ){
			this.setState({is_sending: true});

			$.ajax({
				url: s.save_route,
				type: 'POST',
				data: formData, 
				enctype: 'multipart/form-data',
				contentType: false,
	        	processData: false,
	        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			}).done(function(data){
				//if an object is returned, it's an error
				if( data && _.isObject(data) && !_.isNumber(data) ){
					_this.setState({is_sending: false, err: data.error, errMsg: data.msg});//remove loader
				}else{
                    amplitude.getInstance().logEvent('step4_completed', {content: 'Contact Info'} );
                    
					currentPercentage(data);
					var txt_opt = $('#_text').is(':checked');
					if( txt_opt ){
						_this.setState({is_sending: false, needConfirmation: true});//remove loader
						_this._resendCode();
					}else{
						window.location.href = s.next_route;
					}
				}

				if( wasRedirected() ){
					_this.setState({is_sending: !1});//remove loader
					// $(document).trigger('saved');

				}else if( s.needConfirmation ){
					window.location.href = s.next_route;	
				}
			});
		}
	},

	_keyPressed: function(e){
		if( e.charCode < 48 || e.charCode > 57 ) e.preventDefault();
	},

	_resetConfirmation: function(){
		this.setState({needConfirmation: false});
	},

	_validate: function(e){
		var newState = {}, valid = false,
			id = e.target ? e.target.id : e.id, //if e has target prop, it's coming from event, else e is an object created in _initData
			name = e.target ? e.target.getAttribute('name') : e.name,
			value = e.target ? e.target.value : e.value;

		switch( name ){
			case 'phone':
				valid = true; // phone no longer required
				break;

			case 'address':
				if( /^[a-zA-Z0-9\.,#\- ]+$/.test(value) && value ) valid = true;
				 break;

			case 'city':
				if( /^[a-zA-Z\.\- ]+$/.test(value) && value ) valid = true;
				break;

			case 'country':
				// if changing country from other country (or first time changing) then validate state also
				if( +value === 1 ){
					valid = true; // country is valid if here

					// if state is empty while country is US, make invalid
					if( !this.state.state ){
						this.state._stateValid = false;
						this.state._stateValidated = true;
					}

				}else if( value ) valid = true;

				value = +value;
				break;

			case 'state':
				// state is only required for US students only - optional for all international
				if( this.state.country === 1 ){
					if( value ) valid = true;

				}else valid = true;

				break;

			case 'zip':
				//if zip has a value, validate it, otherwise empty is ok b/c it's not required
				if( value )	{
					if( /^[a-zA-Z0-9\.,\- ]+$/.test(value) ) valid = true;
					else valid = false;
				}else{
					valid = false;
				}

				break;

			case 'code':
				if( value && value.length === 4 && _.isNumber(+value) && (+value >= 1000 && +value <= 9999) ) valid = true;
				break;

			default: return;
		}

		this.state[name] = value;
		this.state[id+'Valid'] = valid;
		this.state[id+'Validated'] = true;
		this.state.focused = id;

		this._enableNextIfFormIsValid();
	},

	_validatePhone: function(e){
		this.setState({phone: e.target.value});
		this._validateWithTwilio(e.target.value);
	},

	_validateWithTwilio: function(phone, country_code){
		var _this = this, s = this.state, 
			phoneNum = '',
			countryCode = '',
			full_phone_num = '';

		phoneNum = phone || s.phone;
		countryCode = country_code || s.dialing_code;
		full_phone_num = countryCode+phoneNum;

		$.ajax({
            url: '/phone/validatePhoneNumber',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {phone: full_phone_num},
            type: 'POST'
        }).done(function(data){
			//if data is an object and has an error property
			if( data && _.isObject(data) && _.has(data, 'error') ){
				var valid = false, 

				valid = !data.error;

				if( valid ){
					//remove country phone code from phoneNumber
					var split = data.phoneNumber.substr(data.country_phone_code.length, data.phoneNumber.length);
					var formatted = data.country_phone_code + ' ' + split;

					_this.state.formatted_phone = formatted;
				}

				_this.state['_phoneValid'] = valid;
				_this.state['_phoneValidated'] = true;
				_this.state.focused = '_phone';

				_this._enableNextIfFormIsValid();
			}
		});
	},

	_enableNextIfFormIsValid: function(){
		var s = this.state, valid = false, newState = {};

		if( !s.needConfirmation ){
			if( s._countryValid && s._addressValid && s._cityValid && s._stateValid && s._zipValid ) valid = true;
			newState.enableConfirmationCode = valid;
		}else{
			if( s._confirmationCodeValid ) valid = true;
			newState.enableNextStep = valid;
		}

		this.setState(newState);
	},

	_toggleDialingCodes: function(){
		this.setState({openDialingCodes: !this.state.openDialingCodes});
	},

	_updateCode: function(country){
		var code = country.country_phone_code;

		this.setState({dialing_code: code, openDialingCodes: false});
		this._validateWithTwilio(null, code);
	},

	_toNextPage: function(){
		var txt_opt = $('#_text').is(':checked');
		if( !txt_opt ){
			window.location.href = this.state.next_route;
		}else{
			this._resendCode();
			this.setState({err: false, needConfirmation: true});
		}
	},

	_closeDifferentCountryModal: function(e){
		this.setState({err: false});
	},

	_togglePhoneEditor: function(){
		this.setState({openPhoneEditor: !this.state.openPhoneEditor});
	},

	_confirmCode: function(e){
		e.preventDefault();
		var _this = this, s = this.state, formData = new FormData( $('form')[0] ), _data = null;

		this.setState({is_sending: true});

		$.ajax({
			url: s.confirm_code_route,
			type: 'POST',
			data: formData, 
			enctype: 'multipart/form-data',
			contentType: false,
        	processData: false,
        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(data){
			_data = JSON.parse(data);
			if( _data && _data.response === 'success' ){
				_this.setState({is_sending: false});
				window.location.href = s.next_route;
			}
			else{
				_this.setState({confirmation_err: true, confirmation_msg: _data.response, is_sending: false, resend_done: true});
				_this._showConfirmErrMsg();
			}
		});
	},

	_showConfirmErrMsg: function(){
		var _this = this;

		setTimeout(function(){
			_this.setState({confirmation_err: false});
		}, 10000);
	},

	_resendCode: function(e){
		if(e) e.preventDefault();
		var _this = this, s = this.state, _data = null, msg = '';

		this.setState({is_sending: true});

		$.ajax({
			url: s.resend_code_route,
			type: 'POST',
			data: {phone: s.phone, dialing_code: s.dialing_code}, 
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(data){
			try {
				_data = JSON.parse(data);
			} catch(e) {
				var stringify = data.match(/{.*}/) && data.match(/{.*}/).join('');
                _data = JSON.parse(stringify);
			}
			msg = _data.response === 'failed' ? 'Failed to send phone authentication code to this number. Please input your correct phone number and country calling number.' : _data.response;
			if (_data.response == 'failed' && _data.error_message && _data.error_message.includes('reached the maximum')) {
				msg = _data.error_message;
			}
			_this.setState({
				is_sending: false, 
				resend_done: false, 
				resend_msg: msg, 
				confirmation_err: false
			});
			_this._showResendMsg();
		});
	},

	_showResendMsg: function(){
		var _this = this;

		setTimeout(function(){
			_this.setState({resend_done: true});
		}, 20000);
	},

	_skipConfirmationCode: function(){
		window.location.href = this.state.next_route;
	},

	_saveNewNumber: function(e){
		e.preventDefault();
		var _this = this, s = this.state, _data = null;

		this.setState({is_sending: true});

		$.ajax({
			url: s.new_phone_num_route,
			type: 'POST',
			data: {phone: s.phone, dialing_code: s.dialing_code}, 
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(data){
			if( data && data.response === 'success' ){
				_this.setState({
					is_sending: false, 
					newNumberSaved: true, 
					newNumberMsg: data.msg, 
					openPhoneEditor: false, 
				});
				_this._showNewNumberMsg();
			}
		});
	},

	_showNewNumberMsg: function(){
		var _this = this;

		setTimeout(function(){
			_this.setState({newNumberSaved: false});
		}, 10000);
	},

	render: function(){
		var s = this.state, _this = this;

		return (
			<div className="step_container">
				<div className="row phone-container">
					<div className="column small-12">
						<form onSubmit={ this._save }>
							<input type="hidden" name="step" value={s.step_num} />		
						{
							!s.needConfirmation ?
							<div className="row">
								<h3 className="columns small-12 phone-title">Colleges need a way to communicate with you</h3>

									{/* <div className="text-container">
										<input 
											id="_text" 
											ref="_txt"
											type="checkbox" 
											name="txt_opt_in"
											defaultChecked={ false }
											className="text-check" />

										<label htmlFor="_text" className="text-consent">
											I consent to receive text messages from Plexuss and Universities on the Plexuss network. 
											<a href="/privacy-policy" target="_blank">Privacy Policy</a>
										</label>
									</div> */}

								<div className="columns small-12 medium-4">
									<label htmlFor="_country">{'Country'}</label>
								</div>

								<div className="columns small-12 medium-8">
									<select 
										id="_country" 
										onChange={ this._validate }
										onFocus={ this._validate }
										value={ s.country || '' }
										style={ s.focused === '_country' ? (s._countryValid ? styles.good : styles.bad) : (s._countryValidated && !s._countryValid ? styles.bad : {}) }
										name="country">
											<option value="" disabled="disabled">{'Select your country...'}</option>
											{ s.country_list.map(function(country){ return <option key={country.id} value={country.id}>{country.country_name}</option> }) }
									</select>
								</div>

								<div className="columns small-12 medium-4">
									<label htmlFor="_address">{'Address'}</label>
								</div>

								<div className="columns small-12 medium-8">
									<input 
										id="_address" 
										name="address" 
										type="text" 
										onChange={ this._validate }
										onFocus={ this._validate }
										value={ s.address || '' }
										style={ s.focused === '_address' ? (s._addressValid ? styles.good : styles.bad) : (s._addressValidated && !s._addressValid ? styles.bad : {}) }
										placeholder="Enter address" />
								</div>

								<div className="columns small-12 medium-4">
									<label htmlFor="_city">{'City'}</label>
								</div>

								<div className="columns small-12 medium-8">
									<input 
										id="_city" 
										type="text" 
										name="city" 
										onChange={ this._validate }
										onFocus={ this._validate }
										value={ s.city || '' }
										style={ s.focused === '_city' ? (s._cityValid ? styles.good : styles.bad) : (s._cityValidated && !s._cityValid ? styles.bad : {}) }
										placeholder="Enter city" />
								</div>

								<div className="columns small-12 medium-4">
									<label htmlFor="_state">{'State/Province'}</label>
								</div>

								<div className="columns small-12 medium-4">
									{
										s.country && s.country == 1 ?
										<select
											id="_state" 
											type="text" 
											name="state" 
											onChange={ this._validate }
											onFocus={ this._validate }
											value={ s.state || '' }
											style={ s.focused === '_state' ? (s._stateValid ? styles.good : styles.bad) : (s._stateValidated && !s._stateValid ? styles.bad : {}) } >
												<option value='' disabled="disabled">{'Select state...'}</option>
												{ s.states.map(function(state){ return <option key={state.state_abbr} value={state.state_abbr}>{state.state_name}</option> }) }
										</select>
										:
										<div>
											<input
												id="_state" 
												type="text" 
												name="state" 
												onChange={ this._validate }
												onFocus={ this._validate }
												value={ s.state || '' }
												style={ s.focused === '_state' ? (s._stateValid ? styles.good : styles.bad) : (s._stateValidated && !s._stateValid ? styles.bad : {}) }
												placeholder="Enter state/province" />
										</div>
									}
									
								</div>

								<div className="columns small-12 medium-1 text-center">
									<label htmlFor="_zip">{'Zip'}</label>
								</div>

								<div className="columns small-12 medium-3">
									<input 
										id="_zip" 
										type="text" 
										name="zip" 
										onChange={ this._validate }
										onFocus={ this._validate }
										value={ s.zip || '' }
										style={ s.focused === '_zip' ? (s._zipValid ? styles.good : styles.bad) : (s._zipValidated && !s._zipValid ? styles.bad : {}) }
										placeholder="Zip" />
								</div>

								<div className="columns small-12 medium-6 phone-back">
									<a href={s.back_route}>Go Back</a>
								</div>

								<div className="columns small-12 medium-6 medium-text-right">
									<button 
										className="phone-submit radius button" 
										disabled={ !s.enableConfirmationCode } >
											Next
									</button>
								</div>

							</div>
							:
							<div className="row text-center text-wrapper">
								<div className="text-title">{"We've sent you an SMS code to"}</div>
								{
									s.openPhoneEditor ?
									<div className="phone-editor-container">
										<div className="row collapse">
											<div className="columns small-9">
												<input 
													id="_phone" 
													name="phone" 
													type="text" 
													className="phone-num"
													onChange={ this._validatePhone }
													onFocus={ this._validatePhone }
													value={ s.phone || '' }
													style={ s.focused === '_phone' ? (s._phoneValid ? styles.good : styles.bad) : (s._phoneValidated && !s._phoneValid ? styles.bad : {}) }
													placeholder="Enter phone number" />

												<div className="dialing-codes-btn editing">
													<span onClick={ this._toggleDialingCodes }>{ s.dialing_code ? '+'+s.dialing_code : '' }</span>
													<div className="arrow" onClick={ this._toggleDialingCodes }></div>
												</div>
												{ s.openDialingCodes ? 
													<div className="dialing-codes-container editing">
														{ s.unique_countries.map(function(country){ return <DialingCode key={country.id+'_'+country.country_code} country={country} updateCode={_this._updateCode} />}) }
													</div>
													:
													null
												}

											</div>
											<div className="columns small-3">
												<button className="button save-edited-phone" onClick={ this._saveNewNumber }>Save</button>
											</div>

											{ s._phoneValidated && !s._phoneValid ? 
												<div className="columns small-12 text-left" style={{color: 'firebrick', margin: '-24px 0 0'}}>
													<small>Invalid phone number. Is your country code correct?</small>
												</div> 
												: null }
										</div>
									</div>
									:
									<div className="text-phone">{s.phone ? '+'+s.dialing_code+' '+s.phone : ''}</div>
								}

								{ s.newNumberSaved && _.isBoolean(s.newNumberSaved) ? <div className="new-phone-success">{s.newNumberMsg || ''}</div> : null }

								<div className="text-center"><div className="text-changenum change cursor" onClick={this._togglePhoneEditor}><u>{'Change Number'}</u></div></div>
								<div className="text-options">
									<div className="text-changenum text-left">To complete your phone number verification, please enter your 4 digit code below.</div>
									<input 
										id="_confirmationCode"
										type="text" 
										name="code"
										value={ s.code || '' }
										maxLength={4} 
										onFocus={ this._validate }
										onChange={ this._validate }
										style={ s.focused === '_confirmationCode' ? (s._confirmationCodeValid ? styles.good : styles.bad) : (s._confirmationCodeValidated && !s._confirmationCodeValid ? styles.bad : {}) }
										onKeyPress={ this._keyPressed } />
								</div>
								<div className="text-center"><div className="text-changenum cursor" onClick={ this._resendCode }><u>Resend code</u></div></div>

								{ _.isBoolean(s.resend_done) && !s.resend_done ? <div className="err">{s.resend_msg || ''}</div> : null }
								{ _.isBoolean(s.confirmation_err) && s.confirmation_err ? <div className="err">{s.confirmation_msg || ''}</div> : null }

								<div className="text-options">
									<div>
										<button 
											disabled={ !s.enableNextStep }
											onClick={ this._confirmCode }
											className="phone-submit text-submit radius button">
												Confirm
										</button>
									</div>
									<div className="clearfix">
										<div className="left cursor" onClick={ this._resetConfirmation }>Go back</div>
										<SkipModal skip={ this._skipConfirmationCode } />
									</div>
								</div>
							</div>							
						}
						</form>
					</div>
				</div>

				<DifferentCountryErrorModal open={ s.err } closeModal={ this._closeDifferentCountryModal } toNext={ this._toNextPage } />

				{ s.is_sending ? <Loader /> : null }
			</div>
		);
	}
});

var DialingCode = React.createClass({
	_update: function(){
		this.props.updateCode(this.props.country);
	},

	render: function(){
		var country = this.props.country;

		return (
			<div className="codes" onClick={ this._update }>
				<div className={'flag flag-'+country.country_code.toLowerCase()}></div>
				<div className="country-name">{country.country_name+' (+'+country.country_phone_code+')'}</div>
			</div>
		);
	}
});

var DifferentCountryErrorModal = React.createClass({
	_close: function(){
		this.props.closeModal();
	},

	_next: function(){
		this.props.toNext();
	},

	render: function(){
		return ( this.props.open ) ? 
			<div className="skip-modal">
				<div className="skip-container">
					<div className="close text-right" onClick={ this._close }>x</div>
					<div className="title">We have noticed your country phone number differs from your country of origin. Make sure your country of origin is reflected properly on Plexuss.</div>
					<div className="text-center">
						<button
							onClick={ this._next }
							className="skip-btn button radius">
								Ok
						</button>
					</div>
				</div>
			</div>
			:
			null
	}
});

var SkipModal = React.createClass({
	getInitialState: function(){
		return {
			open: false,
		};
	},

	_skip: function(){
		this.props.skip();
	},

	_toggle: function(){
		this.setState({open: !this.state.open});
	},

	render: function(){
		var s = this.state;

		return (
			<div 
				className="right"
				onClick={ this._toggle }>
					<span className="cursor">Skip</span>
					{
						s.open ?
						<div className="skip-modal">
							<div className="skip-container">
								<div className="close text-right" onClick={ this._toggle }>x</div>
								<div className="title">By clicking Skip, you are opting out of receiving texts from colleges. You can always opt back in by going to your settings and allowing text notifications.</div>
								<div className="text-center">
									<button
										onClick={ this._skip }
										className="skip-btn button radius">
										Ok
									</button>
								</div>
							</div>
						</div>
						:
						null
					}
			</div>
		);
	}
});

var Loader = React.createClass({
	render: function(){
		var classes = 'gs-loader ';
		if( this.props.size ) classes += this.props.size;

		return(
			<div className={classes}>
				<svg width="70" height="20">
                    <rect width="20" height="20" x="0" y="0" rx="3" ry="3">
                        <animate attributeName="width" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="height" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="x" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="y" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
                    </rect>
                    <rect width="20" height="20" x="25" y="0" rx="3" ry="3">
                        <animate attributeName="width" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="height" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="x" values="35;25;25;25;35" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="y" values="10;0;0;0;10" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                    </rect>
                    <rect width="20" height="20" x="50" y="0" rx="3" ry="3">
                        <animate attributeName="width" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="height" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="x" values="60;50;50;50;60" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="y" values="10;0;0;0;10" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                    </rect>
                </svg>
			</div>
		);
	}
});

ReactDOM.render( <GetStarted_Step6_Component />, document.getElementById('get_started_step6') );