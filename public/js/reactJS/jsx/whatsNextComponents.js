// WhatsNextComponents.jsx

var WhatsNextComponents = React.createClass({displayName: "WhatsNextComponents",
	getInitialState: function(){
		return {
			action: 'init',
			skip: undefined,
			is_loading: !1,
			step_data: {}
		};
	},

	componentWillMount: function(){
		$(document).on('click', '#what-is-next', this.getWhatsNext);	
	},

	getWhatsNext: function(){
		var _this = this;

		_this.setState({is_loading: !0});//is loading is true right before ajax call

		$.ajax({
			url:"/ajax/whatsNext",
			type: "GET",
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: function(data){
				_this.setState({
					is_loading: !1, //loading is false when done
					step_data: JSON.parse(data)
				});
			},
			error: function(err){
				_this.setState({is_loading: !1});//loading is false when done
			}
		});
	},

	post: function(e){
		var id = e.target.id, action = id.split('-').pop(),
			form = new FormData( $('.whatsnext-container form')[0] ),
			_this = this;
			
		$.ajax({
			url:"/ajax/whatsNext",
			type: "POST",
			data: form,
			enctype: 'multipart/form-data',
			contentType: false,
        	processData: false,
        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: function(data){
				_this.handleAction(action);
			},
			error: function(err){
				console.log(err);
			}
		});
	},

	handleAction: function(action){
		switch( action ){
			case 'continue':
				this.getWhatsNext();
				break;
			case 'save':
				$('#what-is-next').trigger('click');
				break;
			case 'skip':
				break;
		}
	},

	render: function(){
		return (
			React.createElement("div", {className: "whatsnext-container"}, 

				React.createElement(Display, {if: this.state.is_loading}, 
					React.createElement(LoadingGif, null)
				), 

				React.createElement(Display, {if: !this.state.is_loading}, 
					React.createElement(Display, {if:  !_.isEmpty(this.state.step_data)}, 
						React.createElement("form", null, 
							React.createElement("input", {type: "hidden", name: "step_num", value: this.state.step_data.step_num}), 
							React.createElement(Find_Step, {step: this.state.step_data.step_num, data: this.state.step_data, post: this.post})
						)	
					)
				)

			)
		);
	}
});

var Option_Buttons = React.createClass({displayName: "Option_Buttons",
	render: function(){
		return (
			React.createElement("div", {className: "clearfix btn-opts"}, 
				React.createElement("div", {id: "wn-is-continue", className: "right continue", onClick: this.props.validate}, 'Save & Continue'), 
				React.createElement("div", {id: "wn-is-save", className: "right save", onClick: this.props.validate}, 'Save')
				/*<div id="wn-is-skip" className="right skip" onClick={this.props.validate}>{'Skip'}</div>*/
			)
		);
	}
});

var Find_Step = React.createClass({displayName: "Find_Step",
	render: function(){
		switch( this.props.step+'' ){
			case '1': return React.createElement(Step1, {data: this.props.data, post: this.props.post});
			case '2': return React.createElement(Step2, {data: this.props.data, post: this.props.post});
			case '3': return React.createElement(Step3, {data: this.props.data, post: this.props.post});
			case '4': return React.createElement(Step4, {data: this.props.data, post: this.props.post});
			case '5': return React.createElement(Step5, {data: this.props.data, post: this.props.post});
			default: return React.createElement(Step_Done, null);
		}
	}
});

