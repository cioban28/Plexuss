// GetStarted_Step5_Component.jsx
var percentage;

function wasRedirected(){
    return JSON.parse(sessionStorage.getItem('college_id'));
};

function currentPercentage(pct){
    if(pct) percentage = pct;
    return percentage;
};

var GetStarted_Step5_Component = React.createClass({displayName: "GetStarted_Step5_Component",
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
			u_max: 4,
			w_max: 5,
			user_info: null,
			no_exams_taken: !1,
			knowsGPA: true,
			showConverter: false,
		};
	},

	componentWillMount: function(){
		var classes = this.state.save_btn_classes, prev, next, num, _this = this;

		// FacebooknowsGPA event tracking
        fbq('track', 'GetStarted_Step5_Grades_Page');

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
			if( data ){
				_this.initUser(data);
				// _this.formIsValid(); 
			}
		});
	},

	initUser: function(data){
		this.setState({user_info: data, unchanged_user_info: Object.assign({}, data)});
		this.formIsValid();
		this.toggleExams();
		this.toggleExams();
	},

	save: function(e){
		if( !this.state.knowsGPA ) $('input[name="unweighted_gpa"]').val('2.87');

		var state = this.state, _this = this, formData = new FormData( $('form')[0] );

		if( $(e.target).hasClass('disable') ) e.preventDefault();
		//track if save btn has already been clicked
		if( !state.save_has_been_clicked ) state.save_has_been_clicked = !0;


		formData.append('no_exams_taken', this.state.no_exams_taken);

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
				if(data) currentPercentage(data);
				if( wasRedirected() ){
					_this.setState({is_sending: !1});//remove loader
					$(document).trigger('saved');
				}else window.location.href = state.next_route;
			});
		}
	},

	formIsValid: function(){
		var inputs = $('form .is-input'), validForm = !0, weighted_gpa = $('input[name="weighted_gpa"]'), 
			validForm = !0, _this = this, val = null, min = null, max = null, tempStateObj = null, 
			name = null, validField = null;

		if (weighted_gpa.length > 0)
			inputs = inputs.add(weighted_gpa);

		$.each(inputs, function(){
			name = $(this)[0].name;
			validField = !0;
			tempStateObj = {};

			tempStateObj[name+'_is_valid'] = validField;

			// If user doesn't know their gpa, skip validation for GPA. Everything else still needs validation
			if ( (name == 'unweighted_gpa' || name == 'weighted_gpa') && !_this.state.knowsGPA )
				return;

			// Do not validate exams if no exam taken
			if (_this.state.no_exams_taken && !name.includes('gpa'))
				return;

			if ($(this)[0].disabled || $(this).closest('.exam-field').hasClass('disabled'))
				return;

			val = $(this).val();
			min = parseFloat($(this).attr('min'));
			max = parseFloat($(this).attr('max'));

			if ( name == 'weighted_gpa' && val == '' ) {
				_this.setState(tempStateObj);
				return;
			}

			if( !val || val < min || val > max ){ //if< value is emtpy then make and return false
				validForm = !1;
				validField = !1;
				tempStateObj[name+'_is_valid'] = validField;
			}

			_this.setState(tempStateObj);
		});

		//when valid change state to true to remove error msg
		_this.setState({is_valid: validForm});
		return validForm;
	},

	makeSaveActive: function(e){
		if( this.formIsValid() ) this.setState({is_valid: !0});
	},

	toggleExams: function(){
		this.setState( { no_exams_taken: !this.state.no_exams_taken },
			function() {

				// Enable/Disable inputs for formData to send to backend
				$('.form-container.gpa-inner-wrap input').prop('disabled', this.state.no_exams_taken);
				
				this.makeSaveActive();

			});
	},

	checkForEnterKey: function(){
		var _this = this;

		$('.submit-btn').on('keydown', function(e){
			if( e.which === 13 ) $(this).trigger('click');
		});
	},

	_idkGPA: function(){
		this.state.knowsGPA = !this.state.knowsGPA;
		this.formIsValid();
	},

	_toggleGPAConverter: function(){
		this.setState({showConverter: !this.state.showConverter});
	},

	render: function(){
		var saveBtnClasses = '',
			user = this.state.user_info,
			unchanged_user_info = this.state.unchanged_user_info,
			unweighted = user ? (user.overall_gpa || user.hs_gpa) : '',
			weighted = user ? (user.weighted_gpa || '') : '',
			knowsGPA = this.state.knowsGPA,
			showConverter = this.state.showConverter,
			is_us_student = user ? user.country_name === 'United States' : true;

		if( !this.state.is_valid ) saveBtnClasses = 'right btn submit-btn text-center disable';
		else saveBtnClasses = 'right btn submit-btn text-center';

		return (
			React.createElement("div", {className: "step_container"}, 
				React.createElement("div", {className: "row gpa_page"}, 
					React.createElement("div", {className: "column small-12"}, 
						React.createElement("div", {className: "row"}, 
							React.createElement("div", {className: "columns small-12 medium-6 large-7"}, 
								React.createElement("div", {className: "intro"}, "Enter your GPA so far"), 
								React.createElement("br", null), 
								React.createElement("form", null, 
									React.createElement("input", {type: "hidden", name: "step", value: this.state.step_num}), 	

									React.createElement("input", {
										id: "idk_gpa", 
										type: "checkbox", 
										checked:  !knowsGPA, 
										onChange:  this._idkGPA, 
										value: "2.87"}), 
									React.createElement("label", {className: "no_exams", htmlFor: "idk_gpa"}, "I don't know my GPA"), 

									React.createElement("br", null), 
									React.createElement("br", null), 

									React.createElement("div", {className:  knowsGPA ? 'all_gpa_container' : 'all_gpa_container hide'}, 
										 !is_us_student && React.createElement(NumberInput, {
																name: "unweighted_gpa", 
																isValid: this.makeSaveActive, 
																req: "true", 
																max: this.state.u_max, 
																val: unweighted, 
																not_us: true, 
																validated: this.state['unweighted_gpa_is_valid'] != null ? this.state['unweighted_gpa_is_valid'] : true}), 

										 is_us_student && React.createElement("div", null, 
																React.createElement(NumberInput, {name: "unweighted_gpa", isValid: this.makeSaveActive, req: "true", 
																			max: this.state.u_max, val: unweighted, 
																			validated: this.state['unweighted_gpa_is_valid'] != null ? this.state['unweighted_gpa_is_valid'] : true}), 
																React.createElement("br", null), 
																React.createElement(NumberInput, {name: "weighted_gpa", isValid: this.makeSaveActive, 
																			max: this.state.w_max, val: weighted, 
																			validated: this.state['weighted_gpa_is_valid'] != null ? this.state['weighted_gpa_is_valid'] : true})
															), 

										React.createElement("br", null)
	
									), 

									React.createElement("input", {id: "no_exams_taken", type: "checkbox", name: "no-exams-taken", onChange: this.toggleExams, checked: this.state.no_exams_taken}), 
									React.createElement("label", {className: "no_exams", htmlFor: "no_exams_taken"}, "I have not taken any exams yet"), 
									React.createElement("br", null), 

									React.createElement(CollegeExams, {scores: user || null, unchangedUserInfo: unchanged_user_info, examsTaken: this.state.no_exams_taken, isValid: this.formIsValid, makeSaveActive: this.makeSaveActive}), 

									 !this.state.is_valid && this.state.save_has_been_clicked ? 
										React.createElement("div", {className: "err"}, 
											React.createElement("div", null, React.createElement("small", null, "Check for invalid entries")), 
											React.createElement("div", null, React.createElement("small", null, "Fields cannot be left empty")), 
											React.createElement("div", null, React.createElement("small", null, 'Unweighted parameters: 0.01 - 4')), 
											React.createElement("div", null, React.createElement("small", null, 'Weighted parameters: 0.01 - 5'))
										) : null, 
									

									React.createElement("div", {className: "submit-row clearfix"}, 
										React.createElement("div", {className: "left btn back-btn hide-for-small-only"}, React.createElement("a", {href: this.state.back_route}, "Go Back")), 
										React.createElement("div", {tabIndex: "0", className: saveBtnClasses, onClick: this.save, onFocus: this.checkForEnterKey}, "Next"), 
										React.createElement("div", {className: "right text-center btn back-btn show-for-small-only"}, React.createElement("a", {href: this.state.back_route}, "Go Back"))
									), 
									React.createElement("div", {className: "clearfix"}, 
										React.createElement("div", {className: "right hide-for-small-only"}, React.createElement("small", null, '*Required for recruitment')), 
										React.createElement("div", {className: "text-center show-for-small-only"}, React.createElement("small", null, '*Required for recruitment'))
									)
								)
							), 
							
							React.createElement("div", {className: "column small-12 medium-6 large-5 g-con"}, 
								React.createElement("div", {className: "gpa-conv-btn hide-for-small-only"}, 
									React.createElement("img", {className: "calc", src: "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/calc_icon.png"}), 
									 !showConverter && React.createElement("div", null, "Not from the United States?"), 
									 !showConverter && React.createElement("div", {onClick:  this._toggleGPAConverter}, React.createElement("u", null, "Use this GPA Converter")), 
									 showConverter && React.createElement("div", {className: "closex", onClick:  this._toggleGPAConverter}, React.createElement("u", null, "Close"))
								), 

								React.createElement("div", {className:  showConverter ? '' : 'hide'}, 
									React.createElement(GPA_Conversion, {country: user ? (user.country_name || '') : ''})
								)
							)
						)
					)
				), 

				 this.state.is_sending ? React.createElement(Loader, null) : null
			)
		);
	}
});

