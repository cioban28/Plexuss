// GetStarted_Step3_Component.jsx
var percentage;

function wasRedirected(){
    return JSON.parse(sessionStorage.getItem('college_id'));
};

function currentPercentage(pct){
    if(pct) percentage = pct;
    return percentage;
};

var styles = {
	good: {
		border: '1px solid #24b26b',
	},
	bad: {
		border: '1px solid firebrick',
	}
};

var GetStarted_Step3_Component = React.createClass({
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
			user_info: null,
			errmsg: '',
			data: [],
			_degreeValid: false,
			_majorValid: false,
			_careerValid: false,
			_schoolTypeValid: false,
			_universityLocationValid: false,
			_formValid: false,
		};
	},

	componentWillMount: function(){
		var classes = this.state.save_btn_classes, prev, next, num, _this = this;

		// Facebook event tracking
        fbq('track', 'GetStarted_Step3_Study_New_Page');

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
		if( data && _.isArray(data) && data.length > 0 ) this.setState({data: data});
	},

	_save: function(e){
		e.preventDefault();

		var formData = new FormData( $('form')[0] ), state = this.state, _this = this;
        
		if( state._formValid ){

			//show loader
			this.setState({is_sending: true});

			$.ajax({
				url: state.save_route,
				type: 'POST',
				data: formData, 
				enctype: 'multipart/form-data',
				contentType: false,
	        	processData: false,
	        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			}).done(function(data){
				//if data has msg prop, it's an error
				if( data.msg ){
					_this.setState({
						errmsg: data.msg,
						is_sending: false
					});
				}else{
                    amplitude.getInstance().logEvent('step3_completed', {content: 'Aspirations and Degree Preference'} );

					if(data) currentPercentage(data);
					if( wasRedirected() ){
						_this.setState({
							is_sending: false,
							errmsg: ''
						});//remove loader and err msg
						$(document).trigger('saved');
					}else window.location.href = state.next_route;
				}
			});
		}
	},

	_validateForm: function(field, bool){
		var s = this.state, obj = {};

		this.state[field+'Valid'] = bool;
		this.state._formValid = this.state._degreeValid && this.state._majorValid && this.state._schoolTypeValid && this.state._universityLocationValid;
        //  && this.state._careerValid

		this.setState({_formValid: this.state._formValid});
	},

	render: function(){
		var s = this.state, degreeData = null, careerData = null, schoolTypeData = null, locationData = null, majorData = null;

		if( s.data.length > 0 ){
			degreeData =  s.data[0];
			majorData = s.data;
			careerData =  s.data[0];
			schoolTypeData =  s.data[0];
			locationData =  s.data[0];
		}

		return (
			<div className="step_container">
				<div className="row">
					<div className="column small-12">

						<div className="inner-col">
							<div className="intro">{'What do you want to study and do?'}</div>
							<br />

							<form onSubmit={ this._save }>
								<input type="hidden" name="step" value={s.step_num} />		

								<Degree validateForm={this._validateForm} data={degreeData} />
								<Majors validateForm={this._validateForm} data={majorData} />
								<Career validateForm={this._validateForm} data={careerData} />
								<SchoolType validateForm={this._validateForm} data={schoolTypeData} />
								<UniversityLocation validateForm={this._validateForm} data={locationData} />

								<div className="submit-row clearfix">
									<div className="left btn back-btn hide-for-small-only"><a href={s.back_route}>Go Back</a></div>
									<div className="right btn">
										<button 
											disabled={ !s._formValid }
											className="submit-form-btn button radius">
												Next
										</button>

                                        <div style={{ color: 'gray', display: 'block' }}>{this.state.errmsg}</div>
									</div>
								</div>
							</form>
						</div>

					</div>
				</div>

				{ this.state.is_sending ? <Loader /> : null }
			</div>
		);
	}
});