var Step1 = React.createClass({displayName: "Step1",
	getInitialState: function(){
		return {
			options: [],
			countries: [],
			content: {},
			step_num: '',
			has_errors: !1
		};
	},

	componentWillMount: function(){
		this.getCountries();

		this.setState({
			options: this.getOptions(),
			content: this.props.data.content,
			step_num: this.props.data.step_num
		});
	},

	makeCountryOptions: function(country){
		var ui = [];

		if( country && country.length > 0 ){
			ui.push( React.createElement("option", {key: -1, value: ''}, 'Select a country') );
			_.each(country, function(obj){
				ui.push( React.createElement("option", {key: obj.id, value: obj.country_code}, obj.country_name) );
			});

			this.setState({countries: ui});
		}
	},

	getCountries: function(){
		var _this = this;
		$.ajax({
			url: '/ajax/getAllCountries',
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: function(data){
				_this.makeCountryOptions(data);
			},
			error: function(err){
				console.log(err);
			}
		});
	},

	getOptions: function(){
		var options = [];
		options.push( React.createElement("option", {key: -1, value: ""}, 'Select user type') );
		options.push( React.createElement("option", {key: 0, value: "student"}, 'Student') );
		options.push( React.createElement("option", {key: 1, value: "alumni"}, 'Alumni') );
		options.push( React.createElement("option", {key: 2, value: "parent"}, 'Parent') );
		options.push( React.createElement("option", {key: 3, value: "counselor"}, 'Counselor') );
		options.push( React.createElement("option", {key: 4, value: "university_rep"}, 'University Rep') );

		return options;
	},

	isValid: function(e){
		var form = $('.whatsnext-container .is-input'), valid = !0, _this = this;

		//check if any fields are empty
		if( form.length > 0 ){
			$.each(form, function(){
				if( $(this).attr('name') === 'zip' ){
					if( !_this.validZip($(this).val()) ){
						valid = !1;
						return !1;
					}
				}else{
					if( $(this).val() === '' ){
						valid = !1;
						return !1;
					}
				}
			});
		}else valid = !1;

		if( valid ) this.props.post(e);
		else this.setState({has_errors: !0});
	},

	validZip: function(val){
		if( !val ) return !0; //zip is optional, so if emtpy, return true
		
		//but if not empty, validate it
		if( val.indexOf('-') > -1 ){ //if zip has dash char, there should be a total of 9 nums and only 1 dash
			var split = val.split('-'), merged = null;
			if( split.length > 2 ) return !1; //if there's multiple dashes, return false
			else if( split.length === 2 ){//can only be two elements when splitting by one dash
				merged = split[0].concat(split[1]);
				if( _.isNumber(+merged) && merged.length === 9 ) return !0;//minus the dash, check if they're numbers at least, if so return true
				return !1; //else return false
			}
		}else{
			if( _.isNumber(+val) && val.length === 5 ) return !0;//if no dash is provided, just check lenght and if all are numbers, if so return true
			return !1;//else return false
		}
	},

	update: function(e){
		var copy = _.extend({}, this.state.content),
			val = e.target.value, name = e.target.getAttribute('name'),
			error = !1;

		if( name === 'zip' && !this.validZip(val) ) error = !0;

		copy[name] = val; //update prop - will create prop, if it doesn't exist
		this.setState({
			content: copy,
			has_errors: error
		});
	},

	render: function(){
		var content = this.state.content,
			user_classes = this.state.has_errors && !content.user_type ? 'is-input has-err' : 'is-input',
			country_classes = this.state.has_errors && !content.country_code ? 'is-input has-err' : 'is-input',
			zip_classes = this.state.has_errors && !this.validZip(content.zip) ? 'is-input has-err' : 'is-input';
		return (
			React.createElement("div", {className: "wn-step"}, 
				React.createElement("label", null, 'I am a(n)...'), 
				React.createElement("select", {name: "user_type", className: user_classes, value: content.user_type || '', onChange: this.update}, 
					this.state.options
				), 
				React.createElement(Display, {if: this.state.has_errors && !content.user_type}, 
					React.createElement("div", null, React.createElement("small", {className: "err"}, 'Please choose a user type'))
				), 

				React.createElement("label", null, 'Country'), 
				React.createElement("select", {name: "country_code", className: country_classes, value: content.country_code || '', onChange: this.update}, 
					this.state.countries
				), 
				React.createElement(Display, {if: this.state.has_errors && !content.country_code}, 
					React.createElement("div", null, React.createElement("small", {className: "err"}, 'Please choose your country'))
				), 

				React.createElement("label", null, 'Zip'), 
				React.createElement("input", {name: "zip", className: zip_classes, type: "text", onChange: this.update, 
						value: this.props.data.value || '', placeholder: 'Enter zip', value: content.zip || ''}), 
				React.createElement(Display, {if: this.state.has_errors && !this.validZip(content.zip)}, 
					React.createElement("div", null, React.createElement("small", {className: "err"}, 'Please enter your zip code'))
				), 

				React.createElement(Option_Buttons, {validate: this.isValid})
			)
		);
	}
});

