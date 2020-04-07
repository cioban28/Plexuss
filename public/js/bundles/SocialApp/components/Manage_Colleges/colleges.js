import './colleges.scss'
import axios from 'axios';
import { connect } from 'react-redux'
import React, { Component } from 'react';
import Gauge from 'react-svg-gauge';
import FavoriteColleges from './collegesMblView'
import MDSpinner from 'react-md-spinner';
import AddMoreColleges from './AddMoreColleges'
import { withRouter, Link } from 'react-router-dom';
import { setRenderManageCollegesIndex } from './../../actions/college';
import { getFavColleges, deleteFavColleges } from './../../api/favColleges'
import { college } from '../../reducers/college';

class Colleges extends Component {

  constructor(props) {
    super(props);
    this.state = {
      allSelected: false,
      selected: [],
      showmore: [],
      viewDetail: [],
      collapse: [],
      notReceived: false,
      rangeNo: 0,
      slugArr: [],
      comparisonUrl: '',
      addMore: false,
    }
    this.handleSelectCheck = this.handleSelectCheck.bind(this);
    this.emptySelectedArr = this.emptySelectedArr.bind(this);
    this.handleBackArrowClick = this.handleBackArrowClick.bind(this);
    this.handleAddMore = this.handleAddMore.bind(this);
  }
  componentDidMount() {
    getFavColleges();
  }
  emptySelectedArr(){
    this.setState({
      selected: []
    })
  }

  handleAllChecks() {
    this.setState({ allSelected: !this.state.allSelected });
  }

