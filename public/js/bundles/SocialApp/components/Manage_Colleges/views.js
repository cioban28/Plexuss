import './views.scss'
import axios from 'axios';
import { connect } from 'react-redux'
import isURL from 'validator/lib/isURL';
import MDSpinner from 'react-md-spinner';
import { orderBy } from 'lodash';
import React, { Component } from 'react';
import Gauge from 'react-svg-gauge';
import CustomModal from '../Modal/CustomModal';
import RecruitmentModal from './recruitmentModal';
import { openModal } from '../../actions/modal';
import { getCollegeRecruited } from '../../api/search';
import CollegeViewSeek from './clgViewSeekMbl'
import { withRouter } from 'react-router-dom';
import { setRenderManageCollegesIndex } from './../../actions/college';
import { Link } from 'react-router-dom'
import { getViewColleges, deleteViewColleges } from './../../api/viewColleges'
class Views extends Component {

  constructor(props) {
    super(props);
    this.state = {
      allSelected: false,
      selected: [],
      notReceived: false,
      showmore: [],
      viewDetail: [],
      collapse: [],
      rangeNo: 0,
      slugArr: [],
      comparisonUrl: '',
      collegeId: '',
    }
    this.openModal = this.openModal.bind(this);
    this.handleSelectCheck = this.handleSelectCheck.bind(this);
    this.emptySelectedArr = this.emptySelectedArr.bind(this);
    this.handleBackArrowClick = this.handleBackArrowClick.bind(this);
    this.handleSingleDelete = this.handleSingleDelete.bind(this);
  }

  componentDidMount() {
    getViewColleges();
  }
  emptySelectedArr(){
    this.setState({
      selected: [],
    })
  }
  openModal(id){
    this.setState({collegeId: id},()=>{
      this.props.openModal();
    })
    this.props.getCollegeRecruited(id)
  }