var Step2 = React.createClass({displayName: "Step2",
	getInitialState: function(){
		return {
			edu_options: [],
			gradyr_options: [],
			content: {},
			step_num: '',
			has_errors: !1,
			result_ui: [],
			force_close: !1,
			traversing: !1
		};
	},

	componentWillMount: function(){
		var _this = this;

		document.addEventListener('keydown', this.keypressed);

		$(document).on('click', '#WhatsNextComponents', function(e){
			if( $(e.target).attr('id') === 'WhatsNextComponents' || $(e.target).is('label') ) _this.forceClose();
		});

		this.setState({
			edu_options: this.getEduOptions(),
			gradyr_options: this.getGradyrOptions(),
			content: this.props.data.content,
			step_num: this.props.data.step_num
		});
	},

	componentWillUnmount: function(){
		$(document).off('click', '#WhatsNextComponents');
		document.removeEventListener('keydown', this.keypressed);
	},

	keypressed: function(e){
		var key = e.which || e.keyCode;

		if( this.state.result_ui.length > 0 ){
			var container = $('.wn-autoresult-container'), elem = null, results = container.children();
			
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
			}else if( key === 13 ){
				e.preventDefault();
				$('.result.highlighted').trigger('click'); //enter key
			}
		}
		
	},

	notTraversing: function(){
		this.state.traversing = !1;
	},

	getEduOptions: function(){
		var options = [];
		options.push( React.createElement("option", {key: '-2', value: ''}, 'Select education level') );
		options.push( React.createElement("option", {key: '0', value: '0'}, 'High School') );
		options.push( React.createElement("option", {key: '1', value: '1'}, 'College') );

		return options;
	},

	getGradyrOptions: function(){
		var options = [], date = new Date(), year = date.getFullYear(), range = 15, yr = null;

		options.push( React.createElement("option", {key: -1, value: ''}, 'Select a year') );
		for (var i = 0; i < range; i++) {
			options.push( React.createElement("option", {key: year, value: year+''}, year) );
			year++;
		};

		return options;
	},

	isValid: function(e){
		var form = $('.whatsnext-container .is-input'), valid = !0;

		//check if any fields are empty
		if( form.length > 0 ){
			$.each(form, function(){
				if( $(this).val() === '' ){
					valid = !1;
					return !1;
				}
			});
		}else valid = !1;

		if( valid ) this.props.post(e);
		else this.setState({has_errors: !0});
	},

	update: function(e){
		var copy = _.extend({}, this.state.content),
			val = e.target.value, name = e.target.getAttribute('name');

		this.notTraversing();

		if( name === 'school_name' ) this.autocomplete(val);

		copy[name] = val; //update prop - will create prop, if it doesn't exist
		this.setState({content: copy}); //update state
	},

	autocomplete: function(val){
		var _this = this, copy = _.extend({}, this.state.content),
			in_college = copy.in_college, route = '/ajax/searchForHighSchools';

		if(in_college && in_college+'' === '1') route = '/ajax/searchForColleges';

		if( val ){
			$.ajax({
				url: route,
				type: 'POST',
				data: {input: val},
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				success: function(data){
					if( data && data.length > 0 ){
						_this.buildResultUI(data);
					}
				},
				error: function(err){
					// console.log(err);
				}
			});
		}
	},

	buildResultUI: function(data){
		var ui = [], _this = this;

		_.each(data, function(obj){
			ui.push( React.createElement("div", {key: obj.id, "data-id": obj.id, className: "result", onClick: _this.schoolChosen}, obj.school_name) );
		});

		this.setState({result_ui: ui});
	},

	schoolChosen: function(e){
		var target = $(e.target).text(),
			copy = _.extend({}, this.state.content);

		copy.school_name = target;
		this.setState({
			content: copy,
			force_close: !0 //close results container
		});
	},

	resetForceClose: function(){
		this.setState({force_close: !1}); //open results container
	},

	forceClose: function(){
		this.setState({force_close: !0});
	},

	render: function(){
		var content = this.state.content,
			edu_classes = this.state.has_errors && !content.in_college ? 'is-input has-err' : 'is-input',
			school_classes = this.state.has_errors && !content.school_name ? 'is-input has-err' : 'is-input',
			grad_classes = this.state.has_errors && !content.grad_year ? 'is-input has-err' : 'is-input';

		return (
			React.createElement("div", {className: "wn-step"}, 
				React.createElement("label", null, "Level of Education"), 
				React.createElement("select", {className: edu_classes, name: "in_college", value: content.in_college ? content.in_college+'' || '' : '', onChange: this.update}, 
					this.state.edu_options
				), 
				React.createElement(Display, {if: this.state.has_errors && !content.in_college}, 
					React.createElement("div", null, React.createElement("small", {className: "err"}, 'Please choose your education level'))
				), 

				React.createElement("label", null, 'School name'), 
				React.createElement("div", {className: "wn-school-name-container"}, 
					React.createElement("input", {className: school_classes, name: "school_name", type: "text", value: content.school_name || '', 
							placeholder: 'Enter school name', onChange: this.update, onBlur: this.notTraversing, 
							onFocus: this.resetForceClose, autoComplete: "off"}), 

					React.createElement(Display, {if: this.state.has_errors && !content.school_name}, 
						React.createElement("div", null, React.createElement("small", {className: "err"}, 'Please search and select your school'))
					), 

					React.createElement(Display, {if: this.state.result_ui && this.state.result_ui.length > 0}, 
						React.createElement(Display, {if: !this.state.force_close}, 
							React.createElement("div", {className: "wn-autoresult-container"}, this.state.result_ui)
						)
					)
				), 

				React.createElement("label", null, "Graduation year"), 
				React.createElement("select", {className: grad_classes, name: "grad_year", value: content.grad_year+'' || '', onChange: this.update}, 
					this.state.gradyr_options
				), 
				React.createElement(Display, {if: this.state.has_errors && !content.grad_year}, 
					React.createElement("div", null, React.createElement("small", {className: "err"}, 'Please select your estimated graduation year'))
				), 

				React.createElement(Option_Buttons, {validate: this.isValid})
			)
		);
	}
});

