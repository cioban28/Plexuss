// WhatsNextComponents.jsx

var WhatsNextComponents = React.createClass({
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
			<div className="whatsnext-container">

				<Display if={this.state.is_loading}>
					<LoadingGif />
				</Display>

				<Display if={!this.state.is_loading}>
					<Display if={ !_.isEmpty(this.state.step_data) }>
						<form>
							<input type="hidden" name="step_num" value={this.state.step_data.step_num} />
							<Find_Step step={this.state.step_data.step_num} data={this.state.step_data} post={this.post} />
						</form>	
					</Display>
				</Display>

			</div>
		);
	}
});

var Option_Buttons = React.createClass({
	render: function(){
		return (
			<div className="clearfix btn-opts">
				<div id="wn-is-continue" className="right continue" onClick={this.props.validate}>{'Save & Continue'}</div>
				<div id="wn-is-save" className="right save" onClick={this.props.validate}>{'Save'}</div>
				{/*<div id="wn-is-skip" className="right skip" onClick={this.props.validate}>{'Skip'}</div>*/}
			</div>
		);
	}
});

var Find_Step = React.createClass({
	render: function(){
		switch( this.props.step+'' ){
			case '1': return <Step1 data={this.props.data} post={this.props.post} />;
			case '2': return <Step2 data={this.props.data} post={this.props.post} />;
			case '3': return <Step3 data={this.props.data} post={this.props.post} />;
			case '4': return <Step4 data={this.props.data} post={this.props.post} />;
			case '5': return <Step5 data={this.props.data} post={this.props.post} />;
			default: return <Step_Done />;
		}
	}
});

var Step1 = React.createClass({
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
			ui.push( <option key={-1} value={''}>{'Select a country'}</option> );
			_.each(country, function(obj){
				ui.push( <option key={obj.id} value={obj.country_code}>{obj.country_name}</option> );
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
		options.push( <option key={-1} value="">{'Select user type'}</option> );
		options.push( <option key={0} value="student">{'Student'}</option> );
		options.push( <option key={1} value="alumni">{'Alumni'}</option> );
		options.push( <option key={2} value="parent">{'Parent'}</option> );
		options.push( <option key={3} value="counselor">{'Counselor'}</option> );
		options.push( <option key={4} value="university_rep">{'University Rep'}</option> );

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
			<div className="wn-step">
				<label>{'I am a(n)...'}</label>
				<select name="user_type" className={user_classes} value={content.user_type || ''} onChange={this.update}>
					{this.state.options}
				</select>
				<Display if={this.state.has_errors && !content.user_type}>
					<div><small className="err">{'Please choose a user type'}</small></div>
				</Display>

				<label>{'Country'}</label>
				<select name="country_code" className={country_classes} value={content.country_code || ''} onChange={this.update}>
					{this.state.countries}
				</select>
				<Display if={this.state.has_errors && !content.country_code}>
					<div><small className="err">{'Please choose your country'}</small></div>
				</Display>

				<label>{'Zip'}</label>
				<input name="zip" className={zip_classes} type="text" onChange={this.update}
						value={this.props.data.value || ''} placeholder={'Enter zip'} value={content.zip || ''} />
				<Display if={this.state.has_errors && !this.validZip(content.zip) }>
					<div><small className="err">{'Please enter your zip code'}</small></div>
				</Display>

				<Option_Buttons validate={this.isValid} />
			</div>
		);
	}
});

