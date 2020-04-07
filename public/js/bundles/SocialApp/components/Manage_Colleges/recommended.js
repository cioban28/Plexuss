import './recommended.scss'
import axios from 'axios';
import { connect } from 'react-redux'
import isURL from 'validator/lib/isURL';
import MDSpinner from 'react-md-spinner';
import { orderBy } from 'lodash';
import React, { Component } from 'react';
import CustomModal from '../Modal/CustomModal';
import RecruitmentModal from './recruitmentModal';
import { openModal } from '../../actions/modal';
import { getCollegeRecruited } from '../../api/search';
import { withRouter } from 'react-router-dom';
import { setRenderManageCollegesIndex } from './../../actions/college';
import { Link } from 'react-router-dom'
import { getRecColleges, deleteRecColleges } from './../../api/recColleges'
import InfiniteScroll from 'react-infinite-scroller';

class Recommended extends Component {
  constructor(props) {
    super(props);
    this.state = {
      allSelected: false,
      selected: [],
      showmore: [],
      notReceived: false,
      viewDetail: [],
      collapse: [],
      rangeNo: 0,
      colleges: [],
      slugArr: [],
      comparisonUrl: '',
      collegeId: '',

      loadSpinner: true,
    }
    this.openModal = this.openModal.bind(this);
    this.handleBackArrowClick = this.handleBackArrowClick.bind(this);
    this.getRecommendedColleges = this.getRecommendedColleges.bind(this);
  }

  getRecommendedColleges(pageNumber) {
    getRecColleges(pageNumber);
  }

  handleAllChecks() {
    this.setState({ allSelected: !this.state.allSelected });
  }
  handleSelectDelete() {
    const selectedColleges = this.state.selected;

    let objs = Object.assign({}, this.state.selected.map((number) => number.toString()));

    deleteRecColleges({obj: objs}, selectedColleges, false)
    .then(() => {
      this.setState({selected: [], allSelected: false});
    })
  }