var Step3 = React.createClass({displayName: "Step3",
	getInitialState: function(){
		return {
			content: {},
			step_num: '',
			has_errors: !1,
			year_options: [],
			term_options: []
		};
	},

	componentWillMount: function(){
		this.setState({
			content: this.props.data.content,
			step_num: this.props.data.step_num,
			year_options: this.getYearOptions(),
			term_options: this.getTermOptions()
		});
	},

	getTermOptions: function(){
		var options = [];

		options.push( React.createElement("option", {key: -1, value: ''}, 'Select a term') );
		options.push( React.createElement("option", {key: 0, value: 'spring'}, 'Spring') );
		options.push( React.createElement("option", {key: 1, value: 'fall'}, 'Fall') );

		return options;
	},

	getYearOptions: function(){
		var options = [], date = new Date(), year = date.getFullYear(), range = 10, yr = null;

		options.push( React.createElement("option", {key: -1, value: ''}, 'Select a year') );
		for (var i = 0; i < range; i++) {
			options.push( React.createElement("option", {key: year, value: year+''}, year) );
			year++;
		}

		return options;
	},

	update: function(e){
		var copy = _.extend({}, this.state.content),
			val = e.target.value, name = e.target.getAttribute('name');

		copy[name] = val; //update prop - will create prop, if it doesn't exist
		this.setState({content: copy}); //update state
	},

	isValid: function(e){
		var form = $('.whatsnext-container .is-input'), valid = !0;

		//check if any fields are empty
		if( form.length > 0 ){
			$.each(form, function(){
				if( $(this).val() === '' ){
					valid = !1;
					return !1;
				}
			});
		}else valid = !1;

		if( valid ) this.props.post(e);
		else this.setState({has_errors: !0});
	},

	render: function(){
		var content = this.state.content,
			term_classes = this.state.has_errors && !content.planned_start_term ? 'is-input has-err' : 'is-input',
			yr_classes = this.state.has_errors && !content.planned_start_yr ? 'is-input has-err' : 'is-input';

		return (
			React.createElement("div", {className: "wn-step"}, 
				React.createElement("label", null, "Planned start term"), 
				React.createElement("select", {className: term_classes, name: "planned_start_term", value: content.planned_start_term || '', onChange: this.update}, 
					this.state.term_options
				), 
				React.createElement(Display, {if: this.state.has_errors && !content.planned_start_term}, 
					React.createElement("div", null, React.createElement("small", {className: "err"}, 'Please choose the term in which you plan to start college.'))
				), 

				React.createElement("label", null, "Planned start year"), 
				React.createElement("select", {className: yr_classes, name: "planned_start_yr", value: content.planned_start_yr || '', onChange: this.update}, 
					this.state.year_options
				), 
				React.createElement(Display, {if: this.state.has_errors && !content.planned_start_yr}, 
					React.createElement("div", null, React.createElement("small", {className: "err"}, 'Please choose the year in which you plan to start college.'))
				), 

				React.createElement(Option_Buttons, {validate: this.isValid})
			)
		);
	}
});