var NumberInput = React.createClass({displayName: "NumberInput",
	getInitialState: function(){
		return {
			valu: ''
		};
	},

	componentWillReceiveProps: function(nextProps){
		//only if TextInput parent passes new val, then setstate with that new value	
		if( nextProps.val !== this.props.val ){
			this.setState({valu: nextProps.val});
		}
	},

	getPlacholder: function(){
		var name = this.props.name;
		// return 'Ex: 3.45';
		return '0.1 - ' + this.props.max + '.0';
	},	

	getLabel: function(){
		var name = this.props.name;

		if( this.props.not_us ) return 'GPA';

		return name.split('_').join(' ').replace('gpa', 'GPA');
	},

	update: function(e){
		this.props.isValid();
		this.setState({valu: e.target.value});
	},

	render: function(){
		var placehldr = this.getPlacholder(), label_name = this.getLabel(),
			input_class = this.props.req ? 'is-input' : '',
			_style = {}, _inputStyle = {}, _finputStyle = {};

		input_class += this.props.validated ? '' : ' invalid';

		if( this.props.not_us ){
			_style = {width: 'auto'};
			_inputStyle = {margin: '0 0 0 40px'};
			_finputStyle = {width: 'auto'};
		}

		return (
			React.createElement("div", {className: "form-container gpa clearfix"}, 
				React.createElement("div", {className: "f-label capitalize", style: _style}, 
					React.createElement("label", null, label_name)
				), 
				React.createElement("div", {className: "f-input", style: _finputStyle}, 
					React.createElement("input", {name: this.props.name, type: "number", min: "0.01", max: this.props.max, step: "0.01", 
							placeholder: placehldr, className: input_class, onChange: this.update, 
							style: _inputStyle, 
							value: this.state.valu}), 
					 !this.props.validated && React.createElement("small", null, "Only values between ", 0.01, " and ", this.props.max, " accepted")
				), 
				
					this.props.req ?
					React.createElement("div", {className: "f-label text-center"}, 
						React.createElement("label", null, '*')
					) : null
				
			)
		);
	}
});

