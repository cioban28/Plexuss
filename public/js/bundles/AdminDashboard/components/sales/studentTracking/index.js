import React from 'react'
import ReactSpinner from 'react-spinjs-fix'
import { connect } from 'react-redux'
import moment from 'moment'
import './styles.scss'
import { getStudentTracking } from './../../../actions/studentTracking'
import _ from 'lodash';

class studentTracking extends React.Component {
	constructor(props) {
    super(props);
    this.state ={
      start_date : moment().subtract(1, 'days').format('YYYY-MM-DD'),
      end_date : moment().subtract(1, 'days').format('YYYY-MM-DD'),
      active_tab : "All",
      date: [new Date(), new Date()],
      value:'Plexuss Users',
      generalData: [],
      monetizationsData: [],
      uniqueVisitsData: [],
    }
    this.onClick = this.onClick.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
    this.handleStartDate = this.handleStartDate.bind(this);
    this.handleEndDate = this.handleEndDate.bind(this);
    this.handleToday = this.handleToday.bind(this);
    this.handleYesterday = this.handleYesterday.bind(this);
    this.handleMonth = this.handleMonth.bind(this);
    this.isEmpty = this.isEmpty.bind(this);

    this.handleGeneralData = this.handleGeneralData.bind(this);
    this.sortGeneraldata = this.sortGeneraldata.bind(this);
    this.setGeneraldData = this.setGeneraldData.bind(this);

    this.handleMonetizationsData = this.handleMonetizationsData.bind(this);
    this.sortMonetizationsdata = this.sortMonetizationsdata.bind(this);
    this.setMonetizationsData = this.setMonetizationsData.bind(this);

    this.setUniqueData = this.setUniqueData.bind(this);
    this.handleUniqueData = this.handleUniqueData.bind(this);
    this.sortUniquedata = this.sortUniquedata.bind(this);

    this.handler = this.handler.bind(this);
    this.getData = this.getData.bind(this);
  }

  componentDidMount(){
    this.getData();
    this.setGeneraldData();
    this.setMonetizationsData();
    this.setUniqueData();
  }
  componentDidUpdate(prevProps){
    if(prevProps.studentTrackingData !== this.props.studentTrackingData){
      this.handler();
    }
  }
  getData(){
    const { getStudentTrackingData } = this.props;
    getStudentTrackingData(this.state.start_date, this.state.end_date);
  }
  handler(){
    this.setGeneraldData();
    this.setMonetizationsData();
    this.setUniqueData();
  }
  /* for montization data*/
  setMonetizationsData(){
    if(this.isEmpty(this.props.studentTrackingData) ||
      this.isEmpty(this.props.studentTrackingData.monetizations) ||
      this.isEmpty(this.props.studentTrackingData.unique_visits) ||
      this.isEmpty(this.props.studentTrackingData.general_data)){
      return;
    }
    let result = [this.props.studentTrackingData.monetizations.reduce((acc, n) => {
      for (var prop in n) {
        if (acc.hasOwnProperty(prop)) acc[prop] = parseInt(acc[prop]) + parseInt(n[prop]);
        else acc[prop] = n[prop];
      }
      return acc;
    }, [])];
    let obj= Object.assign(this.props.studentTrackingData.monetizations.concat(result));
    this.setState({
      monetizationsData: obj,
    })
  }

  handleMonetizationsData(){
    var listItems = this.state.monetizationsData.map((d, index) => <tr key={index}>
    {d.type === 1 && <td>Domestic Students</td> ||
     d.type === 2 && <td>Intl Students</td>
     || d.type === 3 && <td>Total Students</td> }
    <td>{d.total_users}</td>
    <td>{d.zero}</td>
    <td>{d.zero_dollar}</td>
    <td>{d.one_five}</td>
    <td>{d.ten_twenty}</td>
    <td>{d.twenty_fifty}</td>
    <td>{d.fifty_499}</td>
    <td>{d.five_hundred_plus}</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    </tr>);
    return listItems;
  }

  sortMonetizationsdata(type, direction){
    let obj = _.orderBy(this.state.monetizationsData,[type],[direction]);
    this.setState({
      monetizationsData: obj,
    })
  }
  /* end of montization data */