var Step2 = React.createClass({
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
		options.push( <option key={'-2'} value={''}>{'Select education level'}</option> );
		options.push( <option key={'0'} value={'0'}>{'High School'}</option> );
		options.push( <option key={'1'} value={'1'}>{'College'}</option> );

		return options;
	},

	getGradyrOptions: function(){
		var options = [], date = new Date(), year = date.getFullYear(), range = 15, yr = null;

		options.push( <option key={-1} value={''}>{'Select a year'}</option> );
		for (var i = 0; i < range; i++) {
			options.push( <option key={year} value={year+''}>{year}</option> );
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
			ui.push( <div key={obj.id} data-id={obj.id} className="result" onClick={_this.schoolChosen}>{obj.school_name}</div> );
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
			<div className="wn-step">
				<label>Level of Education</label>
				<select className={edu_classes} name="in_college" value={content.in_college ? content.in_college+'' || '' : ''} onChange={this.update}>
					{this.state.edu_options}
				</select>
				<Display if={this.state.has_errors && !content.in_college}>
					<div><small className="err">{'Please choose your education level'}</small></div>
				</Display>

				<label>{'School name'}</label>
				<div className="wn-school-name-container">
					<input className={school_classes} name="school_name" type="text" value={content.school_name || ''}
							placeholder={'Enter school name'} onChange={this.update} onBlur={this.notTraversing}
							onFocus={this.resetForceClose} autoComplete="off" />

					<Display if={this.state.has_errors && !content.school_name}>
						<div><small className="err">{'Please search and select your school'}</small></div>
					</Display>

					<Display if={this.state.result_ui && this.state.result_ui.length > 0}>
						<Display if={!this.state.force_close}>
							<div className="wn-autoresult-container">{this.state.result_ui}</div>
						</Display>
					</Display>
				</div>

				<label>Graduation year</label>
				<select className={grad_classes} name="grad_year" value={content.grad_year+'' || ''} onChange={this.update}>
					{this.state.gradyr_options}
				</select>
				<Display if={this.state.has_errors && !content.grad_year}>
					<div><small className="err">{'Please select your estimated graduation year'}</small></div>
				</Display>

				<Option_Buttons validate={this.isValid} />
			</div>
		);
	}
});

var Step3 = React.createClass({
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

		options.push( <option key={-1} value={''}>{'Select a term'}</option> );
		options.push( <option key={0} value={'spring'}>{'Spring'}</option> );
		options.push( <option key={1} value={'fall'}>{'Fall'}</option> );

		return options;
	},

	getYearOptions: function(){
		var options = [], date = new Date(), year = date.getFullYear(), range = 10, yr = null;

		options.push( <option key={-1} value={''}>{'Select a year'}</option> );
		for (var i = 0; i < range; i++) {
			options.push( <option key={year} value={year+''}>{year}</option> );
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
			<div className="wn-step">
				<label>Planned start term</label>
				<select className={term_classes} name="planned_start_term" value={content.planned_start_term || ''} onChange={this.update}>
					{this.state.term_options}
				</select>
				<Display if={this.state.has_errors && !content.planned_start_term}>
					<div><small className="err">{'Please choose the term in which you plan to start college.'}</small></div>
				</Display>

				<label>Planned start year</label>
				<select className={yr_classes} name="planned_start_yr" value={content.planned_start_yr || ''} onChange={this.update}>
					{this.state.year_options}
				</select>
				<Display if={this.state.has_errors && !content.planned_start_yr}>
					<div><small className="err">{'Please choose the year in which you plan to start college.'}</small></div>
				</Display>

				<Option_Buttons validate={this.isValid} />
			</div>
		);
	}
});