var Degree = React.createClass({
	getInitialState: function(){
		return {
			degree_options: null,
			degree_type: 0,
			_degreeValid: false,
			_degreeValidated: false,
		};
	},

	componentWillMount: function(){
		this._getDegrees();	
	},

	componentWillReceiveProps: function(np){
		if( this.props.data !== np.data ){
			this.state.degree_type = np.data.degree_type;
			this._validate(np.data);
		}
	},

	_getDegrees: function(){
		var _this = this;

		$.ajax({
			url: '/get_started/getDataFor/degree',
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(data) {
			_this._buildDegree(data);
		});
	},

	_buildDegree: function(data){
		var degrees = [];

		//add default choice, but make it unclickable
		degrees.push( <option key={-1} value="" disabled="disabled">{'Select one...'}</option> );	

		//then loop through array and create options elements for select field
		_.each(data, function(obj){
			degrees.push(<option key={obj.id} value={obj.id}>{obj.display_name}</option>);
		});

		// re-render
		this.setState({degree_options: degrees});
	},

	_validate: function(e){
		var valid = false, value = '';

		value = e.target ? e.target.value : e.degree_type;

		if( value ) valid = true;

		this.setState({
			_degreeValid: valid, 
			_degreeValidated: true, 
			focused: '_degree',
			degree_type: value,
		});

		this.props.validateForm('_degree', valid);
	},

	_unsetFocus: function(){
		this.setState({focused: ''});
	},

	render: function(){
		var degreeStyles = {};

		if( this.state.focused === '_degree' ) degreeStyles = this.state._degreeValid ? styles.good : styles.bad;
		else degreeStyles = (!this.state._degreeValid && this.state._degreeValidated) ? styles.bad : {};

		return (
			<div className="row">
				<div className="columns small-12 medium-4 end" style={{marginBottom: '0.5rem'}}>
					<label htmlFor="_degree" className="study-label">
						I would like to get a/an
					</label>
					<select 
						id="_degree"
						name="degree" 
						style={ degreeStyles }
						onChange={ this._validate }
						onFocus={ this._validate }
						onBlur={ this._unsetFocus }
						value={ this.state.degree_type }>
							{this.state.degree_options}
					</select>
					{ !this.state._degreeValid && this.state._degreeValidated ? <div className="study-err">Please select a degree.</div> : null }
				</div>	
			</div>
		);
	}
});

var Majors = React.createClass({
	getInitialState: function(){
		return {
			findMajor_route: '/get_started/searchFor/major',
			majors: {
				department: {},
				popular_majors: [],
				other_majors: [],
			},
			defaultMajorsState: {
				department: {},
				popular_majors: [],
				other_majors: [],
			},
			majors_active: false,
			selected_majors: [],
			currently_scrolling: false,
			hidden_major_inputs: [],
			major_val: '',
			major_tags: [],
			traversing: !1,

			_majorValid: false,
			_majorValidated: false,
			focused: '',
			major_id: null,
			major_name: '',
			showSide: false,
		};
	},

	componentWillMount: function(){
		document.addEventListener('click', this._domClick);
		document.addEventListener('keydown', this.keypressed);
	},

	componentWillUnmount: function(){
		document.removeEventListener('click', this._domClick);
		document.removeEventListener('keydown', this.keypressed);
	},

	componentWillReceiveProps: function(np){
		if( this.props.data !== np.data ) this._initMajors(np.data);
	},

	_initMajors: function(data){
		var majors = [], _this = this;

		_.each(data, function(major){
			// majors.push({id: major.major_id, name: major.major_name});
			_this._addMajor({id: major.major_id, name: major.major_name});
		});
	},

	keypressed: function(e){
		var key = e.which || e.keyCode;

		if( this.state.majors.length > 0 ){
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

	_domClick: function(e){
		if( $(e.target).closest('.results-container').length === 0 ){
			if( $(e.target).attr('id') === '_major' 
				|| $(e.target).closest('.tag.majr').length > 0 
				|| $(e.target).closest('.suggested.mjr').length > 0 ) this.setState({majors_active: true});
			else this._deactivate();
		}
	},	

	_activate: function(e){
		this._validate();
		this.setState({
			majors_active: true,
			showSide: true,
		});
	},

	_deactivate: function(e){
		this.setState({
			majors_active: false,
		});
	},

	_findMajor: function(e){
		var _this = this, val = e.target.value;
		this.setState({major_val: val});

		if( val ){
			$.ajax({
	            url: this.state.findMajor_route,
	            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	            data: {input: val},
	            type: 'POST'
	        }).done(function(data){
				_this.setState({majors: data});
			});
		}
	},

	_addMajor: function(major){
		var copy = this.state.selected_majors.slice(),

    		//find duplicate, if any
    		duplicate = _.findWhere(copy, major);

		//if not a duplicate, add major
		if( !duplicate && copy.length < 4 ){
			copy.push(major);
			this.state.selected_majors = copy;
			this.state.major_val = '';
			if( !this.state.showSide ) this.state.showSide = true;
		}

		this.setState({majors_active: false});
		this._validate();
	},

	_removeMajor: function(major){
		var copy = this.state.selected_majors.slice();

		this.state.selected_majors = _.reject(copy, major);

		this._validate();
	},

	clearInput: function(){
		this.setState({major_val: ''});
	},

	_update: function(e){
		this._findMajor(e);
	},

	_validate: function(){
		var valid = false;

		if( this.state.selected_majors.length > 0 ) valid = true;

		this.state._majorValid = valid;
		this.state._majorValidated = true;
		this.state.focused = '_major';

		this.props.validateForm('_major', valid);
	},

	render: function(){
		var majorStyles = {}, _this = this, s = this.state;

		if( s.focused === '_major' ) majorStyles = s._majorValid ? styles.good : styles.bad;
		else majorStyles = (!s._majorValid && s._majorValidated) ? styles.bad : {};

		return (
			<div className="row" style={{position: 'relative'}}>
				<div className="has-results columns small-12 medium-4" style={{marginBottom: '0.5rem'}}>

					{/* hidden input fields */}
					{ s.selected_majors.map(function(major){
						return <input key={major.id} type="hidden" name="chosen_majors[]" value={major.id} className="is-input" />
					}) }

					<label htmlFor="_major" className="study-label">
						I would like to study
					</label>	

					{ this.state.selected_majors.length === 4 ? <div className="study-warning">{"You've reached the maximum number of majors."}</div> : null }

					<div style={{position: 'relative'}}>
						<input 
							id="_major"
							name="major" 
							type="text" 
							placeholder="Enter major"
							style={ majorStyles }
							onChange={ this._update }
							onFocus={ this._activate }
							value={ s.major_val } />

						<div 
							className="clear-input text-center" 
							onClick={this.clearInput}>x</div>

						{	
							s.majors_active && s.majors && !_.isEmpty(s.majors) ?
								<div className="results-container stylish-scrollbar">

									<div className="text-right popular">Most Popular</div>

									{ s.majors.department && !_.isEmpty(s.majors.department) ? 
										<MajorItem key={s.majors.department.id} major={s.majors.department} addMajor={_this._addMajor} /> 
										: null
									}

									{ s.majors.popular_majors && !_.isEmpty(s.majors.popular_majors) ? 
										s.majors.popular_majors.map( function(major){ return <MajorItem key={major.id} major={major} addMajor={_this._addMajor} />}) 
										: null 
									}

									<div className="text-right all">All<div className="line"></div></div>

									{ s.majors.other_majors && !_.isEmpty(s.majors.other_majors) ? 
										s.majors.other_majors.slice(0,100).map( function(major){ return <MajorItem key={major.id} major={major} addMajor={_this._addMajor} />}) 
										: <NoResult />
									}
									
								</div> : null
						}		

						{ !this.state._majorValid && this.state._majorValidated ? <div className="study-err">Must choose at least one major. Max 4 majors.</div> : null }
					</div>
					
				</div>
				{
					s.showSide ?
					<div className="columns small-12 medium-8 majors-side-view">
						<div>My Majors:</div>
						<div><small>You may choose up to four</small></div>
						<div className="tags clearfix">
							{ s.selected_majors.map( function(major){ return <AddedMajor key={major.name} major={major} removeMajor={_this._removeMajor} /> } ) }
						</div>
						{ s.majors.popular_majors && !_.isEmpty(s.majors.popular_majors) ? <div>People also chose:</div> : null }
						{ s.majors.popular_majors && !_.isEmpty(s.majors.popular_majors) ? s.majors.popular_majors.slice(0,3).map(function(major, i){ return <PopularMajor key={i+'-'+major.id} major={major} addMajor={_this._addMajor} /> }) : null }
					</div>
					: null
				}
				
			</div>
		);
	}
});

var NoResult = React.createClass({
	render: function(){
		var major = this.props.major;

		return (
			<div className="no-results">
				No results
			</div>
		);
	}
});

var MajorItem = React.createClass({
	_addMajor: function(e){
		this.props.addMajor(this.props.major);
	},

	render: function(){
		var major = this.props.major;

		return (
			<div 
				className="result" 
				onClick={ this._addMajor }>
					<div className="add">+</div>
					<div className="majorName">{major.name}</div>
			</div>
		);
	}
});

var PopularMajor = React.createClass({
	_addMajor: function(){
		this.props.addMajor( this.props.major );
	},

	render: function(){
		var major = this.props.major;

		return (
			<div className="suggested mjr">
				<div className="add" onClick={ this._addMajor }>+</div>
				<div className="name" onClick={ this._addMajor }>{ major.name }</div>
			</div>
		);
	}
});

var AddedMajor = React.createClass({
	_removeMajor: function(){
		this.props.removeMajor(this.props.major);
	},

	render: function(){
		var major = this.props.major;

		return (
			<div className="tag majr left">
				{ major.name }
				<div className="remove">
					<div>
						<div onClick={ this._removeMajor }>x</div>
					</div>
				</div>
			</div>
		);
	}
});

var Career = React.createClass({
	getInitialState: function(){
		return {
			findProfession_route: '/get_started/searchFor/career',
			careers: [],
			careers_active: false,
			career_choice: '',
			hidden_career_input: null,
			career_id: '',
			i: 0,
			traversing: !1,
			chose_from_list: false,
			_careerValid: false,
			_careerValidated: false,
			focused: '',
		};
	},

	componentWillMount: function(){
		document.addEventListener('click', this._domClick);
		document.addEventListener('keydown', this.keypressed);
	},

	componentWillUnmount: function(){
		document.removeEventListener('click', this._domClick);
		document.removeEventListener('keydown', this.keypressed);
	},

	componentWillReceiveProps: function(np){
		if( this.props.data !== np.data ){
			this.state.career_choice = np.data.profession_name,
			this.state.hidden_career_input = ( <input name="chosen_career" type="hidden" value={np.data.profession_id} key={-1} /> );
			this._validate();
		}
	},

	_domClick: function(e){
		if( $(e.target).closest('.results-container').length === 0 ) this.setState({careers: []});
	},

	keypressed: function(e){
		var key = e.which || e.keyCode;

		if( this.state.careers.length > 0 ){
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

	_notTraversing: function(){
		this._validate();
		this.setState({traversing: false});
	},

	findProfession: function(e){
		var _this = this, val = e.target.value;

		if( val ){
			$.ajax({
	            url: this.state.findProfession_route,
	            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	            data: {input: val},
	            type: 'POST'
	        }).done(function(data){
				_this.buildProfession(data);
			});
		}
	},

	buildProfession: function(data){
		var profession = [], _this = this;

		if( data.length > 0 ){
			_.each(data, function(obj){
				profession.push(<div className="result" key={obj.id} data-id={obj.id} onClick={_this.makeSelected}>{obj.profession_name}</div>);
			});
		}else{
			profession.push(<div className="result" key={-2} data-id={-2}>{'No results'}</div>);
		}
		
		this.setState({careers: profession});
	},

	_activate: function(){
		this._validate();
		this.setState({careers_active: !0});
	},

	_deactivate: function(){
		this._validate();
		this.setState({careers_active: !1});
	},

	makeSelected: function(e){
		var chosen = $(e.target).data('id'),
			txt = $(e.target).text(), newList = null, hidden_input;

		this.state.career_choice = txt;
		this.state.hidden_career_input = <input name="chosen_career" type="hidden" value={chosen} key={chosen} />;
		this._deactivate();
	},

	_update: function(e){
		this.findProfession(e);
		this._notTraversing();

		//if value is not valid, empty out hidden profession input
		this.state.career_choice = e.target.value;
		if( !e.target.value ) this.state.hidden_career_input = null,

		this._validate();
	},

	_validate: function(){
		var valid = false;

		// if( this.state.career_choice )

        // career is now optional
        valid = true;

		this.setState({
			_careerValid: valid,
			_careerValidated: true,
			focused: '_career',
		});

		this.props.validateForm('_career', valid);
	},

	render: function(){
		var careerStyles = {};

		if( this.state.focused === '_career' ) careerStyles = this.state._careerValid ? styles.good : styles.bad;
		else careerStyles = (!this.state._careerValid && this.state._careerValidated) ? styles.bad : {};

		return (
			<div className="row">
				<div className="columns small-12 medium-4 end" style={{marginBottom: '0.5rem'}}>
					<label htmlFor="_career" className="study-label">
						{'My dream would be to one day work as a(n)'}
					</label>
					<div className="has-results">
						{this.state.hidden_career_input}
						<input 
							id="_career"
							name="career" 
							type="text" 
							placeholder="Enter dream career (Optional)" 
							className="is-input"
							style={ careerStyles }
							onFocus={ this._activate } 
							value={ this.state.career_choice }
							onChange={ this._update } 
							onBlur={this._notTraversing} />

							{	
								this.state.careers_active && this.state.careers.length > 0 ?
								<div className="results-container stylish-scrollbar">
									{ this.state.careers }
								</div> : null
							}

						{ !this.state._careerValid && this.state._careerValidated ? <div className="study-err">Career option cannot be empty.</div> : null }
					</div>
				</div>
			</div>
		);
	}
});

var SchoolType = React.createClass({
	getInitialState: function(){
		return {
			school_options: null,
			school_type: -1,
			types: ['Campus_Only', 'Online_Only', 'Both'],
			_schooltypeValid: false,
			_schooltypeValidated: false,
			focused: '',
		};
	},

	componentWillReceiveProps: function(np){
		if( this.props.data !== np.data ){
			this.state.school_type = np.data.school_type;
			this._validate();
		}
	},

	_update: function(e){
		this.state.school_type = e.target.value;
		this._validate();
	},	

	_validate: function(){
		var valid = false;

		if( this.state.school_type > -1 ) valid = true;

		this.setState({_schooltypeValid: valid, _schooltypeValidated: true, focused: '_schoolType'});

		this.props.validateForm('_schoolType', valid);
	},

	render: function(){
		var schooltypeStyles = {};

		if( this.state.focused === '_schoolType' ) schooltypeStyles = this.state._schooltypeValid ? styles.good : styles.bad;
		else schooltypeStyles = (!this.state._schooltypeValid && this.state._schooltypeValidated) ? styles.bad : {};

		return (
			<div className="row">
				<div className="row-3 columns small-12 medium-4 end" style={{marginBottom: '0.5rem'}}>
					<label htmlFor="_schoolType" className="study-label">
						I am interested in <Tip />
					</label>

					<select 
						id="_schoolType"
						name="school_type" 
						style={ schooltypeStyles }
						onChange={ this._update } 
						onFocus={ this._validate }
						onBlur={ this._validate }
						value={ this.state.school_type }>
							<option key={-1} value="" disabled="disabled">{'Select one...'}</option>
							{ this.state.types.map(function(val, i){
								var converted = val.split('_').join(' ');
								return <option key={val} value={i}>{converted}</option> ;
							}) }
					</select>
					{ !this.state._schooltypeValid && this.state._schooltypeValidated ? <div className="study-err">School type cannot be left empty.</div> : null }
				</div>
			</div>
		);
	}
});

var UniversityLocation = React.createClass({
	getInitialState: function(){
		return {
			countries_ui: [],
			countries: [],
			university_location: '',
			suggested_countries: [],
			selected_countries: [{'id':1,'country_code':'US','country_name':'United States','continent_code':'NA','country_phone_code':1}],
			showSide: false,
			_locationValid: false,
			_locationValidated: false,
			focused: '',
			hidden_country_inputs: [],
			newProps: '1',
		};
	},

	componentWillMount: function(){
		this._getCountries();
		this._getSuggestedCountries();
	},

	componentWillReceiveProps: function(np){
		if( this.props.data !== np.data ){
			this._initData(np.data.university_location);
		}
	},

	_initData: function(data){
		//if data is set, save it
		if( data ) this.state.newProps = data;

		//if data is set, use data, otherwise use the saved data (when being called after getting countries)
		var data = data ? data : this.state.newProps;

		if( data ){
			var ids = data.split(','), country = null, _this = this, has_US = false;

			this.state.selected_countries = [];

			_.each(ids, function(id){
				if( id ){
					country = _.findWhere(_this.state.countries, {id: +id});
					if( country ) _this._addCountry(country);
				}
			});

			this._validate();
		}
	},

	_getCountries: function(){
		var _this = this;
		$.ajax({
			url: '/get_started/getDataFor/country',
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(data) {
			_this._buildCountries(data);
		});
	},

	_getSuggestedCountries: function(){
		var _this = this;
		$.ajax({
			url: '/get_started/getDataFor/suggest_country',
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(data) {
			_this.setState({suggested_countries: data});
		});
	},

	_buildCountries: function(data){
		var options = [];

		if( data.length > 0 ){
			this.state.countries = data;

			options.push(<option key={0} value="" disabled="disabled">{'Select one...'}</option>);

			_.each(data, function(obj){
				options.push(<option key={obj.id} value={obj.id}>{obj.country_name}</option>);
			});

			this._initData();
		}

		this.setState({countries_ui: options});
	},

	_update: function(e){
		var value = e.target.value,
			country = _.findWhere(this.state.countries, {id: +value});

		this._addCountry(country);
	},

	_addCountry: function(country){
		var copy = this.state.selected_countries.slice(),
			alreadySelected = null;

		alreadySelected = _.findWhere(copy, country);

		if( !alreadySelected && copy.length < 4 ){
			copy.push(country);
			this.state.selected_countries = copy;
			if( !this.state.showSide ) this.state.showSide = true;
			this._validate();
		}
		
	},

	_showSide: function(){
		this.state.showSide = true;
		this._validate();
	},

	_hideSide: function(){
		this.setState.showSide = false;
		this._validate();
	},

	_validate: function(){
		var valid = false;

		if( this.state.selected_countries.length > 0 ) valid = true;

		this.setState({_locationValid: valid, _locationValidated: true, focused: '_universityLocation'});

		this.props.validateForm('_universityLocation', valid);
	},

	_removeCountry: function(country){
		var countryFound = _.findWhere(this.state.selected_countries, country);
		if( countryFound ) this.state.selected_countries = _.reject(this.state.selected_countries, country);
		this._validate();
	},

	render: function(){
		var _this = this, locationStyles = {}, s = this.state;

		if( s.focused === '_universityLocation' ) locationStyles = s._locationValid ? styles.good : styles.bad;
		else locationStyles = (!s._locationValid && s._locationValidated) ? styles.bad : {};

		return (
			<div className="row">
				<div className="columns small-12 medium-4" style={{marginBottom: '0.5rem'}}>
					<label htmlFor="_universityLocation" className="study-label">
						{"I'm interested in Universities in these countries"}
					</label>

					{ s.selected_countries.length === 4 ? 
						<div style={{color: '#d90000', fontWeight: '600', fontSize: '11px', border: '1px solid #d90000', background: '#fff', padding: '5px'}}>
							{"You've reached the maximum number of countries."}
						</div> : null }

					{ s.selected_countries.map(function(country){ 
						return <input key={country.id} type="hidden" name="chosen_countries[]" value={country.id} className="is-input" /> }) 
					}

					<select 
						id="_universityLocation"
						name="university_location" 
						onChange={ this._update }
						onFocus={ this._showSide }
						onBlur={ this._hideSide }
						style={ locationStyles }
						value={ s.university_location }>
							{ s.countries_ui }
					</select>	

					{ !s._locationValid && s._locationValidated ? <div className="study-err">You must select at least one country.</div> : null }

					<div className="tags clearfix">
						{s.selected_countries.map(function(country){
							return <Country 
										key={ country.country_name } 
										country={ country } 
										remove={ _this._removeCountry } />
						})}
					</div>
				</div>

				{ s.showSide && s.suggested_countries && s.suggested_countries.length > 0 ?
					<div className="suggested-countries-container columns small-12 medium-8">
						<div>People also chose:</div>
						{ s.suggested_countries.map(function(country){
							return <SuggestedCountry key={country.country_code} country={country} addCountry={_this._addCountry} />
						}) }
					</div> 
					: null
				}
			</div>
		);
	}
});

var SuggestedCountry = React.createClass({
	_addCountry: function(){
		this.props.addCountry( this.props.country );
	},

	render: function(){
		var country = this.props.country;

		return (
			<div className="suggested">
				<div className="add" onClick={ this._addCountry }>+</div>
				<div className="name" onClick={ this._addCountry }>{ country.country_name }</div>
			</div>
		);
	}
});

var Country = React.createClass({
	_remove: function(){
		this.props.remove(this.props.country)
	},

	render: function(){
		var country = this.props.country;

		return (
			<div className="tag left">
				{country.country_name}
				<div 
					className="remove" 
					onClick={ this._remove }>
						x
				</div>
			</div>
		);
	}
});

var Tip = React.createClass({
	getInitialState: function(){
		return {
			is_hovering: false
		};
	},

	showTip: function(){
		this.setState({is_hovering: true});
	},

	hideTip: function(){
		this.setState({is_hovering: false});
	},

	render: function(){
		return (
			<div 
				className="school-tip"
				onMouseEnter={this.showTip} 
				onMouseLeave={this.hideTip} 
				onTouchStart={this.showTip} 
				onTouchEnd={this.hideTip}>
					?

					{ this.state.is_hovering ? 
						<div className="tip-container">
							<div>Types of Schools</div>
							<div>If you are interested in Online schools please select Online Only from the drop down.</div>
							<div className="arrow"></div>
						</div> : null
					}
			</div>
		);
	}
});

var Loader = React.createClass({
	render: function(){
		return(
			<div className="gs-loader">
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

ReactDOM.render( <GetStarted_Step3_Component />, document.getElementById('get_started_step3') );
