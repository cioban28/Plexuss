var SalesApp = React.createClass({
	getInitialState: function(){
		return{
			sideBarIsOpen: false,
			sideBarExpanded: {
				newsfeed: active_page_sales === 'Social Newsfeed',
				reporting: active_page_sales === 'Reporting',
				tracking: active_page_sales === 'Tracking',
				misc: false,
			}
		}
	},

	handleToggleMenuClick: function(){
		this.setState({ sideBarIsOpen: !this.state.sideBarIsOpen }, () => {
			if(this.state.sideBarIsOpen) {
				document.body.classList.add('sidebar-active');
			} else {
				document.body.classList.remove('sidebar-active');
			}
		});
	},

	handleMainItemClick: function(option) {
		const sideBarExpanded = { ...this.state.sideBarExpanded };
		sideBarExpanded[option] = !sideBarExpanded[option];
		this.setState({ sideBarExpanded })
	},

	render: function(){
		const activePage = active_page_sales;
		const { sideBarIsOpen, sideBarExpanded } = this.state;

		return (
			<div className='Site_Performance_App'>
				<div id='sales-header'>
		      <div className='row header'>
		        <div className='columns large-1 medium-1'>
		          <div className='logo-cont'>
		          	<a href='/sales'>
		            	<img className='logo-icon' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/sales/Plexuss+P.svg' />
		          	</a>
		            <img
		            	className='menu-icon'
		            	src={`https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/sales/${sideBarIsOpen ? 'menu - x' : 'menu'}.svg`}
		            	onClick={this.handleToggleMenuClick.bind(this)}
		            />
		          </div>
		        </div>
		        <div className={`columns large-6 medium-6 ${sideBarIsOpen  && 'active-sidebar-nav-padding'}`}>
		        	{
		        		activePage==='Social Newsfeed' && <div className='row-nav-cont'>
			            <ul className='nav-list'>
			              <li>
			                <a href='/sales/social-newsfeed'>
			                  All
			                </a>
			              </li>
			              <li>
			                <a href='/sales/social-newsfeed'>
			                  Published
			                </a>
			              </li>
			              <li>
			                <a href='/sales/social-newsfeed'>
			                  Scheduled
			                </a>
			              </li>
			              <li>
			                <a href='/sales/social-newsfeed'>
			                  Drafts
			                </a>
			              </li>
			              <li>
			                <a href='/sales/social-newsfeed'>
			                  Expired
			                </a>
			              </li>
			            </ul>
			          </div>
		        	}
		        </div>
		        <div className='columns large-5 medium-5'>
		          <div className='header-right-controls'>
		            <div className='messages-icon'>
		              <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/sales/messages.svg' />
		            </div>
		            <div className='bell-sic-icon'>
		              <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/sales/bell-SIC.svg' />
		            </div>
		            <div className='avatar-cont'>
		              <img src='https://images.unsplash.com/photo-1527980965255-d3b416303d12?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=800&q=80' />
		              <span className='username'>Plex Admin</span>
		              <i className='fa fa-chevron-down'></i>
		            </div>
		          </div>
		        </div>
		      </div>
		      {
		      	this.state.sideBarIsOpen &&
				      <div className='sidebar-cont'>
				      	<div className='sidebar'>
					      	<ul className='nav-list'>
					      		<li className='nav-list-item'>
					      			<div className={`main-item ${sideBarExpanded.newsfeed && 'active-main-item'}`} onClick={this.handleMainItemClick.bind(this, 'newsfeed')}>
					      				Newsfeed <div className='arrow-toggle active-arrow'></div>
					      			</div>
					      			{
					      				sideBarExpanded.newsfeed && <ul className='collapsable-menu'>
						      				<li><a href='/sales/social-newsfeed/plexuss-only' className={sub_page === 'Plexuss Only' && 'active-link'}>Plexuss Only</a></li>
						      				<li><a href='/sales/social-newsfeed' className={sub_page === 'All Posts' && 'active-link'}>All</a></li>
						      				<li><a href='/sales/social-newsfeed/reporting'>Reporting</a></li>
						      				<li><a href='/sales/social-newsfeed/moderation'>Moderation</a></li>
						      			</ul>
					      			}
					      		</li>
					      		<li className='nav-list-item'>
					      			<div className={`main-item ${sideBarExpanded.reporting && 'active-main-item'}`} onClick={this.handleMainItemClick.bind(this, 'reporting')}>
					      				Reporting <div className='arrow-toggle active-arrow'></div>
					      			</div>
					      			{
					      				sideBarExpanded.reporting && <ul className='collapsable-menu'>
						      				<li><a href='/sales/device-os-reporting' className={sub_page === 'Device & OS Reporting' && 'active-link'}>Device & OS Reporting</a></li>
						      				<li><a href='/sales/email-reporting' className={sub_page === 'Email Reporting' && 'active-link'}>Email Reporting</a></li>
						      				<li><a href='/sales/agency-reporting' className={sub_page === 'Agency Reporting' && 'active-link'}>Agency Reporting</a></li>
						      				<li><a href='/sales/clients' className={sub_page === 'Client Reporting' && 'active-link'}>Client Reporting</a></li>
						      				<li><a href='/sales/site-performance' className={sub_page === 'Site Performance' && 'active-link'}>Site Performance</a></li>
						      			</ul>
					      			}
					      		</li>
					      		<li className='nav-list-item'>
					      			<div className={`main-item ${sideBarExpanded.tracking && 'active-main-item'}`} onClick={this.handleMainItemClick.bind(this, 'tracking')}>
					      				Tracking <div className='arrow-toggle active-arrow'></div>
					      			</div>
					      			{
					      				sideBarExpanded.tracking && <ul className='collapsable-menu'>
						      				<li><a href='/sales/tracking' className={sub_page === 'Tracking' && 'active-link'}>Tracking</a></li>
						      				<li><a href='/sales/overview-tracking'>Overview Tracking</a></li>
						      			</ul>
					      			}
					      		</li>
					      		<li className='nav-list-item'>
					      			<div className={`main-item ${sideBarExpanded.misc && 'active-main-item'}`} onClick={this.handleMainItemClick.bind(this, 'misc')}>
					      				Misc <div className='arrow-toggle active-arrow'></div>
					      			</div>
					      			{
					      				sideBarExpanded.misc && <ul className='collapsable-menu'>
					      				<li><a href='/sales/scholarships'>Scholarships</a></li>
					      				<li><a href='/sales/pickACollege'>Pick a College</a></li>
					      				<li><a href='/sales/agency-reporting'>Application Order</a></li>
					      				<li><a href='/sales/pixelTrackingTest'>Pixel Testing</a></li>
					      			</ul>
					      			}
					      		</li>
					      	</ul>
				      	</div>
				      </div>
		      }
	      </div>
			</div>
		);
	}
});

React.render( <SalesApp />, document.getElementById('SalesTopNav'));