var CollegeExams = React.createClass({displayName: "CollegeExams",
	getInitialState: function(){
		return {
			exams: [],
			active_exam: null,
			exam_ui: [],
			exam_btn_ui: [],
			scores: null
		};
	},

	componentWillMount: function(){
		var ex = [], 
			Exam = function Exam(id, name, displayName, type, fields, range, rangeToField, step, scores){
				this.id = id || null;//id
				this.name = name || null;//name
				this.displayName = displayName || null;//button display name
				this.type = type || null;//input type Ex: number or text
				this.fields = fields || null;//array of fields, names provided will be label names
				this.range = range || null;//min and max ranges for number inputs
				this.rangeToField = rangeToField || null;//indicates at which field to update the range for, if greater than 1, than multiple ranges have been provided
				this.step = step || null;//step counter
				this.active = !1;
				this.changed = !1;
				this.scores = scores || null;
			};

		ex.push( new Exam('act', ['ACT'], 'ACT', ['number'], ['English', 'Math', 'Composite'], ['1-36'], null, '1') );
		ex.push( new Exam('ap', ['AP'], 'AP Test', ['number'], ['Overall'], ['1-5'], null, '1') );
		// ex.push( new Exam('sat', ['SAT'], ['number'], ['Math', 'Reading/Writing', 'Total'], ['200-800', '400-1600'], ['3'], '100') );//post 2016 sat scores
		ex.push( new Exam('sat', ['SAT'], 'SAT', ['number'], ['Math', 'Reading/Writing', 'Total', 'pre2016Writing', 'pre2016Math', 'pre2016Reading', 'pre2016Total'], ['200-800', '400-1600', '200-800', '600-2400'], ['3', '4', '7'], '100') );//pre 2016 sat scores
		// ex.push( new Exam('psat', ['PSAT'], ['number'], ['Math', 'Reading/Writing', 'Total'], ['160-760', '320-1520'], ['3'], '100') );//post Oct 2015 psat scores
		ex.push( new Exam('psat', ['PSAT'], 'PSAT', ['number'], ['Math', 'Reading/Writing', 'Total', 'pre2016Writing', 'pre2016Math', 'pre2016Reading', 'pre2016Total'], ['160-760', '320-1520', '20-80', '60-240'], ['3', '4', '7'], '100') );//pre Oct 2015 psat scores
		ex.push( new Exam('lsat', ['LSAT'], 'LSAT', ['number'], ['Total'], ['120-180'], null, '1') );
		ex.push( new Exam('ged', ['GED'], 'GED', ['number'], ['Score'], ['200-800'], null, '1') );
		ex.push( new Exam('gmat', ['GMAT'], 'GMAT', ['number'], ['Total'], ['200-800'], null, '1') );
		ex.push( new Exam('gre', ['GRE'], 'GRE', ['number'], ['Verbal Reasoning', 'Quantitative Reasoning', 'Analytical Writing'], ['130-170', '0-6'], ['3'], '1') );
		ex.push( new Exam('toefl', ['TOEFL'], 'TOEFL', ['number'], ['Reading', 'Listening', 'Speaking', 'Writing', 'Total'], ['0-68', '0-30', '0-90'], [3, 5], '1') );
		ex.push( new Exam('ibt', ['iBT'], 'TOEFL iBT', ['number'], ['Reading', 'Listening', 'Speaking', 'Writing', 'Total'], ['0-30', '0-120'], [5], '1') );
		ex.push( new Exam('pbt', ['PBT'], 'TOEFL PBT', ['number'], ['Reading', 'Listening', 'Written', 'Total'], ['31-67', '31-68', '310-677'], [2, 4], '1') );
		ex.push( new Exam('pte', ['PTE'], 'PTE Academic', ['number'], ['Total'], ['10-90'], null, '1') );
		ex.push( new Exam('ielts', ['IELTS'], 'IELTS', ['number'], ['Reading', 'Listening', 'Speaking', 'Writing', 'Total'], ['0-9'], null, '1') );
		ex.push( new Exam('other', ['OTHER'], 'OTHER', ['text', 'number'], ['Exam', 'Score'], [ 'Exam Name', '0-100'], [2], '1') );

		this.state.exams = ex;
		this.buildExams();
	},

	componentWillReceiveProps: function(nextProps){
		//only if TextInput parent passes new val, then setstate with that new value	
		if( nextProps.scores !== this.props.scores ){
			this.setState({scores: nextProps.scores}, this.buildExams);
		}
	},

	examChosen: function(e){
		var target = e.target.id, exam = null, 
			exams = this.state.exams.slice();

		// this.props.isValid(); // Causing problems
		//get exam obj
		exam = this.getExam(target.split('_')[1]);

		//if a different target than the currently active, then close active and make target active
		//if target is same as currently active, make active false
		if( this.state.active_exam ){
			this.resetActiveExams();//reset currently active exam
			if( exam.id !== this.state.active_exam.id  ) exam.active = !0;//set target exam to active if not already active
			else exam = null;//set exam to null if target and active exam are the same
		}else{
			exam.active = !0;//set target exam to active
		}

		this.state.exams = exams;
		this.state.active_exam = exam;
		this.buildExams();
	},

	examChanged: function(e){
		var target = e.target, id = target.id, val = target.value, _this = this,
			type = target.getAttribute('type'), min = null, max = null,
			this_exam = _.extend({}, this.state.active_exam), name = target.getAttribute('name'),
			scores = this.state.scores, tmp_obj = {}, tmp_validated_obj = {}, isValid = true,
			exam_validated = {};

		if( scores ) this.state.scores[name] = val;//update value
		else{
			tmp_obj[name] = val;
			this.state.scores = tmp_obj;
		}
		if( this_exam ) this_exam.scores[name] = val;

		tmp_validated_obj[id + '_is_valid'] = !0;

		//if number convert values to numbers, else type = text so leave it as string
		if( type === 'number' ){
			min = parseInt(target.getAttribute('min')); 
			max = parseInt(target.getAttribute('max'));
			if( ''+val || val == '' ){
				if( ( parseFloat(val) >= min && parseFloat(val) <= max ) || val.length === 0 ) {
					isValid = true;
					tmp_validated_obj[id + '_is_valid'] = isValid;
					this_exam.changed = isValid //changed to true
				}else {
					isValid = false;
					this_exam.changed = isValid;//changed to false
					tmp_validated_obj[id + '_is_valid'] = isValid;
				}
			}else this_exam.changed = !1;//changed to false
		}else{
			if( val ) this_exam.changed = !0;//changed to true
			else this_exam.changed = !1;//changed to false
		}

		this.setState(tmp_validated_obj, 
			
			function() {

				exam_validated[this_exam.id + '_is_valid'] = 
					this.isActiveExamValid() ? true : false;

				this.setState(exam_validated, this.buildExams);
			}

		);
		return isValid;
	},

	validateActiveExam: function() {
		var this_exam = _.extend({}, this.state.active_exam),
			_this = this,
			event = null,
			all_exam_fields_valid = true,
		 	current_field_is_valid = true,
		    exam_valid = {};

		this_exam.fields.forEach(function(field) {
		 	event = {};
			field = field.split('/').join('_').toLowerCase();
			field_id = this_exam.id + '_' + field;

			// Create a pseudo event to work with examChanged(event)
			event['target'] = document.getElementById(field_id);

			current_field_is_valid = _this.examChanged(event);

			if (!current_field_is_valid) { all_exam_fields_valid = false; }

		});

		exam_valid[this_exam.id + '_is_valid'] = all_exam_fields_valid;

		this.setState(exam_valid, this.buildExams);

		return all_exam_fields_valid;
	},

	isActiveExamValid: function() {
		var this_exam = _.extend({}, this.state.active_exam),
		    field = null;

		for ( var i = 0; i < this_exam.fields.length; i++ ) {
			field = this_exam.fields[i].split('/').join('_').toLowerCase();
			field_id = this_exam.id + '_' + field;
			if ( this.state[field_id + '_is_valid'] != null && !this.state[field_id + '_is_valid'] ) {
				return false;
			}
		}

		return true;

	},

	resetActiveExams: function(){
		var copy = this.state.exams.slice(), exam = null, btn = null;

		//reset exam fields
		exam = _.findWhere(copy, {active: !0});
		if( exam ) exam.active = !1; //make inactive
	},

	getExam: function(exam){
		var copy = this.state.exams.slice();
		return _.findWhere(copy, {id: exam});
	},

	buildExams: function(){
		var e = this.state.exams.slice(), ui = [],
			btn_ui = [], btn_name = '', btn_id = '', key = null,
			scores = this.state.scores, prop = null, score_vals = null,
			validations = null, exam_validated = null, is_pre_2016 = null;
		
		for (var i = 0; i < e.length; i++) {
			is_pre_2016 = 0;
			exam_validated = this.state[e[i].id+'_is_valid'] != null 
								? this.state[e[i].id+'_is_valid']
								: true;

			btn_name = e[i].displayName;

			btn_id = 'exam_'+e[i].id;
			key = (i+1)*-1;

			validations = _.pick(this.state, function(value, key) {
				return key.startsWith(e[i].id) && key.endsWith('_is_valid');
			});

			//reset array
			score_vals = {};
			//store only values that would belong to this section
			if( scores ){
				for(prop in scores){
					if( prop.includes('ibt') || prop.includes('pbt') ) {
						if( ( prop.includes('ibt') && e[i].name.includes('iBT') ) || ( prop.includes('pbt') && e[i].name.includes('PBT') ) )
							score_vals[prop] = scores[prop];
					}else{
						if( prop.split('_')[0] === e[i].name[0].toLowerCase() )
							score_vals[prop] = scores[prop];
					}
				}
				
				if (e[i].id == 'sat' || e[i].id == 'psat') {
					is_pre_2016 = this.props.unchangedUserInfo['is_pre_2016_' + e[i].id];
				}
			}

			e[i].scores = score_vals;
			
			btn_ui.push( React.createElement(Exam_Button, {key: key, id: btn_id, name: btn_name, 
				examChosen: this.examChosen, active: e[i].active, validated: exam_validated, makeSaveActive: this.props.makeSaveActive, changed: e[i].changed}) );

			ui.push(React.createElement(Exam, {key: i, exam: e[i].name, type: e[i].type, fields: e[i].fields, is_pre_2016: is_pre_2016 || 0, 
				range: e[i].range, whichRangeToField: e[i].rangeToField, step: e[i].step, validateActiveExam: this.validateActiveExam, 
				active: e[i].active, changed: this.examChanged, val: score_vals, examValidated: exam_validated, validations: validations, validate: this.props.isValid, makeSaveActive: this.props.makeSaveActive}));
		}

		this.setState({
			exam_ui: ui,
			exam_btn_ui: btn_ui
		});
	},

	render: function(){
		var active_exam = this.state.active_exam, btn_classes,
			containerClasses = 'form-container gpa-inner-wrap';

		this.props.examsTaken ? containerClasses += ' hide' : null;

		return (
			React.createElement("div", {className: containerClasses}, 
				React.createElement("div", null, "College Entrance Exams"), 
				React.createElement("div", {className: "clearfix exam-options"}, 
					this.state.exam_btn_ui					
				), 
				this.state.exam_ui
			)
		);	
	}
});

