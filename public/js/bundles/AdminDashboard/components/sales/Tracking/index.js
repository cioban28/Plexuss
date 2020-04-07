// index.js

import React from 'react'

import ReactSpinner from 'react-spinjs-fix'

import DateRangePicker from '../../dateRangePicker'

import { connect } from 'react-redux'

import { bindActionCreators } from 'redux'

import moment from 'moment'

import { orderBy } from 'lodash'

import style from './styles.scss'

import Tooltip from 'react-tooltip'

import completed from './Tracking_icons/completed.png'

import international from './Tracking_icons/international.png'

import students from './Tracking_icons/students.png'

import us from './Tracking_icons/us.png'

import axios from 'axios'


class Tracking extends React.Component {
	constructor(props) {
		super(props);
		this.state ={
			value:'Plexuss Users',
			otherVal : 'Invite only users',
			appVal : 'Mobile App Users',
			userStats:[],
			school:'Monetised',
			loading:false,
			start_date : "",
			end_date : "",
		}
		this.handleChange = this.handleChange.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
		this.handleStartDate = this.handleStartDate.bind(this);
		this.handleEndDate = this.handleEndDate.bind(this);
		this.handleToday = this.handleToday.bind(this);
		this.handleYesterday = this.handleYesterday.bind(this);
		this.handleMonth = this.handleMonth.bind(this);
		this.schoolEvent = this.schoolEvent.bind(this);
	}

	componentWillMount() {
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth() + 1; //January is 0!
		var yyyy = today.getFullYear();
		if (dd < 10) {
				dd = '0' + dd;
		}
		if (mm < 10) {
				mm = '0' + mm;
		}
		let start = yyyy + '-' + mm + '-' + '01';
		let end = yyyy + '-' + mm + '-' + dd;
		this.setState({
			start_date : start,
			end_date : end
		});

    this.getUserStats(start, end);
  }

	handleChange(e) {
		if(e.target.value == this.state.otherVal){
			this.setState({otherVal : this.state.value});
	    	this.setState({value: e.target.value});
		}else{
			this.setState({appVal : this.state.otherVal});
			this.setState({otherVal : this.state.value});
		    this.setState({value: e.target.value});
		}
	    this.getUserStats(this.state.start_date, this.state.end_date, e.target.value);
  }

	handleStartDate(e){
		this.setState({
			start_date : e.target.value
		});
	}

	handleEndDate(e){
		this.setState({
			end_date : e.target.value
		});
	}

  handleSubmit(event){
  	event.preventDefault();
		let start = this.state.start_date;
		let end = this.state.end_date;
		this.setState({
			start_date : start,
			end_date : end
		});
		this.getUserStats(start,end);
  }

	handleToday(e){
		e.preventDefault();
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth() + 1; //January is 0!
		var yyyy = today.getFullYear();
		if (dd < 10) {
				dd = '0' + dd;
		}
		if (mm < 10) {
				mm = '0' + mm;
		}
		let start = yyyy + '-' + mm + '-' + dd;
		let end = start;
		this.setState({
			start_date : start,
			end_date : end
		});
		this.getUserStats(start,end);
	}

	handleYesterday(e){
		e.preventDefault();
		var yesterday = new Date((new Date()).valueOf() - 1000 * 60 * 60 * 24);
		var dd = yesterday.getDate();
		var mm = yesterday.getMonth() + 1; //January is 0!
		var yyyy = yesterday.getFullYear();
		if (dd < 10) {
				dd = '0' + dd;
		}
		if (mm < 10) {
				mm = '0' + mm;
		}
		let start = yyyy + '-' + mm + '-' + dd;
		let end = start;
		this.setState({
			start_date : start,
			end_date : end
		});
		this.getUserStats(start, end);

	}

	handleMonth(e){
		e.preventDefault();
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth() + 1; //January is 0!
		var yyyy = today.getFullYear();
		if (dd < 10) {
				dd = '0' + dd;
		}
		if (mm < 10) {
				mm = '0' + mm;
		}
		let start = yyyy + '-' + mm + '-' + '01';
		let end = yyyy + '-' + mm + '-' + dd;
		this.setState({
			start_date : start,
			end_date : end
		});
		this.getUserStats(start, end);
	}

