import './recruit.scss'
import axios from 'axios';
import { connect } from 'react-redux'
import isURL from 'validator/lib/isURL';
import MDSpinner from 'react-md-spinner';
import { orderBy } from 'lodash';
import React, { Component } from 'react';
import Gauge from 'react-svg-gauge';
import CustomModal from '../Modal/CustomModal';
import RecruitmentModal from './recruitmentModal';
import CollegeViewSeek from './clgViewSeekMbl'
import { openModal } from '../../actions/modal';
import { getCollegeRecruited } from '../../api/search';
import { withRouter } from 'react-router-dom';
import { setRenderManageCollegesIndex } from './../../actions/college';
import { Link } from 'react-router-dom'
import { getRecuColleges, deleteRecuColleges } from './../../api/recuColleges'

class Recruit extends Component {

  constructor(props) {
    super(props);
    this.state = {
      allSelected: false,
      selected: [],
      showmore: [],
      viewDetail: [],
      notReceived: false,
      rangeNo: 0,
      collapse: [],
      collegeInfo: [],
      slugArr: [],
      comparisonUrl: '',
    }
    this.openModal = this.openModal.bind(this);
    this.handleSelectCheck = this.handleSelectCheck.bind(this);
    this.emptySelectedArr = this.emptySelectedArr.bind(this);
    this.handleBackArrowClick = this.handleBackArrowClick.bind(this);
    this.handleSingleDelete = this.handleSingleDelete.bind(this);
  }
  emptySelectedArr(){
    this.setState({
      selected: [],
    })
  }
  handleDetail(){
    this.setState({
        viewDetail: !this.state.viewDetail,
    })
  }
  openModal(id){
    this.setState({collegeId: id},()=>{
      this.props.openModal();
    })
    this.props.getCollegeRecruited(id)
  }
  componentDidMount() {
    getRecuColleges();
  }

  handleAllChecks() {
    this.setState({ allSelected: !this.state.allSelected });
  }