var Exam_Button = React.createClass({displayName: "Exam_Button",
	getInitialState: function(){
		return {
			validated: true
		}
	},

	componentWillReceiveProps: function(nextProps){
		if( nextProps.validated !== this.state.validated ){
			this.setState({validated : nextProps.validated}, this.props.makeSaveActive);
		}
	},

	render: function(){
		var classes = this.props.active ? 'left exam-btn active' : 'left exam-btn',
			name = this.props.changed ? [this.props.name, React.createElement("span", {key: -1}, "✓")] : this.props.name;

			classes += this.state.validated ? '' : ' invalid';

		return(
			React.createElement("div", {id: this.props.id, className: classes, onClick: this.props.examChosen}, name)
		);
	}
});

var Exam = React.createClass({displayName: "Exam",
	getInitialState: function(){
		return {
			fields: null,
			scores: null,
			resultingFields: null,
			is_pre_2016: 0
		};
	},

	componentWillMount: function(){
		var exam_fields = this.getExamFields();
		this.setState({fields: exam_fields});
	},

	componentWillReceiveProps: function(nextProps){
		//only if TextInput parent passes new val, then setstate with that new value	
		if( nextProps.val !== this.props.val ){

			// this.state.scores = nextProps.val;

			this.setState({ examValidated: nextProps.examValidated,
							validations: nextProps.validations, 
							fields: this.getExamFields(),
							scores: nextProps.val },
							this.getExamFields);
		}

		if ( this.props.is_pre_2016 != nextProps.is_pre_2016 ){
			this.setState({	is_pre_2016: nextProps.is_pre_2016 },
						    this.getExamFields);
		}
	},

	getExamFields: function(){
		var p = this.props, curr_type = null, curr_range = null, rangeField_cnt = 0,
			curr_rangeToField = null, eFields = [], curr_min = null, curr_max = null,
			curr_name = null, placeholder = '', scores = this.state.scores, score = '', is_old_exam,
			field_class = '', validations = this.state.validations, validation = null, 
			hidden_div_classes = null, display_label = null, input_name = null, checkBoxName = null,
			pre2016_checkbox_name = null

		curr_type = p.type[0];
		curr_range = p.range[0];
		curr_min = curr_range.split('-')[0];
		curr_max = curr_range.split('-')[1];

		is_old_exam = (p.exam[0] === 'PSAT' || p.exam[0] === 'SAT') && (p.fields.length === 7) ? !0 : !1;
		
		pre2016_checkbox_name = 'is_pre_2016_' + p.exam[0].toLowerCase();

		if( is_old_exam ){
			eFields.push(
				React.createElement("div", null, 
					React.createElement("input", {className: this.state.examValidated ? '' : 'invalid', id: 'pre_'+p.exam[0], type: "checkbox", name: pre2016_checkbox_name, onChange: this.togglePreExams, checked: this.state.is_pre_2016, value: this.state.is_pre_2016}), 
					React.createElement("label", {className: "is-pre", htmlFor: 'is_pre_'+p.exam[0]}, "I took the ", p.exam[0], " before 2016")
				)
			);
		}

		for (var i = 0; i < parseInt(p.fields.length); i++) {
			//each prop will have a type
			//if there are multiple types, then after each iteration, update current type
			//if at the last type in arr, then keep that as current type
			if( i < p.type.length ){
				if( curr_type !== p.type[i] ){
					curr_type = p.type[i];
				}
			}

			//if prop has whichRangeToField prop
			if( p.whichRangeToField ){
				//if first value of this prop is reached, update the current min/max
				if( i+1 === parseInt(p.whichRangeToField[rangeField_cnt]) ){
					curr_range = p.range[++rangeField_cnt];
					curr_min = curr_range.split('-')[0];
					curr_max = curr_range.split('-')[1];
				}
			}	
			
			//used for input name, id, and label for, remove 'pre2016' from exams before 2016
			display_label = p.fields[i].replace('pre2016', '');

			curr_name = p.exam+'_'+p.fields[i].replace('/', '_');

			input_name = curr_name.replace('pre2016', '').replace('/', '_');

			if (p.exam.includes('iBT') || p.exam.includes('PBT')) {
				input_name = 'toefl_' + curr_name;
			}

			//if name is other_score then change name
			if( curr_name.toLowerCase() === 'other_score' ) {
				curr_name = curr_name.toLowerCase().replace('score', 'values');
				input_name = input_name.toLowerCase().replace('score', 'values');
			}

			//if name has a space, split it and get first value
			if( curr_name.indexOf(' ') > -1 ) curr_name = curr_name.split(' ')[0];

			//if name contains a '/' it is the new combined reading/writing field, so convert to db col name readingWriting
			if( curr_name.indexOf('/') > -1 ) curr_name = curr_name.split('/').join('_');

			//if scores is not falsy, get relevant score by name, else give it empty string
			if( scores ) {
				score = scores[input_name.toLowerCase()];
			} else {
				score = '';	
			}

			field_class = score ? 'is-input' : ''; 
			placeholder = curr_min && curr_max ? curr_min + ' - ' + curr_max : p.range[i];

			validated = validations ? validations[curr_name.toLowerCase() + '_is_valid'] : null; 
			
			field_class += ( validated != null && validated == false ) ? ' invalid' : '';

			hidden_div_classes = 'left exam-field disabled hide';
			if( curr_type === 'number' ){
				if( this.state.is_pre_2016 ) {
					eFields.push( React.createElement("div", {className: p.fields[i].includes('pre2016') ? 'left exam-field' : hidden_div_classes, key: i}, 
									React.createElement("label", {htmlFor: curr_name.toLowerCase()}, display_label+':'), 
									React.createElement("input", {id: curr_name.toLowerCase(), name: input_name.toLowerCase(), placeholder: placeholder, 
											type: curr_type, min: curr_min, max: curr_max, className: field_class, 
											step: p.step, onChange: p.changed, value: score, disabled: !p.fields[i].includes('pre2016')}), 
									 validated != null && !validated && React.createElement("small", null, "Only values between ", curr_min, " and ", curr_max, " accepted")
								) );
				}else {
					eFields.push( React.createElement("div", {className: p.fields[i].includes('pre2016') ? hidden_div_classes : 'left exam-field', key: i}, 
									React.createElement("label", {htmlFor: curr_name.toLowerCase()}, display_label+':'), 
									React.createElement("input", {id: curr_name.toLowerCase(), name: input_name.toLowerCase(), placeholder: placeholder, 
											type: curr_type, min: curr_min, max: curr_max, className: field_class, 
											step: p.step, onChange: p.changed, value: score, disabled: p.fields[i].includes('pre2016')}), 
									 validated != null && !validated && React.createElement("small", null, "Only values between ", curr_min, " and ", curr_max, " accepted")
								) );
				} 
			}else{
				
				eFields.push( React.createElement("div", {className: "left exam-field", key: i}, 
								React.createElement("label", {htmlFor: curr_name.toLowerCase()}, p.fields[i]+':'), 
								React.createElement("input", {id: curr_name.toLowerCase(), name: input_name.toLowerCase(), placeholder: placeholder, 
									 type: curr_type, onChange: p.changed, value: score, className: field_class})
							) );
			}
		};

		// return eFields;
		this.setState({resultingFields: eFields});
	},

	togglePreExams: function(event){
		event.persist();
		this.setState({ is_pre_2016: +!this.state.is_pre_2016 },
			function() {
				this.props.changed(event);
				this.props.validateActiveExam();
			}
		);
	},

	render: function(){
		if( !this.props.active ) classes = 'clearfix exam hide';
		else classes = 'clearfix exam';
		return (
			React.createElement("div", {className: classes}, 
				this.state.resultingFields
			)
		);
	}
});