  /* for unique visists */
  setUniqueData(){
    if(this.isEmpty(this.props.studentTrackingData) ||
      this.isEmpty(this.props.studentTrackingData.monetizations) ||
      this.isEmpty(this.props.studentTrackingData.unique_visits) ||
      this.isEmpty(this.props.studentTrackingData.general_data)){
      return;
    }
    let result = [this.props.studentTrackingData.unique_visits.reduce((acc, n) => {
      for (var prop in n) {
        if (acc.hasOwnProperty(prop)) acc[prop] = parseInt(acc[prop]) + parseInt(n[prop]);
        else acc[prop] = n[prop];
      }
      return acc;
    }, [])];
    let obj= Object.assign(this.props.studentTrackingData.unique_visits.concat(result));
    this.setState({
      uniqueVisitsData: obj,
    })
  }

  handleUniqueData(){
    var listItems = this.state.uniqueVisitsData.map((d, index) => <tr key={index}>
    {d.type === 1 && <td>Domestic Students</td> ||
    d.type === 2 && <td>International Students</td>
    || d.type === 3 && <td>Total Students</td> }
    <td>{d.total_users}</td>
    <td>{d.one}</td>
    <td>{d.two}</td>
    <td>{d.three}</td>
    <td>{d.four}</td>
    <td>{d.five}</td>
    <td>{d.six}</td>
    <td>{d.seven}</td>
    <td>{d.eight}</td>
    <td>{d.nine}</td>
    <td>{d.ten_plus}</td>
    </tr>);
    return listItems;
  }

  sortUniquedata(type, direction){
    let obj = _.orderBy(this.state.uniqueVisitsData,[type],[direction]);
    this.setState({
      uniqueVisitsData: obj,
    })
  }
/* end of unique data */
  setGeneraldData(){
    if(this.isEmpty(this.props.studentTrackingData) ||
      this.isEmpty(this.props.studentTrackingData.monetizations) ||
      this.isEmpty(this.props.studentTrackingData.unique_visits) ||
      this.isEmpty(this.props.studentTrackingData.general_data)){
      return;
    }
    let result = [this.props.studentTrackingData.general_data.reduce((acc, n) => {
      for (var prop in n) {
        if (acc.hasOwnProperty(prop)) acc[prop] = parseInt(acc[prop]) + parseInt(n[prop]);
        else acc[prop] = n[prop];
      }
      return acc;
    }, [])];
    let obj= Object.assign(this.props.studentTrackingData.general_data.concat(result));
    this.setState({
      generalData: obj,
    })
  }

  handleGeneralData(){
    var listItems = this.state.generalData.map((d, index) => <tr key={index}>
    {d.type === 1 && <td>Domestic Students</td> || d.type === 2 && <td>International Students</td> || d.type === 3 && <td>Total Students</td> }
    <td>{d.total_users}</td>
    <td>{d.thirty}</td>
    <td>{d.thirty_public_profile}</td>
    <td>{d.thirty_completed_app}</td>
    <td>{d.thirty_public_profile_completed_app}</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    </tr>);
    return listItems;
  }

  sortGeneraldata(type, direction){
    let obj = _.orderBy(this.state.generalData,[type],[direction]);
    this.setState({
      generalData: obj,
    })
  }

  onClick(e){
    this.setState({
      active_tab : e.target.id
    });
  }