  handleSingleDelete(id) {
    let objs = [{id: id}];
    deleteRecuColleges({obj: objs}, this.props.colleges, id)
    .then(() => {
      this.setState({selected: [], allSelected: false});
    })
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

  handleBackArrowClick() {
    // this.props.setRenderManageCollegesIndex(true);
    this.props.history.push('/social/mbl-manage-colleges');
  }

  getUserCollegeInfo(id) {
    let collegeInfo = this.state.collegeInfo;

    if (!collegeInfo.includes(id)) {
      axios.get(`/ajax/recruite-me/portalcollegeinfo/${id}`)
      .then(response => {
        collegeInfo.push(response);

        this.setState({ collegeInfo: [...collegeInfo]});
      })
      .catch(error => {

      })
    }
  }
  handleSelectDelete() {
    const selectedColleges = this.state.selected;

    let objs = Object.assign({}, this.state.selected.map((number) => number.toString()));

    deleteRecuColleges({obj: objs}, selectedColleges, false)
    .then(() => {
      this.setState({selected: [], allSelected: false});
    })
  }

  render(){
    const { allSelected, colleges, newSelected, viewDetail } = this.state;
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
        <span><div className='m-detail-link-rec'>QUICK FACTS</div> <div className='sch-details-arrow down'></div></span> :
        <span><div className='m-detail-link-rec'>QUICK FACTS</div> <div className='sch-details-arrow up'></div></span>
      );
    };
    const renderDetail = (college) => {

      return (


        <div className='schooldropdown small-12 column recruit recrow'>
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
              <div className="mbl_title" onClick={this.handleBackArrowClick}>Colleges recruiting you</div>
            </div>
            <div>

            </div>
        </div>
          <div className="scholarships-background">
            <div className="row custom-row">
              <CollegeViewSeek colleges={this.props.colleges} selected={this.state.selected} handleSelectCheck={this.handleSelectCheck} openModal={this.openModal} loadSpinner={this.props.loadSpinner} handleSingleDelete={this.handleSingleDelete}/>
              <div className='sch-content-container sch-content-container1 rec'>
                <div className="row">
                  <div className="small-4 large-8 columns recommendation-main-heading">
                    <h1 className="mh">COLLEGES RECRUITING YOU</h1>
                    <p className="minih">Say "yes" to add to your Favorite Colleges, or "no" to remove</p>
                  </div>
                </div>

                <div className='sch-table-container fav'>
                  <div className="row" style={{display: renderDisplay()}}>
                    <span>
                      <a href="#" className="button secondary btn-se" onClick={this.handleSelectDelete.bind(this)}><i className="fa fa-trash" aria-hidden="true"></i> MOVE TO THRASH</a>
                      <a href={"/comparison?UrlSlugs="+this.state.comparisonUrl} className="button secondary btn-se"><i className="fa fa-plus">    </i> COMPARE COLLEGES</a>
                    </span>
                  </div>
                  <div className='sch-table-headers sch-table-headers1 clearfix'>
                    <div className='sch-col sch-col-check'>
                      <input type="checkbox" className="ch1" onClick={this.handleAllChecks.bind(this)}/>
                    </div>

                    <div className='sch-col sch-col-name-fav'>
                      SCHOOL
                    </div>

                    <div className="sch-col sch-col-rank">
                      RANK
                    </div>

                    <div className="sch-col sch-col-eng">
                      WANT TO BE RECRUITED?
                    </div>
                  </div>
                  {
                    this.props.loadSpinner && this.props.colleges && this.props.colleges.length == 0 &&
                      <div className="new-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>
                  }
                  {
                    !this.props.loadSpinner && this.props.colleges && this.props.colleges.length == 0 &&
                      <div className="no-record-found">No Record Found</div>
                  }
                  {
                   !!this.props.colleges && this.props.colleges.length > 0 && this.props.colleges.map((colleges, index) => (
                      <div className='sch-table-content-box' key={index} style={{ overflow: 'auto' }}>
                        <div className='sch-table-result-wrapper' added='false'>
                          <div className="sch-table-result clearfix">

                            <div className="mobile-view-recommendation">
                              <div className="row checkbox-row">

                                <div className='recommendation-checkbox'>
                                  <input type="checkbox" className="ch" onClick={this.handleSelectCheck.bind(this, colleges)}/>
                                </div>

                                <div className="recommendation-name">
                                  <a href="{{$sch->website}}" target="_blank">
                                    <div className="recommendation-school-name"><Link to={'/college/' +  colleges.school_name.replace(/ /g,'-')}>{colleges.school_name}</Link></div>
                                  </a>
                                  <div className="recommendation-provider-name">{colleges.city}, {colleges.state}</div>
                                </div>

                                <div className="column recommendation-rank-status">
                                  <div className="row recommendation-rank"><div className="rank">{ colleges.rank !== 'N/A' ? `#${colleges.rank}` : 'N/A' }</div></div>
                                </div>
                              </div>
                              <p>
                                <div className="recommended-heading-button">
                                  WANT TO BE RECRUITED?
                                </div>
                              </p>
                              <div className='small-2 large-2 columns yesnomb'>
                                <div className="yesmb" onClick={() => this.openModal(colleges.college_id)}><b>YES</b></div>
                                <div className="nomb"  onClick={() => this.handleSingleDelete(colleges.college_id)}><b>NO</b></div>
                              </div>
                              <span onClick={this.handleViewDetail.bind(this, colleges.college_id)}>
                                { renderDetailsLinkM(colleges.college_id) }
                              </span>
                            </div>
                            <div className="rec-desktop-view">
                              <div className='sch-col sch-col-check'>
                                <input type="checkbox" className="ch" checked={allSelected || this.state.selected && this.state.selected.includes(colleges.college_id)} onClick={() => this.handleSelectCheck(colleges)}/>
                              </div>

                              <div className='sch-col sch-col-logo'>
                                <img className="logo_college" src={`${'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/' + colleges.logo_url}`} />
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


                              <div className="sch-col sch-col-rank">
                                <div className="sch-rank-recruit">#{colleges.rank}</div>
                              </div>

                              <div className="small-2 large-2 columns yesb">
                                <div className="button success yes"  onClick={() => this.openModal(colleges.college_id)}><b>YES</b></div>
                              </div>

                              <div className="small-2 large-2 columns nob">
                                <div className="button success no" onClick={() => this.handleSingleDelete(colleges.college_id)}><b>NO</b></div>
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
          {
            this.props.isOpen && <CustomModal reactClassName='modalClass1'>
              <RecruitmentModal collegeId={this.state.collegeId}/>
            </CustomModal>
          }
          <div className="bottom_card_favorite">
            {
              this.state.selected.length > 0 &&
              <div className="card_parent">
                <div className="cross_btn" onClick={this.emptySelectedArr}>x</div>
                <div className="left_portion">
                  <a href={"/comparison?UrlSlugs="+this.state.comparisonUrl} className="_img_parent">
                    <div>
                      <img className="compare" src="/social/images/rightBar/Compare Colleges.png" />
                    </div>
                    <div>COMPARE</div>
                  </a>
                  <div className="_img_parent" onClick={this.handleSelectDelete.bind(this)}>
                    <div>
                      <img className="trash" src="/social/images/trash/trash@2x.png" />
                    </div>
                    <div>REMOVE</div>
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
    loadSpinner: state.recuColleges && state.recuColleges.loadSpinner,
    colleges: state.recuColleges && state.recuColleges.colleges,
    isOpen: state.modal.isOpen,
  }
}
function mapDispatchToProps(dispatch) {
  return {
    fetchApplications: () => { dispatch(fetchApplications) },
    openModal: () => { dispatch(openModal()) },
    getCollegeRecruited: (collegeId) => { dispatch(getCollegeRecruited(collegeId)) },
    setRenderManageCollegesIndex: (value) => { dispatch(setRenderManageCollegesIndex(value)) },
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(withRouter(Recruit));