var GPA_Conversion = React.createClass({displayName: "GPA_Conversion",
	getInitialState: function(){
		return {
			getCountries_route: '/get_started/getGradeCountries',
			getConversions_route: '/get_started/getGradeConversions',
			country: '',
			country_list: [],
			conversion_ui: [],
			country_options: []
		};
	},

	componentWillMount: function(){
		this.getCountries();		
	},

	componentWillReceiveProps: function(nextProps){
		if( nextProps.country !== this.props.country ){
			if( this.state.country_list.length === 0 ) this.state.country = nextProps.country;
			else if( this.weHaveUsersCountry(nextProps.country) ) this.initConversions(nextProps.country);
		}
	},

	weHaveUsersCountry: function(name){
		var match = !1;

		_.each(this.state.country_list, function(obj){
			if( obj === name ){
				match = !0;
				return !0;
			}
		});

		return match;
	},

	initConversions: function(name){
		this.setState({country: name});
		this.getConversions(name);
	},

	getCountries: function(){
		var _this = this, ctry = _this.state.country;
		$.ajax({
            url: _this.state.getCountries_route,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'POST'
        }).done(function(data){
			if( data && data.length > 0 ){
				_this.state.country_list = data;
				_this.buildOptionsUI();
			}

			ctry = _this.state.country;
			if( ctry && _this.weHaveUsersCountry(ctry) ) _this.initConversions(ctry);
		});
	},

	getConversions: function(val){
		var _this = this;
		$.ajax({
            url: _this.state.getConversions_route,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {name: val},
            type: 'POST'
        }).done(function(data){
			if( data && data.length > 0 ) _this.buildConversionsUI(data);
		});
	},

	buildOptionsUI: function(data){
		var ui = [];

		ui.push( React.createElement("option", {key: -1, value: ''}, 'Select country...') );
		_.each(this.state.country_list, function(name, i){
			ui.push( React.createElement("option", {key: i, value: name}, name) );
		});

		this.setState({country_options: ui});
	},

	buildConversionsUI: function(data){
		var ui = [];

		_.each(data, function(obj, i){
			ui.push( React.createElement(Conversion, {key: i, data: obj}) );
		});

		this.setState({conversions_ui: ui});
	},

	update: function(e){
		this.setState({country: e.target.value});
		this.getConversions(e.target.value);
	},

	render: function(){
		return (
			React.createElement("div", {className: "grade-conversion hide-for-small-only"}, 
				React.createElement("div", null, "Grade Conversion"), 
				React.createElement("label", {htmlFor: "country_conversion"}, "Country"), 
				React.createElement("select", {id: "country_conversion", name: "countryconversion", value: this.state.country, onChange: this.update}, 
					this.state.country_options
				), 
				React.createElement("div", {className: "country"}, this.state.country), 
				 this.state.conversions_ui ? 
						React.createElement("div", {className: "conversions"}, 
							React.createElement("div", null, "Scale"), 
							React.createElement("div", null, "Description"), 
							React.createElement("div", null, "U.S. Grade")
						) : null, 
				
				React.createElement("div", {className: "conversions-container stylish-scrollbar-mini"}, 
					this.state.conversions_ui
				)
			)
		);
	}
});

var Conversion = React.createClass({displayName: "Conversion",
	render: function(){
		return (
			React.createElement("div", {className: "conversions"}, 
				React.createElement("div", null, this.props.data.scale), 
				React.createElement("div", null, this.props.data.description), 
				React.createElement("div", null, this.props.data.us_grade)
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
})

ReactDOM.render( React.createElement(GetStarted_Step5_Component, null), document.getElementById('get_started_step5') );