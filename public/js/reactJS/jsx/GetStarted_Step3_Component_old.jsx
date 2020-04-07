// GetStarted_Step3_Component.jsx
var percentage;

function wasRedirected(){
    return JSON.parse(sessionStorage.getItem('college_id'));
};

function currentPercentage(pct){
    if(pct) percentage = pct;
    return percentage;
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
			errmsg: ''
		};
	},

	componentWillMount: function(){
		var classes = this.state.save_btn_classes, prev, next, num, _this = this;

		// Facebook event tracking
        fbq('track', 'GetStarted_Step3_Study_Old_Page');

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
			_this.formIsValid();
		});
	},

	initUser: function(data){
		var info = {
			degree_type: null,
			major_ids: [],
			profession_id: null
		}, found = false;

		_.each(data, function(obj, ind){
			if( ind === 0 ){ //on first index, save degree, profession id and name, school type, and add first major if it doesn't already exist
				info.degree_type = obj.degree_type;
				found = _.findWhere(info.majors, {id: obj.major_id});
				if( !found ) info.major_ids.push({id: +obj.major_id, name: obj.major_name});
				info.profession_id = obj.profession_id;
				info.profession_name = obj.profession_name;
				info.school_type = obj.school_type;
			}else{
				info.major_ids.push({id: +obj.major_id, name: obj.major_name});
			}
		});

		this.setState({user_info: info});
	},

	save: function(e){
		var formData = new FormData( $('form')[0] ), state = this.state, _this = this;

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
				//if data has msg prop, it's an error
				if( data.msg ){
					_this.setState({
						errmsg: data.msg,
						is_sending: !1
					});
				}else{
					if(data) currentPercentage(data);
					if( wasRedirected() ){
						_this.setState({
							is_sending: !1,
							errmsg: ''
						});//remove loader and err msg
						$(document).trigger('saved');
					}else window.location.href = state.next_route;
				}
			});
		}
	},

	formIsValid: function(){
		var inputs = $('form .is-input'), valid = !0, _this = this;

		$.each(inputs, function(){
			if( !$(this).val() ){ //if value is emtpy then make and return false
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

	checkForEnterKey: function(){
		var _this = this;

		$('.submit-btn').on('keydown', function(e){
			if( e.which === 13 ) $(this).trigger('click');
		});
	},

	render: function(){
		var saveBtnClasses = '', 
			user = this.state.user_info,
			degree = user ? (user.degree_type || '') : '',
			majors = user ? (user.major_ids || '') : '',
			profess = user ? (user.profession_id || '') : '',
			profess_name = user ? (user.profession_name || '') : '',
			type = user ? user.school_type : '';

		if( !this.state.is_valid ) saveBtnClasses = 'right btn submit-btn text-center disable';
		else saveBtnClasses = 'right btn submit-btn text-center';

		return (
			<div className="step_container">
				<div className="row">
					<div className="column small-12">

						<div className="inner-col">
							<div className="intro">{'What do you want to study and do?'}</div>
							<br />

							<form>
								<input type="hidden" name="step" value={this.state.step_num} />		

								<FormField_1 isValid={this.makeSaveActive} degree={degree} majors={majors} />
								<FormField_2 isValid={this.makeSaveActive} profession={profess} professionName={profess_name} error={this.state.errmsg} />
								<FormField_3 isValid={this.makeSaveActive} type={type} />
								
								{ !this.state.is_valid && this.state.save_has_been_clicked ? <div className="err"><small>Fields cannot be emtpy.</small></div> : null }
								{ this.state.errmsg ? <div className="err"><small>{this.state.errmsg}</small></div> : null }

								<div className="submit-row clearfix">
									<div className="left btn back-btn hide-for-small-only"><a href={this.state.back_route}>Go Back</a></div>
									<div tabIndex="0" className={saveBtnClasses} onClick={this.save} onFocus={this.checkForEnterKey}>Next</div>
									<div className="right text-center btn back-btn show-for-small-only"><a href={this.state.back_route}>Go Back</a></div>
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

var FormField_1 = React.createClass({
	getInitialState: function(){
		return {
			degree_options: null,
			findMajor_route: '/get_started/searchFor/major',
			majors: [],
			majors_active: false,
			selected_majors: [],
			currently_scrolling: false,
			hidden_major_inputs: null,
			major_val: '',
			major_tags: null,
			degree_val: '',
			traversing: !1
		};
	},

	componentWillMount: function(){
		var _this = this;

		document.addEventListener('click', this.domClick);
		document.addEventListener('keydown', this.keypressed);

		$.ajax({
			url: '/get_started/getDataFor/degree',
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(data) {
			_this.buildDegree(data);
		});
	},

	componentWillReceiveProps: function(nextProps){
		if( nextProps.degree !== this.props.degree ){
			this.state.selected_majors = nextProps.majors.slice();
			this.state.degree_val = nextProps.degree;
			this.state.major_val = nextProps.majors.slice().pop().name;
			this.buildMajors();
		}
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

	domClick: function(e){
		if( $(e.target).closest('.results-container').length === 0 ) this.setState({majors: []});
	},

	buildDegree: function(data){
		var degrees = [];

		degrees.push( <option key={-1} value="">{'Select one...'}</option> );	
		_.each(data, function(obj){
			degrees.push(<option key={obj.id} value={obj.id}>{obj.display_name}</option>);
		});
		this.setState({degree_options: degrees});
	},	

	buildMajor: function(data){
		var major = [], _this = this;

		if( data.length > 0 ){
			_.each(data, function(obj){
				major.push(<div className="result" key={obj.id} data-id={obj.id} onClick={_this.addMajor}>{obj.name}</div>);
			});
		}else{
			major.push(<div className="result" key={-1} data-id={-1}>{'No results'}</div>);
		}

		this.setState({majors: major});
	},

	findMajor: function(e){
		var _this = this, val = e.target.value;
		this.setState({major_val: val});

		if( val ){
			$.ajax({
	            url: this.state.findMajor_route,
	            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	            data: {input: val},
	            type: 'POST'
	        }).done(function(data){
				_this.buildMajor(data);
			});
		}
	},

	activate: function(){
		this.setState({majors_active: !0});
	},

	deactivate: function(e){
		this.setState({majors_active: !1});
	},

	isScrolling: function(){
		this.state.currently_scrolling = !0;
	},

	addMajor: function(e){
		var copy = this.state.selected_majors.slice(),
			chosen = $(e.target).data('id'),
			txt = $(e.target).text(), newList = null;

		//find duplicate, if any
		duplicate = _.findWhere(copy, {id: chosen});

		//if not a duplicate, add major
		if( !duplicate ){
			copy.push({id: chosen, name: txt});
			this.state.selected_majors = copy;
			this.buildMajors();
			this.state.major_val = txt;
		}

		this.deactivate();
	},

	//building hidden inputs and ui tags for majors selected
	buildMajors: function(){
		var hidden = [], tgs = [], _this = this, copy = this.state.selected_majors.slice();

		_.each(copy, function(obj, i){
			if( obj.name && +obj.id > 0 ){
				hidden.push( <input key={i} type="hidden" name="chosen_majors[]" value={obj.id} className="is-input" /> );
				tgs.push( <div className="tag left" key={obj.id} data-id={''+obj.id}>{obj.name}<div className="remove"><div><div onClick={_this.removeMajor}>x</div></div></div></div> );
			}
		});

		this.setState({
			hidden_major_inputs: hidden,
			major_tags: tgs
		});
	},

	removeMajor: function(e){
		var copy = this.state.selected_majors.slice(), 
			clicked = +$(e.target).closest('.tag').data('id');

		copy = _.reject(copy, {id: clicked});
		this.state.selected_majors = copy;
		this.buildMajors();
	},

	clearInput: function(){
		this.setState({
			major_val: '',
			majors: [] 
		});
		this.deactivate();
	},

	ifEmpty: function(e){
		if( $(e.target).attr('name') === 'major' ) this.notTraversing();
		if( !e.target.value ) this.deactivate();
	},

	notTraversing: function(){
		this.state.traversing = !1;
	},

	update: function(e){
		var target = $(e.target);
		this.props.isValid();
		
		if( target.attr('name') === 'major' ) this.notTraversing();

		if( target.attr('name') === 'degree' ) this.setState({degree_val: target.val()});
		else this.findMajor(e);
	},

	render: function(){
		return (
			<div className="form-field row-1">
				<div className="tags lg-only clearfix">
					{this.state.major_tags}
				</div>
				<div className="field">{'I would like to get a/an'}</div>
				<div className="field">
					<select name="degree" className="is-input" onChange={this.update} value={this.state.degree_val}>
						{this.state.degree_options}
					</select>
				</div>
				<div className="field"> studying </div>
				<div className="field tags not-lg clearfix">{this.state.major_tags}</div>
				<div className="field has-results">
					{this.state.hidden_major_inputs}
					<input name="major" type="text" className="is-input" placeholder="You can choose more than one major"
							onChange={this.update} onFocus={this.activate} value={this.state.major_val} 
							onBlur={this.ifEmpty}  />
					<div className="clear-input text-center" onClick={this.clearInput}>x</div>
					{	
						this.state.majors_active && this.state.majors.length > 0 ?
						<div className="results-container stylish-scrollbar">
							{this.state.majors}
						</div> : null
					}		
					
				</div>
			</div>	
		);
	}
});

var FormField_2 = React.createClass({
	getInitialState: function(){
		return {
			findProfession_route: '/get_started/searchFor/career',
			careers: [],
			careers_active: false,
			career_choice: '',
			hidden_career_input: null,
			career_id: '',
			i: 0,
			traversing: !1
		};
	},

	componentWillMount: function(){
		document.addEventListener('click', this.domClick);
		document.addEventListener('keydown', this.keypressed);
	},

	componentWillReceiveProps: function(nextProps){
		if( nextProps.profession !== this.props.profession ){
			this.setState({
				career_choice: nextProps.professionName,
				hidden_career_input: <input name="chosen_career" type="hidden" value={nextProps.profession} key={-1} />
			});
		}
	},

	domClick: function(e){
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

	notTraversing: function(){
		this.state.traversing = !1;
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

	activate: function(){
		this.setState({careers_active: !0});
	},

	deactivate: function(){
		this.setState({careers_active: !1});
	},

	makeSelected: function(e){
		var chosen = $(e.target).data('id'),
			txt = $(e.target).text(), newList = null, hidden_input;

		this.state.career_choice = txt;
		this.state.hidden_career_input = <input name="chosen_career" type="hidden" value={chosen} key={chosen} />;
		this.deactivate();
	},

	update: function(e){
		var value = e.target.value;

		this.findProfession(e);
		this.notTraversing();

		//if value is not valid, empty out hidden profession input
		if( value ){
			this.setState({career_choice: value});
		}else{
			this.setState({
				career_choice: value,
				hidden_career_input: null
			});
		}

		this.props.isValid();
	},

	render: function(){
		return (
			<div className="form-field row-2">
				<div className="field">{'My dream would be to one day work as a(n)'}</div>
				<div className="field has-results">
					{this.state.hidden_career_input}
					<input name="career" type="text" placeholder="Enter dream career" className="is-input"
							onFocus={this.activate} value={this.state.career_choice}
							onChange={this.update} onBlur={this.notTraversing} />
					{	
						this.state.careers_active && this.state.careers.length > 0 ?
						<div className="results-container stylish-scrollbar">
							{ this.state.careers }
						</div> : null
					}
				</div>
			</div>
		);
	}
});

var FormField_3 = React.createClass({
	getInitialState: function(){
		return {
			school_options: null,
			valu: '',
			is_hovering: !1
		};
	},

	componentWillReceiveProps: function(nextProps){
		if( nextProps.type !== this.props.type ){
			this.initValue(nextProps.type);			
		}
	},

	initValue: function(type){
		if( _.isNumber(+type) ){
			var value = '';
			switch( +type ){
				case 0: value = 'campus_only';
					break;
				case 1: value = 'online_only';
					break;
				case 2: value = 'both';
					break;
			}
			this.setState({valu: value});
		}
	},

	componentWillMount: function(){
		var tmp = [], types = ['Online_Only', 'Campus_Only', 'Both'];

		tmp.push( <option key={-1} value="">{'Select one...'}</option> );	
		_.each(types, function(val, i){
			converted = val.split('_').join(' ');
			tmp.push( <option key={i} value={val.toLowerCase()}>{converted}</option> );	
		});
		this.setState({school_options: tmp});
	},

	update: function(e){
		this.setState({valu: e.target.value});
		this.props.isValid();
	},

	showTip: function(){
		this.setState({is_hovering: !0});
	},

	hideTip: function(){
		this.setState({is_hovering: !1});
	},

	render: function(){
		return (
			<div className="form-field row-3">
				<div className="field">I am interested in </div>
				<div className="field">
					<span onMouseEnter={this.showTip} onMouseLeave={this.hideTip} onTouchStart={this.showTip} onTouchEnd={this.hideTip}>?</span>
					{this.state.is_hovering ? <Tip /> : null }
				</div>
				<div className="field">
					<select name="school_type" className="is-input" onChange={this.update} value={this.state.valu}>
						{this.state.school_options}
					</select>
				</div>
				<div className="field"> schools.</div>
			</div>
		);
	}
});

var Tip = React.createClass({
	render: function(){
		return (
			<div className="tip-container">
				<div>Types of Schools</div>
				<div>If you are interested in Online schools please select Online Only from the drop down.</div>
				<div className="arrow"></div>
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