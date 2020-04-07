import './scholarship.scss';
import { connect } from 'react-redux'
import isURL from 'validator/lib/isURL';
import MDSpinner from 'react-md-spinner';
import React, { Component } from 'react';


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

class ScholarshipsM extends Component {
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
      }
      this.clearSelectedItems = this.clearSelectedItems.bind(this);
    }
  
    componentDidMount() {
      this.props.fetchScholarships();
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
      return (
         <div className="scholarship-container">
           <div className="scholarship_mobile_view">
              <div className="left_side">
                
                <div>Scholarship</div>
              </div>
              <div>
                
              </div>
           </div>
            <div className="scholarships-background">
              <div className="row custom-row">
                <div className='sch-content-container'>
                  <div className="row upper_banner">
                    <div className="small-4 large-7 columns">
                      <h1 className="mh">SCHOLARSHIPSsssssssssssss</h1>
                      <p className="minih">Apply for scholarships and receive scholarship recommendations</p>
                    </div>
                    <div className="small-4 large-5 columns">
                      <div className="row">
                        <span className="error-1">You have pending scholarship applications, click next to finish.</span>
                      </div>
                      <div className="row">
                        <a href="/college-application/scholarships?isScholarship=true" className="button success suc">NEXT</a>
                      </div>
                    </div>
                  </div>
  
                  <div className='sch-table-container'>
                    <div className="row table_upper_banner">
                      <span>
                        <a href="/scholarships" className="button secondary btn-se-scholarship"><i className="fa fa-plus">    </i> ADD SCHOLARSHIPS</a>
                        {
                          this.state.selected && this.state.selected.length != 0 && <a href="#" className="button secondary btn-se" onClick={this.handleSelectDelete.bind(this)}><i className="fa fa-trash" aria-hidden="true"></i> MOVE TO TRASH</a>
                        }
                      </span>
                    </div>
                    <div className='sch-table-headers clearfix'>
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
                      !!scholarships && scholarships.length > 0 && scholarships.map((scholarship) => (
                        <div className='sch-table-content-box'>
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
                                <input type="checkbox" className="ch" checked={allSelected || this.state.newSelected && this.state.newSelected.includes(1)} onClick={this.handleSelectCheck.bind(this, scholarship.id)}/>
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
                                  { renderDetailsLink(scholarship.id) }
                                </span>
                              </div>
                                <div className="sch_col_parent">
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
    }
  }
  
  function mapDispathToProps(dispatch) {
    return {
      deletedQueueScholarship: (userId, scholarshipId, status) => { dispatch(queueScholarship(userId, scholarshipId, status, deletedQueueScholarshipSuccess, deletedQueueScholarshipFailure)) },
      fetchScholarships: () => { dispatch(fetchScholarships(fetchScholarshipsSuccess, fetchScholarshipsFailure)) },
      queueScholarship: (userId, scholarshipId, status) => { dispatch(queueScholarship(userId, scholarshipId, status, queueScholarshipSuccess, queueScholarshipFailure)) },
      deleteScholarship: (ids) => { dispatch(deleteScholarship(ids, deleteScholarshipSuccess, deleteScholarshipFailure)) }
    }
  }
  
  export default connect(mapStateToProps, mapDispathToProps)(ScholarshipsM);
  