// GetStarted_Step1_Component.jsx

var percentage;

function wasRedirected(){
    return JSON.parse(sessionStorage.getItem('college_id'));
};

function currentPercentage(pct){
    if(pct) percentage = pct;
    return percentage;
};

var GetStarted_Step1_Component = React.createClass({
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
            weighted_gpa: '',
            unweighted_gpa: '',
            bdayPending: true,
            currentView: 'birthday', // birthday || gpa
		};
	},

	componentWillMount: function(){
		var classes = this.state.save_btn_classes, prev, next, num, _this = this;

		// Facebook event tracking
        fbq('track', 'GetStarted_Step2_PlannedStart_Page');

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
			_this.validateForm();
		});

	},

	initUser: function(data){
        var newData = {};

        newData['user_info'] = data;

        if (data.weighted_gpa) {
            newData['weighted_gpa'] = data.weighted_gpa;
        }

        if (data.hs_gpa) {
            newData['unweighted_gpa'] = data.hs_gpa;
        } else if (data.overall_gpa) {
            newData['unweighted_gpa'] = data.overall_gpa;
        }

        if (data.birth_date) {
            const split = data.birth_date.split('-');

            newData['year'] = split[0];
            newData['month'] = split[1]
            newData['day'] = split[2];

            // this.setState({currentView: 'gpa'});
        }

		this.setState({bdayPending: false});

		this.setState(newData);
	},

	save: function(e){
		var formData = new FormData( $('form')[0] ), state = this.state, _this = this;

		if( $(e.target).hasClass('disable') ) e.preventDefault();
		//track if save btn has already been clicked
		if( !state.save_has_been_clicked ) state.save_has_been_clicked = !0;

		if( this.validateForm() ){
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
                amplitude.getInstance().logEvent('step2_completed', {content: 'GPA'} );

				if(data) currentPercentage(data);
				if( wasRedirected() ){
					_this.setState({is_sending: !1});//remove loader
					$(document).trigger('saved');
				}else window.location.href = state.next_route;
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

    validateForm: function() {
        var potentialKeys = ['unweighted_gpa', 'weighted_gpa'];
        var that = this;
        var unweighted_gpa_valid = true;
        var weighted_gpa_valid = true;

        _.each(potentialKeys, function(key) {
            var value = parseFloat(that.state[key]);
            switch (key) {
                case 'unweighted_gpa':
                    unweighted_gpa_valid = !!(value && (0.1 <= value && value <= 4.0));
                    break;

                case 'weighted_gpa': 
                    weighted_gpa_valid = !!(!value || (0.1 <= value && value <= 5.0));
                    break;
            }
        });

        this.setState({
            is_valid: weighted_gpa_valid && unweighted_gpa_valid, 
            weighted_gpa_valid: weighted_gpa_valid, 
            unweighted_gpa_valid: unweighted_gpa_valid
        });

        return weighted_gpa_valid && unweighted_gpa_valid;
    },

    onChangeGPA: function(event) {
        var value = event.target.value;
        var key = event.target.name;
        var inputObject = {};

        inputObject[key] = value;

        this.setState(inputObject, this.validateForm);
    },

	makeSaveActive: function(e){
		if( this.validateForm() ) this.setState({is_valid: !0});
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
			term = user ? (user.planned_start_term || '') : '',
			yr = user ? (user.planned_start_yr || '') : '',
            weighted_border_color = this.state.weighted_gpa_valid ? '#ccc' : 'firebrick',
            unweighted_border_color = this.state.unweighted_gpa_valid ? '#ccc' : 'firebrick',
            currentView = this.state.currentView,
            bdayExists = false;



    	if(this.state.bdayPending == false && this.state.day && this.state.month && this.state.year){
    		// this.setState({currentView: 'gpa'});
    		currentView = 'gpa';
    		bdayExists = true;
    	}

		if( !this.state.is_valid ) saveBtnClasses = 'right btn submit-btn text-center disable';
		else saveBtnClasses = 'right btn submit-btn text-center';

		return (
			<div className="step_container">
				<div className="row">
                    {this.state.bdayPending == true &&
                    		<div className="spinLoader"></div>}	
                    { currentView == 'birthday' && this.state.bdayPending == false &&
	                        <BirthdayFields 
	                            day={this.state.day || ''}
	                            month={this.state.month || ''}
	                            year={this.state.year || ''}
	                            save_route={this.state.save_route} 
	                            go_next_route={() => this.setState({ currentView: 'gpa' })}
	                            back_route={this.state.back_route} /> }

                    { currentView == 'gpa' && this.state.bdayPending == false && 
    					<div className="column small-12 medium-6 large-7">
    						<div className="intro">Enter your (estimated) GPA so far (you can change this later)</div>
                            <br />
    						<br />
    						<form>
    							<input type="hidden" name="step" value={this.state.step_num} />
                                <div>
                                    <div style={{display: 'flex', justifyContent: 'space-between', width: '70%'}}>
                                        <label for='unweighted_gpa'>
                                        	Unweighted GPA *
                                        	<ToolTip>
                                        		 <div className="tiptitle">Unweighted GPA:</div> 
                                        		 Measured on a scale of 0 to 4.0 and do not take the difficulty of courses into account. An A in a low-level class and an A received in an AP class both translate into 4.0s.
                                        	</ToolTip>
                                        </label>
                                        <input name='unweighted_gpa' value={this.state.unweighted_gpa} onChange={this.onChangeGPA} style={{width: '35%', textAlign: 'center', border: '1px solid ' + unweighted_border_color}} type='number' step='0.01' placeholder='0.10 - 4.00' />
                                    </div>
                                    
                                    <br />

                                    <div style={{display: 'flex', justifyContent: 'space-between', width: '70%'}}>
                                        <label for='weighted_gpa'>
                                        	Weighted GPA 
                                        	<ToolTip>
                                        		<div className="tiptitle">Weighted GPA:</div> 
                                        		Typically measured on a scale of 0 to 5.0 and takes the difficulty of courses into account. An A in an AP class would be given a 5.0, while an A received in a low-level class would be assigned a 4.0.
                                        	</ToolTip>
                                        </label>
                                        <input name='weighted_gpa' value={this.state.weighted_gpa} onChange={this.onChangeGPA} style={{width: '35%', textAlign: 'center', border: '1px solid ' + weighted_border_color}} type='number' step='0.01' placeholder='0.10 - 5.00'/>
                                    </div>
                                </div>

    							{ !this.state.is_valid && this.state.save_has_been_clicked ? <div className="err"><small>Fields cannot be empty.</small></div> : null }

    							<div className="submit-row clearfix">
    								<div className="left btn back-btn hide-for-small-only" style={{color: '#9be3ba'}} onClick={() => { if(bdayExists) window.location.href = '/get_started/1'; else this.setState({ currentView: 'birthday'});}}>Go Back</div>
    								<div tabIndex="0" className={saveBtnClasses} onClick={this.save} onFocus={this.checkForEnterKey}>Next</div>
    								<div className="right text-center btn back-btn show-for-small-only" style={{color: '#9be3ba'}} onClick={() => this.setState({ currentView: 'birthday'})}>Go Back</div>
    							</div>
    						</form>
    					</div> }
					
					{/* <div className="column small-12 medium-6 large-5">
						<div className="promo-msg">With 7,700 universities on Plexuss, we need to know a little about you to introduce you to the right colleges.</div>
					</div> */}
				</div>

				{ this.state.is_sending ? <Loader /> : null }
			</div>
		);
	}
});