  handleSingleDelete(id) {
    let objs = [{id: id}];
    deleteViewColleges({obj: objs}, this.props.colleges, id)
    .then(() => {
      this.setState({selected: [], allSelected: false});
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

  handleAllChecks() {
    this.setState({ allSelected: !this.state.allSelected });
  }

  handleSelectDelete() {
    const selectedColleges = this.state.selected;

    let objs = Object.assign({}, this.state.selected.map((number) => number.toString()));

    deleteViewColleges({obj: objs}, selectedColleges, false)
    .then(() => {
      this.setState({selected: [], allSelected: false});
    })
  }

  handleSelectCheck(college) {
    let newSelected = [...this.state.selected];
    if (!newSelected.includes(college.college_id)) {
      newSelected.push(college.college_id);
    } else {
      let index = newSelected.findIndex((sel) => sel == college.college_id);
      newSelected.splice(index, 1);
    }
    this.setState({ selected: newSelected });
  }

  handleBackArrowClick() {
    // this.props.setRenderManageCollegesIndex(true);
    this.props.history.push('/social/mbl-manage-colleges');
  }

  render(){
    const { allSelected, viewDetail } = this.state;


    const renderDetailsLink = (id) => {
      return (
        !viewDetail.includes(id) ?
        <span><div className='sch-view-details'>QUICK FACTS</div> <div className='sch-details-arrow down'></div></span> :
        <span><div className='sch-view-details'>QUICK FACTS</div> <div className='sch-details-arrow up'></div></span>
      );
    };

    const renderDetail = (college) => {

      return (
        <div className='schooldropdown small-12 column viewrow'>
        {
          <div className='row'>
            <div className="small-2 large-2 columns collapse-view r1">

              <div className='row'>
                <div className='head'>Admission Deadline:</div>
                <div className='ans'>{this.state.collapse[renderCollegeIndex(college)].deadline}</div>
              </div>

              <div className='row'>
                <div className='head'>Acceptance Rate:</div>
                <div className='ans'>N/A</div>
              </div>

              <div className='row'>
                <div className='head'>Student-Teacher Ratio</div>
                <div className='ans'>{this.state.collapse[renderCollegeIndex(college)].student_faculty_ratio}</div>
              </div>

            </div>

            <div className="small-2 large-2 columns collapse-view r2">

              <div className='row'>
                <div className='head'>In-state Tution:</div>
                <div className='ans'>{this.state.collapse[renderCollegeIndex(college)].inStateTution}</div>
              </div>

              <div className='row'>
                <div className='head'>Out-of-State Tution:</div>
                <div className='ans'>{this.state.collapse[renderCollegeIndex(college)].outStateTution}</div>
              </div>

              <div className='row'>
                <div className='head'>Student Body Size</div>
                <div className='ans'>{this.state.collapse[renderCollegeIndex(college)].student_body_total}</div>
              </div>

              <div className='row'>
                <div className='head'>Athletics</div>
                <div className='ans'>{this.state.collapse[renderCollegeIndex(college)].athletic}</div>
              </div>

            </div>

            <div className="small-4 large-6 columns marks">
              <div className="row">
                <div className="large-6 columns">
                  <div className='sat-score'>
                  <div  className='sat'>SAT SCORE</div>
                  <div  className='sat-head2'>{this.state.collapse[renderCollegeIndex(college)].sat_total ? this.state.collapse[renderCollegeIndex(college)].sat_total : 0}</div>
                  <div  className='sat-head2'>
                      <Gauge value={this.state.collapse[renderCollegeIndex(college)].sat_total ? this.state.collapse[renderCollegeIndex(college)].sat_total : 0} width={130} height={130} label="" color="#2AC56C" min={0} max={2500} minMaxLabelStyle={{display: "none"}}/>

                  </div>
                </div>
                </div>
                <div className="large-6 columns">
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
        }
      </div>
      )
    }

    const renderCollegeIndex = (id) => {

      return this.state.collapse.findIndex((record) => record.id == id)
    };
    const renderDisplay = () => this.state.selected && this.state.selected.length != 0 ? 'block' : 'none';

    return (
        <div className="scholarship-container">
          <div className="scholarship_mobile_view">
            <div className="left_side">
              <i className="fa fa-chevron-left" onClick={this.handleBackArrowClick}></i>
              <div className="mbl_title" onClick={this.handleBackArrowClick}>Colleges viewing you</div>
            </div>
            <div>

            </div>
          </div>
          <div className="scholarships-background">
            <div className="row custom-row">
              <CollegeViewSeek colleges={this.props.colleges} selected={this.state.selected} handleSelectCheck={this.handleSelectCheck} openModal={this.openModal} loadSpinner={this.props.loadSpinner} handleSingleDelete={this.handleSingleDelete}/>
              <div className='sch-content-container sch-content-container1 rec'>
                <div className="row">
                  <div className="small-4 large-8 columns">
                    <h1 className="mh">COLLEGES VIEWING YOU</h1>
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
                  <div className='sch-table-headers sch-table-headers clearfix'>
                    <div className='sch-col sch-col-name-view'>
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
                    this.props.loadSpinner && this.props.colleges && this.props.colleges.length == 0 && this.state.notReceived == false &&
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
                              <div className="sch-rank-view">{ colleges.rank !== 'N/A' ? `#${colleges.rank}` : 'N/A' }</div>
                            </div>

                            <div className="small-2 large-2 columns yesb">
                              <div className="button success yes" onClick={() => this.openModal(colleges.college_id)}><b>YES</b></div>
                            </div>

                            <div className="small-2 large-2 columns nob">
                              <div className="button success no" onClick={() => this.handleSingleDelete(colleges.college_id)}><b>NO</b></div>
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
                  <Link to={"/comparison?UrlSlugs="+this.state.comparisonUrl} className="_img_parent">
                    <div>
                      <img className="compare" src="/social/images/rightBar/Compare Colleges.png" />
                    </div>
                    <div>COMPARE</div>
                  </Link>
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
    loadSpinner: state.viewColleges && state.viewColleges.loadSpinner,
    colleges: state.viewColleges && state.viewColleges.colleges,
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

export default connect(mapStateToProps, mapDispatchToProps)(withRouter(Views));