var Step4 = React.createClass({displayName: "Step4",
	getInitialState: function(){
		return {
			content: {},
			step_num: '',
			degrees: [],
			school_type: [],
			has_errors: !1,
			major_results_ui: [],
			profession_results_ui: [],
			majors_force_close: !1,
			professions_force_close: !1,
			traversing: !1
		};
	},

	componentWillMount: function(){
		var _this = this;

		document.addEventListener('keydown', this.keypressed);

		$(document).on('click', '#WhatsNextComponents', function(e){
			if( $(e.target).attr('id') === 'WhatsNextComponents' || $(e.target).is('label') ) _this.forceClose();
		});

		this.setState({
			content: this.props.data.content,
			step_num: this.props.data.step_num,
			degrees: this.getDegreeOptions(),
			school_type: this.getSchoolTypeOptions()
		});
	},

	componentWillUnmount: function(){
		document.removeEventListener('keydown', this.keypressed);
		$(document).off('click', '#WhatsNextComponents');
	},

	keypressed: function(e){
		var key = e.which || e.keyCode;

		if( this.state.major_results_ui.length > 0 || this.state.profession_results_ui.length > 0 ){
			var container = $('.wn-autoresult-container'), elem = null, results = container.children();
			
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

	getDegreeOptions: function(){
		var degrees = this.props.data.degree, options = [];

		if( degrees && degrees.length > 0 ){
			options.push( React.createElement("option", {key: -1, value: ''}, 'Select a degree') );
			_.each(degrees, function(obj){
				options.push( React.createElement("option", {key: obj.id, value: obj.id}, obj.display_name) );
			});
		}

		return options;
	},

	getSchoolTypeOptions: function(){
		var options = [];

		options.push( React.createElement("option", {key: -2, value: ''}, 'Select a school type') );
		options.push( React.createElement("option", {key: -3, value: '0'}, 'Campus only') );
		options.push( React.createElement("option", {key: -4, value: '1'}, 'Online only') );
		options.push( React.createElement("option", {key: -5, value: '2'}, 'Both') );

		return options;
	},

	autocomplete: function(name, val){
		var _this = this, copy = _.extend({}, this.state.content),
			in_college = copy.in_college, route = '/ajax/searchForMajors';

		if( name && name === 'profession_name' ) route = '/ajax/searchForProfessions';

		if( val ){
			$.ajax({
				url: route,
				type: 'POST',
				data: {input: val},
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				success: function(data){
					if( data && data.length > 0 ){
						_this.buildResultUI(name, data);
					}
				},
				error: function(err){
					// console.log(err);
				}
			});
		}
	},

	buildResultUI: function(name, data){
		var ui = [], _this = this;

		_.each(data, function(obj){
			if( name === 'major_name' ) ui.push( React.createElement("div", {key: obj.id, "data-id": obj.id, className: "result major", onClick: _this.schoolChosen}, obj.name) );
			else ui.push( React.createElement("div", {key: obj.id, "data-id": obj.id, className: "result profession", onClick: _this.schoolChosen}, obj.profession_name) );
		});

		if( name === 'major_name' ) this.setState({major_results_ui: ui});
		else this.setState({profession_results_ui: ui});
	},

	schoolChosen: function(e){
		var target = $(e.target).text(),
			id = $(e.target).data('id'),
			copy = _.extend({}, this.state.content),
			name = $(e.target).hasClass('major') ? 'major' : 'profession';

		copy[name+'_name'] = target;
		copy[name+'_id'] = id;
		this.state.content = copy;

		if( name === 'major' ) this.setState({majors_force_close: !0}); //close results container
		else this.setState({professions_force_close: !0});
	},

	isValid: function(e){
		var form = $('.whatsnext-container .is-input'), valid = !0;

		//check if any fields are empty
		if( form.length > 0 ){
			$.each(form, function(){
				if( $(this).val() === '' ){
					valid = !1;
					return !1;
				}
			});
		}else valid = !1;

		if( valid ) this.props.post(e);
		else this.setState({has_errors: !0});
	},

	update: function(e){
		var copy = _.extend({}, this.state.content),
			val = e.target.value, name = e.target.getAttribute('name'),
			tmp = name.split('_')[0];

		this.notTraversing();

		if( name === 'major_name' || name === 'profession_name' ) this.autocomplete(name, val);

		copy[name] = val; //update prop - will create prop, if it doesn't exist
		this.setState({content: copy}); //update state
	},

	resetForceClose: function(e){
		var name = e.target.getAttribute('name');

		if( name === 'major_id' ) this.setState({majors_force_close: !1}); //open majors results container
		else this.setState({professions_force_close: !1}); //open professions results container
	},

	forceClose: function(){
		this.setState({
			majors_force_close: !0,
			professions_force_close: !0
		});
	},

	render: function(){
		var content = this.state.content,
			degree_classes = this.state.has_errors && !content.degree_id ? 'is-input has-err' : 'is-input',
			major_classes = this.state.has_errors && !content.major_name ? 'is-input has-err' : 'is-input',
			profession_classes = this.state.has_errors && !content.profession_name ? 'is-input has-err' : 'is-input',
			type_classes = this.state.has_errors && !(content.interested_school_type+'') ? 'is-input has-err' : 'is-input';

		return (
			React.createElement("div", {className: "wn-step"}, 
				React.createElement("label", null, "Choose a degree"), 
				React.createElement("select", {className: degree_classes, name: "degree_id", value: content.degree_id || '', onChange: this.update}, 
					this.state.degrees
				), 
				React.createElement(Display, {if: this.state.has_errors && !content.degree_id}, 
					React.createElement("div", null, React.createElement("small", {className: "err"}, 'Please select a desired degree.'))
				), 

				React.createElement("label", null, "Choose a major"), 
				React.createElement("div", {className: "wn-school-name-container"}, 
					React.createElement("input", {type: "hidden", value: content.major_id || '', name: "major_id"}), 
					React.createElement("input", {className: major_classes, name: "major_name", type: "text", value: content.major_name || '', 
							placeholder: 'Enter school name', onChange: this.update, onBlur: this.notTraversing, 
							onFocus: this.resetForceClose, autoComplete: "off"}), 

					React.createElement(Display, {if: this.state.has_errors && !content.major_name}, 
						React.createElement("div", null, React.createElement("small", {className: "err"}, 'Please search and select at least one desired major.'))
					), 

					React.createElement(Display, {if: this.state.major_results_ui && this.state.major_results_ui.length > 0}, 
						React.createElement(Display, {if: !this.state.majors_force_close}, 
							React.createElement("div", {className: "wn-autoresult-container"}, this.state.major_results_ui)
						)
					)
				), 

				React.createElement("label", null, "Choose a profession"), 
				React.createElement("div", {className: "wn-school-name-container"}, 
					React.createElement("input", {type: "hidden", value: content.profession_id || '', name: "profession_id"}), 
					React.createElement("input", {className: profession_classes, name: "profession_name", type: "text", value: content.profession_name || '', 
							placeholder: 'Enter school name', onChange: this.update, onBlur: this.notTraversing, 
							onFocus: this.resetForceClose, autoComplete: "off"}), 

					React.createElement(Display, {if: this.state.has_errors && !content.profession_name}, 
						React.createElement("div", null, React.createElement("small", {className: "err"}, 'Please search and select a desired profession.'))
					), 

					React.createElement(Display, {if: this.state.profession_results_ui && this.state.profession_results_ui.length > 0}, 
						React.createElement(Display, {if: !this.state.professions_force_close}, 
							React.createElement("div", {className: "wn-autoresult-container"}, this.state.profession_results_ui)
						)
					)
				), 

				React.createElement("label", null, "Choose the type of school"), 
				React.createElement("select", {className: type_classes, name: "interested_school_type", value: content.interested_school_type+'' || '', onChange: this.update}, 
					this.state.school_type
				), 
				React.createElement(Display, {if: this.state.has_errors && !(content.interested_school_type+'')}, 
					React.createElement("div", null, React.createElement("small", {className: "err"}, 'Please select the type of desired school.'))
				), 

				React.createElement(Option_Buttons, {validate: this.isValid})
			)
		);
	}
});

var Step5 = React.createClass({displayName: "Step5",
	getInitialState: function(){
		return {
			content: {},
			step_num: '',
			has_errors: !1,
			financial_options: []
		};
	},

	componentWillMount: function(){
		this.setState({
			content: this.props.data.content,
			step_num: this.props.data.step_num,
			financial_options: this.getFinancialOptions()
		});
	},

	getFinancialOptions: function(){
		var options = [], values = [[0], [0, 5], [5, 10], [10, 20], [20, 30], [30, 50], [50]], txt = '', range = '';

		options.push(React.createElement("option", {key: -1, value: ""}, 'Select one...'));
		for (var i = 0; i < values.length; i++) {

			if( i === 0 && values[i].length === 1 ){//if first iteration, amount is $0
				txt = '$' + values[i][0];
				range = txt.substr(1);
			}else if( i === values.length-1 && values[i].length === 1 ){//if last iteration, amount is $50,000+
				txt = '$'+values[i][0]+',000+';
				range = txt.substring(1, txt.length-1);
			}else if( values[i].length === 2 ){//else amount is a range
				if( values[i][0] === 0 ) txt = '$'+values[i][0]+' - $'+values[i][1]+',000';
				else txt = '$'+values[i][0]+',000 - $'+values[i][1]+',000';
				range = txt.split('$').join('');
			}

			//add to array
			options.push(React.createElement("option", {key: i, value: range}, txt));
		}

		return options;
	},

	isValid: function(e){
		var form = $('.whatsnext-container .is-input'), valid = !0, val = '', _this = this;

		//check if any fields are empty
		if( form.length > 0 ){
			$.each(form, function(){
				if( $(this).attr('name') === 'overall_gpa' && !_this.isValidGPA($(this).val()) ){
					valid = !1;
					return !1;
				}else if( $(this).val() === '' ){
					valid = !1;
					return !1;
				}
			});
		}else valid = !1;

		if( valid ) this.props.post(e);
		else this.setState({has_errors: !0});
	},

	isValidGPA: function(val){
		if( !val ) return !1;

		if( val.indexOf('.') > -1 ){ //has decimal point
			var split = val.split('.');
			if( split.length !== 2 ) return !1; //if there's a decimal point, there can only be a whole number and decimal nums
			if( +split[0] < 0 || +split[0] > 4 ) return !1; //whole number is less than 1 or greater than 4, false
			if( +split[1] < 0 || +split[1] > 99 ) return !1; //if decimal number is less than 0 or greater 99, false
			if( +split[0] <= 0 && +split[1] <= 0 ) return !1; //if whole number is 0, decimal can't be 0
		}else{ //no decimal
			if( val.length > 1 || +val <= 0 || +val > 4 ) return !1; //if more than 1 chars long, less than or equal to 0, or greater than 4 then false
		}

		return !0;
	},

	update: function(e){
		var copy = _.extend({}, this.state.content),
			val = e.target.value, name = e.target.getAttribute('name');

		copy[name] = val; //update prop - will create prop, if it doesn't exist
		this.setState({content: copy}); //update state
	},

	render: function(){
		var content = this.state.content,
			validGPA = this.isValidGPA(content.overall_gpa),
			financial_classes = this.state.has_errors && !content.financial_firstyr_affordibility ? 'is-input has-err' : 'is-input',
			gpa_classes = this.state.has_errors && !validGPA ? 'is-input has-err' : 'is-input',
			financials = content.financial_firstyr_affordibility ? content.financial_firstyr_affordibility.split('.')[0] : '';

		return (
			React.createElement("div", {className: "wn-step"}, 
				React.createElement("label", null, 'How much can you and your family contribute towards your college education?'), 
				React.createElement("select", {className: financial_classes, name: "financial_firstyr_affordibility", value: financials, onChange: this.update}, 
					this.state.financial_options
				), 
				React.createElement(Display, {if: this.state.has_errors && !content.financial_firstyr_affordibility}, 
					React.createElement("div", null, React.createElement("small", {className: "err"}, 'Please enter the estimated amount you and your family would be able to afford.'))
				), 

				React.createElement("label", null, 'Enter your overall gpa'), 
				React.createElement("input", {className: gpa_classes, type: "number", value: content.overall_gpa || '', name: "overall_gpa", onChange: this.update, 
						placeholder: "Ex: 3.45", min: "0.01", max: "4", step: "0.01"}), 
				React.createElement(Display, {if: this.state.has_errors && !validGPA}, 
					React.createElement("div", null, React.createElement("small", {className: "err"}, 'Please enter your gpa. Range: 0.01 - 4.00'))
				), 

				React.createElement(Option_Buttons, {validate: this.isValid})
			)
		);
	}
});

var Step_Done = React.createClass({displayName: "Step_Done",
	render: function(){
		return (
			React.createElement("div", {className: "wn-done"}, 
				React.createElement("h5", null, 'You are now recruitment ready!'), 
				React.createElement("div", null, 'By increasing your profile percentage and filling out your profile, you are increasing your chances of getting recruited.'), 
				React.createElement("div", {className: "mt20"}, 'Let\'s finish your ', " ", React.createElement("a", {href: "/profile"}, "profile"), '!')
			)
		);
	}
});

var LoadingGif = React.createClass({displayName: "LoadingGif",
	render: function(){
		return (
			React.createElement("div", {className: "text-center"}, 
				React.createElement("img", {className: "loader", src: "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/ajax_loader.gif"})
			)
		);
	}
});

var Display = React.createClass({displayName: "Display",
	render: function(){
		return (this.props.if) ? React.createElement("div", null, this.props.children) : null;
	}
});

ReactDOM.render( React.createElement(WhatsNextComponents, null), document.getElementById('WhatsNextComponents') );