class ToolTip extends React.Component {
	constructor(props){
		super(props);

		this.state = {
			hovering: false
		};

	}
	componentDidMount(){
		this.tt.addEventListener('mouseenter', () => {
			this.setState({hovering: true});
		})
		this.tt.addEventListener('mouseleave', () => {
			this.setState({hovering: false});	
		})
	}
	render(){
		let {hovering} = this.state;

		return (
			<div className="_Tooltip">
				<div className="tooltip" ref={(div) => this.tt=div}>?</div>
				
				{hovering &&
					<div className="tip">
						{this.props.children}
					</div>}
			</div>
		);
	}
}


class BirthdayFields extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            month: props.month || '',
            day: props.day || '',
            year: props.year || '',
            is_sending: false,
            timeout: null,
        };

        this._onChange = this._onChange.bind(this);
        this._saveBirthday = this._saveBirthday.bind(this);
        this._validateForm = this._validateForm.bind(this);
    }

    componentWillReceiveProps(newProps) {
        const { month: newMonth, day: newDay, year: newYear } = newProps,
            { month, day, year } = this.props;

        if (month !== newMonth || day !== newDay || year !== newYear) {
            this.setState({
                day: newDay,
                month: newMonth,
                year: newYear,
            });
        }
    }

    _onChange(event) {
        const key = event.target.name.replace('birth_', ''),
            value = event.target.value;
        let next = null;

        clearTimeout(this.state.timeout);

        this.setState({[key]: value});


        if(key == 'month')
        	next = this.dInput;
        if(key == 'day')
        	next = this.yInput;

        if(next){
	        let tm = window.setTimeout(() => {
	           //if some amount of time passes -- autofocus next input
    	       clearTimeout(this.state.timeout);

	        	next.focus();
	        }, 900);

	        this.setState({timeout: tm});
	    }
    }

    _validateForm() {
        const potentialKeys = ['month', 'day', 'year'],
            { month, day, year } = this.state;

        let age = '',
            dateString = '',
            birthday = null,
            isValid = false,
            regexValid = false;

        if (month === '' || day === '' || year === '') {
            return isValid;
        }

        potentialKeys.forEach((key, index) => {
            dateString += this.state[key];

            if (index !== 2) {
                dateString += '/';
            }
        });

        regexValid = /^(0[1-9]|1[0-2]|[1-9])\/(|[1-9]|0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2}$/.test(dateString);

        if (regexValid) {
            age = moment().diff(dateString, 'years');

            if (age >= 13) {
                isValid = true;
            }

        }

        return isValid;
    }

    _saveBirthday() {
        const { save_route, go_next_route } = this.props,
            { month, day, year } = this.state;

        this.setState({ is_sending: true });
        $.ajax({
            url: save_route,
            type: 'POST',
            data: {step: 'birthday', month, day, year},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).done((response) => {
            this.setState({ is_sending: false });
            go_next_route();
        });
    }

    render() {
        const { back_route } = this.props,
            { month, day, year } = this.state,
            isValid = this._validateForm();

        let saveBtnClasses = '';

        if( !isValid ) saveBtnClasses = 'right btn submit-btn text-center disable';
        else saveBtnClasses = 'right btn submit-btn text-center';

        return (
            <div className="column small-12 medium-6 large-7 birthday-step-container">
                <div className="intro" style={{textAlign: 'center'}}>Please enter your birthday</div>

                <div className='birthday-input-container'>
                    <input name='birth_month' value={month} onChange={this._onChange} type='number' placeholder='M' ref={(input) => this.mInput = input} />
                    <span>/</span>
                    <input name='birth_day' value={day} onChange={this._onChange} type='number' placeholder='D' ref={(input) => this.dInput = input}/>
                    <span>/</span>
                    <input name='birth_year' value={year} onChange={this._onChange} type='number' placeholder='Year' ref={(input) => this.yInput = input}/>
                </div>

                { !isValid && <small className='birthday-error-message'>Please insert a valid date (MM/DD/YYYY). Must be 13 years or older.</small> }

                <div className="submit-row clearfix">
                    <div className="left btn back-btn hide-for-small-only"><a href={back_route}>Go Back</a></div>
                    <div tabIndex="0" className={saveBtnClasses} onClick={this._saveBirthday}>Next</div>
                    <div className="right text-center btn back-btn show-for-small-only"><a href={back_route}>Go Back</a></div>
                </div>

                { this.state.is_sending ? <Loader /> : null }
            </div>
        );
    }
}

