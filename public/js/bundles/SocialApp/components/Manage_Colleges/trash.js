import './trash.scss'
import axios from 'axios';
import { connect } from 'react-redux'
import MDSpinner from 'react-md-spinner';
import React, { Component } from 'react';
import Gauge from 'react-svg-gauge';
import { withRouter } from 'react-router-dom';
import { setRenderManageCollegesIndex } from './../../actions/college';
import TrashCard from './TrashCard'
import { Link } from 'react-router-dom'
import { getTrashColleges, deleteTrashColleges } from '../../api/trash';
class Trash extends Component {
  constructor(props) {
    super(props);
    this.state = {
      allSelected: false,
      selected: [],
      showmore: [],
      notReceived: false,
      collapse: [],
      viewDetail: [],
      rangeNo: 0,
    }
    this.handleBackArrowClick = this.handleBackArrowClick.bind(this);
    this.handleSelectRestore = this.handleSelectRestore.bind(this);
    this.handleSelectCheck = this.handleSelectCheck.bind(this);
    this.doDelete = this.doDelete.bind(this);
    this.emptySelectedArr = this.emptySelectedArr.bind(this);
  }
  componentDidMount() {
    getTrashColleges();
  }
  handleAllChecks() {
    this.setState({ allSelected: !this.state.allSelected });
  }
  handleSelectCheck(id) {
    let newSelected = [...this.state.selected];
    if (!newSelected.includes(id)) {
      newSelected.push(id);
    } else {
      let index = newSelected.findIndex((sel) => sel == id);
      newSelected.splice(index, 1);
    }
    this.setState({ selected: newSelected });
  }
  emptySelectedArr(){
    this.setState({
      selected: [],
    })
  }
  handleViewDetail(id) {
    let detail = this.state.viewDetail;
    let collapses = this.state.collapse;
    if (!detail.includes(id)) {
      axios.get(`/ajax/recruiteme/portalcollegeinfo/data/${id}`)
      .then(response => {
        detail.push(id);
        collapses.push(response.data);

        this.setState({ viewDetail: detail })
        this.setState({ collapse: collapses });
      })
      .catch(error => {
      })
    }
    else {
      let index = detail.findIndex((rec) => rec == id)
      let index2 = collapses.findIndex((rec) => rec == id)

      let filtered = [...detail];
      let filtered2 = [...collapses];
      filtered.splice(index, 1);
      filtered2.splice(index2, 1);

      this.setState({ viewDetail: [...filtered] });
      this.setState({ collapse: [...filtered2] });
    }
  }

  handleSelectRestore() {
    let coll = this.props.colleges;
    const selectedColleges = this.state.selected;
    let arr = [];
    this.state.selected.map((college) => {
      let index_col = coll.findIndex((rec) => rec.college_id == college);
      arr.push({id: coll[index_col].college_id, type: coll[index_col].type});
    });

    deleteTrashColleges({obj: arr}, selectedColleges, false)
    .then(() => {
      this.setState({selected: [], allSelected: false});
    })
  }
  doDelete(college) {
    let objs = [{id: college.college_id, type: college.type}];
    deleteTrashColleges({obj: objs}, this.props.colleges, college.college_id)
    .then(() => {
      this.setState({selected: [], allSelected: false});
    })
  }

  handleBackArrowClick() {
    // this.props.setRenderManageCollegesIndex(true);
    this.props.history.push('/social/mbl-manage-colleges');
  }

