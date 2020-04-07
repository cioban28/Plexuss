/* \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ top level component - start //////////////////////////////// */
var PlexEventsApp = React.createClass({
	getInitialState: function(){
		return{
			listOfOnlineEvent_array: null,
			listOfOfflineEvent_array: null,
			listofNearestEvent_array:null,
		}
	},

	render: function(){
		return (
			<div>
				<PlexContainer/>
			</div>
		);
	}
});

var PlexContainer = React.createClass({





	getInitialState: function(){
		return{
			active_page:"onlineEvents",
			get_country_route: '/ajax/getCountryNames',
                        get_current_route : '/college-fairs-events/?country',
			listOfOnlineEvent_array: null,
			listOfOfflineEvent_array: null,
			listofNearestEvent_array: null,
			activePage : 'online',
			countryName : null,
			countryList : null,
			eventCountry: null,
			eventCity : null,
                        countryfilter:window.location.search.substr(1)||null,
                        cityfilter:false,        
                        currentRequest : null,

                        
		}
	},
        myhandler:function() {
               this.setState({
                   cityfilter: true
               });
           },

	setListOfObjects: function(list, type){
		if( type === 'online' ){
			this.setState({listOfOnlineEvent_array: list});
		}else if(type === 'nearest'){
			this.setState({listofNearestEvent_array: list});
		}
		else{
			this.setState({listOfOfflineEvent_array: list});
		}
	},

	openOnlineEvents : function(){
		this.setState({active_page:"onlineEvents", activePage: "online"});
	},

	openOfflineEvents : function(){
		this.setState({active_page:"offlineEvents", activePage: "offline"});
	},

	openNearestEvents : function(){
		this.setState({active_page:"nearestEvents", activePage: "nearest"});
	},
	toggle:function(e){
        this.setState({addClass: !this.state.addClass});
       },
  

	componentWillMount: function(){
		var _this = this;

		//check if state variables are set, if not, run ajax call and use return to save state variables
		//if already set, no need to make another ajax call
		if( _this.state.countryName === null ){
			$.ajax({
				url: _this.state.get_country_route,
				type: 'GET',
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				//~ async: false,
			}).done(function(data){
				var arr=[];
				for (var key in data) {
				  arr.push(data[key]);
				}
				_this.setState({countryList: arr});
			});
		}

	},


	render: function(){
		 let boxClass = ["dropdown-content"];

		 if(this.state.addClass){
		 boxClass.push('show');
		 }
                 
                  let filterClass =["resetingFil"];
                  if(this.state.cityfilter ||this.state.countryfilter)
                  {
                   filterClass.push('showclearfilter');
                  }
                 

             



		if(this.state.active_page == "onlineEvents"){
			activePage = (<OnlineEvents
							eventLists={this.state.listOfOnlineEvent_array}
							setListFunction={this.setListOfObjects} />);
		}else if(this.state.active_page == "nearestEvents"){
              activePage = (<NearestEvents
							eventLists={this.state.listofNearestEvent_array}
							setListFunction={this.setListOfObjects} eventCountry={this.state.eventCountry}  action={this.myhandler}  eventCity={this.state.eventCity}  currentRequest={this.state.currentRequest}/>);

		}else {
			activePage = (<OfflineEvents
							eventLists={this.state.listOfOfflineEvent_array}
							setListFunction={this.setListOfObjects} />);
		}




		if(this.state.activePage == 'online'){
			links = (<ul className="nav nav-tabs">
						<li id="onlineEvents" className="active" onClick={this.openOnlineEvents}><a data-toggle="tab" href="JavaScript:Void(0);">Upcoming Events</a></li>
						<li id="nearestEvents" onClick={this.openNearestEvents}><a data-toggle="tab" href="JavaScript:Void(0);">Events around me</a></li>
						<li id="offlineEvents" onClick={this.openOfflineEvents}><a data-toggle="tab" href="JavaScript:Void(0);">Past Events</a></li>
					</ul>)
		}else if(this.state.activePage == 'nearest'){

            links = (<ul className="nav nav-tabs">
						<li id="onlineEvents"  onClick={this.openOnlineEvents}><a data-toggle="tab" href="JavaScript:Void(0);">Upcoming Events</a></li>
						<li id="nearestEvents" onClick={this.openNearestEvents} className="active"><a data-toggle="tab" href="JavaScript:Void(0);">Events around me</a></li>
						<li id="offlineEvents" onClick={this.openOfflineEvents}><a data-toggle="tab" href="JavaScript:Void(0);">Past Events</a></li>
					</ul>)
		} else {
			links = (<ul className="nav nav-tabs">
						<li id="onlineEvents" onClick={this.openOnlineEvents}><a data-toggle="tab" href="JavaScript:Void(0);">Upcoming Events</a></li>
						<li id="nearestEvents" onClick={this.openNearestEvents} ><a data-toggle="tab" href="JavaScript:Void(0);">Events around me</a></li>
						<li id="offlineEvents" className="active" onClick={this.openOfflineEvents}><a data-toggle="tab" href="JavaScript:Void(0);">Past Events</a></li>
					</ul>)
		}
		return (
			<div className="event-container">
				<div className="upper_sec">
					<div className="container">
						<div className="header_sec">
							<h1>Get your college questions answered. RSVP to a college fair or university event below!</h1>
						</div>
					</div>
				</div>
				<div className="banner_sec">
					<img src="/images/college-fairs-and-university-events.jpg" alt="College Fairs and University Events" />
				</div>
				<div className="container">
					<div className="top_sec">
						{links}
                                <div className="clearfilter"><a href='/college-fairs-events'><span className={filterClass.join(' ')}>Clear All  x</span></a></div>      
						 <div className="dropdown">
							<button onClick={this.toggle.bind(this)} className="dropbtn dropcountry"><span className="fill-country">Filter by country</span><span className="navigation-arrow-down11"></span></button>
							<div id="myDropdown" className={boxClass.join(' ')}>
                                                       
                                                        
                                                                                      
								{
									this.state.countryList ? this.state.countryList.map((countrylist,i)=><a href={this.state.get_current_route+'='+countrylist.event_country} ><img src={"/images/flags-mini/"+countrylist.event_country+".png"} /> {countrylist.event_country}</a>) : null
								}
							</div>
						</div> 
						{activePage}
					</div>
				</div>
				<div className="footer_sec">
				</div>
			</div>
		);
	}
});

