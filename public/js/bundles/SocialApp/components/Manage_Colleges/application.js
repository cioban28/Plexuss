
import './application.scss'
import './styles.scss';
import axios from 'axios';
import { connect } from 'react-redux'
import isURL from 'validator/lib/isURL';
import MDSpinner from 'react-md-spinner';
import { orderBy } from 'lodash';
import React, { Component } from 'react';
import { withRouter } from 'react-router-dom';
import { setRenderManageCollegesIndex } from './../../actions/college';
import AddMoreColleges from './AddMoreColleges'
import { Link } from 'react-router-dom'
import { APP_ROUTES } from './../OneApp/constants'
import { getProfileData, getStudentProfile } from '../../../StudentApp/actions/Profile';
import { fetchApplications } from './../../api/application'
let myIncomplete = []

class Applications extends Component {

  constructor(props) {
    super(props);
    this.state = {
      selected: [],
      showmore: [],
      notReceived: false,
      viewDetail: [],
      filteredScholarships: [],
      scholarshipLoaders: [],
      rangeNo: 0,
      slugArr: [],
      comparisonUrl: '',
      addMore: false,
    }
    this.handleBackArrowClick = this.handleBackArrowClick.bind(this);
    this.handleAddMore = this.handleAddMore.bind(this);
    this.emptySelectedArr = this.emptySelectedArr.bind(this);
  }

  componentDidMount() {
    fetchApplications()
  }

  emptySelectedArr(){
    this.setState({selected: []})
  }

  handleViewDetail(id) {
    let detail = this.state.viewDetail;

    if (!detail.includes(id)) {
      detail.push(id);

      this.setState({ viewDetail: detail })
    } else {
      let index = detail.findIndex((rec) => rec == id)

      let filtered = [...detail];
      filtered.splice(index, 1);

      this.setState({ viewDetail: [...filtered] });
    }
  }
  handleSelectCheck(college) {

    let newSelected = [...this.state.selected];
    let newSlugArr = this.state.slugArr;
    if (!newSelected.includes(college.college_id)) {
      newSelected.push(college.college_id);
      newSlugArr.push(college.slug);
    } else {
      let index = newSelected.findIndex((sel) => sel == college.college_id);
      newSelected.splice(index, 1);
      index = newSlugArr.findIndex((s) => s == college.slug);
      newSlugArr.splice(index, 1);
    }
    this.setState({
      selected: newSelected,
      slugArr: newSlugArr,
    }, ()=>{
      let url = '';
      this.state.slugArr.map((elem)=>{
        url += elem+','
      })
      this.setState({
        comparisonUrl: url,
      })
    });
  }

  handleBackArrowClick() {
    // this.props.setRenderManageCollegesIndex(true);
    this.props.history.push('/social/mbl-manage-colleges');
  }

  handleAddMore() {
    this.setState({addMore: !this.state.addMore})
  }

  _filterSections = () => {
    let { _profile } = this.props,

    routesToSkipInSic = ['review', 'sponsor', 'essay', 'uploads', 'demographics'],
    complete = [], incomplete = [];

    if(Object.keys(_profile).length < 10) {return false}

    // !(_profile.txt_opt_in > 0) &&
    routesToSkipInSic.push('verify')
    APP_ROUTES.map(route => {
        if(!routesToSkipInSic.includes(route.id)) {
            (this.props._profile[route.id+'_form_done'])  ?
                complete.push(route) :
                (
                    (route.id === 'verify' && this.props._profile.verified_phone === 1) ||
                    (route.id === 'applications' && this.props._profile.MyApplicationList.length > 0)
                ) ?
                    complete.push(route) :
                    incomplete.push(route)
        }})

    myIncomplete = incomplete;
    return incomplete.length === 0 ? true : false;
  }

