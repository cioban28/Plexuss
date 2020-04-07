/* \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ top level component - start //////////////////////////////// */
var SalesTrackingApp = React.createClass({
	getInitialState: function(){
		return{
			// publisher menu
			publisher_page_array: [
				{page_name: 'Email Reporting', is_active: true, active_class: 'active'},
				{page_name: 'dashboard', is_active: false, active_class: ''},
				{page_name: 'Tracking', is_active: false, active_class: ''},
				{page_name: 'clientReporting', is_active: false, active_class: ''},
				{page_name: 'agencyReporting', is_active: false, active_class: ''},
				{page_name: 'scholarships', is_active: false, active_class: ''},
				{page_name: 'pickACollege', is_active: false, active_class: ''},
				{page_name: 'applicationOrder', is_active: false, active_class: ''},
				{page_name: 'pixelTesting', is_active: false, active_class: ''},
				{page_name: 'studentTracking', is_active: false, active_class: ''},
			],
			active_pub_page_obj: {page_name: 'Email Reporting', is_active: true, active_class: 'active'},
			page_name: 'Email Reporting',
		}
	},

	setActivePublisherPage: function(e, from_elsewhere){
		var page = '';

		if( from_elsewhere === 'Email Reporting' ){
			page = from_elsewhere;
		}else{
			page = e.target.id;
		}

		//set all nav icons to inactive and empty active class
		_.each(this.state.publisher_page_array, function(value, key, obj){
			obj[key].is_active = false;
			obj[key].active_class = '';
		}, this);

		//for the tag button that was clicked, set that respective objects was_clicked to true
		_.each(this.state.publisher_page_array, function(value, key, obj){
			if( page === obj[key].page_name ){
				obj[key].is_active = true;
				obj[key].active_class = ' active';
			}
		}, this);

		//find the object where was_clicked is true and save that object in clicked_tag_component
		var temp_arr = _.where(this.state.publisher_page_array, {is_active: true});
		this.setState({active_pub_page_obj: temp_arr[0], page_name: e.target.innerHTML});
	},
	render: function(){
		var active_page = null;

		switch( this.state.active_pub_page_obj.page_name ){
			case 'Tracking':
				active_page = <EmailReporting_page
									appState={this.state}
									 />;
				break;
		}

		return (
			<div className="SalesTracking_App">
				<NavComponent setPage={this.setActivePublisherPage.bind(this)} setTitle={this.state.page_name}/>
				{active_page}
			</div>
		);
	}
});
// ---------- Email Reporting Page
var EmailReporting_page = React.createClass({
	render: function(){
		return (
			<div style={{display:'none'}}>
			</div>
		);
	}
});

/* \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ Top navbar ///////////////////////// */

var NavComponent = React.createClass({
	getInitialState: function(){
		return {
			isOpen: false,
		}
	},

	onClose : function(e){
		this.props.setPage(e);
		this.closeMenu();
	},

	openMenu : function(){
		this.setState({
		  isOpen: true
		});
	  },

	closeMenu : function(e){
		this.setState({
		  isOpen: false
		});
	  },

	render: function() {
		page_name = this.props.setTitle;
		if(this.state.isOpen) {
			 menuList = (
					<div className="menu-overlay">
					<div className="menu-expand">
							<img src="/images/close-x-white.png" className="close-menu" onClick={this.closeMenu} /> <span className="menu-heading">Menu</span>
							<ul className="menu-expand-ul">
								<li id="dashboard"><a href="/sales">Dashboard</a></li>
								<li id="Tracking"><a href="/sales/tracking">Tracking</a></li>
								<li id="studentTracking"><a href="/sales/studentTracking">Overview Tracking</a></li>
								<li id="emailReporting">Email Reporting</li>
								<li id="Site Performance"><a href="/sales/site-performance">Site Performance</a></li>
								<li id="clientReporting"><a href="/sales/clients">Client Reporting</a></li>
								<li id="agencyReporting"><a href="/sales/agency-reporting">Agency Reporting</a></li>
								<li id="scholarships"><a href="/sales/scholarships">Scholarships</a></li>
								<li id="pickACollege"><a href="/sales/pickACollege">Pick a College</a></li>
								<li id="applicationOrder"><a href="/sales/application-order">Application Order</a></li>
								<li id="pixelTesting"><a href="/sales/pixelTrackingTest">Pixel Testing</a></li>
								<li id="social-newsfeed"><a href="/sales/social-newsfeed">Social Newsfeed</a></li>
							</ul>
						</div>
					</div>);
		} else {
			menuList = null;
		}

		return (
			<nav>
				<div className="navWide">
					<div className="wideDiv">
						{menuList}
						<img src="/images/hamburger_button.png" className="hamburger-img" onClick={this.openMenu} />
						<span className="page-title">{page_name}</span>
						<a href="/admin/dashboard" className="publisher-logo">
							<img className="plex_logo_resize" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/premium_page/plexuss-white.png" />
						</a>
					</div>
				</div>
			</nav>
		);
	},
});
/* \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ Top navbar ///////////////////////// */

React.render( <SalesTrackingApp />, document.getElementById('SalesTopNav'));
/*\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ top level component - end //////////////////////////////// */