var Step4 = React.createClass({
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
			options.push( <option key={-1} value={''}>{'Select a degree'}</option> );
			_.each(degrees, function(obj){
				options.push( <option key={obj.id} value={obj.id}>{obj.display_name}</option> );
			});
		}

		return options;
	},

	getSchoolTypeOptions: function(){
		var options = [];

		options.push( <option key={-2} value={''}>{'Select a school type'}</option> );
		options.push( <option key={-3} value={'0'}>{'Campus only'}</option> );
		options.push( <option key={-4} value={'1'}>{'Online only'}</option> );
		options.push( <option key={-5} value={'2'}>{'Both'}</option> );

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
			if( name === 'major_name' ) ui.push( <div key={obj.id} data-id={obj.id} className="result major" onClick={_this.schoolChosen}>{obj.name}</div> );
			else ui.push( <div key={obj.id} data-id={obj.id} className="result profession" onClick={_this.schoolChosen}>{obj.profession_name}</div> );
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
			<div className="wn-step">
				<label>Choose a degree</label>
				<select className={degree_classes} name="degree_id" value={content.degree_id || ''} onChange={this.update}>
					{this.state.degrees}
				</select>
				<Display if={this.state.has_errors && !content.degree_id}>
					<div><small className="err">{'Please select a desired degree.'}</small></div>
				</Display>

				<label>Choose a major</label>
				<div className="wn-school-name-container">
					<input type="hidden" value={content.major_id || ''} name="major_id" />
					<input className={major_classes} name="major_name" type="text" value={content.major_name || ''}
							placeholder={'Enter school name'} onChange={this.update} onBlur={this.notTraversing}
							onFocus={this.resetForceClose} autoComplete="off" />

					<Display if={this.state.has_errors && !content.major_name}>
						<div><small className="err">{'Please search and select at least one desired major.'}</small></div>
					</Display>

					<Display if={this.state.major_results_ui && this.state.major_results_ui.length > 0}>
						<Display if={!this.state.majors_force_close}>
							<div className="wn-autoresult-container">{this.state.major_results_ui}</div>
						</Display>
					</Display>
				</div>

				<label>Choose a profession</label>
				<div className="wn-school-name-container">
					<input type="hidden" value={content.profession_id || ''} name="profession_id" />
					<input className={profession_classes} name="profession_name" type="text" value={content.profession_name || ''}
							placeholder={'Enter school name'} onChange={this.update} onBlur={this.notTraversing}
							onFocus={this.resetForceClose} autoComplete="off" />

					<Display if={this.state.has_errors && !content.profession_name}>
						<div><small className="err">{'Please search and select a desired profession.'}</small></div>
					</Display>

					<Display if={this.state.profession_results_ui && this.state.profession_results_ui.length > 0}>
						<Display if={!this.state.professions_force_close}>
							<div className="wn-autoresult-container">{this.state.profession_results_ui}</div>
						</Display>
					</Display>
				</div>

				<label>Choose the type of school</label>
				<select className={type_classes} name="interested_school_type" value={content.interested_school_type+'' || ''} onChange={this.update}>
					{this.state.school_type}
				</select>
				<Display if={this.state.has_errors && !(content.interested_school_type+'')}>
					<div><small className="err">{'Please select the type of desired school.'}</small></div>
				</Display>

				<Option_Buttons validate={this.isValid} />
			</div>
		);
	}
});

var Step5 = React.createClass({
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

		options.push(<option key={-1} value="">{'Select one...'}</option>);
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
			options.push(<option key={i} value={range}>{txt}</option>);
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
			<div className="wn-step">
				<label>{'How much can you and your family contribute towards your college education?'}</label>
				<select className={financial_classes} name="financial_firstyr_affordibility" value={financials} onChange={this.update}>
					{this.state.financial_options}
				</select>
				<Display if={this.state.has_errors && !content.financial_firstyr_affordibility}>
					<div><small className="err">{'Please enter the estimated amount you and your family would be able to afford.'}</small></div>
				</Display>

				<label>{'Enter your overall gpa'}</label>
				<input className={gpa_classes} type="number" value={content.overall_gpa || ''} name="overall_gpa" onChange={this.update}
						placeholder="Ex: 3.45" min="0.01" max="4" step="0.01" />
				<Display if={this.state.has_errors && !validGPA}>
					<div><small className="err">{'Please enter your gpa. Range: 0.01 - 4.00'}</small></div>
				</Display>

				<Option_Buttons validate={this.isValid} />
			</div>
		);
	}
});

var Step_Done = React.createClass({
	render: function(){
		return (
			<div className="wn-done">
				<h5>{'You are now recruitment ready!'}</h5>
				<div>{'By increasing your profile percentage and filling out your profile, you are increasing your chances of getting recruited.'}</div>
				<div className="mt20">{'Let\'s finish your '} <a href="/profile">profile</a>{'!'}</div>
			</div>
		);
	}
});

var LoadingGif = React.createClass({
	render: function(){
		return (
			<div className="text-center">
				<img className="loader" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/ajax_loader.gif"/>
			</div>
		);
	}
});

var Display = React.createClass({
	render: function(){
		return (this.props.if) ? <div>{this.props.children}</div> : null;
	}
});

ReactDOM.render( <WhatsNextComponents />, document.getElementById('WhatsNextComponents') );