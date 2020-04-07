import React from 'react'
import { connect } from 'react-redux'
import moment from 'moment'
import ReactSpinner from 'react-spinjs-fix';
import ReactTooltip from 'react-tooltip';
import Fragment from 'react-dot-fragment';
import './styles.scss'
import { getSitePerformance, getSitePerformanceByFilter } from './../../../actions/getSitePerformance'

class SitePerformance extends React.Component {
	constructor(props) {
		super(props);
		this.state ={
			start_date : moment().subtract(1, 'days').format('YYYY-MM-DD'),
			end_date : moment().subtract(1, 'days').format('YYYY-MM-DD'),
			active_tab : "All",
			date: [new Date(), new Date()],
			userType: 'all',
		}
		this.handleFilterClick = this.handleFilterClick.bind(this);
		this.handleStartDate = this.handleStartDate.bind(this);
		this.handleEndDate = this.handleEndDate.bind(this);
		this.handleToday = this.handleToday.bind(this);
		this.handleYesterday = this.handleYesterday.bind(this);
		this.handleMonth = this.handleMonth.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
		this.handleUserTypeChange = this.handleUserTypeChange.bind(this);
	}
	componentDidMount(){
		const { getSitePerformanceData, SitePerformance } = this.props;
		getSitePerformanceData(this.state.start_date, this.state.end_date);
	}

	handleFilterClick(e) {
		this.setState({
			active_tab : e.target.id
		});

		if(e.target.id === 'All') {
			this.getData();
		} else {
	    const { start_date, end_date, userType } = this.state;
	    this.props.getSitePerformanceByFilter(start_date, end_date, e.target.id.split(' ').join('_').toLowerCase(), userType);
		}
	}

	handleStartDate(e) {
		this.setState({
			start_date: e.target.value
		});
	}

	handleEndDate(e) {
		this.setState({
			end_date: e.target.value
		});
	}

	handleSubmit(event) {
		event.preventDefault();
		let start = this.state.start_date;
		let end = this.state.end_date;
		this.setState({
			start_date : start,
			end_date: end
		},() => {
			this.getData();
		});
	}

	getData() {
		const { getSitePerformanceData } = this.props;
		getSitePerformanceData(this.state.start_date, this.state.end_date);
	}

	handleToday(e) {
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
		},() => {
			this.getData();
		});
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
		},() => {
			this.getData();
		});
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
		},() => {
			this.getData();
		});
	}

  getTableRows = () => {
    const { siteData } = this.props
    let rows = []
    let keys = Object.keys(siteData)

    for (let i =0; i < keys.length; i++){
      let res = this.getSingleRow(keys[i])
      rows = rows.concat(res)
    }
    return rows;
  }

  getSingleRow = (key) => {

    const { siteData } = this.props
    let row = []

    row.push(
      <tr key={key+'-main'} className="top-line">
        <td><b>{ `${key}` }</b></td>
        <td></td>
        <td></td>
      </tr>
    )

    let keys = Object.keys(siteData[key])

    for (let i = 0; i < keys.length; i++){
      row.push(
      	<Fragment key={`fragment-${key}-${i}`}>
	      	<ReactTooltip id={`sitePerformanceRow-${key}-${i}`} type='info' multiline={true}>
	      	{
		      	!!siteData[key][keys[i]].urls && siteData[key][keys[i]].urls.map((url, index) => (
		      		<Fragment key={`fragment-${index}`}>
			      		<span>{url}</span>
			      		<br />
		      		</Fragment>
	      		))
	      	}
	      	</ReactTooltip>

	        <tr key={`${key}-${i}`} data-tip data-for={`sitePerformanceRow-${key}-${i}`}>
	          <td>&nbsp;&nbsp;&nbsp;&nbsp;{ `${keys[i]}` }</td>
	          <td>{ siteData[key][keys[i]].View }</td>
	          <td>{ siteData[key][keys[i]].Click }</td>
	        </tr>
	      </Fragment>
      )
    }
    return row
	}

  handleUserTypeChange(e) {
    this.setState({ userType: e.target.value });

    const { start_date, end_date, active_tab } = this.state;
    this.props.getSitePerformanceByFilter(start_date, end_date, active_tab.split(' ').join('_').toLowerCase(), e.target.value);
  }

	render() {
		const { isFetching } = this.props;

    return (
			<div className="main_container">
		    <div className="action-bar">
		      <div className="action-bar-col">
		        <ul>
		          <li id="All" onClick={this.handleFilterClick} className={this.state.active_tab == 'All'?'active':null}>All</li>
		          <li id="Desktop" onClick={this.handleFilterClick} className={this.state.active_tab == 'Desktop'?'active':null}>Desktop</li>
		          <li id="Mobile Web" onClick={this.handleFilterClick} className={this.state.active_tab == 'Mobile Web'?'active':null}>Mobile Web</li>
		          <li id="IOS" onClick={this.handleFilterClick} className={this.state.active_tab == 'IOS'?'active':null}>IOS</li>
		          <li id="Android" onClick={this.handleFilterClick} className={this.state.active_tab == 'Android'?'active':null}>Android</li>
		        </ul>
		      </div>

		      <div className="action-bar-col site-performance-select">
            <div style={{marginTop:"20px"}}>
              <select className="action-bar-select" value={this.state.userType} onChange={this.handleUserTypeChange}>
                <option value='all'>All</option>
                <option value='unique_users'>Unique Users</option>
              </select>
            </div>
          </div>

					<div className="action-bar-col d-picker-wrapper">
						<form>
							<ul className="flex-display">
								<li>
									<input id="start_date" type="date" name="start_date" value={this.state.start_date} required onChange={this.handleStartDate} />
								</li>
								<li>
									<input id="end_date" type="Date" name="end_date" value={this.state.end_date} required onChange={this.handleEndDate} />
								</li>
								<li>
									<button onClick={this.handleSubmit}>Search</button>
								</li>
							</ul>
						</form>
					</div>
		    </div>

				<div className="action-bar">
					<div className="action-bar-col"></div>
					<div className="action-bar-col d-picker-wrapper export-wrapper">
						<ul className="flex-display">
							<li><a id="fetchToday" onClick={this.handleToday}>Today</a></li>
							<li><a id="fetchYesterday" onClick={this.handleYesterday}>Yesterday</a></li>
							<li><a id="fetchMonth" onClick={this.handleMonth}>This Month</a></li>
						</ul>
					</div>
				</div>

				{ isFetching && <div className="spin-container"><ReactSpinner color="#24b26b" /></div> }

				{

			  	!isFetching && <div className="performance-wrapper">
			      <table>

	            <thead>
			          <tr>
			            <th className="width30"></th>
			            <th className="width10">
				            Total Views
			            </th>
			            <th className="width10">
				            Clicks
			            </th>
			          </tr>
			        </thead>

	            <tbody>
	              { this.getTableRows() }
	  	       	</tbody>

	          </table>
			    </div>

				}
		  </div>
		);
	}
}

function mapStateToProps(state) {
	return {
		my_data: state,
    siteData: state.sitePerformance.siteData,
    isFetching: state.sitePerformance.isFetching,
	};
}

function mapDispatchToProps(dispatch) {
	return {
		getSitePerformanceData: (start_date, end_date) => { dispatch(getSitePerformance(start_date, end_date)) },
		getSitePerformanceByFilter: (start_date, end_date, filter, userType) => { dispatch(getSitePerformanceByFilter(start_date, end_date, filter, userType)) },
	}
}

export default connect(mapStateToProps, mapDispatchToProps)(SitePerformance);
