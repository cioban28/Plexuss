// GetStarted_Step4_Component.jsx
var percentage;

function wasRedirected(){
    return JSON.parse(sessionStorage.getItem('college_id'));
};

function currentPercentage(pct){
    if(pct) percentage = pct;
    return percentage;
};

var GetStarted_Step4_Component = React.createClass({
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
			user_info: null
		};
	},

	componentWillMount: function(){
		var classes = this.state.save_btn_classes, prev, next, num, _this = this;

		// Facebook event tracking
        fbq('track', 'GetStarted_Step4_FinancialContribution_Old_Page');

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
		this.setState({user_info: data});
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
			if( $(this).val() === '' ){ //if value is emtpy then make and return false
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
			financial = user ? (user.financial_firstyr_affordibility || '') : '',
			aid = user ? (user.interested_in_aid || '') : '';

		if( !this.state.is_valid ) saveBtnClasses = 'right btn submit-btn text-center disable';
		else saveBtnClasses = 'right btn submit-btn text-center';

		return (
			<div className="step_container">
				<div className="row">
					<div className="column small-12 medium-7">
						<div className="intro">{'How much can you and your family contribute towards your college education?'}</div>
						<br />
						<form>
							<input type="hidden" name="step" value={this.state.step_num} />	

							<FinancialContribution_Field isValid={this.makeSaveActive} val={financial} />
							<OptionalInNeedOfFunding_Field val={aid} />
							
							{ !this.state.is_valid && this.state.save_has_been_clicked ? <div className="err"><small>Fields cannot be emtpy.</small></div> : null }

							<div className="submit-row clearfix">
								<div className="left btn back-btn hide-for-small-only"><a href={this.state.back_route}>Go Back</a></div>
								<div tabIndex="0" className={saveBtnClasses} onClick={this.save} onFocus={this.checkForEnterKey}>Next</div>
								<div className="right text-center btn back-btn show-for-small-only"><a href={this.state.back_route}>Go Back</a></div>
							</div>
						</form>
					</div>
					
					<div className="column small-12 medium-5">
						<div className="promo-msg old-msg">{"You're almost done!"}</div>
					</div>
				</div>
				{ this.state.is_sending ? <Loader /> : null }
			</div>
		);
	}
});

var FinancialContribution_Field = React.createClass({
	getInitialState: function(){
		return {
			options: null,
			valu: ''
		};
	},

	componentWillMount: function(){
		var financialOptions = this.getFinancialOptions();
		this.setState({options: financialOptions});
	},

	componentWillReceiveProps: function(nextProps){
		//only if TextInput parent passes new val, then setstate with that new value	
		if( nextProps.val !== this.props.val ){
			this.setState({valu: nextProps.val});
		}
	},

	getFinancialOptions: function(){
		var options = [], values = [[0], [0, 5], [5, 10], [10, 20], [20, 30], [30, 50], [50]], txt = '', range = '';

		options.push(<option key={-1} value="">{'Select one...'}</option>);
		for (var i = 0; i < values.length; i++) {

			if( i === 0 && values[i].length === 1 ){//if first iteration, amount is $0
				txt = '$' + values[i][0];
				range = txt.substr(1) + '.00';
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

	update: function(e){
		this.props.isValid();
		this.setState({valu: e.target.value});
	},

	render: function(){
		return (
			<div className="form-container">
				<select name="financial_contribution" className="is-input" onChange={this.update} value={this.state.valu}>
					{this.state.options}
				</select>
			</div>
		);
	}
});

var OptionalInNeedOfFunding_Field = React.createClass({
	getInitialState: function(){
		return {
			checked: !0 
		};
	},

	componentWillReceiveProps: function(nextProps){
		//only if user has already been here and filled this step out should we check if they have checked this previously	
		if( $('.step.active').hasClass('done') ){
			if( nextProps.val !== this.props.val ){
				this.setState({checked: !!nextProps.val});
			}
		}
		
	},

	update: function(){
		this.setState({checked: !this.state.checked});
	},

	render: function(){
		return (
			<div className="form-container has-chkbx">
				<div>
					<input name="interestedInFunding" id="interestedInFunding" type="checkbox" checked={this.state.checked} onChange={this.update} />
				</div>
				<label className="needFunding" htmlFor="interestedInFunding">{'I am interested in financial aid, grants, and scholarships'}</label>
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

ReactDOM.render( <GetStarted_Step4_Component />, document.getElementById('get_started_step4') );