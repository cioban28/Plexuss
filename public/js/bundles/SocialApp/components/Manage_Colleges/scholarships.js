import './scholarship.scss';
import { connect } from 'react-redux'
import isURL from 'validator/lib/isURL';
import MDSpinner from 'react-md-spinner';
import React, { Component } from 'react';
import { withRouter, Link } from 'react-router-dom';
import { setRenderManageCollegesIndex } from './../../actions/college';
import { APP_ROUTES } from './../OneApp/constants'
import { getProfileData, getStudentProfile } from '../../../StudentApp/actions/Profile';

import {
  deleteScholarship,
  fetchScholarships,
  queueScholarship,
} from '../../api/scholarship';
import {
  fetchScholarshipsSuccess,
  fetchScholarshipsFailure,
  queueScholarshipSuccess,
  queueScholarshipFailure,
  deleteScholarshipSuccess,
  deleteScholarshipFailure,
  deletedQueueScholarshipSuccess,
  deletedQueueScholarshipFailure,
} from '../../actions/scholarship';

let myIncomplete = []
class Scholarships extends Component {
  constructor(props) {
    super(props);
    this.state = {
      allSelected: false,
      selected: [],
      showmore: [],
      viewDetail: [],
      filteredScholarships: [],
      scholarshipLoaders: [],
      rangeNo: 0,
      loadSpinner: true,
    }
    this.clearSelectedItems = this.clearSelectedItems.bind(this);
    this.handleBackArrowClick = this.handleBackArrowClick.bind(this);
  }