var OnlineEvents = React.createClass({
	getInitialState: function(){
		return {
			event_lists: this.props.eventLists || null,
			get_event_route: '/ajax/getOnlineEvents',
			isOpen: false,
			page_type: 'online',
		}
	},

	componentWillMount: function(){
		var _this = this;

		//check if state variables are set, if not, run ajax call and use return to save state variables
		//if already set, no need to make another ajax call
		if( _this.state.event_lists === null ){
			$.ajax({
				url: _this.state.get_event_route,
				type: 'GET',
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				//~ async: false,
			}).done(function(data){
				var arr=[];
				for (var key in data) {
				  arr.push(data[key]);
				}
				_this.setState({event_lists: arr});
				_this.props.setListFunction(data, _this.state.page_type);
			});
		}

	},

	render : function(){
		return(
			<div className="tab-content">
				<div className="column small-12 cards">
					{
						this.state.event_lists ? this.state.event_lists.map((event_list,i)=> <EventCard key={i} value={event_list}/>) : null
					}
				</div>
			</div>
		);
	}
});

var OfflineEvents = React.createClass({
	getInitialState: function(){
		return {
			event_lists: this.props.eventLists || null,
			get_event_route: '/ajax/getOfflineEvents',
			isOpen: false,
			page_type: 'offline',
		}
	},
	
	componentWillMount: function(){
		var _this = this;

		//check if state variables are set, if not, run ajax call and use return to save state variables
		//if already set, no need to make another ajax call
		if( _this.state.event_lists === null ){
			$.ajax({
				url: _this.state.get_event_route,
				type: 'GET',
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				//~ async: false,
			}).done(function(data){
				var arr=[];
				for (var key in data) {
				  arr.push(data[key]);
				}
				_this.setState({event_lists: arr});
				_this.props.setListFunction(data, _this.state.page_type);
			});
		}

	},

	render : function(){
		return(
			<div className="tab-content">
				<div className="column small-12 cards">
					{
						this.state.event_lists ? this.state.event_lists.map((event_list,i)=> <EventCard key={i} value={event_list}/>) : null
					}
				</div>
			</div>
		);
	}
});