  handleSingleDelete(id) {
    let objs = [{id: id}];
    deleteRecColleges({obj: objs}, this.props.colleges, id)
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

  openModal(id){
    this.setState({collegeId: id},()=>{
      this.props.openModal();
    })
    this.props.getCollegeRecruited(id)
  }
  render(){
    const { allSelected, viewDetail, collapse } = this.state;

    const renderDisplay = () => this.state.selected && this.state.selected.length != 0 ? 'block' : 'none';

    const renderDetailsLink = (id) => {
      return (
        !viewDetail.includes(id) ?
        <span><div className='sch-view-details-rec'>Why we recommended this school</div> <div className='sch-details-arrow down'></div></span> :
        <span><div className='sch-view-details-rec'>Why we recommended this school</div> <div className='sch-details-arrow up'></div></span>
      );
    };
    const renderDetailsLinkM = (id) => {
      return (
        !viewDetail.includes(id) ?
        <span><div className='m-detail-link-rec'>Why we recommended this school <i className="fa fa-caret-down" aria-hidden="true"></i></div> </span> :
        <span><div className='m-detail-link-rec'>Why we recommended this school <i className="fa fa-caret-up" aria-hidden="true"></i></div> </span>
      );
    };
    const renderMajorOrDepartment = (college) => {
      if (college.is_major_recommend) {
        return <li>They offer your degree and major</li>;
      } else if(college.is_department_recommend) {
        return <li>They offer your degree and majors in the same department</li>;
      }
    };

    return (
      <div className="scholarship-container">
        <div className="scholarship_mobile_view">
            <div className="left_side">
              <i className="fa fa-chevron-left" onClick={this.handleBackArrowClick}></i>
              <div className="mbl_title" onClick={this.handleBackArrowClick}>My Recommendations</div>
            </div>
            <div>
            </div>
          </div>
        <div className="scholarships-background">
          <div className="row custom-row">
            <div className='sch-content-container rec'>
              <div className="row">
                <div className="small-4 large-8 columns recommendation-main-heading">
                  <h1 className="mh">MY RECOMMENDATIONS</h1>
                  <p className="minih">Say "yes" to add to your Favorite Colleges, or "no" to remove</p>
                </div>
              </div>
              <InfiniteScroll
                pageStart={0}
                loadMore={this.getRecommendedColleges}
                hasMore={this.props.hasMoreColleges}
              >
                <div className='sch-table-container main_header_manage_clg fav'>
                  <div className="row recommendation-compare-colleges-menu" style={{display: renderDisplay()}}>
                    <span>
                      <a href="#" className="button secondary btn-se"><i className="fa fa-trash" aria-hidden="true"></i> MOVE TO THRASH</a>
                      <Link to={"/comparison?UrlSlugs="+this.state.comparisonUrl} className="button secondary btn-se"><i className="fa fa-plus">    </i> COMPARE COLLEGES</Link>
                    </span>
                  </div>
                  <div className='sch-table-headers sch-table-headers1 clearfix'>
                    <div className='sch-col sch-col-check'>
                      <input type="checkbox" className="ch1" onClick={this.handleAllChecks.bind(this)}/>
                    </div>

                    <div className='sch-col sch-col-name-recommended recom_shl'>
                      SCHOOL
                    </div>

                    <div className="sch-col sch-col-rank-recommended recom_rank">
                      RANK
                    </div>

                    <div className="sch-col sch-col-eng recom_want">
                      WANT TO BE RECRUITED?
                    </div>
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
                  this.props.colleges && this.props.colleges.length > 0 && this.props.colleges.map((college, index) => (
                    <div className='sch-table-content-box' key={index} style={{ overflow: 'auto' }}>
                      <div className='sch-table-result-wrapper' added='false'>
                        <div className='sch-table-result clearfix'>
                          <div className="mobile-view-recommendation">
                            <div className="row checkbox-row">
                              <div className='recommendation-checkbox'>
                                <input type="checkbox" className="ch" onClick={this.handleSelectCheck.bind(this, college)}/>
                              </div>

                                <div className="recommendation-name">
                                  <a href="{{$sch->website}}" target="_blank">
                                    <div className="recommendation-school-name"><Link to={'/college/' +  college.school_name.replace(/ /g,'-')}>{college.school_name}</Link></div>
                                  </a>
                                  <div className="recommendation-provider-name">{college.city}, {college.state}</div>
                                </div>

                                <div className="recommendation-rank-status">
                                  <div className="row recommendation-rank"><div className="rank">{ college.rank !== 'N/A' ? `#${college.rank}` : 'N/A' }</div></div>
                                </div>
                              </div>
                              <p>
                                <div className="recommended-heading-button">
                                  WANT TO BE RECRUITED?
                                </div>
                              </p>
                              <div className='small-2 large-2 columns yesnomb'>
                                <div className="yesmb" onClick={() => this.openModal(college.college_id)}><b>YES</b></div>
                                <div className="nomb"  onClick={this.handleSingleDelete.bind(this, college.college_id)}><b>NO</b></div>
                              </div>
                              <span onClick={this.handleViewDetail.bind(this, college.college_id)}>
                                { renderDetailsLinkM(college.college_id) }
                              </span>
                            </div>
                            <div className="rec-desktop-view">
                              <div className='sch-col sch-col-check'>
                                <input type="checkbox" className="ch" checked={allSelected || this.state.selected && this.state.selected.includes(college.college_id)} onClick={this.handleSelectCheck.bind(this, college)}/>
                              </div>

                              <div className='sch-col sch-col-logo'>
                                <img className='logo_college' src={`${college.logo_url}`} />
                              </div>

                              <div className='sch-col sch-col-name-ap name'>
                                <a href="{{$sch->website}}" target='_blank'>
                                  <div className='sch-linkout-recommended'><Link to={'/college/' +  college.slug}>{college.school_name}</Link></div>
                                </a>
                                <div className='sch-provider'>{college.city}, {college.state}</div>
                                <span onClick={this.handleViewDetail.bind(this, college.college_id)}>
                                  { renderDetailsLink(college.college_id) }
                                </span>
                              </div>


                              <div className='sch-col sch-col-rank'>
                                <div className="sch-rank-recommended">{college.rank !== 'N/A' ? `#${college.rank}` : 'N/A'}</div>
                              </div>

                              <div className='small-2 large-2 columns yesb'>
                                <div className="button success yes" onClick={() => this.openModal(college.college_id)}><b>YES</b></div>
                              </div>

                              <div className='small-2 large-2 columns nob'>
                                <div className="button success no"  onClick={this.handleSingleDelete.bind(this, college.college_id)}><b>NO</b></div>
                              </div>
                            </div>
                          </div>
                        </div>

                        {
                          <div className='schooldropdown small-12 column' style={{display: viewDetail.includes(college.college_id) ? 'block' : 'none'}}>
                            {
                              college.recommend_based_on_college_name ?
                              <div className='row'>
                                <div className='small-12 large-6 column'>
                                  <div className='recommended-recruit-info'>
                                    You are receiving this recommendation because you chose {college.recommend_based_on_college_name} on {college.date_added} .
                                  </div>
                                </div>

                                <div className='small-12 large-6 column'>
                                  <div className='row collapse recruitleftbox'>
                                    <div className='small-12 column recruitschooltitle'>{college.school_name} has:</div>
                                    <div className='small-12 column'>
                                      <ol type='a' className="reclist">
                                        {
                                          college.is_higher_rank_recommend && college.is_higher_rank_recommend == 1 ?
                                          <li>A higher rank</li> : ''
                                        }
                                        {
                                          college.is_major_recommend ?
                                          <li>They offer your degree and major</li> : ''
                                        }
                                        {
                                          college.is_department_recommend ?
                                          <li>They offer your degree and majors in the same department</li> : ''
                                        }
                                        {
                                          college.is_lower_tuition_recommend && college.is_lower_tuition_recommend == 1 ?
                                          <li>Lower Tuition</li> : ''
                                        }
                                        {
                                          college.is_top_75_percentile_recommend && college.is_top_75_percentile_recommend == 1 ?
                                          <li> Your score put you in the top 75% percentile of their past year’s enrollment class</li> : ''
                                        }
                                      </ol>
                                    </div>
                                  </div>
                                </div>
                              </div> :
                              <div className='row recruitinfo_parent'>
                                <div className='small-12 large-6 column recruitinfo'>
                                  You are receiving this recommendation because you are within the top 75% percentile of these colleges past year enrollment class.
                                </div>
                                <div className='small-12 large-6 column box_rec'>
                                  In order to get better recommendation you can also choose the schools you are interested in to attend.
                                </div>
                              </div>
                            }
                          </div>
                        }
                      </div>
                    ))
                  }
                </div>
              </InfiniteScroll>
            </div>
          </div>
        </div>
        {
          this.props.isOpen && <CustomModal reactClassName='modalClass'>
            <RecruitmentModal collegeId={this.state.collegeId}/>
          </CustomModal>
        }
      </div>
    );
  }
}
function mapStateToProps(state){
  return{
    loadSpinner: state.recColleges && state.recColleges.loadSpinner,
    colleges: state.recColleges && state.recColleges.colleges,
    isOpen: state.modal.isOpen,
    hasMoreColleges: state.recColleges && state.recColleges.hasMoreColleges,
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

export default connect(mapStateToProps, mapDispatchToProps)(withRouter(Recommended));