  render(){
    const { allSelected, viewDetail } = this.state;
    const renderDisplay = () => this.state.selected && this.state.selected.length != 0 ? 'block' : 'none';
    const renderDetailsLink = (id) => {
      return (
        !viewDetail.includes(id) ?
        <span><div className='sch-view-details'>QUICK FACTS</div> <div className='sch-details-arrow down'></div></span> :
        <span><div className='sch-view-details'>QUICK FACTS</div> <div className='sch-details-arrow up'></div></span>
      );
    };
    const renderDetailsLinkM = (id) => {
      return (
        !viewDetail.includes(id) ?
          <span><div className='m-detail-link-tra'>QUICK FACTS <i className="fa fa-caret-down" aria-hidden="true"></i></div></span> :
          <span><div className='m-detail-link-tra'>QUICK FACTS <i className="fa fa-caret-up" aria-hidden="true"></i></div> </span>
      );
    };
    const renderDetail = (college) => {

      return (
        <div className='schooldropdown small-12 column'>
        {
           <div className='row'>
            <div className="desktop-coll">
              <div className="small-12 large-6 columns">
                <ul className="collage-info">
                  <li>
                    <div className="small-6 large-6 columns left-a">
                      <h4 className='head'>Admission Deadline:</h4>
                      <p className='ans'>{this.state.collapse[renderCollegeIndex(college)].deadline}</p>
                    </div>
                    <div className="small-6 large-6 columns right-a">
                      <h4 className='head'>In-state Tution:</h4>
                      <p className='ans'>{this.state.collapse[renderCollegeIndex(college)].inStateTution}</p>
                    </div>
                  </li>

                  <li>
                    <div className="small-6 large-6 columns left-a">
                      <h4 className='head'>Acceptance Rate:</h4>
                      <p className='ans'>N/A</p>
                    </div>
                    <div className="small-6 large-6 columns right-a">
                      <h4 className='head'>Out-of-State Tution:</h4>
                      <p className='ans'>{this.state.collapse[renderCollegeIndex(college)].outStateTution}</p>
                    </div>
                  </li>

                  <li>
                    <div className="small-6 large-6 columns left-a">
                      <h4 className='head'>Student-Teacher Ratio</h4>
                      <p className='ans'>{college.student_faculty_ratio}</p>
                    </div>
                    <div className="small-6 large-6 columns right-a">
                      <h4 className='head'>Student Body Size</h4>
                      <p className='ans'>{this.state.collapse[renderCollegeIndex(college)].student_body_total}</p>
                    </div>
                  </li>
                  <li>
                    <div className="small-6 large-6 columns left-a">
                        <h4 className='head'></h4>
                        <p className='ans'></p>
                    </div>
                    <div className="small-6 large-6 columns right-a">
                      <h4 className='head'>Athletics</h4>
                      <p className='ans'>{this.state.collapse[renderCollegeIndex(college)].athletic}</p>
                    </div>
                  </li>
                </ul>
              </div>


              <div className="small-12 large-6 columns marks">
                <div className="row">
                  <div className="small-6 large-6 columns">
                    <div className='sat-score'>
                    <div  className='sat'>SAT SCORE</div>
                    <div  className='sat-head2'>{this.state.collapse[renderCollegeIndex(college)].sat_total ? this.state.collapse[renderCollegeIndex(college)].sat_total : 0}</div>
                    <div  className='sat-head2'>
                        <Gauge value={this.state.collapse[renderCollegeIndex(college)].sat_total ? this.state.collapse[renderCollegeIndex(college)].sat_total : 0} width={130} height={130} label="" color="#2AC56C" min={0} max={2500} minMaxLabelStyle={{display: "none"}}/>
                    </div>
                  </div>
                  </div>
                  <div className="small-6 large-6 columns">
                    <div className='act-score'>
                    <div  className='sat'>ACT SCORE</div>
                      <div  className='sat-head2'>{this.state.collapse[renderCollegeIndex(college)].act ? this.state.collapse[renderCollegeIndex(college)].act : 0}</div>
                      <div  className='sat-head2'>
                        <Gauge value={this.state.collapse[renderCollegeIndex(college)].act ? this.state.collapse[renderCollegeIndex(college)].act : 0} width={130} height={130} label="" color="#2AC56C" min={0} max={2500} minMaxLabelStyle={{display: "none"}}/>
                      </div>
                     </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        }
      </div>
      )
    }

    const renderCollegeIndex = (id) => {
      return this.state.collapse.findIndex((record) => record.id == id)
    };

    return (
       <div className="scholarship-container">
          <div className="scholarship_mobile_view">
            <div className="left_side">
              <i className="fa fa-chevron-left" onClick={this.handleBackArrowClick}></i>
              <div className="mbl_title" onClick={this.handleBackArrowClick}>Trash</div>
            </div>
          </div>
          <div className="scholarships-background">
            <div className="row custom-row">
              <TrashCard colleges={this.props.colleges} selected={this.state.selected} handleSelectCheck={this.handleSelectCheck} loadSpinner={this.props.loadSpinner} handleSelectRestore={this.handleSelectRestore} doDelete={this.doDelete}/>
              <div className='sch-content-container sch-content-container1 rec'>
                <div className="row">
                  <div className="small-4 large-8 columns recommendation-main-heading">
                    <h1 className="mh">TRASH</h1>
                    <p className="minih">Click 'restore' to restore schools and scholarships</p>
                  </div>
                </div>

                <div className='sch-table-container main_header_manage_clg fav'>
                  <div className="row" style={{display: renderDisplay()}}>
                    <span>
                      <a href="#" className="button secondary btn-se" onClick={this.handleSelectRestore}><img className="logo_res" src="/social/images/Icons/RESTORE Icon.svg" /> RESTORE</a>
                    </span>
                  </div>
                  <div className='sch-table-headers sch-table-headers1 clearfix'>
                    <div className='sch-col sch-col-check'>
                      <input type="checkbox" className="ch1" onClick={this.handleAllChecks.bind(this)}/>
                    </div>

                    <div className='sch-col sch-col-name-fav trash_sch_name'>
                      SCHOOL & SCHOLARSHIPS
                    </div>

                    <div className="sch-col sch-col-restore trash_sch_restore" >
                      RESTORE?
                    </div>
                  </div>
                  {
                    this.props.loadSpinner && this.props.colleges && this.props.colleges.length == 0 && this.state.notReceived == false &&
                     <div className="new-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div> ||
                    !this.props.loadSpinner && this.props.colleges.length == 0 &&
                      <div className="no-record-found">No Record Found</div>
                  }
                  {
                    this.props.colleges && this.props.colleges.length == 0 && this.state.notReceived == true &&
                      <div className="datanotavailable">Data Not Available</div>
                  }
                  {
                   !!this.props.colleges && this.props.colleges.length > 0 && this.props.colleges.map((colleges, index) => (
                      <div className='sch-table-content-box' key={index} style={{ overflow: 'auto' }}>
                        <div className='sch-table-result-wrapper' added='false'>
                          <div className="sch-table-result clearfix">

                            <div className="mobile-view-recommendation">
                              <div className="row checkbox-row">

                                <div className='recommendation-checkbox'>
                                  <input type="checkbox" className="ch" onClick={() => this.handleSelectCheck(colleges)}/>
                                </div>

                                <div className="recommendation-name">
                                  <a href="{{$sch->website}}" target="_blank">
                                    <div className="recommendation-school-name"><Link to={'/college/' +  colleges.school_name.replace(/ /g,'-')}>{colleges.school_name}</Link></div>
                                  </a>
                                  <div className="recommendation-provider-name">{colleges.city}, {colleges.state}</div>
                                  <div className="mbl-restore" onClick={() => this.doDelete(colleges)} >
                                    <a href="#" className="restore-pointer-mbl">
                                      <img className="logo_res" src="/social/images/Icons/RESTORE Icon.svg" />
                                      RESTORE
                                    </a>
                                  </div>
                                </div>
                              </div>

                              <span onClick={this.handleViewDetail.bind(this, colleges.college_id)}>
                                { renderDetailsLinkM(colleges.college_id) }
                              </span>
                            </div>


                            <div className="rec-desktop-view">
                              <div className='sch-col sch-col-check'>
                                <input type="checkbox" className="ch" checked={allSelected || this.state.selected && this.state.selected.includes(colleges.college_id)} onClick={() => this.handleSelectCheck(colleges.college_id)}/>
                              </div>

                              <div className='sch-col sch-col-logo'>
                                <img className="logo_college" src={`${colleges.logo_url}`} />
                              </div>

                              <div className="sch-col sch-col-name-ap name">
                                <a href="{{$sch->website}}" target="_blank">
                                  <div className="sch-name sch-linkout"><Link to={'/college/' +  colleges.slug}>{colleges.school_name}</Link></div>
                                </a>
                                <div className="sch-provider">{colleges.city}, {colleges.state}</div>
                                <span onClick={this.handleViewDetail.bind(this, colleges.college_id)}>
                                  { renderDetailsLink(colleges.college_id) }
                                </span>
                              </div>
                            </div>

                            <div className="sch-col sch-col-res tras">
                              <div className="sch-restore cursor" onClick={() => this.doDelete(colleges)} >
                                <a href="#" className="restore-pointer">
                                  <img className="logo_res" src="/social/images/Icons/RESTORE Icon.svg" />
                                  RESTORE
                                </a>
                              </div>
                            </div>

                          </div>
                        </div>
                        {
                          this.state.collapse && this.state.collapse.findIndex((record) => record.id == colleges.college_id) != -1 &&
                          renderDetail(colleges.college_id)
                        }
                      </div>
                      ))

                  }
                </div>
              </div>
            </div>
          </div>
          <div className="bottom_card_favorite">
            {
              this.state.selected.length > 0 &&
              <div className="card_parent">
                <div className="cross_btn" onClick={this.emptySelectedArr}>x</div>
                <div className="left_portion">
                  <div className="restore" onClick={this.handleSelectRestore}>
                      <div className="img_parent">
                        <img className="" src="/social/images/Icons/RESTORE Icon.svg" />
                      </div>
                      <div className="content_res">RESTORE SELECTED</div>
                  </div>
                </div>
              </div>
            }
          </div>
        </div>
    );
  }
}
function mapStateToProps(state){
  return{
    loadSpinner: state.trash && state.trash.loadSpinner,
    colleges: state.trash && state.trash.colleges,
  }
}
function mapDispatchToProps(dispatch) {
  return {
    fetchApplications: () => { dispatch(fetchApplications) },
    setRenderManageCollegesIndex: (value) => { dispatch(setRenderManageCollegesIndex(value)) },
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(withRouter(Trash));
