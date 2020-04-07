import React from 'react';
import { connect } from 'react-redux';
import moment from 'moment';
import ReactSpinner from 'react-spinjs-fix';
import './styles.scss';
import ReportingTable from './ReportingTable';
import {
  getSitePerformanceByPlatform,
  getSitePerformanceByBrowser,
  getSitePerformanceByDevice
} from './../../../actions/deviceOSReportingActions';


class DeviceOSReporting extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      userType: 'all',
      startDate : moment().subtract(1, 'days').format('YYYY-MM-DD'),
      endDate : moment().subtract(1, 'days').format('YYYY-MM-DD'),
    }

    this.onClick = this.onClick.bind(this);
    this.handleUserTypeChange = this.handleUserTypeChange.bind(this);
    this.handleStartDate = this.handleStartDate.bind(this);
    this.handleEndDate = this.handleEndDate.bind(this);
    this.handleToday = this.handleToday.bind(this);
    this.handleYesterday = this.handleYesterday.bind(this);
    this.handleMonth = this.handleMonth.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  componentDidMount(){
    this.props.getSitePerformanceByPlatform(this.state.userType, this.state.startDate, this.state.endDate);
    this.props.getSitePerformanceByBrowser(this.state.userType, this.state.startDate, this.state.endDate);
    this.props.getSitePerformanceByDevice(this.state.userType, this.state.startDate, this.state.endDate);
  }

  onClick(e) {
    this.setState({
      active_tab : e.target.id
    });
  }

  handleStartDate(e) {
    this.setState({
      startDate: e.target.value
    });
  }

  handleEndDate(e) {
    this.setState({
      endDate: e.target.value
    });
  }

  handleSubmit(event) {
    event.preventDefault();
    let start = this.state.startDate;
    let end = this.state.endDate;
    this.setState({
      startDate : start,
      endDate: end
    },() => {
      this.getData();
    });
  }

  getData() {
    this.props.getSitePerformanceByPlatform(this.state.userType, this.state.startDate, this.state.endDate);
    this.props.getSitePerformanceByBrowser(this.state.userType, this.state.startDate, this.state.endDate);
    this.props.getSitePerformanceByDevice(this.state.userType, this.state.startDate, this.state.endDate);
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
      startDate : start,
      endDate : end
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
      startDate : start,
      endDate : end
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
      startDate : start,
      endDate : end
    },() => {
      this.getData();
    });
  }

  handleUserTypeChange(e) {
    this.setState(
      { userType: e.target.value },
      () => {
        this.getData()
      }
    );
  }

  render() {
    const { userType, startDate, endDate } = this.state;
    const { platformReport, browserReport, deviceReport, isFetching } = this.props;

    return (
      <div className="main_container">
        <div className="action-bar">
          <div className="action-bar-col"></div>
          <div className="action-bar-col">
            <div style={{marginTop:"20px"}}>
              <select className="action-bar-select" value={userType} onChange={this.handleUserTypeChange}>
                <option value='all'>All</option>
                <option value='logged_in'>Logged In</option>
                <option value='logged_out'>Logged Out</option>
              </select>
            </div>
          </div>
          <div className="action-bar-col">
            <div className='upperDiv'>
              <form>
                <ul>
                  <li>
                    <input id="startDate" type="date" name="startDate" value={startDate} required onChange={this.handleStartDate} />
                  </li>
                  <li>
                    <input id="endDate" type="date" name="endDate" value={endDate} required onChange={this.handleEndDate} />
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
                <li><a id="export" href={'exportUserStats/' + startDate + '/' + endDate + '/' + userType } target="_blank">Export</a></li>
              </ul>
            </div>
          </div>
        </div>
        { isFetching && <div className="spin-container"><ReactSpinner color="#24b26b" /></div> }
        {
          !isFetching &&
            <div className="content">
              <div className="inner">
                <ReportingTable title='Platform' reportData={platformReport} />
                <ReportingTable title='Device' reportData={deviceReport} />
                <ReportingTable title='Browser' reportData={browserReport} />
              </div>
            </div>
        }
      </div>
    );
  }
}

function mapStateToProps(state) {
  return {
    platformReport: state.deviceOSReporting.platformReport,
    browserReport: state.deviceOSReporting.browserReport,
    deviceReport: state.deviceOSReporting.deviceReport,
    isFetching: state.deviceOSReporting.isFetching,
  };
}

function mapDispatchToProps(dispatch) {
  return {
    getSitePerformanceByPlatform: (userType, startDate, endDate) => { dispatch(getSitePerformanceByPlatform(userType, startDate, endDate)) },
    getSitePerformanceByBrowser: (userType, startDate, endDate) => { dispatch(getSitePerformanceByBrowser(userType, startDate, endDate)) },
    getSitePerformanceByDevice: (userType, startDate, endDate) => { dispatch(getSitePerformanceByDevice(userType, startDate, endDate)) },
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(DeviceOSReporting);
