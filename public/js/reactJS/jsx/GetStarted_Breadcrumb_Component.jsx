// GetStarted_Breadcrumb_Component.jsx
var step = null,
	is_done = !1,
	percentage = 0;

function thisStep(num){
	if( num ) step = num;
	else return +step;
};

function submitRecruitmeModal(schoolId){
	var input = $('#recruitMeModal').serialize();
	$.ajax({
        url: '/ajax/recruiteme/' + schoolId,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: input,
        type: 'POST'
    }).done(function(data, textStatus, xhr) {
		if( thisStep() === 7 ) justInquired(data.inquired_list); //only step has justInquired function and only step that needs it
		else{
			removeRedirected();
			window.location.href = '/portal'; //otherwise just send to portal
		}

		$('#recruitMeModal').foundation('reveal', 'close');
	});
};

//if redirect here from college page (or some future page), will return college id
function wasRedirected(){
	return JSON.parse(sessionStorage.getItem('college_id'));
};

// $(document).on('click', '.closeX', removeRedirected());

window.onbeforeunload = function(){
	removeRedirected();
};

function removeRedirected(){
	sessionStorage.removeItem('college_id');
}

function restOfStepsDone(done){
	if( done ) is_done = done;
	else return !1;
}

function currentPercentage(pct){
	if(pct) percentage = pct;
	return percentage;
}

//receives a college id and open get recruited modal for that school
function openRecruitModal(id){
	if( id ){
		var container = $('#recruitmeModal');
		$.ajax({
			url: '/ajax/recruitme/'+id,
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(data) {
			container.html(data);
			container.find('#recruitmeModal').foundation('reveal', 'open');
		});
	}
};

var GetStarted_Breadcrumb_Component = React.createClass({
	getInitialState: function(){
		return {
			num_of_steps: 8,
			max_steps: 7,
			step_crumbs: null,
			steps: null,
			active_step: null,
			steps_completed: null,
			percentage: 0,
			badges: [],
			badges_ui: null
		};
	},

	componentWillMount: function(){
		var stepBreadCrumbs = [], step_num, steps = [], obj = null,
			activeStep = null, steps_completed = null, 
			breadcrumb = $('#get_started_breakcrumb'), badge = null, 
			copy = this.state.badges.slice(), is_locked = !0,

		Badge = function Badge(name, lock){
			this.name = name || '';
			this.locked = typeof lock === 'boolean' ? lock : !0;
		};

		//obj of step props and bool values to show if steps are done or not
		steps_completed = breadcrumb.data('steps-completed');
		step_num = breadcrumb.data('currentStep');

		for (var i = 1; i <= this.state.num_of_steps; i++) {
			obj = {
				name: 'Step '+i,
				is_active: parseInt(i) === parseInt(step_num),
				num: parseInt(i),
				currStep: parseInt(step_num),
				done: steps_completed['step_'+i+'_complete'],
				total_num_of_steps: this.state.max_steps
			};

			if( obj.is_active ) activeStep = obj;
			steps.push(obj);
			if( i <= this.state.max_steps ) stepBreadCrumbs.push(<Step key={i} stepObj={obj} />);
		}

		//if at 30%, unlock basic badge
		if( +steps_completed.profile_percent >= 30 ) is_locked = !is_locked;
		copy.push( new Badge('basic', is_locked) );
		copy.push( new Badge('premium') );
		this.state.badges = copy;
		this.initBadges();

		//save step components and current step num
		this.setState({
			step_crumbs: stepBreadCrumbs,
			current_step: step_num,
			active_step: activeStep,
			percentage: steps_completed.profile_percent,
		});

		//save step globally
		thisStep(step_num);
	},

	componentDidMount: function(){
		$(document).on('saved', this.handleRedirected);
	},

	handleRedirected: function(){
		var pct = currentPercentage(),
			redirected = wasRedirected();

		if( pct && _.isNumber(+pct) ){
			this.setState({percentage: +pct});
			if( redirected && +pct === 30 ) openRecruitModal(redirected);
			else window.location.href = '/get_started';
		}
	},

	initBadges: function(){
		var copy = this.state.badges.slice(), ui = [];

		_.each(copy, function(obj, i){
			ui.push( <ProfileBadges key={i} badge={obj} /> );
		});

		this.state.badges_ui = ui;
	},

	render: function(){
		var congrats = "Congrats you're ready to be recruited!";
        
		return (
			<div className="breadcrumb-container text-center">
				{ this.state.active_step.num === 8 ?
					<div className="congrats text-left">{congrats}</div> :
					<div className="breadcrumb-bar clearfix">
						{this.state.step_crumbs}
					</div>
				}
				{ this.state.badges_ui }
				<ProfileMeter perc={this.state.percentage} />
			</div>
		);
	}
});

var Step = React.createClass({
	getInitialState: function(){
		return {

		};
	},

	ableToRoute: function(e){
		var this_step = this.props.stepObj;
		if( !this_step.done ) e.preventDefault();
	},

	render: function(){
		var step = this.props.stepObj,
			classes = 'icon icon-'+step.num,
			stepClass = 'step',
			route = '/get_started/'+step.num;

		//if this step is active, add active class
		if( step.is_active ) stepClass += ' active';
		//if this step is done, add done class, else prevent from routing
		if( step.done ) stepClass += ' done';
		else route = '';

		return (
			<div className={stepClass}>
				<a href={route} onClick={this.ableToRoute}>
					<div className={classes}></div>
					<div className="step-num">Step {step.num} <span className="show-for-small-only"> of {step.total_num_of_steps}</span></div>
				</a>
			</div>
		);
	}
});

var ProfileMeter = React.createClass({
	componentDidMount: function(){
		this.findOffset();
	},

	findOffset: function(){
		var val = parseInt(this.props.perc),
			circle = $('#svg #bar'), pct = 0, diff, 
			max = 496, min = 364, whole = max - min;

		if( !_.isNumber(val) || val < 0 ) val = 0;
		if( val > 100 ) val = 100;

		diff = (val / 100) * whole;
		pct = max - diff;

		circle.animate({strokeDashoffset: pct}, 'slow');
	},

	render: function(){
		return (
			<div className="meter-container hide-for-small-only">
				<div id="cont" data-pct={this.props.perc}>
					<svg id="svg" width="60" height="60" viewPort="0 0 100 100" version="1.1" xmlns="http://www.w3.org/2000/svg">
						<circle r="21" cx="30" cy="30" fill="transparent"></circle>
						<circle id="bar" r="21" cx="30" cy="30" fill="transparent"></circle>
					</svg>
				</div>
			</div>
		);
	}
});

var ProfileBadges = React.createClass({
	render: function(){
		var badge = this.props.badge, badgeClass = 'bimg badge ' + badge.name;

		return (
			<div className="profile-badges hide-for-small-only">
				<div className={badgeClass}>
					{ badge.locked ? <div className="bimg locked"></div> : null }
				</div>	
			</div>
		);
	}
});

ReactDOM.render( <GetStarted_Breadcrumb_Component />, document.getElementById('get_started_breakcrumb') );