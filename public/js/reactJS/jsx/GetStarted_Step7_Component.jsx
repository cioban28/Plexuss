// GetStarted_Step7_Component.jsx
var percentage;

function wasRedirected(){
    return JSON.parse(sessionStorage.getItem('college_id'));
};

function currentPercentage(pct){
    if(pct) percentage = pct;
    return percentage;
};

$(document).on('click', '.recruit-me-pls', function(e){
	e.preventDefault();
	var id = $(this).data('id'), container = $('#recruitmeModal');

	$.ajax({
		url: '/ajax/recruitme/'+id,
		type: 'GET',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function(data) {
		container.html(data);
		container.foundation('reveal', 'open');
	});
});

function justInquired(data){
	var elem = null, checkmark = '<span class="check">&#x02713;</span>';

	_.each(data, function(school){
		elem = $('.recruit-me-pls[data-id="'+school+'"]');
		if( elem.length > 0 ) elem.parent().html(checkmark);
	});
};

var GetStarted_Step7_Component = React.createClass({
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
			sections: ['National Universities', 'Liberal Arts Colleges', 
				'Community Colleges', 'Specialty Schools - ARTS',
				'Specialty Schools - MUSIC', 'International Colleges', 'Specialty Schools - ENGINEERING'],
			carousels: [],
			carousel_ui: null,
			specialty_tabs: [
				{name: 'ENGINEERING', active: !1},
				{name: 'MUSIC', active: !1},
				{name: 'ARTS', active: !0},
			]
		};
	},

	componentWillMount: function(){	
		var classes = this.state.save_btn_classes, prev, next, num,
			Carousel = null, carou = [], _this = this;

		// Facebook event tracking
        fbq('track', 'GetStarted_Step7_CollegeRecruitment_Page');

		//get current step num
		this.state.step_num = $('.gs_step').data('step');
		this.state.get_route += this.state.step_num;

		//build prev step route
		num = parseInt(this.state.step_num);
		prev = num - 1;
		next = num + 1;
		this.state.back_route = '/get_started/'+prev;
		this.state.next_route = '/get_started/';

		//carousel constructor
		Carousel = function Carousel(sect){
			this.section = sect || null;
			this.schools = [];
		};

		//create/save array of carousel sections
		for (var i = 0; i < this.state.sections.length; i++) {
			carou.push( new Carousel( this.state.sections[i] ) );
		}

		this.state.carousels = carou;
		this.buildCarousels();
	},

	buildCarousels: function(){
		var copy = this.state.carousels.slice(), ui = [],
			options = !1, tab = null, activeTab = this.getActiveTab();

		//build and save college carousel component
		for (var i = 0; i < copy.length; i++) {
			options = !1;
			tab = null;

			//if it's a specialty school, only create it, if it's the active one
			//else create it
			if( copy[i].section.indexOf('Specialty Schools') > -1 ){
				if( copy[i].section === 'Specialty Schools - ' + activeTab.name ){
					options = !0;
					tab = this.state.specialty_tabs;
					ui.push( <College_Carousel key={i} name={copy[i].section} route={this.state.get_route} 
											hasOptions={options} tabs={tab} changeTab={this.changeTab} /> );
				}
			}else ui.push( <College_Carousel key={i} name={copy[i].section} route={this.state.get_route} /> );
		}

		this.setState({carousel_ui: ui});
	},

	save: function(){
		var _this = this;	

		this.setState({is_sending: !0});
		$.ajax({
            url: '/get_started/getRecruitedStepDone',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {done: 1},
            type: 'POST'
        }).done(function(data){
			window.location.href = _this.state.next_route;
		});
	},

	getActiveTab: function(){
		return _.findWhere(this.state.specialty_tabs.slice(), {active: !0});
	},

	getTab: function(tab){
		return _.findWhere(this.state.specialty_tabs.slice(), {name: tab});
	},

	changeTab: function(e){
		var val = $(e.target).text(), 
			activeTab = this.getActiveTab(), 
			tab = null, copy = this.state.specialty_tabs.slice();

		//if val is set and not currently active, then update the tab
		if( val && val !== activeTab.name ){
			activeTab.active = !1;
			tab = this.getTab(val);
			if( tab ){
				tab.active = !0;
				this.buildCarousels();
			}
		}
	},

	getName: function(){
		var name = this.props.name.split('-');
		if( name.length > 1 ) return name[1].trim();
		return this.props.name;
	},

	render: function(){
		return (
			<div className="step-container step6-head">
				<div className="row">
					<div className="column small-12 medium-7 large-6 medium-centered">
						<h3>{'Congrats, you can now be recruited by colleges!'}</h3>
					</div>
					<div className="column small-12 medium-3 medium-offset-9 text-right hide-for-small-only">
						{'Select the colleges that you are interested in'}
					</div>
				</div>

				<div className="row">
					{this.state.carousel_ui}					
				</div>

				<div className="row">
					<div className="column small-12">
						<div className="page-nav text-center">
							<div><a href={this.state.back_route}>Back</a></div>
							<div onClick={this.save}>Continue</div>
							<div><a href="" onClick={this.save}>Skip</a></div>
						</div>
					</div>
				</div>

				{ this.state.is_sending ? <Loader /> : null }
			</div>
		);
	}
});

