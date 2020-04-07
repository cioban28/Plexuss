// GetStarted_Step2_Component.jsx
import { Provider } from 'react-redux'
import store from './stores/getStartedStore'

var percentage;

function wasRedirected(){
    return JSON.parse(sessionStorage.getItem('college_id'));
};

function currentPercentage(pct){
    if(pct) percentage = pct;
    return percentage;
};

var GetStarted_Step2_Component = React.createClass({displayName: "GetStarted_Step2_Component",
	getInitialState: function(){
		return {
			save_route: '/get_started/save',
			get_route: '/get_started/getDataFor/step',
			step_num: null,
			is_valid: false,
			is_sending: false,
			back_route: null,
			next_route: null,
			save_btn_classes: 'right btn submit-btn text-center',
			save_has_been_clicked: !1,
			user_info: {},
		};
	},

	componentWillMount: function(){
		var classes = this.state.save_btn_classes, prev, next, num, _this = this;

		// Facebook event tracking
        fbq('track', 'GetStarted_Step1_GeneralInfo_Page');

		//get current step num
		this.state.step_num = $('.gs_step').data('step');
		this.state.get_route += this.state.step_num;

		//build prev step route
		num = parseInt(this.state.step_num);
		prev = num - 1;
		next = num + 1;
		this.state.back_route = '/get_started/'+prev;
		this.state.next_route = '/get_started/';

		$.ajax({
			url: this.state.get_route,
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(data) {
			_this.initUser(data);
		});
	},

	initUser: function(data){
		var valid = false;

		// check user type
		for( var prop in data ){
			if( data.hasOwnProperty(prop) && prop.indexOf('is_') > -1 ){
				if( +data[prop] ) valid = true;
			}
		}

		if( data.college_grad_year || data.hs_grad_year ) valid = true;
		else valid = false;	

		valid = !!(data.current_school_id || data.school_name);

		this.state.is_valid = valid;
		this.setState({user_info: data});
	},

	updateUser: function(e){
		var val = e.target.value, target = $(e.target),
			user = _.extend({}, this.state.user_info), prop = 'is_';

		if( target.attr('name') === 'user_type' ){
			prop += val;
			user['is_student'] = 0;
			user['is_alumni'] = 0;
			user['is_parent'] = 0;
			user['is_counselor'] = 0;
			user['is_university_rep'] = 0;
			user[prop] = 1;
		}else if( target.attr('name') === 'home_schooled' ){
			user[target.attr('name')] = target.is(':checked');
		}else if( target.attr('name') === 'edu_level' ){
			user.edu_level = val === 'hs' ? 0 : 1;
		}else{
			//else it's the country select field
			user.country_id = parseInt(target.val());
		}
		
		this.setState({user_info: user});
	},

	save: function(e){
		var formData = new FormData( $('form')[0] ), state = this.state, _this = this;

        var user_type = $('select[name=user_type]').val();

		if( $(e.target).hasClass('disable') ) e.preventDefault();
		//track if save btn has already been clicked
		if( !state.save_has_been_clicked ) state.save_has_been_clicked = !0;

		if( this.formIsValid() ){
			this.setState({is_sending: !0});

			$.ajax({
				url: state.save_route,
				type: 'POST',
				data: formData, 
				enctype: 'multipart/form-data',
				contentType: false,
	        	processData: false,
	        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			}).done(function(data){
                amplitude.getInstance().logEvent('step1_completed', {content: 'Basic Info'} );
                
				if(data) currentPercentage(data);

				if( wasRedirected() ){
					_this.setState({is_sending: !1});//remove loader
					$(document).trigger('saved');
                    
				} else if (user_type !== 'student') {
                    window.location.href = '/profile/edit_public';

                } else window.location.href = state.next_route;
			});
		}
	},

	formIsValid: function(){
		var inputs = $('form .is-input'), valid = !0, _this = this, val = '';
		$.each(inputs, function(){
			val = $(this).val();
			if ($(this).attr('name') === 'school') {
                if ($('#is_home_schooled').is(':checked')) {
                    return true;
                } else {
                    if (!val) {
                        valid = !1;
                        _this.setState({is_valid: valid}); //set state to false to show error msg
                        return !1;
                    }
                }
            } else if( !val ){ //if value is emtpy then make and return false
				valid = !1;
				_this.setState({is_valid: valid}); //set state to false to show error msg
				return !1;
			}
		});

		//when valid change state to true to remove error msg
		_this.setState({is_valid: valid});

		return valid;
	},

	makeSaveActive: function(e){
		if( this.formIsValid() ) this.setState({is_valid: !0});
	},

	getUserType: function(){
		var user = this.state.user_info,
			type = 'is_student', //default to student
			prop;

		if( user ){
			for(prop in user ){
				if( user.hasOwnProperty(prop) && prop.indexOf('is') !== -1 ){
					if( parseInt(user[prop]) ) return prop;
				}
			}
		}

		return type;
	},

	getGradYr: function(){
		var user = this.state.user_info;
		if( user && parseInt(user.in_college) <= 1 ) return parseInt(user.in_college) ? +user.college_grad_year : +user.hs_grad_year;
		return '';
	},

	checkForEnterKey: function(){
		var _this = this;

		$('.submit-btn').on('keydown', function(e){
			if( e.which === 13 ) $(this).trigger('click');
		});
	},

	_setEmail: function(_email){
		var user = _.extend({}, this.state.user_info, {email: _email});
		this.setState({user_info: user});
	},

	render: function(){
		var saveBtnClasses = '', user = this.state.user_info,
			intro = user ? ['Tell us a little about yourself, ', React.createElement("span", {key: 0}, user.fname), '...'] : 'Tell us a little about yourself...',
			type = user ? this.getUserType() : 'is_student',
			zip = user ? (user.zip || '') : '',
			edu = user && _.isNumber(+user.in_college) ? +user.in_college : '',
            gender = user ? user.gender : '',
			school = user ? (user.school_name || '') : '',
			school_id = user ? (user.current_school_id || '') : '',
			grad = user ? this.getGradYr() : '',
			country = user ? +user.country_id : '',
			hm_school = user && user.home_schooled ? user.home_schooled : !1,
			is_us = !0,
            is_in_college = null;

		if( user && _.isNumber(user.edu_level) ) is_in_college = user.edu_level;
		else if( user && _.isNumber(user.in_college) ) is_in_college = user.in_college;
		else is_in_college = !1;

		if( user && ''+user.country_id !== '1' ) is_us = !1;

		if( !this.state.is_valid ) saveBtnClasses = 'right btn submit-btn text-center disable';
		else saveBtnClasses = 'right btn submit-btn text-center';

		return (
			React.createElement("div", {className: "step_container"}, 
				 user && !_.isEmpty(user) && !user.email ? React.createElement(GetEmail, {data: this.state, setEmail: this._setEmail}) : null, 
	 			
	 				user && !_.isEmpty(user) && user.email ?
	 				React.createElement("div", {className: "row"}, 
						React.createElement("div", {className: "column small-12 medium-7"}, 
							React.createElement("div", {className: "intro"}, intro), 
							React.createElement("div", null, React.createElement("small", null, "There are a few questions we need in order to get you started with Plexuss.")), 
							React.createElement("br", null), 
							React.createElement("form", null, 
								React.createElement("input", {type: "hidden", name: "step", value: this.state.step_num, isValid: this.makeSaveActive}), 	
								React.createElement(SelectInput, {name: "user_type", isValid: this.makeSaveActive, val: type, update: this.updateUser}), 
								React.createElement("br", null), 
								React.createElement(SelectInput, {name: "gender", isValid: this.makeSaveActive, val: gender, update: this.updateUser}), 
								React.createElement("br", null), 
								/* is_us ? <TextInput name="zip" isValid={this.makeSaveActive} val={zip} /> : null */
								/* is_us ? <br /> : null */
								React.createElement(SelectInput, {name: "edu_level", isValid: this.makeSaveActive, val: edu, usertype: type, update: this.updateUser}), 
                                React.createElement("br", null), 
								React.createElement(SelectInput, {name: "grad_yr", isValid: this.makeSaveActive, val: grad}), 
                                 !is_in_college ? React.createElement(CheckboxInput, {name: "home_schooled", isValid: this.makeSaveActive, val: hm_school, update: this.updateUser}) : null, 
                                 is_in_college ? React.createElement("br", null) : null, 
                                 !hm_school || is_in_college ? React.createElement(TextInput, {name: "school", isValid: this.makeSaveActive, val: school, sId: school_id}) : null, 
                                 !hm_school || is_in_college ? React.createElement("br", null) : null, 
								 !this.state.is_valid && this.state.save_has_been_clicked ? React.createElement("div", {className: "err"}, React.createElement("small", null, "Fields cannot be emtpy.")) : null, 

								React.createElement("div", {className: "submit-row clearfix"}, 
									React.createElement("div", {tabIndex: "0", className: saveBtnClasses, onClick: this.save, onFocus: this.checkForEnterKey}, "Next")
								)
							)
						), 
						
						React.createElement("div", {className: "column small-12 medium-5"}, 
							React.createElement("div", {className: "promo-msg"}, "By completing these steps colleges will be able to recruit you. You will also have the ability to contact colleges directly.")
						)
					)
				: null, 

				 this.state.is_sending ? React.createElement(Loader, null) : null
			)
		);
	}
});

var GetEmail = React.createClass({displayName: "GetEmail",
	getInitialState: function(){
		return {
			email: '',
			emailValid: false,
			emailValidated: false,
			emailTaken: false,
		};
	},

	_saveEmail: function(e){
		e.preventDefault();

		var formData = new FormData( $('#_no_email_submit')[0] ),
			_this = this,
			current_step = this.props.data.step_num;

		if( this.state.emailValid ){
			this.setState({is_sending: true});
			$.ajax({
				url: '/get_started/saveEmail',
				type: 'POST',
				data: formData, 
				enctype: 'multipart/form-data',
				contentType: false,
	        	processData: false,
	        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			}).done(function(ret){
				// reload this step

				if( ret === 'saved' ){
					_this.setState({
						is_sending: false,
						emailTaken: false,
					});
					//doesn't matter where you're coming from, just go to next possible step
					window.location.href = '/get_started';
				}else if( ret === 'taken' ){
					_this.setState({
						is_sending: false,
						emailTaken: true,
					});
				}else console.log('oops');
			});
		}
	},

	_hasEmailNow: function(){
		this.props.setEmail(this.state.email);
	},

	_validateEmail: function(e){
		this.setState({
			email: e.target.value,
			emailValidated: true,
			emailValid: /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(e.target.value),
		});
	},

	render: function(){
		var data = this.props.data,
			email = this.state.email,
			eValid = this.state.emailValid,
			eValidated = this.state.emailValidated,
			emailTaken = this.state.emailTaken;

		return (
			React.createElement("div", {className: "row"}, 
				React.createElement("div", {className: "column small-12"}, 
					React.createElement("div", {className: "intro"}, "Please enter your email address"), 
					React.createElement("br", null), 
					React.createElement("form", {id: "_no_email_submit", onSubmit:  this._saveEmail}, 
						React.createElement("input", {
							id: "_email_step0", 
							className: "", 
							type: "email", 
							name: "email", 
							style:  eValidated ? (eValid ? emailStyles.good : emailStyles.bad) : (emailStyles.good), 
							onChange:  this._validateEmail, 
							value:  email || '', 
							placeholder: "Email Address"}), 

						 emailTaken ? React.createElement("div", {style: emailStyles.taken}, "This email is already taken. Please choose another email.") : null, 

						React.createElement("div", {className: "submit-row clearfix"}, 
							React.createElement("button", {
								className: 'button radius', 
								style: emailStyles.btn, 
								disabled:  !eValid, 
								onClick:  this._saveEmail}, 
									"Next"
							)
						)
					)
				), 

				 this.state.is_sending ? React.createElement(Loader, null) : null
			)
		);
	}
});

var emailStyles = {
	good: {
		maxWidth: '300px',
	},
	bad: {
		maxWidth: '300px',
		border: '1px solid firebrick',
	},
	taken:{
		color: 'firebrick',
		fontSize: '14px',
		margin: '0 0 15px',
	},
	btn: {
		background: '#FF5C26',
		padding: '15px 40px',
	}
};

var CheckboxInput = React.createClass({displayName: "CheckboxInput",
	getInitialState: function(){
		return {
			valu: '',
			options: null,
			checked: !1
		};
	},

	componentWillMount: function(){
		if( this.props.val ) this.state.checked = this.props.val;
	},

	componentWillReceiveProps: function(nextProps){
		//only if TextInput parent passes new val, then setstate with that new value	
		if( nextProps.val !== this.props.val ){
			this.setState({checked: nextProps.val});
		}
	},

	update: function(e){
		this.props.isValid();
		if( this.props.update ) this.props.update(e);
		this.setState({checked: $(e.target).is(':checked')});
	},

	render: function(){
		var checked = this.state.checked;
		return (
			React.createElement("div", {className: "form-container ckbx"}, 
				React.createElement("div", {className: "f-input"}, 
					React.createElement("input", {id: "is_home_schooled", type: "checkbox", name: this.props.name, className: "is-input", 
						onChange: this.update, value: "35829", checked: checked || !1}), 
					React.createElement("label", {htmlFor: "is_home_schooled"}, "Home schooled")
				)
			)
		);
	}
});

var SelectInput = React.createClass({displayName: "SelectInput",
	getInitialState: function(){
		return {
			valu: '',
			options: null
		};
	},

	componentWillReceiveProps: function(nextProps){
		//only if TextInput parent passes new val, then setstate with that new value	
		if( nextProps.val !== this.props.val ){
			this.setState({valu: this.convertVal(nextProps.val)});
		}
	},

	componentWillMount: function(){
        var val = _.isEmpty(this.props.val) ? '' : this.props.val;

		this.getSelectOptions();
		this.setState({
			label: this.getLabel(),
			valu: this.convertVal(val),
		});
	},

	convertVal: function(val){
		if( _.isNumber(val) ){
			if( (''+val).length > 1 ) return val;
			else if(this.props.name === 'country') return val;
			else return val ? 'college' : 'hs';
		}else if(_.isString(val) && val.length == 1) {
            return val;
        }else return _.rest(val.split('_'), 1).join('_');
	},

	getSelectOptions: function(){
		var options = [], this_yr = null, end_yr = null, start_yr = null, countries = null, user;

		options.push(React.createElement("option", {key: 0, value: ""}, 'Select one...'));
		if( this.props.name === 'user_type' ){
			options.push(React.createElement("option", {key: 1, value: "student"}, "Student"));
			options.push(React.createElement("option", {key: 2, value: "alumni"}, "Alumni"));
			options.push(React.createElement("option", {key: 3, value: "parent"}, "Parent or Guardian"));
			options.push(React.createElement("option", {key: 4, value: "counselor"}, "Counselor or Teacher"));
			options.push(React.createElement("option", {key: 5, value: "university_rep"}, "University Rep"));
			this.state.options = options;
		}else if( this.props.name === 'edu_level' ){
			options.push(React.createElement("option", {key: 1, value: "hs"}, "High School"));
			options.push(React.createElement("option", {key: 2, value: "college"}, "College"));
			this.state.options = options;
		}else if( this.props.name === 'country' ){
			this.getDataFor(this.props.name);
        }else if( this.props.name === 'gender' ){
            options.push(React.createElement("option", {key: 1, value: "m"}, "Male"));
            options.push(React.createElement("option", {key: 2, value: "f"}, "Female"));
            this.state.options = options;
		}else{
			this_yr = moment().year();
			start_yr = this_yr - 50;
			end_yr = this_yr + 10;
			for (var i = end_yr; i >= start_yr; i--) {
				options.push(React.createElement("option", {key: i, value: i}, i));
			}
			this.state.options = options;
		}

	},

	getDataFor: function(name){
		var _this = this;
		$.ajax({
			url: '/get_started/getDataFor/'+name,
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(data) {
			_this.buildCountries(data);
		});
	},

	buildCountries: function(data){
		var options = [];

		if( data.length > 0 ){
			options.push(React.createElement("option", {key: 0, value: ""}, 'Select one...'));
			_.each(data, function(obj){
				options.push(React.createElement("option", {key: obj.id, value: obj.id}, obj.country_name));
			});
		}

		this.setState({options: options});
	},

	getLabel: function(){
		var name = this.props.name, type = this.props.usertype;
		if( name === 'user_type' ) return 'I am a(n)...';
		else if( name === 'edu_level' ){
			if( type ){
				if( type.indexOf('student') > -1 ) return 'Current level of education';
				else if( type.indexOf('parent') > -1 ) return 'Your level of education';
				else return 'Highest level of education';
			}
			return 'Current level of education';
		}else if( name === 'country' ){ 
            return 'Your country';
        }else if( name === 'gender' ){
            return 'I am a';
        }else return 'Your year of graduation';
	},

	update: function(e){
		this.props.isValid();
		if( this.props.update ) this.props.update(e);
		this.setState({valu: e.target.value});
	},

	render: function(){
		return (
			React.createElement("div", {className: "form-container"}, 
				React.createElement("div", {className: "f-label"}, 
					React.createElement("label", null, this.state.label)
				), 
				React.createElement("div", {className: "f-input"}, 
					React.createElement("select", {name: this.props.name, className: "is-input", 
							onChange: this.update, value: this.state.valu, refs: this.props.name}, 
						this.state.options
					)
				)
			)
		);
	}
});

var TextInput = React.createClass({displayName: "TextInput",
	getInitialState: function(){
		return {
			valu: '',
			route: '/get_started/searchFor/college',
			list_active: !1,
			schools: [],
			hidden_val: '',
			traversing: !1
		};
	},

	componentWillMount: function(){
		document.addEventListener('click', this.domClick);
		document.addEventListener('keydown', this.keypressed);
		
		if( this.props.name === 'zip' ) this.setState({valu: this.props.val});
	},

	componentWillReceiveProps: function(nextProps){
		//only if TextInput parent passes new val, then setstate with that new value	
		if( nextProps.val !== this.props.val ) this.setState({valu: nextProps.val});
		if( nextProps.sId !== this.props.sId ) this.setState({hidden_val: nextProps.sId});
	},

	keypressed: function(e){
		var key = e.which || e.keyCode;

		if( this.state.schools.length > 0 ){
			var container = $('.results-container'), elem = null, results = container.children();
			
			//if elem is valid
			if( key === 40 ){//down
				if( !$('.result:first-child').hasClass('highlighted') && !this.state.traversing ){
					$('.result.highlighted').removeClass('highlighted'); //just in case - clearing all highlighted ones
					$('.result:first-child').addClass('highlighted');
					this.state.traversing = !0;
				}else $('.result:not(:last-child).highlighted').removeClass('highlighted').next().addClass('highlighted');

				//scroll while traversing
				container.scrollTop( ( $('.result.highlighted').offset().top - container.offset().top ) + container.scrollTop() );
				
			}else if( key === 38 ){ //up key
				$('.result:not(:first-child).highlighted').removeClass('highlighted').prev().addClass('highlighted');
			    container.scrollTop( $('.result.highlighted').offset().top - container.offset().top + container.scrollTop() );
			}else if( key === 13 ) $('.result.highlighted').trigger('click'); //enter key
		}
		
	},

	notTraversing: function(){
		this.state.traversing = !1;
	},

	domClick: function(e){
		if( $(e.target).closest('.results-container').length === 0 ){
			this.setState({schools: []});
		}
	},

	getPlacholder: function(){
		var props = this.props;
		if( props.name === 'zip' ) return 'Zip code';
		else return 'School name';
	},	

	getLabel: function(){
		var name = this.props.name;
		if( name === 'zip' ) return 'Your '+name+' code';
		else return 'Name of your ' + name;
	},

	update: function(e){
		var route = null, _this = this;
		this.props.isValid();

		if( this.props.name === 'school' ){
			this.notTraversing();
			this.state.valu = e.target.value;
			route = this.state.route + '_' + $('select[name="edu_level"]').val();
			$.ajax({
	            url: route,
	            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	            data: {input: e.target.value},
	            type: 'POST'
	        }).done(function(data){
				_this.buildResults(data);
			});
		}else{
			this.setState({valu: e.target.value});
		}
	},

	buildResults: function(data){
		var hidden = [], list = [], _this = this;

		_.each(data, function(obj, i){
			if( obj.school_name ) 
				list.push(React.createElement("li", {className: "result", key: obj.id, "data-id": obj.id, onClick: _this.addSchool}, obj.school_name));
		});

		this.setState({schools: list});
	},

	addSchool: function(e){
		var target = $(e.target), val = target.text(),
			id = target.data('id');

		this.setState({
			valu: val,
			hidden_val: id,
			schools: []
		});
	},

	render: function(){
		var placehldr = this.getPlacholder(), 
			label_name = this.getLabel(),
			classes = this.props.name === 'school' ? 'f-input has-results' : 'f-input';

		return (
			React.createElement("div", {className: "form-container"}, 
				React.createElement("div", {className: "f-label"}, 
					React.createElement("label", null, label_name)
				), 
				React.createElement("div", {className: classes}, 
					 this.props.name === 'school' ? 
						React.createElement("input", {name: "school_id", type: "hidden", value: this.state.hidden_val}) : null, 
					
					React.createElement("input", {name: this.props.name, type: "text", placeholder: placehldr, 
							className: "is-input", onChange: this.update, value: this.state.valu, onBlur: this.notTraversing}), 
							this.props.name === 'school' && this.state.schools.length > 0 ?
							React.createElement("ul", {className: "results-container stylish-scrollbar", onKeyPress: this.keypressed}, 
								 this.state.schools
							) : null
						
						
				)
			)
		);
	}
});

var Loader = React.createClass({displayName: "Loader",
	render: function(){
		return(
			React.createElement("div", {className: "gs-loader"}, 
				React.createElement("svg", {width: "70", height: "20"}, 
                    React.createElement("rect", {width: "20", height: "20", x: "0", y: "0", rx: "3", ry: "3"}, 
                        React.createElement("animate", {attributeName: "width", values: "0;20;20;20;0", dur: "1000ms", repeatCount: "indefinite"}), 
                        React.createElement("animate", {attributeName: "height", values: "0;20;20;20;0", dur: "1000ms", repeatCount: "indefinite"}), 
                        React.createElement("animate", {attributeName: "x", values: "10;0;0;0;10", dur: "1000ms", repeatCount: "indefinite"}), 
                        React.createElement("animate", {attributeName: "y", values: "10;0;0;0;10", dur: "1000ms", repeatCount: "indefinite"})
                    ), 
                    React.createElement("rect", {width: "20", height: "20", x: "25", y: "0", rx: "3", ry: "3"}, 
                        React.createElement("animate", {attributeName: "width", values: "0;20;20;20;0", begin: "200ms", dur: "1000ms", repeatCount: "indefinite"}), 
                        React.createElement("animate", {attributeName: "height", values: "0;20;20;20;0", begin: "200ms", dur: "1000ms", repeatCount: "indefinite"}), 
                        React.createElement("animate", {attributeName: "x", values: "35;25;25;25;35", begin: "200ms", dur: "1000ms", repeatCount: "indefinite"}), 
                        React.createElement("animate", {attributeName: "y", values: "10;0;0;0;10", begin: "200ms", dur: "1000ms", repeatCount: "indefinite"})
                    ), 
                    React.createElement("rect", {width: "20", height: "20", x: "50", y: "0", rx: "3", ry: "3"}, 
                        React.createElement("animate", {attributeName: "width", values: "0;20;20;20;0", begin: "400ms", dur: "1000ms", repeatCount: "indefinite"}), 
                        React.createElement("animate", {attributeName: "height", values: "0;20;20;20;0", begin: "400ms", dur: "1000ms", repeatCount: "indefinite"}), 
                        React.createElement("animate", {attributeName: "x", values: "60;50;50;50;60", begin: "400ms", dur: "1000ms", repeatCount: "indefinite"}), 
                        React.createElement("animate", {attributeName: "y", values: "10;0;0;0;10", begin: "400ms", dur: "1000ms", repeatCount: "indefinite"})
                    )
                )
			)
		);
	}
});

ReactDOM.render( React.createElement(Provider, {store: store}, React.createElement(GetStarted_Step2_Component, null)), document.getElementById('get_started_step2') );