  componentDidMount() {
    this.props.fetchScholarships();
    if(Object.keys(this.props._profile).length < 10){
      this.props.getProfileData();
      this.props.getStudentProfile();
    }
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

  handleBackArrowClick() {
    // this.props.setRenderManageCollegesIndex(true);
    this.props.history.push('/social/mbl-manage-colleges');
  }

  handleSelectsm(id) {

    let newsm = [...this.state.showmore];
    if (!newsm.includes(id)) {
      newsm.push(id);
      this.setState({ showmore: newsm });
    } else {

      let indexs = newsm.findIndex((sel) => sel == id);
      newsm.splice(indexs, 1);
      this.setState({ showmore: newsm });
    }
  }
  clearSelectedItems(){
    this.setState({
      selected: [],
    })
  }
  componentWillReceiveProps(nextProps) {

    if (nextProps.scholarships.length != 0 && nextProps.scholarships != this.props.scholarships) {

      this.setState({filteredScholarships: nextProps.scholarships});
    }
    if (nextProps.deletedQueuedScholarships) {
      let index = this.state.scholarshipLoaders.findIndex((scholar) => scholar.id == nextProps.deletedQueuedScholarships.scholarship);

      let filtered = [...this.state.scholarshipLoaders];
      filtered.splice(index, 1);

      this.setState({ scholarshipLoaders: [...filtered] });
    }
    if (nextProps.queuedScholarships.length != 0 && nextProps.queuedScholarships.length != this.props.queuedScholarships.length) {
      let index = this.state.scholarshipLoaders.length-1


      let filtered = [...this.state.scholarshipLoaders];

      let filteredRecord = {
        id: this.state.scholarshipLoaders[index].id,
        active: true,
      }

      filtered[index] = filteredRecord;

      this.setState({ scholarshipLoaders: [...filtered] });
    }
  }

  handleAddScholarship(id) {
    this.props.queueScholarship(this.props.userId, id, 'finish');

    let loaders = this.state.scholarshipLoaders;

    loaders.push({
      id: id,
      active: false,
    })

    this.setState({ scholarshipLoaders: loaders });
  }

  handleRemoveScholarship(id) {
    this.props.deletedQueueScholarship(this.props.userId, id, '');

    let loaders = [...this.state.scholarshipLoaders];

    let index = loaders.findIndex((record) => record.id == id );

    loaders[index] = ({
      id: id,
      active: false,
    });

    this.setState({ scholarshipLoaders: loaders });
  }
  handleSelectDelete() {
    this.props.deleteScholarship(this.state.selected);
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
    return incomplete.length === 0 ? true : false
  }

  render(){
    const { scholarships, signedIn } = this.props;
    const { scholarshipLoaders, viewDetail, allSelected } = this.state;
    const checkForButton = (scholarship) => signedIn != 0 && scholarshipLoaders.filter((sch) => sch.id == scholarship.id).length == 0;
    const checkForActiveButton = (scholarship) => signedIn != 0 && scholarshipLoaders.filter((sch) => sch.id == scholarship.id ).length != 0;
    const renderDescription = (value) => value ? value : 'none';
    const renderDetailsLink = (id) => {
      return (
        !viewDetail.includes(id) ?
        <span><div className='sch-view-details'>VIEW DETAILS</div> <div className='sch-details-arrow down'></div></span> :
        <span><div className='sch-view-details'>HIDE DETAILS</div> <div className='sch-details-arrow up'></div></span>
      );
    }
    const renderDetailsLink2 = (id) => {
      return (
        !viewDetail.includes(id) ?
        <span><div className='mbl-det'>VIEW DETAILS <i className="fa fa-caret-down" aria-hidden="true"></i> </div> </span> :
        <span><div className='mbl-det'>HIDE DETAILS <i className="fa fa-caret-up" aria-hidden="true"></i> </div> </span>
      );
    }
    return (
      <div id="my_scholarships" className="scholarship-container">
        <div className="scholarship_mobile_view">
          <div className="left_side">
            <i className="fa fa-chevron-left" onClick={this.handleBackArrowClick}></i>
            <div className="mbl_title" onClick={this.handleBackArrowClick}>My Scholarships</div>
          </div>
          <div>

          </div>
        </div>
          <div className="scholarships-background">
            <div className="row custom-row">
              <div className='sch-content-container'>
                <div className="row upper_banner">
                  <div className="small-4 large-7 columns">
                    <h1 className="mh">MY SCHOLARSHIPS</h1>
                    <p className="minih">Apply for scholarships and receive scholarship recommendations</p>
                  </div>
                  {
                    this._filterSections() &&
                    <div className="small-4 large-5 columns">
                      <div className="row">
                        <span className="success-1" style={{background: "#2ac56c !important", border: "1px solid #2ac56c !important"}}>Click next to add scholarships.</span>
                      </div>
                      <div className="row">
                        <a href="/scholarships" className="button success suc">NEXT</a>
                      </div>
                    </div>
                  }
                  {
                    !this._filterSections() &&
                    <div className="small-4 large-5 columns">
                      <div className="row">
                        <span className="error-1">Pending: Click Next and complete your College Application Assessment.</span>
                      </div>
                      <div className="row">
                        <a href={!!myIncomplete[0] && myIncomplete[0].path} className="button success suc orange-clr">NEXT</a>
                      </div>
                    </div>
                  }
                </div>

                <div className='sch-table-container main_header_manage_clg'>
                  <div className="row table_upper_banner">
                    <span>
                      <a href="/scholarships" className="button secondary btn-se-scholarship"><i className="fa fa-plus">    </i> ADD SCHOLARSHIPS</a>
                      {
                        this.state.selected && this.state.selected.length != 0 && <a href="#" className="button secondary btn-se" onClick={this.handleSelectDelete.bind(this)}><i className="fa fa-trash" aria-hidden="true"></i> MOVE TO TRASH</a>
                      }
                    </span>
                  </div>
                  <div className='sch-table-headers sch-table-headers1 clearfix'>
                    <div className='sch-col sch-col-check'>
                      <input type="checkbox" className="ch1" onClick={this.handleAllChecks.bind(this)}/>
                    </div>

                    <div className='sch-col sch-col-name-sc'>
                      <div className='sch-sort-arrows' data-col='name'></div>NAME
                    </div>

                    <div className="sch-col sch-col-amount">
                      <div className="sch-sort-arrows" data-col="amount"></div>AMOUNT
                    </div>

                    <div className="sch-col sch-col-due">
                      <div className="sch-sort-arrows"  data-col="due"></div>DEADLINE
                    </div>

                    <div className="sch-col sch-col-add">
                      <div className="sch-sort-arrows"  data-col="added"></div>STATUS
                    </div>

                    <div className="sch-col sch-col-usd sch-usd-dropdown-btn">
                      <div className="sch-drop-down-arrow"></div>
                      <span className="sch-usd-img">$</span>
                      <span className="sch-usd-txt">USD</span>
                      <div className="sch-usd-dropdown">
                        <div className="sm-loader mt20"></div>
                      </div>
                    </div>

                  </div>
                  {
                    // scholarships && scholarships.length == 0 &&
                      // <div className="Spinner-sch"><MDSpinner singleColor="#2AC56C"/></div>
                  }

                  {
                    !!scholarships && scholarships.length > 0 && scholarships.map((scholarship, index) => (
                      <div className='sch-table-content-box' key={index} style={{ overflow: 'auto' }}>
                        <div
                          className='sch-table-result-wrapper'
                          data-sid={scholarship.id}
                          data-name={scholarship.scholarship_name}
                          data-provider={scholarship.provider_name}
                          data-amount={scholarship.amount}
                          data-due={scholarship.deadline}
                          added='false'
                        >
                          <div className='sch-table-result clearfix'>
                            <div className='sch-col sch-col-check'>
                              <input type="checkbox" className="ch" checked={allSelected || this.state.selected && this.state.selected.includes(scholarship.id)} onClick={this.handleSelectCheck.bind(this, scholarship.id)}/>
                            </div>
                            <div className='sch-col sch-col-name-sc'>
                              {
                                scholarship.ro_id && scholarship.website && isURL(scholarship.website) ?
                                  <a href={scholarship.website} target='_blank'>
                                    <div className='sch-name sch-linkout'>{scholarship.scholarship_name && scholarship.provider_name}</div>
                                  </a>
                                :
                                  <div className='sch-name'>{scholarship.scholarship_name}</div>
                              }
                              <div className='sch-provider'>Scholarship provided by {scholarship.provider_name || 'Anonymous'}</div>

                              <span onClick={this.handleViewDetail.bind(this, scholarship.id)}>
                                <span className="desktop">{ renderDetailsLink(scholarship.id) }</span>
                                <span className="mobile">{ renderDetailsLink2(scholarship.id) }</span>
                              </span>
                            </div>
                              <div className="sch_col_parent">
                                <div className='sch-col sch-col-amount'>
                                  <div className='sch-amount'>$
                                    {
                                      (scholarship.amount && scholarship.amount == 0) ? '&nbsp;' : scholarship.amount.toLocaleString(navigator.language, { minimumFractionDigits: 2 })
                                    }
                                  </div>
                                </div>

                                <div className='sch-col sch-col-due'>
                                  <div className='sch-due'>{scholarship.deadline}</div>
                                </div>

                                <div className="deskview">
                                  <div className='sch-col sch-col-add'>
                                    {
                                      !this._filterSections() && checkForButton(scholarship) &&
                                      <div className={'sch-add-btn no'}>
                                        {
                                          scholarshipLoaders.filter((sch) => sch.id == scholarship.id).length != 0 ?
                                          <MDSpinner color1='#ffffff' color2='#ffffff' color3='#fffff' color4='#ffffff' size={15}/> :
                                          <span>PENDING</span>
                                        }

                                      </div>
                                    }
                                    {
                                      this._filterSections() &&
                                      <div className={'sch-add-btn school-status'} style={{background: '#2AC56C !important'}}>
                                          <span>Submitted</span>
                                      </div>
                                    }
                                    {
                                      checkForActiveButton(scholarship) && scholarshipLoaders.filter((sch) => sch.id == scholarship.id && sch.active == false).length != 0 &&
                                      <div className='sch-add-btn no' onClick={this.handleRemoveScholarship.bind(this, scholarship.id)}>
                                        <MDSpinner color1='#ffffff' color2='#ffffff' color3='#fffff' color4='#ffffff' size={15}/>
                                      </div>
                                    }
                                    {
                                      checkForActiveButton(scholarship) && scholarshipLoaders.filter((sch) => sch.id == scholarship.id && sch.active == false).length == 0 &&
                                      <div className='sch-add-btn yes' onClick={this.handleRemoveScholarship.bind(this, scholarship.id)}>
                                        <span>SUBMITTED</span>
                                      </div>
                                    }
                                    {
                                      signedIn == 0 &&
                                      <div className='sch-add-btn-login'>+</div>
                                    }
                                  </div>
                                </div>

                                <div className="mobileview">
                                  <div className='sch-col sch-col-add-mobile'>
                                    {
                                      checkForButton(scholarship) &&
                                      <div className={'sch-add-btn no'} onClick={this.handleAddScholarship.bind(this, scholarship.id)}>
                                        {
                                          scholarshipLoaders.filter((sch) => sch.id == scholarship.id).length != 0 ?
                                          <MDSpinner color1='#ffffff' color2='#ffffff' color3='#fffff' color4='#ffffff' size={15}/> :
                                          <span>PENDING</span>
                                        }

                                      </div>
                                    }
                                    {
                                      checkForActiveButton(scholarship) && scholarshipLoaders.filter((sch) => sch.id == scholarship.id && sch.active == false).length != 0 &&
                                      <div className='sch-add-btn no' onClick={this.handleRemoveScholarship.bind(this, scholarship.id)}>
                                        <MDSpinner color1='#ffffff' color2='#ffffff' color3='#fffff' color4='#ffffff' size={15}/>
                                      </div>
                                    }
                                    {
                                      checkForActiveButton(scholarship) && scholarshipLoaders.filter((sch) => sch.id == scholarship.id && sch.active == false).length == 0 &&
                                      <div className='sch-add-btn yes' onClick={this.handleRemoveScholarship.bind(this, scholarship.id)}>
                                        <span>SUBMITTED</span>
                                      </div>
                                    }
                                    {
                                      signedIn == 0 &&
                                      <div className='sch-add-btn-login'>+</div>
                                    }
                                  </div>
                                </div>

                                <div className='sch-col sch-col-usd'>
                                  <div className='sch-usd'>USD</div>
                                </div>
                              </div>
                          </div>
                          {
                            <div className='sch-result-details-cont-scholarship' style={{display: viewDetail.includes(scholarship.id)?'block':'none'}}>
                              <div className='sch-desc-title sch-due-mobile'>Deadline</div>
                              <div className='sch-desc  sch-due-mobile'>{scholarship.deadline}</div>
                              <div className='sch-desc-title mt20'>Description</div>
                              <div className='sch-desc'>{ renderDescription(scholarship.description) }</div>
                            </div>
                          }
                        </div>
                      </div>
                    ))
                  }
                  {
                    scholarships.length == 0 &&
                    <div className="new-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>
                  }
                </div>
              </div>
            </div>
            <div className="bottom_card">
              {
                this.state.selected.length > 0 &&
                <div className="card_parent">
                  <div className="upper_banner">
                    <div className="count">{this.state.selected.length}</div>
                    <div className="card_text">
                      Scholarship have been selected,<br /> Click next to apply to them.
                    </div>
                    <a href="/college-application/scholarships?isScholarship=true" className="next_btn">Next</a>
                  </div>
                  <div className="canncel_btn" onClick={this.clearSelectedItems}>CANCEL</div>
                </div>
              }
            </div>
          </div>
        </div>
    );
  }
}

function mapStateToProps(state) {

  return {
    deletedQueuedScholarships: state.scholarships.deletedQueuedScholarships,
    signedIn: state.scholarships.signedIn,
    scholarships: state.scholarships.scholarships,
    queuedScholarships: state.scholarships.queuedScholarships,
    userId: state.scholarships.userId,
    _profile: state._profile
  }
}

function mapDispathToProps(dispatch) {
  return {
    deletedQueueScholarship: (userId, scholarshipId, status) => { dispatch(queueScholarship(userId, scholarshipId, status, deletedQueueScholarshipSuccess, deletedQueueScholarshipFailure)) },
    fetchScholarships: () => { dispatch(fetchScholarships(fetchScholarshipsSuccess, fetchScholarshipsFailure)) },
    queueScholarship: (userId, scholarshipId, status) => { dispatch(queueScholarship(userId, scholarshipId, status, queueScholarshipSuccess, queueScholarshipFailure)) },
    deleteScholarship: (ids) => { dispatch(deleteScholarship(ids, deleteScholarshipSuccess, deleteScholarshipFailure)) },
    setRenderManageCollegesIndex: (value) => { dispatch(setRenderManageCollegesIndex(value)) },
    getStudentProfile: () => dispatch(getProfileData()) ,
    getProfileData: () => dispatch(getStudentProfile()) ,
  }
}

export default connect(mapStateToProps, mapDispathToProps)(withRouter(Scholarships));
