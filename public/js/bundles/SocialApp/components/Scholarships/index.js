// import './react-loader-spinner/dist/loader/css/Plane.css';
import { connect } from 'react-redux'
import isURL from 'validator/lib/isURL';
import MDSpinner from 'react-md-spinner';
import React, { Component } from 'react';
import { orderBy } from 'lodash';

import './styles.scss';
import {
  fetchScholarships,
  queueScholarship,
} from '../../api/scholarship';
import {
  fetchScholarshipsSuccess,
  fetchScholarshipsFailure,
  queueScholarshipSuccess,
  queueScholarshipFailure,
  deletedQueueScholarshipSuccess,
  deletedQueueScholarshipFailure,
} from '../../actions/scholarship';
import { APP_ROUTES } from './../OneApp/constants'
import { getProfileData, getStudentProfile } from '../../../StudentApp/actions/Profile';
import { Helmet } from 'react-helmet';
let myIncomplete = []
class Scholarships extends Component {

  constructor(props) {
    super(props);
    this.state = {
      viewDetail: [],
      filteredScholarships: [],
      scholarshipLoaders: [],
      rangeNo: 0,
    }

    this.handleRangeURL = this.handleRangeURL.bind(this);
    this.handleFilter = this.handleFilter.bind(this);
  }

  componentDidMount() {
    this.props.fetchScholarships();
    this.handleFilter();
    if(Object.keys(this.props._profile).length < 10){
      this.props.getProfileData();
      this.props.getStudentProfile;
    }
  }
  componentDidUpdate(prevProps) {
    if(this.props.location !== prevProps.location){
      this.handleFilter();
    }
  }
  handleFilter(){
    if(window.location.pathname === '/scholarships'){
      let params = (new URL(window.location)).searchParams;
      let step = params.get('filter');
      switch (step){
        case '1000': this.handleRangeURL(0, 999); break;
        case '5000': this.handleRangeURL(0, 4999); break;
        case '10000': this.handleRangeURL(5000, 9999); break;
        case 'above': this.handleRangeURL(10000, -1); break;
        default: this.handleRangeURL(0, -2); break;
      }
    }
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

  handleRangeURL(lower, upper) {
    let check = setInterval(() => {
      if(this.state.filteredScholarships.length !== 0){
        this.handleRangeClick(lower, upper);
        clearInterval(check);
      }
    },100);
  }

  handleRangeClick(lowerLimit, upperLimit) {
    const { scholarships } = this.props;

    let filteredScholarships = scholarships.filter((scholarship) => (scholarship.amount <= upperLimit) && (scholarship.amount >= lowerLimit))
    if (upperLimit === -2)
      filteredScholarships = scholarships;

    let rangeNo = 0;

    if (upperLimit === 999) { rangeNo = 1; }
    else if (upperLimit === 4999) { rangeNo = 2; }
    else if (upperLimit === 9999) { rangeNo = 3; }
    else if (upperLimit === -1) { rangeNo = 4; }

    this.setState({
      filteredScholarships: filteredScholarships,
      rangeNo: rangeNo,
    });
  }

  handleClearFilterClick() {
    this.setState({ filteredScholarships: this.props.scholarships, rangeNo: 0 });
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

  handleScholarshipSortUp(value) {
    let filteredScholarships = orderBy(this.state.filteredScholarships, [value],['asc']);

    this.setState({ filteredScholarships: filteredScholarships });
  }

  handleScholarshipSortDown(value) {
    let filteredScholarships = orderBy(this.state.filteredScholarships, [value],['desc']);

    this.setState({ filteredScholarships: filteredScholarships });
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
        myIncomplete = incomplete
    return incomplete.length === 0 ? true : false
  }

  render() {
    const { queuedScholarships, scholarships, signedIn } = this.props;
    const { filteredScholarships, rangeNo, scholarshipLoaders, viewDetail } = this.state;

    const checkForButton = (scholarship) => signedIn != 0 && scholarshipLoaders.filter((sch) => sch.id == scholarship.id).length == 0;
    const checkForActiveButton = (scholarship) => signedIn != 0 && scholarshipLoaders.filter((sch) => sch.id == scholarship.id ).length != 0;
    const checkRecordPresent = () => scholarshipLoaders.filter((record) => record.active == true).length == 0;
    const renderDescription = (value) => value ? value : 'none';

    const renderDetailsLink = (id) => {
      return (
        !viewDetail.includes(id) ?
        <span><div className='sch-view-details'>VIEW DETAILS</div> <div className='sch-details-arrow down'></div></span> :
        <span><div className='sch-view-details'>HIDE DETAILS</div> <div className='sch-details-arrow up'></div></span>
      );
    }

    return (
      <div id='scholarships_main_div'>
        <Helmet>
          <title>Scholarship Finder | Scholarship Search | Plexuss.com</title>
          <meta name="description" content="Wondering how to get a scholarship? Start your scholarship search using the Plexuss Scholarship Finder." />
          <meta name="keywords" content="scholarships, college scholarships" />
        </Helmet>
        <div className='row custom-row' style={{marginTop: 0}}>
          <div id='_ScholarshipsPage' style={{display: 'block'}}>
          <div className='sch-content-container'>

            <div className='howto-box'>
              <div className="above-table-content">
                {
                  this._filterSections() &&
                  <div className='to-manage-schols'>
                    <span className='to-manage-schols-span'>Click next to manage scholarhips</span>
                    <div className='howto-right'>
                      <div className='sch-next-btn' style={{width: '100%', textAlign: 'center', height: '45px', paddingTop: '5%'}}><a href="/social/manage-colleges/scholarship" style={{color: 'white'}}>Next</a></div>
                    </div>
                  </div>
                }

                {
                  !this._filterSections() &&
                  <div className='to-manage-schols'>
                    <div className='howto-right'>
                      <div className='sch-next-btn' style={{width: '100%', textAlign: 'center', height: '45px', paddingTop: '5%'}}><a href={!!myIncomplete[0] ? myIncomplete[0].path : '#'} style={{color: 'white'}}>Next</a></div>
                    </div>
                  </div>
                }

                <div className='howto-left'>
                  <div className='sch-num-box'>{scholarshipLoaders.filter((sch) => sch.active == true).length != 0 ? scholarshipLoaders.length : 0}</div>
                  <span className='sch-sel-txt'>Select scholarships below and click next to apply.</span>
                </div>

                <div className='search-filter-containers row '>
                  <div className='large-2 medium-12 small-12 filter-heading' style={{float: 'left', fontWeight: 'bold' }}>Filter by Amount: &nbsp; </div>
                  <div className=' large-1 medium-2 small-2' style={{float: 'left'}}><span onClick={this.handleRangeClick.bind(this, 0, 999)} className={rangeNo == 1 ? 'amount active' : 'amount'} style={{cursor: 'pointer'}}>Below $1K</span></div>
                  <div className=' large-1 medium-2 small-2' style={{float: 'left'}}><span onClick={this.handleRangeClick.bind(this, 0, 4999)} className={rangeNo == 2 ? 'amount active' : 'amount'} style={{cursor: 'pointer'}}>Upto $5K</span></div>
                  <div className=' large-1 medium-2 small-2' style={{float: 'left'}}><span onClick={this.handleRangeClick.bind(this, 5000, 9999)} className={rangeNo == 3 ? 'amount active' : 'amount'} style={{cursor: 'pointer'}}>$5K-$10K</span></div>
                  <div className=' large-1 medium-2 small-2' style={{float: 'left'}}><span onClick={this.handleRangeClick.bind(this, 10000, -1)} className={rangeNo == 4 ? 'amount active' : 'amount'} style={{cursor: 'pointer'}}>$10K &amp; Up</span></div>
                  <div  className=' large-2 medium-4 small-4 clear-filters-link' style={{float: 'right'}} ><span onClick={this.handleClearFilterClick.bind(this)} style={{cursor: 'pointer'}}>Clear Filter</span></div>
                </div>
              </div>
            <div className='sch-table-container'>
              <div className='sch-table-headers clearfix'>
                <div className='sch-col sch-col-name'>
                  <div className='sch-sort-arrows' data-col='name'>
                    <div className='sch-sort-up' onClick={this.handleScholarshipSortUp.bind(this, 'scholarship_name')}></div>
                    <div className='sch-sort-down' onClick={this.handleScholarshipSortDown.bind(this, 'scholarship_name')}></div>
                   </div>Name
                </div>

                <div className='sch-col sch-col-amount'>
                  <div className='sch-sort-arrows' data-col='amount'>
                    <div className='sch-sort-up' onClick={this.handleScholarshipSortUp.bind(this, 'amount')}></div>
                    <div className='sch-sort-down' onClick={this.handleScholarshipSortDown.bind(this, 'amount')}></div>
                   </div>Amount
                </div>

                <div className='sch-col sch-col-due'>
                  <div className='sch-sort-arrows' data-col='due'>
                    <div className='sch-sort-up' onClick={this.handleScholarshipSortUp.bind(this, 'deadline')}></div>
                    <div className='sch-sort-down' onClick={this.handleScholarshipSortDown.bind(this, 'deadline')}></div>
                   </div>Deadline
                </div>

                <div className='sch-col sch-col-add'>
                  <div className='sch-sort-arrows'  data-col='added'>
                    <div className='sch-sort-up'></div>
                    <div className='sch-sort-down'></div>
                   </div>Add
                </div>

                <div className='sch-col sch-col-usd sch-usd-dropdown-btn'>
                  <div className='sch-drop-down-arrow'></div>
                  <span className='sch-usd-img'>$</span>
                  <span className='sch-usd-txt'>USD</span>
                  <div className='sch-usd-dropdown'>
                    <div className='sm-loader mt20'></div>
                  </div>

                </div>
              </div>

              <div className='sch-table-content-box'>
                {
                  filteredScholarships.length != 0 && filteredScholarships.map((scholarship) => (
                    <div
                      className='sch-table-result-wrapper'
                      data-sid={scholarship.id}
                      data-name={scholarship.scholarship_name}
                      data-provider={scholarship.provider_name}
                      data-amount={scholarship.amount}
                      data-due={scholarship.deadline}
                      data-added='false'
                      key={scholarship.id}
                    >
                      <div className='sch-table-result clearfix'>
                        <div className='sch-col sch-col-name'>
                          {
                            scholarship.ro_id && scholarship.website && isURL(scholarship.website) ?
                              <a href={scholarship.website} target='_blank'>
                                <div className='sch-name sch-linkout'>{scholarship.scholarship_name && scholarship.provider_name}</div>
                              </a>
                            :
                              <div className='sch-name'>{scholarship.scholarship_name} or {scholarship.provider_name}</div>
                          }
                          <div className='sch-provider'>Scholarship provided by {scholarship.provider_name || 'Anonymous'}</div>

                          <span onClick={this.handleViewDetail.bind(this, scholarship.id)}>
                            { renderDetailsLink(scholarship.id) }
                          </span>
                        </div>

                        <div className='sch-col sch-col-amount'>
                          <div className='sch-amount'>$
                            {
                              (scholarship.amount && scholarship.amount == 0) ? '&nbsp;' : scholarship.amount.toFixed(2)
                            }
                          </div>
                        </div>

                        <div className='sch-col sch-col-due'>
                          <div className='sch-due'>{scholarship.deadline}</div>
                        </div>

                        <div className='sch-col sch-col-add'>
                          {
                            checkForButton(scholarship) &&
                            <div className={'sch-add-btn no'} onClick={this.handleAddScholarship.bind(this, scholarship.id)}>
                              {
                                scholarshipLoaders.filter((sch) => sch.id == scholarship.id).length != 0 ?
                                <MDSpinner color1='#ffffff' color2='#ffffff' color3='#fffff' color4='#ffffff' size={15}/> :
                                <span>+</span>
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
                              <span>Added</span>
                            </div>
                          }
                          {
                            signedIn == 0 &&
                            <div className='sch-add-btn-login'>+</div>
                          }
                        </div>

                        <div className='sch-col sch-col-usd'>
                          <div className='sch-usd'>USD</div>
                        </div>
                      </div>

                      {
                        <div className='sch-result-details-cont' style={{display: viewDetail.includes(scholarship.id)?'block':'none'}}>
                          <div className='sch-desc-title sch-due-mobile'>Deadline</div>
                          <div className='sch-desc  sch-due-mobile'>{scholarship.deadline}</div>
                          <div className='sch-desc-title mt20'>Description</div>
                          <div className='sch-desc'>{ renderDescription(scholarship.description) }</div>
                          <ul>
                            <li>Must be undergrad student</li>
                            <li>must currently attend a university</li>
                          </ul>
                        </div>
                      }
                    </div>
                  ))
                }
                {
                  filteredScholarships.length == 0 &&
                  <div className='sch-no-results'>No results found</div>
                }
              </div>

                <div className='sch-bottom-next-cont text-right mt20'>
                  <div className='sch-next-btn'>Next</div>
                </div>
              </div>
            </div>
          </div>
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
    getStudentProfile: () => dispatch(getProfileData()) ,
    getProfileData: () => dispatch(getStudentProfile()) ,
  }
}

export default connect(mapStateToProps, mapDispathToProps)(Scholarships);