  handleAddMore() {
    this.setState({addMore: !this.state.addMore})
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


  handleSelectDelete() {
    const selectedColleges = this.state.selected;

    let objs = Object.assign({}, this.state.selected.map((number) => number.toString()));

    deleteFavColleges({obj: objs}, selectedColleges)
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

  handleBackArrowClick() {
    // this.props.setRenderManageCollegesIndex(true);
    this.props.history.push('/social/mbl-manage-colleges');
  }

  render(){
    const { allSelected, viewDetail, collapse } = this.state;
    const renderDetailsLink = (id) => {

      return (
        !viewDetail.includes(id) ?
        <span><div className='sch-view-details'>QUICK FACTS</div> <div className='sch-details-arrow down'></div></span> :
        <span><div className='sch-view-details'>QUICK FACTS</div> <div className='sch-details-arrow up'></div></span>
      );
    };
    const renderDetail = (college) => {

      return (
        <div className='schooldropdown small-12 column fav-c fav-colleges'>
        {
          <div className='row'>
            <div className="small-2 large-2 columns collapse-view r1">

              <div className='row'>
                <div className='head'>Admission Deadline:</div>
                <div className='ans'>{this.state.collapse[renderCollegeIndex(college)].deadline}</div>
              </div>

              <div className='row'>
                <div className='head'>Acceptance Rate:</div>
                <div className='ans'>{this.state.collapse[renderCollegeIndex(college)].percent_admitted ? (this.state.collapse[renderCollegeIndex(college)].percent_admitted + '%'): 'N/A' }</div>
              </div>

              <div className='row'>
                <div className='head'>Student-Teacher Ratio</div>
                <div className='ans'>{this.state.collapse[renderCollegeIndex(college)].student_faculty_ratio}</div>
              </div>

            </div>

            <div className="small-2 large-2 columns collapse-view r2">

              <div className='row'>
                <div className='head'>In-state Tuition:</div>
                <div className='ans'>{this.state.collapse[renderCollegeIndex(college)].inStateTuition}</div>
              </div>

              <div className='row'>
                <div className='head'>Out-of-State Tuition:</div>
                <div className='ans'>{this.state.collapse[renderCollegeIndex(college)].outStateTuition}</div>
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

            <div className="small-4 large-6 columns marks-c">
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
                      <Gauge value={this.state.collapse[renderCollegeIndex(college)].act ? this.state.collapse[renderCollegeIndex(college)].act : 0} width={130} height={130} label="" color="#2AC56C" min={0} max={36} minMaxLabelStyle={{display: "none"}}/>

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
       <div id="fav-container" className="scholarship-container">
          <div className="scholarship_mobile_view">
            <div className="left_side">
              <i className="fa fa-chevron-left" onClick={this.handleBackArrowClick}></i>
              <div className="mbl_title" onClick={this.handleBackArrowClick}>My Favorites</div>
            </div>
            <div>
            </div>
         </div>
          <div className="scholarships-background">
            <div className="row custom-row">
              <FavoriteColleges colleges={this.props.colleges} selected={this.state.selected} handleSelectCheck={this.handleSelectCheck} loadSpinner={this.props.loadSpinner}/>
              <div className='sch-content-container sch-content-container1 fav'>
                <div className="row">
                  <div className="small-4 large-8 columns">
                    <h1 className="mh">MY FAVORITES</h1>
                    <p className="minih">Manage the colleges you have requested to be recruited by</p>
                  </div>
                  <div className="small-4 large-4 columns">
                    <div className="button success suc" onClick={this.handleAddMore}><b>ADD COLLEGES</b></div>
                    {
                      this.state.addMore &&
                      <AddMoreColleges handleAddMore={this.handleAddMore} />
                    }
                  </div>
                </div>

                <div className='sch-table-container fav'>
                  <div className="row" style={{display: renderDisplay()}}>
                    <span>
                      <a href="#" className="button secondary btn-se" onClick={this.handleSelectDelete.bind(this)}><i className="fa fa-trash" aria-hidden="true"></i> MOVE TO TRASH</a>
                      <Link to={"/comparison?UrlSlugs="+this.state.comparisonUrl} className="button secondary btn-se"><i className="fa fa-plus">    </i> COMPARE COLLEGES</Link>
                    </span>
                  </div>
                  <div className='sch-table-headers-fav clearfix'>
                    <div className='sch-col sch-col-check'>
                      <input type="checkbox" className="ch1" onClick={this.handleAllChecks.bind(this)}/>
                    </div>

                    <div className='sch-col sch-col-name-fav'>
                      NAME
                    </div>

                    <div className="sch-col sch-col-rank">
                      RANK
                    </div>

                  {
                   // <div className="sch-col sch-col-eng">
                   //    HANDSHAKE
                   //  </div>
                  } 

                     <div className="sch-col sch-col-und">
                      APPLLIED
                    </div>

                  {
                    // <div className="sch-col sch-col-gra">
                    //   PLEXUSS MEMBER?
                    // </div>
                  }

                  </div>
                  {
                    this.props.loadSpinner && this.props.colleges && this.props.colleges.length == 0 && this.state.notReceived == false &&
                      <div className="new-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>
                  }
                  {
                  this.props.loadSpinner == false && this.props.colleges.length == 0 &&
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
                            <div className='sch-col sch-col-check'>
                              <input type="checkbox" className="ch" checked={allSelected || this.state.selected && this.state.selected.includes(colleges.college_id)} onClick={() =>
                                this.handleSelectCheck(colleges)}/>
                            </div>

                            <div className='sch-col sch-col-logo'>
                              <img className="logo_college" src={colleges.logo_url} />
                            </div>

                            <div className="sch-col sch-col-name-ap name clg_name">
                              <div>
                                <div className="sch-linkout-fav">
                                <Link to={'/college/' +  colleges.slug}>{colleges.school_name}</Link>
                                </div>
                              </div>
                              <div className="sch-provider">{colleges.city}, {colleges.state}</div>
                              <span onClick={this.handleViewDetail.bind(this, colleges.college_id)}>
                                { renderDetailsLink(colleges.college_id) }
                              </span>
                            </div>


                            <div className="sch-col sch-col-rank">
                              <div className="sch-rank-fav">{ colleges.rank !== 'N/A' ? `#${colleges.rank}` : 'N/A' }</div>
                            </div>

                            {
                            // <div className='sch-col sch-col-hand'>
                            //   {
                            //     colleges.hand_shake ==true ?
                            //       <div className='sch-eng'><img src="/social/images/Icons/handshake.svg"/></div>
                            //     :
                            //       <div className='sch-eng'><img src="/social/images/Icons/handshake.svg"/></div>
                            //   }
                            // </div>
                            }

                            <div className="sch-col sch-col-app">
                              {
                                colleges.user_applied > 0 ?
                                  <div className='sch-und'>Yes</div>
                                :
                                  <div className='sch-und'>No</div>

                              }
                            </div>

                            {
                            // <div className="sch-col sch-col-mem-fav">
                            //   {
                            //     colleges.in_our_network > 0 ?
                            //       <div className='sch-gra'>.</div>
                            //     :
                            //       <div className='sch-grad'></div>
                            //   }
                            // </div>
                            }

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
const mapStateToProps = (state) => {
  return{
    loadSpinner: state.favColleges && state.favColleges.loadSpinner,
    colleges: state.favColleges && state.favColleges.colleges,
  }
}
function mapDispatchToProps(dispatch) {
  return {
    fetchApplications: () => { dispatch(fetchApplications) },
    setRenderManageCollegesIndex: (value) => { dispatch(setRenderManageCollegesIndex(value)) },
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(withRouter(Colleges));