var SelectInput = React.createClass({
	getInitialState: function(){
		return {
			options: null,
			valu: '',
			is_hovering: !1
		};
	},

	componentWillMount: function(){
		this.state.options = this.getSelectOptions();
	},

	componentWillReceiveProps: function(nextProps){
		//only if TextInput parent passes new val, then setstate with that new value	
		if( nextProps.val !== this.props.val ){
			this.setState({valu: nextProps.val});
		}
	},

	getSelectOptions: function(){
		var options = [], this_yr = null, end_yr = null;

		if( this.props.name === 'term' ){
			options.push(<option key={0} value="">{'Select one...'}</option>);
			options.push(<option key={1} value="spring">Spring</option>);
			options.push(<option key={2} value="fall">Fall</option>);
		}else{
			this_yr = moment().year();
			if(moment().month() > 7){
				this_yr += 1;
			}
			end_yr = this_yr + 10;
			options.push(<option key={-1} value="">{'Select one...'}</option>);
			for (i = this_yr; i < end_yr; i++) {
				options.push(<option key={i} value={i}>{i}</option>);
			}
		}

		return options;
	},

	update: function(e){
		this.props.isValid();
		this.setState({valu: e.target.value});
	},

	showTip: function(){
		this.setState({is_hovering: !0});
	},

	hideTip: function(){
		this.setState({is_hovering: !1});
	},

	render: function(){
		var tipname = this.props.name + '_tip';
		return (
			<div className="form-container">
				<div className="f-label capitalize smllr">
					<label className="has-ttip">
						<div>{this.props.name}</div>
						<div className={tipname} onMouseEnter={this.showTip} onMouseLeave={this.hideTip} onTouchStart={this.showTip} onTouchEnd={this.hideTip}>
							<span>{this.props.name === 'term' ? '?' : ''}</span>
							{ this.props.name === 'term' && this.state.is_hovering ? <TermTip /> : null }
						</div>
					</label>
				</div>
				<div className="f-input">
					<select name={this.props.name} className="is-input" onChange={this.update} value={this.state.valu}>
						{this.state.options}						
					</select>
				</div>
			</div>
		);
	}
});

var TermTip = React.createClass({
	render: function(){
		return (
			<div className="term-tip-container">
				<div><b>For colleges and universities using the Semester System</b></div>
				<ul>
					<li>{'Fall Term starts in late summer, around mid-August'}</li>
					<li>{'Spring Term starts in late winter, around mid-January'}</li>
				</ul>

				<div><b>For colleges and universities using the Quarter System</b></div>
				<ul>
					<li>{'Fall Term starts in late summer or early autumn, around mid-September'}</li>
					<li>{'Winter Term starts in mid-winter, around early-January'}</li>
					<li>{'Spring Term starts in late winter or early spring, around mid-March'}</li>
				</ul>

				<div><em>{'Term start-dates depend on the college or university, though there are some general guidelines. Most students begin during the Fall Term.'}</em></div>
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

ReactDOM.render( <GetStarted_Step1_Component />, document.getElementById('get_started_step1') );