  render(){
    const { viewDetail } = this.state;
    const renderDetailsLink = (id) => {
      return (
        !viewDetail.includes(id) ?
        <span><div className='sch-view-details'>WHY SHOULD I APPLY?</div> <div className='sch-details-arrow down'></div></span> :
        <span><div className='sch-view-details'>WHY SHOULD I APPLY?</div> <div className='sch-details-arrow up'></div></span>
      );
    };
    const renderDetailsLinkM = (id) => {
      return (
        !viewDetail.includes(id) ?
        <span><div className='m-detail-link'>WHY SHOULD I APPLY? <i className="fa fa-caret-down" aria-hidden="true"></i></div> </span> :
        <span><div className='m-detail-link'>WHY SHOULD I APPLY? <i className="fa fa-caret-up" aria-hidden="true"></i></div> </span>
      );
    };
    return (
       <div id="x" className="scholarship-container app">
          <div className="scholarship_mobile_view">
            <div className="left_side">
              <i className="fa fa-chevron-left" onClick={this.handleBackArrowClick}></i>
              <div className="mbl_title" onClick={this.handleBackArrowClick}>My Applications</div>
            </div>
            <div>
            </div>
          </div>
          <div className="scholarships-background">
            <div className="row custom-row">

              <div className='sch-content-container applications-container'>
                <div className="row">
                  <div className="small-4 large-7 columns application-main-heading">
                    <h1 className="mh">MY APPLICATIONS</h1>
                    <p className="minih">Manage and track the status of your applications</p>
                  </div>
                  {
                    this._filterSections() &&
                    <div className="small-4 large-5 columns">
                      <div className="button success suc" style={{width: '189px'}} onClick={this.handleAddMore}><b>ADD COLLEGES</b></div>
                      {
                        this.state.addMore &&
                        <AddMoreColleges title="Your Applications" handleAddMore={this.handleAddMore} />
                      }
                    </div>
                  }
                  {
                    !this._filterSections() &&
                    <div className="small-12 medium-5 large-5 columns">
                      <div className="row">
                        <span className="error-1">Pending: Click Next and complete your College Application Assessment.</span>
                      </div>
                      <div className="row">
                        <a href={!!myIncomplete[0] && myIncomplete[0].path} className="button success suc orange-clr">NEXT</a>
                      </div>
                    </div>
                  }

                </div>

                <div className='sch-table-container'>
                  <div className="row application-compare-colleges-menu">
                      <Link to={"/comparison?UrlSlugs="+this.state.comparisonUrl} className="button secondary btn-se-application"><i className="fa fa-plus">    </i> COMPARE COLLEGES</Link>
                  </div>
                  <div className='sch-table-headers sch-table-headers1 clearfix'>

                    <div className='sch-col sch-col-name name-width'style={{width: "36% !important"}}>
                      NAME
                    </div>

                    <div className="sch-col sch-col-rank rank-width"style={{width: "12% !important"}}>
                      RANK
                    </div>

                     <div className="sch-col sch-col-und und-width"style={{width: "20% !important"}}>
                      UNDERGRAD
                    </div>

                    <div className="sch-col sch-col-gra gra-width"style={{width: "12% !important"}}>
                      GRAD
                    </div>

                    <div className="sch-col sch-col-status apl-width" style={{width: "16% !important"}}>
                      APPLICATION LINK
                    </div>
                  </div>
                  <div className="add-more-colleges" onClick={this.handleAddMore}>Add More Colleges</div>
                  {
                    this.state.addMore &&
                    <AddMoreColleges title="Your Applications" handleAddMore={this.handleAddMore} />
                  }
                  {
                    this.props.loadSpinner && this.props.applications && this.props.applications.length == 0 && this.state.notReceived == false &&
                      <div className="new-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>
                  }
                  {
                  this.props.loadSpinner == false && this.props.applications.length == 0 &&
                    <div className="no-record-found">No Record Found</div>
                  }
                  {
                    this.props.applications && this.props.applications.length == 0 && this.state.notReceived == true &&
                      <div className="datanotavailable">Data Not Available</div>
                  }
                  {
                   !!this.props.applications && this.props.applications.length > 0 && this.props.applications.map((application, index) => (
                      <div className='sch-table-content-box' key={index} style={{ overflow: 'auto' }}>
                      <div className='sch-table-result-wrapper' added='false'>
                        <div className="sch-table-result clearfix">
                          <div className="mobile-view-application">
                            <div className="row checkbox-row">

                              <div className='application-checkbox'>
                                <input type="checkbox" className="ch" onClick={this.handleSelectCheck.bind(this, application)}/>
                              </div>

                              <div className="application-name">
                                <a href="{{$sch->website}}" target="_blank">
                                  <div className="application-school-name"><Link to={'/college/' +  application.school_name.replace(/ /g,'-')}>{application.school_name}</Link></div>
                                </a>
                                <div className="application-provider-name">{application.city}, {application.state}</div>
                              </div>

                              <div className="application-rank-status">
                                <div className="row application-rank"><div className="rank">{ application.rank !== 'N/A' ? `#${application.rank}` : 'N/A' }</div></div>
                                <div className="row application-status-head">STATUS</div>
                              </div>
                            </div>
                            <div className="row application-status-message"><div className="application-status-message-text new-status-mobile">
                                <a href={"/college/" + application.slug} style={{color: 'white'}}><i className="fa fa-chain" style={{color: 'white'}}></i> Apply</a>
                              </div>
                            </div>

                            <div className="row under-grad">
                              <div className="column">
                                <div className="heading">UNDERGRAD</div>
                                <div className="undergrad-cost">
                                  {
                                    !!application.undergrad_column_cost && application.undergrad_column_cost != 0 ?
                                      <div className='prog-inner-text'>${application.undergrad_column_cost.toLocaleString()}</div>
                                    :
                                      <div className='prog-inner-text'>N/A</div>
                                  }
                                </div>
                              </div>
                              <div className="column grad-column">
                                <div className="heading">GRAD</div>
                                <div className="grad-cost">
                                  {
                                    application.grad_column_cost != 0 ?
                                      <div className='prog-inner-text'>${application.grad_column_cost}</div>
                                    :
                                      <div className='prog-inner-text'>N/A</div>
                                  }
                                </div>
                              </div>
                            </div>
                            <div className="row application-collapse">
                              <span onClick={this.handleViewDetail.bind(this, application.college_id)}>
                                { renderDetailsLinkM(application.college_id) }
                              </span>
                            </div>
                          </div>
                          <div className="abc">
                            <div className='sch-col sch-col-check'>
                              <input type="checkbox" className="ch" onClick={this.handleSelectCheck.bind(this, application)}/>
                            </div>

                            <div className='sch-col sch-col-logo'>
                              <img className="logo_college" src={`${application.logo_url}`} />
                            </div>
                            <div className="sch-col sch-col-name-ap name sch_col_name_ap_name namec-width" style={{width: "28% !important"}}>
                              <a href="{{$sch->website}}" target="_blank">
                                <div className="sch-name sch-linkout"><Link to={'/college/' +  application.school_name.replace(/ /g,'-')}>{application.school_name}</Link></div>
                              </a>
                              <div className="sch-provider">{application.city}, {application.state}</div>
                              <span onClick={this.handleViewDetail.bind(this, application.college_id)}>
                                { renderDetailsLink(application.college_id) }
                              </span>
                            </div>

                            <div className="sch-col sch-col-rank rankc-width" style={{width: "12% !important"}}>
                              <div className="sch-rank">{ application.rank !== 'N/A' ? `#${application.rank}` : 'N/A' }</div>
                            </div>

                            <div className="sch-col sch-col-und undc-width" style={{width: "20% !important"}}>
                              {
                                !!application.undergrad_column_cost && application.undergrad_column_cost != 0 ?
                                  <div className='sch-und'>${application.undergrad_column_cost.toLocaleString()}</div>
                                :
                                  <div className='sch-und'>N/A</div>

                              }
                            </div>

                            <div className="sch-col sch-col-gra grac-width" style={{width: "12% !important"}}>
                              {
                                application.grad_column_cost != 0 ?
                                  <div className='sch-gra'>${application.grad_column_cost}</div>
                                :
                                  <div className='sch-gra'>N/A</div>
                              }
                            </div>

                            <div id="college_status" className="sch-col sch-col-status" style={{width: "10% !important"}}>
                              <div className="sch-status new-status" style={{background: "#00A3D9 !important", fontSize: "15px !important", fontWeight: "normal !important", marginTop: "23px !important"}}>
                                <a href={"/college/" + application.slug} style={{color: 'white'}}><i className="fa fa-chain" style={{color: 'white'}}></i> Apply</a>
                              </div>
                            </div>

                          </div>
                        </div>
                      </div>
                      {
                        <div className='schooldropdown small-12 column' style={{display: viewDetail.includes(application.college_id) ? 'block' : 'none'}}>
                          <div className="row collapse-row-pad">
                            {application.school_name}
                          </div>
                          <div className="row collapse-row-pad">
                            {application.city}
                          </div>
                        </div>
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
                  <Link to={"/comparison?UrlSlugs="+this.state.comparisonUrl} className="_img_parent">
                    <div>
                      <img className="compare" src="/social/images/rightBar/Compare Colleges.png" />
                    </div>
                    <div>COMPARE</div>
                  </Link>
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
    _profile: state._profile,
    loadSpinner: state.applications && state.applications.loadSpinner,
    applications: state.applications && state.applications.applications,
  }
}

function mapDispatchToProps(dispatch) {
  return {
    setRenderManageCollegesIndex: (value) => { dispatch(setRenderManageCollegesIndex(value)) },
    getProfileData: () => dispatch(getProfileData()) ,
    getStudentProfile: () => dispatch(getStudentProfile()) ,
    getProfileDataLists: () => dispatch(getProfileDataLists()) ,
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(withRouter(Applications));