  handleStartDate(e) {
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
    },() => {
      this.getData();
    });
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

  isEmpty(obj) {
    for(var key in obj) {
        if(obj.hasOwnProperty(key))
            return false;
    }
    return true;
  }

	render() {
    return (
			<div className="main_container">
        <div className="action-bar">
          <div className="action-bar-col"></div>
          <div className="action-bar-col">
            <div className='upperDiv'>
              <form>
                <ul>
                  <li>
                    <input id="start_date" type="date" name="start_date" value={this.state.start_date} required onChange={this.handleStartDate} />
                  </li>
                  <li>
                    <input id="end_date" type="date" name="end_date" value={this.state.end_date} required onChange={this.handleEndDate} />
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
                <li><a id="export" href={'exportUserStats/'+this.state.start_date+'/'+this.state.end_date+'/'+this.state.value} target="_blank">Export</a></li>
              </ul>
            </div>
          </div>
        </div>


		    <div className="performance-wrapper">
		      <table>
            <thead>
		          <tr key={'6'}>
		            <th className="">General</th>
		            <th className=""><div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortGeneraldata('total_users','asc')}></div><div onClick={()=> this.sortGeneraldata('total_users','desc')}></div>Total</div></th>
		            <th className="">
                  <div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortGeneraldata('thirty','asc')}></div><div onClick={()=> this.sortGeneraldata('thirty','desc')}></div>30%</div>
                </th>
                <th className="">
                  <div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortGeneraldata('thirty_public_profile','asc')}></div><div onClick={()=> this.sortGeneraldata('thirty_public_profile','desc')}></div>30% + Public Profile</div>
                </th>
                <th className="">
                  <div className="sch-sort-arrows" data-col="name" ><div onClick={()=> this.sortGeneraldata('thirty_completed_app','asc')}></div><div onClick={()=> this.sortGeneraldata('thirty_completed_app','desc')}></div>30% + Completed App.</div>
                </th>
                <th className="">
                  <div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortGeneraldata('thirty_public_profile_completed_app','asc')}></div><div onClick={()=> this.sortGeneraldata('thirty_public_profile_completed_app','desc')}></div>30% + Public Profile + Completed App.</div>
                </th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
		          </tr>
		        </thead>
            <tbody>
              {this.handleGeneralData()}

              <tr key={'monetizations'} className="table-heading">
                <td>Monetization</td>
                <td><div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortMonetizationsdata('total_users','asc')}></div><div onClick={()=> this.sortMonetizationsdata('total_users','desc')}></div>Total</div></td>
                <td><div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortMonetizationsdata('zero','asc')}></div><div onClick={()=> this.sortMonetizationsdata('zero','desc')}></div>$0</div></td>
                <td><div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortMonetizationsdata('zero_dollar','asc')}></div><div onClick={()=> this.sortMonetizationsdata('zero_dollar','desc')}></div>$.01 to $1</div></td>
                <td><div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortMonetizationsdata('one_five','asc')}></div><div onClick={()=> this.sortMonetizationsdata('one_five','desc')}></div>$1.01 to $5</div></td>
                <td><div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortMonetizationsdata('ten_twenty','asc')}></div><div onClick={()=> this.sortMonetizationsdata('ten_twenty','desc')}></div>$10.01 to $20</div></td>
                <td><div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortMonetizationsdata('twenty_fifty','asc')}></div><div onClick={()=> this.sortMonetizationsdata('twenty_fifty','desc')}></div>$20.01 to $50</div></td>
                <td><div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortMonetizationsdata('fifty_499','asc')}></div><div onClick={()=> this.sortMonetizationsdata('fifty_499','desc')}></div>$50.01 to $499</div></td>
                <td><div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortMonetizationsdata('five_hundred_plus','asc')}></div><div onClick={()=> this.sortMonetizationsdata('five_hundred_plus','desc')}></div>$500+</div></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              {this.handleMonetizationsData()}

              <tr key={'unique_visits'} className="table-heading">
                <td>Unique Visits</td>
                <td><div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortUniquedata('total_users','asc')}></div><div onClick={()=> this.sortUniquedata('total_users','desc')}></div>Total</div></td>
                <td><div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortUniquedata('one','asc')}></div><div onClick={()=> this.sortUniquedata('one','desc')}></div>1</div></td>
                <td><div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortUniquedata('two','asc')}></div><div onClick={()=> this.sortUniquedata('two','desc')}></div>2</div></td>
                <td><div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortUniquedata('three','asc')}></div><div onClick={()=> this.sortUniquedata('three','desc')}></div>3</div></td>
                <td><div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortUniquedata('four','asc')}></div><div onClick={()=> this.sortUniquedata('four','desc')}></div>4</div></td>
                <td><div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortUniquedata('five','asc')}></div><div onClick={()=> this.sortUniquedata('five','desc')}></div>5</div></td>
                <td><div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortUniquedata('six','asc')}></div><div onClick={()=> this.sortUniquedata('six','desc')}></div>6</div></td>
                <td><div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortUniquedata('seven','asc')}></div><div onClick={()=> this.sortUniquedata('seven','desc')}></div>7</div></td>
                <td><div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortUniquedata('eight','asc')}></div><div onClick={()=> this.sortUniquedata('eight','desc')}></div>8</div></td>
                <td><div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortUniquedata('nine','asc')}></div><div onClick={()=> this.sortUniquedata('nine','desc')}></div>9</div></td>
                <td><div className="sch-sort-arrows" data-col="name"><div onClick={()=> this.sortUniquedata('ten_plus','asc')}></div><div onClick={()=> this.sortUniquedata('ten_plus','desc')}></div>10+</div></td>
              </tr>
              {this.handleUniqueData()}
            </tbody>
          </table>
		    </div>
		  </div>
		);
	}
}
function mapStateToProps(state) {
  return {
    studentTrackingData: state.studentTracking.studentTrackingData,
  };
}

function mapDispatchToProps(dispatch) {
  return {
    getStudentTrackingData: (start_date, end_date) => { dispatch(getStudentTracking(start_date, end_date)) }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(studentTracking);
