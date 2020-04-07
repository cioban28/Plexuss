// PortalSideNav_Component.jsx

var PortalSideNav_Component = React.createClass({displayName: "PortalSideNav_Component",
	getInitialState: function(){
		return {
			open: !1,
			tabs: [],
			tab_ui: []
		};
	},

	componentWillMount: function(){
		// var _this = this;
		//add event listener on component mount
		// document.addEventListener('click', function(e){
		// 	_this.closeNav();
		// });
		this.getPath();
		this.getData();
		this.initTabs();
	},

	componentDidMount: function(){
		if( window.innerWidth >= 642 ) $(document).trigger('showTutorial');
	},

	getData: function(){
		var _this = this;
		$.ajax({
			url: '/ajax/portal/getPortalData',
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(data) {
			_this.updateCounts(data);
		});
	},

	initTabs: function(){
		var arr = [], obj, Tab = null, _this = this;

		Tab = function Tab(legacyname, text, count, is_toggler){
			this.name = legacyname || null;
			this.txt = text || null;
			this.open = !1;
			console.log(  );
			this.active = _this.getPath() === legacyname ? !0 : !1;
			this.handler = is_toggler ? _this.toggleNav : _this.openTabSection;
			this.count = count || 0;
			this.showTip = !1;
		};
		arr.push( new Tab('navtoggle', 'Menu', 0, !0) );
		arr.push( new Tab('messages', 'Messages', 0, !1) );
		arr.push( new Tab('scholarships', 'Scholarships', 0, !1));
		arr.push( new Tab('applications', 'Applications', 0, !1) );
		arr.push( new Tab('portal', 'Your list', 0, !1) );
		arr.push( new Tab('recommendationlist', 'Recommended by Plexuss', 0, !1) );
		arr.push( new Tab('collegesrecruityou', 'Schools want to recruit you', 0, !1) );
		arr.push( new Tab('collegesviewedprofile', 'Colleges viewing you', 0, !1) );
		arr.push( new Tab('getTrashSchoolList', 'Trash', 0, !1) );

		this.state.tabs = arr;
		this.buildTabUI();
	},

	getPath: function(){
		return window.location.pathname.split('/').pop();
	},

	updateCounts: function(data){
		var copy = this.state.tabs.slice(), tab = null, prop = null;

		for(prop in data){
			if( data.hasOwnProperty(prop) ){
				tab = _.findWhere(copy, {name: prop});
				tab.count = data[prop] ? data[prop] : 0;
			}
		}

		this.state.tabs = copy;
		this.buildTabUI();
	},

	buildTabUI: function(){
		var ui = [], copy = this.state.tabs.slice();

		_.each(copy, function(obj, i){
			ui.push( React.createElement(Nav_Item, {key: i, tab: obj}) );
		});

		this.setState({tab_ui: ui});
	},

	toggleNav: function(e){
		var copy = this.state.tabs.slice(), is_open = !1;

		_.each(copy, function(obj){
			obj.open = !obj.open;
			is_open = obj.open;
		});

		this.state.open = is_open;
		this.state.tabs = copy;
		this.buildTabUI();
	},

	openTabSection: function(e){
		var target = $(e.target),
			name = target.data('name') || target.closest('.item').data('name'),
			copy = this.state.tabs.slice();

		_.each(copy, function(obj){
			if( obj.name === name ) obj.active = !0;
			else obj.active = !1;
		});

		loadPortalTabs(name);
		this.getData();
		this.buildTabUI();
	},

	render: function(){
		var nav_classes = this.state.open ? 'nav-container open' : 'nav-container closed'; 
		return (
			React.createElement("div", {className: nav_classes}, 
				React.createElement("ul", null, 
					this.state.tab_ui
                )
			)
		);
	}
});

var Nav_Item = React.createClass({displayName: "Nav_Item",
	getInitialState: function(){
		return {
			this_tab: null  
		};
	},

	componentWillMount: function(){
		this.setState({this_tab: this.props.tab});
	},

	showTip: function(){
		var tab = this.props.tab;

		if( !tab.open ){
			tab.showTip = !0;
			this.setState({this_tab: tab});
		}
	},

	hideTip: function(){
		var tab = this.props.tab;
		tab.showTip = !1;
		this.setState({this_tab: tab});
	},

	render: function(){
		var tab = this.props.tab, classes = 'icon '+tab.name, 
			item_classes = tab.active ? 'item active' : 'item',
			count = tab.count > 10 ? '10+' : tab.count;

		return (
			React.createElement("li", {className: item_classes, onClick: tab.handler, "data-name": tab.name, onMouseEnter: this.showTip, onMouseLeave: this.hideTip}, 
				React.createElement("div", {className: classes}, 
					 +tab.count > 0 ? React.createElement("div", {className: "count text-center"}, count) : null
				), 
				 tab.open ? React.createElement("div", {className: "name"}, tab.txt) : null, 
				 this.state.this_tab.showTip ? React.createElement("div", {className: "tip"}, React.createElement("span", {className: "pointer"}), tab.txt) : null
			)
		);
	}
});

ReactDOM.render( React.createElement(PortalSideNav_Component, null), document.getElementById('portal-nav-window') );