	schoolEvent(e){
		e.preventDefault();
		this.setState({
			school:e.target.id
		});
	}

  getUserStats(start=null, end=null, user_type = null){
  	this.setState({
  		userStats:[]
  	})

		if(!user_type){
			user_type = this.state.value;
		}

		this.setState({
      loading:true
    });

		axios.get('getStats', {
			params : {
				startDate: start,
				endDate: end,
				userType: user_type
			}
		})
  	.then(response=>this.setState({
  		userStats:response.data,
  		loading : false,
  	})
  	);

  }

	render() {
		let {value,otherVal,appVal,userStats,school,loading,start_date,end_date} = this.state;

		let Monetised_US_1_5 = ''
		let Monetised_US_Over_5 = ''
		let Monetised_Intl_1_5 = ''
		let Monetised_Intl_Over_5 = ''
		let All_US_1_5 = ''
		let All_US_Over_5 = ''
		let All_Intl_1_5 = ''
		let All_Intl_Over_5 = ''
		if(school == 'Monetised'){
			Monetised_US_1_5 				= 	userStats.monetized_us_students_selected_1_5;
			Monetised_US_Over_5 		= 	userStats.monetized_us_students_selected_over_5;
			Monetised_Intl_1_5 			= 	userStats.monetized_intl_students_selected_1_5;
			Monetised_Intl_Over_5 	= 	userStats.monetized_intl_students_selected_over_5;
		}
		else{
			All_US_1_5 				= 	userStats.allschools_us_students_selected_1_5;
			All_US_Over_5 		= 	userStats.allschools_us_students_selected_over_5;
			All_Intl_1_5 			= 	userStats.allschools_intl_students_selected_1_5;
			All_Intl_Over_5 	= 	userStats.allschools_intl_students_selected_over_5;
		}
		return (
			<div className="main_container">
				<div className="action-bar">
			    <div className="action-bar-col"></div>
			    <div className="action-bar-col">
			    	<div style={{marginTop:"20px"}}>
							<select className="action-bar-select"  value={value} onChange={this.handleChange}>
								<option value={value}>{value}</option>
								<option value={otherVal}>{otherVal}</option>
								<option value={appVal}>{appVal}</option>
							</select>
						</div>
						<div className="inline-row">
							<div>
								<li id="Monetised" className={school=='Monetised'?'active':null} onClick={this.schoolEvent} >Monetised Schools</li>
							</div>
							<div>
								<li id="All" className={school=='All'?'active':null} onClick={this.schoolEvent} >All Schools</li>
							</div>
						</div>
			    </div>
			    <div className="action-bar-col">
						<div className='upperDiv'>
							<form>
								<ul>
									<li>
										<input id="start_date" type="date" name="start_date" value={start_date} required onChange={this.handleStartDate} />
									</li>
									<li>
										<input id="end_date" type="date" name="end_date" value={end_date} required onChange={this.handleEndDate} />
									</li>
									<li>
										<button onClick={this.handleSubmit}>Search</button>
									</li>
								</ul>
							</form>
						</div>
			    	<div id="exportDiv" className='upperDiv'>
							<ul>
								<li><a id="fetchToday" onClick={this.handleToday}>Today</a></li>
								<li><a id="fetchYesterday" onClick={this.handleYesterday}>Yesterday</a></li>
								<li><a id="fetchMonth" onClick={this.handleMonth}>This Month</a></li>
								<li><a id="export" href={'exportUserStats/'+start_date+'/'+end_date+'/'+value} target="_blank">Export</a></li>
							</ul>
						</div>
			    </div>
				</div>
				<div className="content">
					<div className="inner">

					{this.state.value == "Mobile App Users" &&
						<div className="buildingBlocks">
							<h6>
								Mobile Installs
								<a className='tooltipInfo' data-tip data-for='completedProfile'>?</a>
								<Tooltip id='completedProfile' type='info'>
									<span>Students who have installed <br/>The Plexuss Mobile App</span>
								</Tooltip>
							</h6>
							<table className="userStatsTable">
							  <thead>
							    <tr>
							      <th></th>
							      <th>
							      	<a data-tip data-for='students'>
							      		<img src={students} alt={"students_image"} />
							      		<img src={completed} alt="completed" style={{visibility: 'hidden'}}/>
							      	</a>
							      	<Tooltip id='students' type='info'>
							          <span># of Installs</span>
							        </Tooltip>
							      </th>
							      <th>
							      	iOS
							      	<img src={completed} alt="completed" style={{visibility: 'hidden'}}/>
							      </th>
							      <th>
							      	Android
							      </th>
							    </tr>
							  </thead>
							  <tbody>
							    <tr>
							      <th>
								      <a data-tip data-for='us_students'>
							      		<img src={us} alt={"us students"}/>
							      	</a>
							      	<Tooltip id='us_students' type='info'>
							          <span>US Students</span>
							        </Tooltip>
							      </th>
							      <td>{!loading ? (!(userStats.num_of_us_ios && userStats.num_of_us_android ) ? 0 : Intl.NumberFormat().format(Number(userStats.num_of_us_ios) + Number(userStats.num_of_us_android) )): ''}</td>
							      <td>{!loading ? (!(userStats.num_of_us_ios) ? 0 : Intl.NumberFormat().format(userStats.num_of_us_ios)): ''}</td>
							      <td>{!loading ? (!(userStats.num_of_us_android) ? 0 : Intl.NumberFormat().format(userStats.num_of_us_android)): ''}</td>
							    </tr>
							    <tr>
							      <th>
							      <a data-tip data-for='intl_students'>
							    		<img src={international} alt={"international students"}/>
							    	</a>
							    	<Tooltip id='intl_students' type='info'>
							        <span>International Students</span>
							      </Tooltip>

							      </th>
							      <td>{!loading ? (!(userStats.num_of_intl_ios && userStats.num_of_intl_android) ? 0 : Intl.NumberFormat().format(Number(userStats.num_of_intl_ios) + Number(userStats.num_of_intl_android) )) : ''}</td>
							      <td>{!loading ? (!(userStats.num_of_intl_ios) ? 0 : Intl.NumberFormat().format(userStats.num_of_intl_ios)) : ''}</td>
							      <td>{!loading ? (!(userStats.num_of_intl_android) ? 0 : Intl.NumberFormat().format(userStats.num_of_intl_android)) : ''}</td>
							    </tr>
							    <tr>
							      <th>Total</th>
							      <td>{!loading ? Intl.NumberFormat().format(Number(userStats.num_of_us_ios) + Number(userStats.num_of_us_android) + Number(userStats.num_of_intl_ios) + Number(userStats.num_of_intl_android)) : ''}</td>
							      <td>{!loading ? Intl.NumberFormat().format(Number(userStats.num_of_us_ios) + Number(userStats.num_of_intl_ios)) : ''}</td>
							      <td>{!loading ? Intl.NumberFormat().format(Number(userStats.num_of_us_android) + Number(userStats.num_of_intl_android)) : ''}</td>
							    </tr>
							  </tbody>
							</table>
						</div>
					}

					{this.state.value == "Mobile App Users" &&
						<div className="buildingBlocks" style={{visibility: 'hidden'}}>
							<h6>
								Mobile Uninstalls
								<a className='tooltipInfo' data-tip data-for='completedProfile'>?</a>
								<Tooltip id='completedProfile' type='info'>
									<span>Students who have installed <br/>The Plexuss Mobile App</span>
								</Tooltip>
							</h6>
							<table className="userStatsTable">
							  <thead>
							    <tr>
							      <th></th>
							      <th>
							      	<a data-tip data-for='students'>
							      		<img src={students} alt={"students_image"} />
							      		<img src={completed} alt="completed" style={{visibility: 'hidden'}}/>
							      	</a>
							      	<Tooltip id='students' type='info'>
							          <span># of Uninstalls</span>
							        </Tooltip>
							      </th>
							      <th>
							      	iOS
							      	<img src={completed} alt="completed" style={{visibility: 'hidden'}}/>
							      </th>
							      <th>
							      	Android
							      </th>
							    </tr>
							  </thead>
							  <tbody>
							    <tr>
							      <th>
								      <a data-tip data-for='us_students'>
							      		<img src={us} alt={"us students"}/>
							      	</a>
							      	<Tooltip id='us_students' type='info'>
							          <span>US Students</span>
							        </Tooltip>
							      </th>
							      <td>{!loading ? (!(userStats.num_of_us_ios && userStats.num_of_us_android ) ? 0 : Intl.NumberFormat().format(Number(userStats.num_of_us_ios) + Number(userStats.num_of_us_android) )): ''}</td>
							      <td>{!loading ? (!(userStats.num_of_us_ios) ? 0 : Intl.NumberFormat().format(userStats.num_of_us_ios)): ''}</td>
							      <td>{!loading ? (!(userStats.num_of_us_android) ? 0 : Intl.NumberFormat().format(userStats.num_of_us_android)): ''}</td>
							    </tr>
							    <tr>
							      <th>
							      <a data-tip data-for='intl_students'>
							    		<img src={international} alt={"international students"}/>
							    	</a>
							    	<Tooltip id='intl_students' type='info'>
							        <span>International Students</span>
							      </Tooltip>

							      </th>
							      <td>{!loading ? (!(userStats.num_of_intl_ios && userStats.num_of_intl_android) ? 0 : Intl.NumberFormat().format(Number(userStats.num_of_intl_ios) + Number(userStats.num_of_intl_android) )) : ''}</td>
							      <td>{!loading ? (!(userStats.num_of_intl_ios) ? 0 : Intl.NumberFormat().format(userStats.num_of_intl_ios)) : ''}</td>
							      <td>{!loading ? (!(userStats.num_of_intl_android) ? 0 : Intl.NumberFormat().format(userStats.num_of_intl_android)) : ''}</td>
							    </tr>
							    <tr>
							      <th>Total</th>
							      <td>{!loading ? Intl.NumberFormat().format(Number(userStats.num_of_us_ios) + Number(userStats.num_of_us_android) + Number(userStats.num_of_intl_ios) + Number(userStats.num_of_intl_android)) : ''}</td>
							      <td>{!loading ? Intl.NumberFormat().format(Number(userStats.num_of_us_ios) + Number(userStats.num_of_intl_ios)) : ''}</td>
							      <td>{!loading ? Intl.NumberFormat().format(Number(userStats.num_of_us_android) + Number(userStats.num_of_intl_android)) : ''}</td>
							    </tr>
							  </tbody>
							</table>
						</div>
					}


						<div className="buildingBlocks">
							<h6>
								Complete Profile at 30%
								<a className='tooltipInfo' data-tip data-for='completedProfile'>?</a>
	              <Tooltip id='completedProfile' type='info'>
	                <span>Students who have completed <br/>the Get Started <span className='active' style={{textDecoration:'underline' }}>profile</span>
									</span>
	              </Tooltip>
							</h6>
							<table className="userStatsTable">
							  <thead>
							    <tr>
							      <th></th>
							      <th>
							      	<a data-tip data-for='students'>
							      		<img src={students} alt={"students_image"} />
							      	</a>
							      	<Tooltip id='students' type='info'>
							          <span># of Students</span>
							        </Tooltip>
							      </th>
							      <th>
							      	<a data-tip data-for='students_completed'>
							      		<img src={students} alt={"students_image"}/>
							      		<img src={completed} alt="completed" />
							      	</a>
							      	<Tooltip id='students_completed' type='info'>
							          <span># of Completed</span>
							        </Tooltip>
							      </th>
							      <th>
							      	%
							      </th>
							    </tr>
							  </thead>
							  <tbody>
							    <tr>
							      <th>
								      <a data-tip data-for='us_students'>
							      		<img src={us} alt={"us students"}/>
							      	</a>
							      	<Tooltip id='us_students' type='info'>
							          <span>US Students</span>
							        </Tooltip>
							      </th>
							      <td>{!loading ? (!(userStats.num_of_us_students) ? 0 : Intl.NumberFormat().format(userStats.num_of_us_students)): ''}</td>
							      <td>{!loading ? (!(userStats.num_of_us_students_com) ? 0 : Intl.NumberFormat().format(userStats.num_of_us_students_com)): ''}</td>
							      <td>
							      	{!loading?(Number(userStats.num_of_us_students)!=0 ? (Math.round((userStats.num_of_us_students_com/userStats.num_of_us_students)*100) + '%') : '0%'):''}
							      </td>
							    </tr>
							    <tr>
							      <th>
							      <a data-tip data-for='intl_students'>
							    		<img src={international} alt={"international students"}/>
							    	</a>
							    	<Tooltip id='intl_students' type='info'>
							        <span>International Students</span>
							      </Tooltip>

							      </th>
							      <td>{!loading ? (!(userStats.num_of_intl_students) ? 0 : Intl.NumberFormat().format(userStats.num_of_intl_students)) : ''}</td>
							      <td>{!loading ? (!(userStats.num_of_intl_students_com) ? 0 : Intl.NumberFormat().format(userStats.num_of_intl_students_com)) : ''}</td>
							      <td>{!loading?(Number(userStats.num_of_intl_students)!=0 ? (Math.round((userStats.num_of_intl_students_com/userStats.num_of_intl_students)*100) + '%') : '0%'):''}</td>
							    </tr>
							    <tr>
							      <th>Total</th>
							      <td>{!loading ? Intl.NumberFormat().format(Number(userStats.num_of_us_students) + Number(userStats.num_of_intl_students)) : ''}</td>
							      <td>{!loading ? Intl.NumberFormat().format(Number(userStats.num_of_us_students_com) + Number(userStats.num_of_intl_students_com)) : ''}</td>
							      <td>{!loading?((Number(userStats.num_of_us_students) + Number(userStats.num_of_intl_students))!=0 ? (Math.round(((Number(userStats.num_of_us_students_com) + Number(userStats.num_of_intl_students_com))/(Number(userStats.num_of_us_students )+ Number(userStats.num_of_intl_students)))*100) + '%') : '0%'):''}</td>
							    </tr>
							  </tbody>
							</table>
						</div>
						<div className="buildingBlocks">
							<h6>Selected One School
								<a className='tooltipInfo' data-tip data-for='oneSchoolSelected'>?</a>
	              <Tooltip id='oneSchoolSelected' type='info'>
	              	<span>Users that have chosen 1 to 4 colleges</span>
	              </Tooltip>
							</h6>
							<table className="userStatsTable">
							  <thead>
							    <tr>
							      <th></th>
							      <th>
							      	<a data-tip data-for='students'>
							      		<img src={students} alt={"students_image"} />
							      	</a>
							      	<Tooltip id='students' type='info'>
				                <span># of Students</span>
				              </Tooltip>
							      </th>
							      <th>
							      	<a data-tip data-for='students_completed'>
							      		<img src={students} alt={"students_image"}/>
							      		<img src={completed} alt="completed" />
							      	</a>
							      	<Tooltip id='students_completed' type='info'>
				                <span># of Completed</span>
				              </Tooltip>

							      	</th>
							      <th>
							      	%
							      </th>
							    </tr>
							  </thead>
							  <tbody>
							    <tr>
							      <th>
								      <a data-tip data-for='us_students'>
							      		<img src={us} alt={"us students"}/>
							      	</a>
							      	<Tooltip id='us_students' type='info'>
				                <span>US Students</span>
				              </Tooltip>
							      </th>
							      <td>{!loading ? (!(userStats.num_of_us_students) ? 0 : Intl.NumberFormat().format(userStats.num_of_us_students)): ''}</td>
							      <td>{	!loading ?(school=='Monetised' ?
							      							      			(!Monetised_US_1_5 ? 0 : Intl.NumberFormat().format(Monetised_US_1_5)) :
							      							      			(!All_US_1_5 ? 0 : Intl.NumberFormat().format(All_US_1_5))) : ''
							      		}</td>
							      <td>
							      {!loading?
							      	(Number(userStats.num_of_us_students)!=0 ?
							      		(school=='Monetised'?(Math.round((Monetised_US_1_5/userStats.num_of_us_students)*100) + '%') : (Math.round((All_US_1_5/userStats.num_of_us_students)*100) + '%')) :
							      		 '0%'):
							      	''}
							      	</td>
							    </tr>
							    <tr>
							      <th>
							      <a data-tip data-for='intl_students'>
						      		<img src={international} alt={"international students"}/>
						      	</a>
						      	<Tooltip id='intl_students' type='info'>
			                <span>International Students</span>
			              </Tooltip>

							      </th>
							      <td>{!loading ? (!(userStats.num_of_intl_students) ? 0 : Intl.NumberFormat().format(userStats.num_of_intl_students)) : ''}</td>
							      <td>{	!loading ?(school=='Monetised' ?
							      							      			(!Monetised_Intl_1_5 ? 0 : Intl.NumberFormat().format(Monetised_Intl_1_5)) :
							      							      			(!All_Intl_1_5 ? 0 : Intl.NumberFormat().format(All_Intl_1_5))) : ''
							      		}</td>
							      <td>
							      	{!loading?
							      	(Number(userStats.num_of_intl_students)!=0 ?
							      		(school=='Monetised'?(Math.round((Monetised_Intl_1_5/userStats.num_of_intl_students)*100) + '%') : (Math.round((All_Intl_1_5/userStats.num_of_intl_students)*100) + '%')) :
							      		 '0%'):
							      	''}
							      </td>
							    </tr>
							    <tr>
							      <th>Total</th>
							      <td>{!loading ? Intl.NumberFormat().format(Number(userStats.num_of_us_students) + Number(userStats.num_of_intl_students)) : ''}</td>
							      <td>{!loading ? (school=='Monetised'? Intl.NumberFormat().format(Number(Monetised_US_1_5) + Number(Monetised_Intl_1_5)) : Intl.NumberFormat().format(Number(All_US_1_5) + Number(All_Intl_1_5))) : ''}</td>
							      <td>
							      	{
							      		!loading
							      		?
							      		((Number(userStats.num_of_us_students) + Number(userStats.num_of_intl_students)) !=0
							      			?
							      			(school=='Monetised'
							      				?
							      					(Math.round(((Number(Monetised_US_1_5) + Number(Monetised_Intl_1_5))/(Number(userStats.num_of_us_students) + Number(userStats.num_of_intl_students)))*100) + '%')
							      				:
							      					(Math.round(((Number(All_US_1_5) + Number(All_Intl_1_5))/(Number(userStats.num_of_us_students) + Number(userStats.num_of_intl_students)))*100) + '%')
							      				)
							      			:
							      			'0%'
							      			)
							      		:
							      		''
							      	}

							      </td>
							      	}
							    </tr>
							  </tbody>
							</table>
						</div>
						<div className="buildingBlocks">
							<h6>Selected 5 Schools
								<a className='tooltipInfo' data-tip data-for='FiveSchoolsSelected'>?</a>
	              <Tooltip id='FiveSchoolsSelected' type='info'>
	                <span>Users that have chosen 5 or more colleges</span>
	              </Tooltip>
							</h6>
							<table className="userStatsTable">
							  <thead>
							    <tr>
							      <th></th>
							      <th>
							      	<a data-tip data-for='students'>
							      		<img src={students} alt={"students_image"} />
							      	</a>
							      	<Tooltip id='students' type='info'>
				                <span># of Students</span>
				              </Tooltip>
							      </th>
							      <th>
							      	<a data-tip data-for='students_completed'>
							      		<img src={students} alt={"students_image"}/>
							      		<img src={completed} alt="completed" />
							      	</a>
							      	<Tooltip id='students_completed' type='info'>
				                <span># of Completed</span>
				              </Tooltip>

							      	</th>
							      <th>
							      	%
							      </th>
							    </tr>
							  </thead>
							  <tbody>
							    <tr>
							      <th>
								      <a data-tip data-for='us_students'>
							      		<img src={us} alt={"us students"}/>
							      	</a>
							      	<Tooltip id='us_students' type='info'>
				                <span>US Students</span>
				              </Tooltip>
							      </th>
							      <td>{!loading ? (!(userStats.num_of_us_students) ? 0 : Intl.NumberFormat().format(userStats.num_of_us_students)): ''}</td>
							      <td>{ !loading ? (school=='Monetised' ?
							      							      			(!Monetised_US_Over_5? 0 : Intl.NumberFormat().format(Monetised_US_Over_5)):
							      							      			(!All_US_Over_5 ? 0 : Intl.NumberFormat().format(All_US_Over_5))) : ''
							      		}</td>
							      <td>
							      	{!loading?
							      	(Number(userStats.num_of_us_students)!=0 ?
							      		(school=='Monetised'?(Math.round((Monetised_US_Over_5/userStats.num_of_us_students)*100) + '%') : (Math.round((All_US_Over_5/userStats.num_of_us_students)*100) + '%')) :
							      		 '0%'):
							      	''}

							      </td>
							    </tr>
							    <tr>
							      <th>
							      <a data-tip data-for='intl_students'>
						      		<img src={international} alt={"international students"}/>
						      	</a>
						      	<Tooltip id='intl_students' type='info'>
			                <span>International Students</span>
			              </Tooltip>

							      </th>
							      <td>{!loading ? (!(userStats.num_of_intl_students) ? 0 : Intl.NumberFormat().format(userStats.num_of_intl_students)) : ''}</td>
							      <td>{ !loading ? (school=='Monetised' ?
							      							      			(!Monetised_Intl_Over_5? 0 : Intl.NumberFormat().format(Monetised_Intl_Over_5)):
							      							      			(!All_Intl_Over_5 ? 0 : Intl.NumberFormat().format(All_Intl_Over_5))) : ''
							      		}</td>
							      <td>
							      	{!loading?
							      	(Number(userStats.num_of_intl_students)!=0 ?
							      		(school=='Monetised'?(Math.round((Monetised_Intl_Over_5/userStats.num_of_intl_students)*100) + '%') : (Math.round((All_Intl_Over_5/userStats.num_of_intl_students)*100) + '%')) :
							      		 '0%'):
							      	''}
							      </td>
							    </tr>
							    <tr>
							      <th>Total</th>
							      <td>{!loading ? Intl.NumberFormat().format(Number(userStats.num_of_us_students) + Number(userStats.num_of_intl_students)) : ''}</td>
							      <td>{!loading ? (school=='Monetised'? Intl.NumberFormat().format(Number(Monetised_US_Over_5) + Number(Monetised_Intl_Over_5)) : Intl.NumberFormat().format(Number(All_US_Over_5) + Number(All_Intl_Over_5))) : ''}</td>
							      <td>
							      	{
							      		!loading
							      		?
							      		((Number(userStats.num_of_us_students) + Number(userStats.num_of_intl_students)) !=0
							      			?
							      			(school=='Monetised'
							      				?
							      					(Math.round(((Number(Monetised_US_Over_5) + Number(Monetised_Intl_Over_5))/(Number(userStats.num_of_us_students) + Number(userStats.num_of_intl_students)))*100) + '%')
							      				:
							      					(Math.round(((Number(All_US_Over_5) + Number(All_Intl_Over_5))/(Number(userStats.num_of_us_students) + Number(userStats.num_of_intl_students)))*100) + '%')
							      				)
							      			:
							      			'0%'
							      			)
							      		:
							      		''
							      	}
							      </td>
							    </tr>
							  </tbody>
							</table>
						</div>
						<div className="buildingBlocks">
							<h6>Upgrade to Premium
								<a className='tooltipInfo' data-tip data-for='premiumStudents'>?</a>
	              <Tooltip id='premiumStudents' type='info'>
	                <span>Students who have paid for <br/>premium services</span>
	              </Tooltip>
							</h6>
							<table className="userStatsTable">
							  <thead>
							    <tr>
							      <th></th>
							      <th>
							      	<a data-tip data-for='students'>
							      		<img src={students} alt={"students_image"} />
							      	</a>
							      	<Tooltip id='students' type='info'>
				                <span># of Students</span>
				              </Tooltip>
							      </th>
							      <th>
							      	<a data-tip data-for='students_completed'>
							      		<img src={students} alt={"students_image"}/>
							      		<img src={completed} alt="completed" />
							      	</a>
							      	<Tooltip id='students_completed' type='info'>
				                <span># of Completed</span>
				              </Tooltip>

							      	</th>
							      <th>
							      	%
							      </th>
							    </tr>
							  </thead>
							  <tbody>
							    <tr>
							      <th>
								      <a data-tip data-for='us_students'>
							      		<img src={us} alt={"us students"}/>
							      	</a>
							      	<Tooltip id='us_students' type='info'>
				                <span>US Students</span>
				              </Tooltip>
							      </th>
							      <td>{!loading ? ((!userStats.num_of_us_students_premium) ? 0 : Intl.NumberFormat().format(userStats.num_of_us_students_premium)) : ''}</td>
							      <td>{!loading ? ((!userStats.num_of_us_students_com_premium) ? 0 : Intl.NumberFormat().format(userStats.num_of_us_students_com_premium)) : ''}</td>
							      <td>{!loading?(Number(userStats.num_of_us_students_premium)!=0 ? (Math.round((userStats.num_of_us_students_com_premium/userStats.num_of_us_students_premium)*100) + '%') : '0%'):''}</td>
							    </tr>
							    <tr>
							      <th>
							      <a data-tip data-for='intl_students'>
						      		<img src={international} alt={"international students"}/>
						      	</a>
						      	<Tooltip id='intl_students' type='info'>
			                <span>International Students</span>
			              </Tooltip>

							      </th>
							      <td>{!loading ? ((!userStats.num_of_intl_students_premium) ? 0 : Intl.NumberFormat().format(userStats.num_of_intl_students_premium)) : ''}</td>
							      <td>{!loading ? ((!userStats.num_of_intl_students_com_premium) ? 0 : Intl.NumberFormat().format(userStats.num_of_intl_students_com_premium)) : ''}</td>
							      <td>{!loading?(Number(userStats.num_of_intl_students_premium)!=0 ? (Math.round((userStats.num_of_intl_students_com_premium/userStats.num_of_intl_students_premium)*100) + '%') : '0%'):''}</td>
							    </tr>
							    <tr>
							      <th>Total</th>
							      <td>{!loading ? Intl.NumberFormat().format(Number(userStats.num_of_us_students_premium) + Number(userStats.num_of_intl_students_premium)) : ''}</td>
							      <td>{!loading ? Intl.NumberFormat().format(Number(userStats.num_of_us_students_com_premium) + Number(userStats.num_of_intl_students_com_premium)) : ''}</td>
							      <td>{!loading?((Number(userStats.num_of_us_students_premium) + Number(userStats.num_of_intl_students_premium))!=0 ? (Math.round(((Number(userStats.num_of_us_students_com_premium) + Number(userStats.num_of_intl_students_com_premium))/(Number(userStats.num_of_us_students_premium) + Number(userStats.num_of_intl_students_premium)))*100) + '%') : '0%'):''}</td>
							    </tr>
							  </tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		Tracking: state.Tracking,
	};
}

export default Tracking;