var NearestEvents = React.createClass({
	getInitialState: function(){
		return {
			event_lists: this.props.eventLists || null,
			event_country: this.props.eventCountry || null,
			event_city: this.props.eventCity || null,
			get_event_route: '/ajax/getnearestEvents',
			get_city_route: '/ajax/getnearestCityEvents',
			isOpen: false,
			page_type: 'nearest',
			isOpenInputLocation: false,
			isOpenAutoComplete: false,
                        get_city_list:'/ajax/getcityNames',
                        city_lists :  null,
                        userlocation:null,
                        result_city: true,
                        user_select_city: 0,
                        current_request: this.props.currentRequest,
                       
                      
                        
                     

		}
	},
          onnnClick: function(e){
               this.changeCity(e);
               this.props.action();
          },

	openInputLocation: function(){
		this.setState({
		  isOpenInputLocation: true
		});
	},
         capitalize :function(str){
            return str.charAt(0).toUpperCase() + str.slice(1);
          },
	
	openAutoComplete: function(event){
		this.setState({
		  isOpenAutoComplete: true
		});
                    
		var _this = this;
        var keyword =  event.target.value;
          

            this.setState({userlocation:keyword }) ;  
        
        if(keyword){
      
  	    $.ajax({
			url: _this.state.get_city_list,
			type: 'POST',
			data: {cityname:keyword},
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			//~ async: false,
		}).done(function(data){
			var cityarr=[];
                       if(data.code == 1)
                        {
                       _this.setState({city_based_filter:true});
                       _this.setState({
		          result_city: true
		         });

 			for (var key in data.result) {
			  cityarr.push(data.result);
			}
                       }else if((data.code == 00 )||(data.code == 0)){
                          _this.setState({
		          result_city: false
		         });
                          var blankcity =  ["No City Found"];
                          cityarr.push(blankcity);

                        }
             
			_this.setState({city_lists: cityarr});
		
			 
		});
              }else{
                this.setState({
		  isOpenAutoComplete: false
		});
            
         }


	},
   
	changeCity: function(e){
		var new_city = e.target.innerHTML;

                
                  this.setState({userlocation:new_city }) ;            
                 
                var _this = this;

             
                this.setState({isOpenAutoComplete:false});
		$.ajax({
			url: _this.state.get_city_route,
			type: 'POST',
			data: {cityname:new_city},
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			//~ async: false,
		}).done(function(data){
                        
			var arr=[];
			for (var key in data.getAllEventsReturn) {
			  arr.push(data.getAllEventsReturn[key]);
			}
			console.log(arr);
			_this.setState({event_lists: arr});
			_this.setState({event_city: data.city, event_country: data.country});
			_this.props.setListFunction(arr, _this.state.page_type);
		});
             
		
	},
   
	
	componentWillMount: function(){
		var _this = this;
		//check if state variables are set, if not, run ajax call and use return to save state variables
		//if already set, no need to make another ajax call
		if( _this.state.event_lists === null ){
			$.ajax({
				url: _this.state.get_event_route,
				type: 'GET',
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				//~ async: false,
			}).done(function(data){
				var arr=[];
				for (var key in data.getAllEventsReturn) {
				  arr.push(data.getAllEventsReturn[key]);
				}
				_this.setState({event_lists: arr});
				_this.setState({event_city: data.city, event_country: data.country});
				_this.props.setListFunction(arr, _this.state.page_type);
			});
		}

	},

	render : function(){
                
               

		if(this.state.isOpenAutoComplete) {

			autoComplete = (<div className="autocomplete citylist"><ul>
                        {this.state.result_city?(this.state.city_lists ? this.state.city_lists.map((city_list,i) => <li key={i} onClick={this.onnnClick} >{this.capitalize(city_list[i].city_name)}</li>) : null):<li>No Result Found</li> }
                                     </ul></div>);
		} else {
			autoComplete = null;
		}
		if(this.state.isOpenInputLocation) {
			 inputLocation = (
					<div className="inputLocation">
						<div className="okl"><input type="text" onChange={this.openAutoComplete.bind(this)} className="usernearestlocation"  name="userlocation"  value={this.state.userlocation} /></div>{autoComplete}</div>);
		} else {
			inputLocation = (
					<p className="location_para">Showing events near {this.state.event_city},{this.state.event_country}
						<br/>
						<a href="JavaScript:Void(0);" onClick={this.openInputLocation}>Not your location?</a>
					</p>);
		}
		return(
			<div className="tab-content">
				<div className="column small-12 cards">
					{inputLocation}
					{
						this.state.event_lists ? this.state.event_lists.map((event_list,i)=> <EventCard key={i} value={event_list}/>) : null
					}
				</div>
			</div>
		);
	}
});

var EventCard = React.createClass({
	render : function(){
		var starttimestring = this.props.value.event_start_time;
		var H = +starttimestring.substr(0, 2);
		var h = H % 12 || 12;
		var ampm = (H < 12 || H === 24) ? " AM" : " PM";
		starttimestring = h + starttimestring.substr(2, 3) + ampm;

		var endtimestring = this.props.value.event_end_time;
		var H = +endtimestring.substr(0, 2);
		var h = H % 12 || 12;
		var ampm = (H < 12 || H === 24) ? " AM" : " PM";
		endtimestring = h + endtimestring.substr(2, 3) + ampm;

		var startdate = moment(this.props.value.event_start_date, 'YYYY-MM-DD').format('MMMM DD, YYYY');
		var enddate = moment(this.props.value.event_end_date, 'YYYY-MM-DD').format('MMMM DD, YYYY');

		if(startdate == enddate){
			eventdate = startdate;
		}else{
			eventdate = startdate+" - "+enddate;
		}

		return(
			<div className="column small-12 medium-6 large-4 event-card">
			    <div className="scrollbox">
				<div className="event-content">
					<div className="pic_sec">
						<img src={this.props.value.event_image}/>
					 </div>
					<div className="taxes_sec">
						<h1>{this.props.value.event_title}</h1>
					</div>
					<div className="para_sec">
						<p>{this.props.value.event_description}</p>
					</div>
					<div className="spanner_sec">
						<img src="/images/clock.png"/><span>{starttimestring} - {endtimestring}</span><br/>
						<img src="/images/cal.png"/><span>{eventdate}</span>
					</div>
					<div className="btn_div">
						<a target="_blank" href={this.props.value.event_url} className="register_btn">Register</a>
					</div>
				</div>
				</div>
			</div>
		);
	}
});



React.render( <PlexEventsApp />, document.getElementById('eventcontent_left'));