var College_Carousel = React.createClass({
	getInitialState: function(){
		return {
			owlItems: [],
			inquiried: false,
			route: null,
			skip: 0,
			take: 10,
			schools: [],
			just_added_schools: [],
			owl: null,
			initDone: !1,
			last_position: 0
		};
	},

	componentWillMount: function(){
		this.state.route = this.buildRoute();
		this.state.owl = '.'+this.modName()+'_owl';
		this.getOwlItems();
	},

	buildRoute: function(){
		var name = this.props.name,
			specialty = '';
		
		if( name.indexOf('Specialty') > -1 ) specialty = name.split(' ').pop();
		return this.props.route + '_' + this.props.name.split(' ')[0].toLowerCase() + specialty + '_' 
				+ this.state.skip + '_' + this.state.take;
	},

	getOwlItems: function(){
		var _this = this;

		//update route
		this.state.route = this.buildRoute();

		$.ajax({
			url: this.state.route,
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(data) {
			if( data && data.length > 0 ){
				_this.increaseSkip();
				_this.addSchools(data);
			}
		});
	},

	increaseSkip: function(){
		this.state.skip += this.state.take;
	},

	addSchools: function(data){
		var _this = this, copy = this.state.schools.slice(),
			just_added_copy = [];

		_.each(data, function(obj){
			copy.push(obj);
			just_added_copy.push(obj);
		});

		_this.state.schools = copy;
		_this.state.just_added_schools = just_added_copy;

		if( !_this.state.initDone ){
			_this.initOwlItems();
			 _this.initOwl();
		}else _this.addToOwl(data);
	},

	addToOwl: function(data){
		var html = '', index = 0, _this = this, tmp, url = '', img = '';

		if( data.length > 0 ){
			_.each(data, function(obj){
				img = obj.logo_url || 'default-missing-college-logo.png';
				url = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'+img;

				html = '';
				html += '<div class="item text-center">';
				html += 	'<div class="logo" style="background-image: url('+url+')"></div>';
				html += 	'<div class="name">'+obj.school_name.substr(0, 20)+'...'+'</div>';
				html += 	'<div>'
				html += 		'<a href="" class="recruit-me-pls" data-id="'+obj.id+'">';
				html += 			'<div class="plus">+</div>';
				html += 		'</a>';
				html += 	'</div>'
				html += '</div>';

				$(_this.state.owl).trigger('add.owl.carousel', [html]).trigger('refresh.owl.carousel');
			});
		}
	},

	initOwl: function(){
		var _this = this;

		$(this.state.owl).owlCarousel({
			loop: false,
			margin: 10,
			responsive:{
		        0:{
		            items:1
		        },
		        600:{
		            items:3
		        },
		        1000:{
		            items:6
		        }
		    },
		    onChanged: function(e){
		    	if( this._current && this._current % 3 === 0 && this._current > _this.state.last_position ){
		    		_this.getOwlItems();
		    		_this.state.last_position = this._current;
		    	}
		    }
		});

		this.state.initDone = !0;
	},

	initOwlItems: function(){
		var items = [], _this = this, schools = _this.state.schools, url = '', img = '';

		_.each(schools, function(obj){
			img = obj.logo_url || 'default-missing-college-logo.png';
			url = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'+img;
			items.push( <OwlItem key={obj.id} logo={url} name={obj.school_name} school_id={obj.id} /> );	
		});
		
		_this.setState({owlItems: items});		
	},

	modName: function(){
		return this.props.name.split(' ').join('_').toLowerCase();
	},	

	render: function(){
		var owl_name = 'owl-carousel '+this.modName()+'_owl',
			header = <BasicHeader name={this.props.name} />,
			body = <Small_Loader size="sm" />;

		if( this.props.hasOptions ) header = <OptionsHeader name={this.props.name.split('-')[0]} tabs={this.props.tabs} changeTab={this.props.changeTab} />;
		if( this.state.owlItems && this.state.owlItems.length > 0 ){
			body = <Carousel owlItems={this.state.owlItems} 
							 owl_name={owl_name} 
						 	 noMoreItems={this.state.no_more_items}
						 	 visibleCount={this.state.items_visibile}
						 	 diff={this.state.current_index_minus_total_items} />;
		}

		return (
			<div className="column small-12">
				<div className="carousel-container">
					{header}
					{body}
				</div>
			</div>
		);
	}
});

var Carousel = React.createClass({
	getInitialState: function(){
		return {
			current: 0,
			total_items: 10,
			diff: 10,//for now, disabling toggling arrows based on if at end of owl list
			at_beginning: !1,
			at_end: !1
		};
	},

	componentDidMount: function(){
		var name = this.props.owl_name.split(' ')[1],
			owl = $('.'+name), _this = this,
			current = 0, total = 0, visible = 0;

		owl.on('changed.owl.carousel', function(e){
			current = e.relatedTarget._current;//current pos
			total = e.relatedTarget._items.length;//total items
			visible = e.relatedTarget.settings.items;//visible count

			//if total items - current item position = visible item count, then at end, else not at end
			if( total - current === visible ) _this.setState({at_end: !0});
			else if( _this.state.at_end ) _this.setState({at_end: !1});

			// if current is 0 or falsy, means at beginning, else not
			if( !current ) _this.setState({at_beginning: !0});
			else if( _this.state.at_beginning ) _this.setState({at_beginning: !1});
		})
	},

	render: function(){	
		return (
			<div className="gs-carousel">
				<Arrow direction="a-left" />
				<div className="owl-container">
					<div className={this.props.owl_name}>
						{this.props.owlItems}	
					</div>
				</div>
				<Arrow direction="a-right" />
			</div>
		);
	}
});

var OptionsHeader = React.createClass({
	getInitialState: function(){
		return {
			tab_ui: null
		};
	},

	componentWillMount: function(){
		if( this.props.tabs && this.props.tabs.length > 0 ) this.buildTabs();
	},

	componentWillReceiveProps: function(nextProps){
		if( nextProps.activeName !== this.props.activeName ){
			this.buildTabs();
		}
	},

	buildTabs: function(){
		var p = this.props,  tabs = p.tabs, ui = [], classes = '';

		_.each(tabs, function(obj, i){
			if( obj.active ) classes = 'o-btn right active';
			else classes = 'o-btn right';
			ui.push( <div className={classes} key={i} onClick={p.changeTab}>{obj.name}</div> );
		});

		this.setState({tab_ui: ui});
	},

	render: function(){
		return(
			<div className="carousel-name w-options">
				<div>{this.props.name}</div>
				<div className="clearfix">
					{this.state.tab_ui}
				</div>
			</div>
		);
	}
});

var BasicHeader = React.createClass({
	render: function(){
		return (
			<div className="carousel-name">
				<div>{this.props.name}</div>
			</div>
		);
	}
});

var Arrow = React.createClass({
	move: function(e){
		var target = $(e.target), 
			is_left_arrow = target.parent().hasClass('a-left'),
			owl = target.closest('.gs-carousel').find('.owl-carousel');

		//if left arrow, trigger prev, else trigger next
		if( is_left_arrow ) owl.trigger('prev.owl.carousel');
		else owl.trigger('next.owl.carousel');
	},

	render: function(){
		var classes = 'arrow-container hide-for-small-only ' + this.props.direction;

		return (
			<div className={classes} onClick={this.move}>
				<div className="arrow"></div>
				<div className="arrow shadow"></div>
			</div>
		);
	}
});

var OwlItem = React.createClass({
	getInitialState: function(){
		return {
			school: null 
		};
	},

	componentWillMount: function(){
		var skool = {
			inquiried: !1//default false
		};

		this.setState({school: skool});
	},

	madeInquirie: function(e){
		e.preventDefault();
	},

	render: function(){
		var p = this.props, img = { backgroundImage: 'url('+p.logo+')' };

		return(
			<div className="item text-center">
				<div className="logo" style={img}></div>
				<div className="name">{p.name.substr(0, 20)+'...'}</div>
				<Recruit_Btn inqd={this.state.school.inquiried} madeInq={this.madeInquirie}
						school_id={this.props.school_id} />
			</div>
		);
	}
});

var Recruit_Btn = React.createClass({
	render: function(){
		return (
			<div>
				{ this.props.inqd ? 
					[<span className="check" key={-1}>&#x02713;</span>] : 
					<a href="" className="recruit-me-pls" data-id={this.props.school_id}>
						<div className="plus">{'+'}</div>
					</a>
				}
			</div>
		);
	}
});

var Small_Loader = React.createClass({
	render: function(){
		return (
			<div className="small-loader">
				<Loader size="sm" />
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

// if( checkForRedirects() ){
// 	sessionStorage.removeItem(hasReturnKey);
// 	window.location.href = hasReturnKey;
// }else{
// 	ReactDOM.render( <GetStarted_Step7_Component />, document.getElementById('get_started_step6') );
// }

	ReactDOM.render( <GetStarted_Step7_Component />, document.getElementById('get_started_step7') );
