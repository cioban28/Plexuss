import React, { Component } from 'react';
import { connect } from 'react-redux';
import { SelectedFilter } from './SelectedFilter.jsx';
import RecommendationMeter from './RecommendationMeter.jsx';
import { setRecommendationFilter, resetRecommendationFilter } from '../../../../../actions/newsfeedActions';
import objectToFormData from 'object-to-formdata';

const tabName = 'uploads';
class Uploads extends Component {
  constructor(props) {
    super(props);

    const state = (this.props.filterChanges.tabName === tabName && this.props.filterChanges.state) || this.props.uploads;

    this.state = { ...state };

    this.handleRemoveFilter = this.handleRemoveFilter.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
    this.handleResetFilterClick = this.handleResetFilterClick.bind(this);
  }

  componentWillReceiveProps(nextProps) {
    if(nextProps.resetFilter) {
      this.props.setResetTargetingFilter(false);
      this.setState({ ...nextProps.uploads });
    }
  }

  componentWillUnmount() {
    if(!_.isEqual(this.state, this.props.uploads)) {
      const filter = {};
      filter.formData = this.buildFormData();
      filter.state = { ...this.state };
      filter.tabName = tabName;
      this.props.setFilterCurrentChanges(filter);
    }
  }

  handleCheckboxChange(filterName) {
    const uploads = { ...this.state.uploads };
    uploads[filterName] = !uploads[filterName];
    this.setState({ uploads });
  }
  
  handleRemoveFilter(filter, filterName) {
    const uploads = { ...this.state.uploads };
    uploads[filter] = false;
    this.setState({ uploads });
  }

  buildFormData() {
    const values = {
      transcript_filter: this.state.uploads.transcript,
      financialInfo_filter: this.state.uploads.financialInfo,
      ielts_fitler: this.state.uploads.ietls,
      toefl_filter: this.state.uploads.toefl,
      resume_filter: this.state.uploads.resume,
      passport_filter: this.state.uploads.passport,
      essay_filter: this.state.uploads.essay,
      other_filter: this.state.uploads.others,
      sales_pid: this.props.salesPostId,
    };
    return objectToFormData(values);
  }

  handleSubmit(e) {
    e.preventDefault();

    this.props.setRecommendationFilterUploads({ ...this.state });
    this.props.setRecommendationFilter(tabName, this.buildFormData());
  }

  handleResetFilterClick() {
    this.props.resetRecommendationFilter(tabName);
  }

  render() {
    const { uploads } = this.state;
    const { isLoading } = this.props;

    return (
      <form onSubmit={this.handleSubmit}>
        <div className='selected-filters-list'>
          <span className='filter-list-title'>Uploads: </span>
          {
            Object.keys(uploads).filter(key => !!uploads[key]).map(key => <SelectedFilter
              key={key}
              filter={key}
              filterName={key}
              handleRemoveFilter={this.handleRemoveFilter}
            />)
          }
        </div>
        <RecommendationMeter />
        <div className='filtering-cont'>
          <p className='description-para mb-10'>In your recommended students, we will give priority to students that have uploaded their:</p>
          <div className='row'>
            <div className='columns small-12'>
              <input type='checkbox' id='transcript-uploads' checked={uploads.transcript} onChange={this.handleCheckboxChange.bind(this, 'transcript')} />
              <label htmlFor='transcript-uploads'>Transcript</label>
            </div>
            <div className='columns small-12'>
              <input type='checkbox' id='financial-uploads' checked={uploads.financialInfo} onChange={this.handleCheckboxChange.bind(this, 'financialInfo')} />
              <label htmlFor='financial-uploads'>Financial Info (Int'l Only)</label>
            </div>
            <div className='columns small-12'>
              <input type='checkbox' id='ielts-uploads' checked={uploads.ietls} onChange={this.handleCheckboxChange.bind(this, 'ietls')} />
              <label htmlFor='ielts-uploads'>Copy of IELTS score</label>
            </div>
            <div className='columns small-12'>
              <input type='checkbox' id='toefl-uploads' checked={uploads.toefl} onChange={this.handleCheckboxChange.bind(this, 'toefl')} />
              <label htmlFor='toefl-uploads'>Copy of TOEFL score</label>
            </div>
            <div className='columns small-12'>
              <input type='checkbox' id='cv-uploads' checked={uploads.resume} onChange={this.handleCheckboxChange.bind(this, 'resume')} />
              <label htmlFor='cv-uploads'>Resume / CV</label>
            </div>
            <div className='columns small-12'>
              <input type='checkbox' id='passport-uploads' checked={uploads.passport} onChange={this.handleCheckboxChange.bind(this, 'passport')} />
              <label htmlFor='passport-uploads'>Passport</label>
            </div>
            <div className='columns small-12'>
              <input type='checkbox' id='essay-uploads' checked={uploads.essay} onChange={this.handleCheckboxChange.bind(this, 'essay')} />
              <label htmlFor='essay-uploads'>Essay</label>
            </div>
            <div className='columns small-12'>
              <input type='checkbox' id='other-uploads' checked={uploads.others} onChange={this.handleCheckboxChange.bind(this, 'others')} />
              <label htmlFor='other-uploads'>Others</label>
            </div>
          </div>
        </div>
        <div className='btn-submit-cont mt-10'>
          <span className='reset-filter-btn' onClick={this.handleResetFilterClick}>Reset this filter</span>
          <button type='submit' disabled={isLoading} className='btn-submit' onSubmit={this.handleSubmit}>{isLoading ? 'Saving...' : 'Save'}</button>
        </div>
      </form>
    )
  }
}

const mapStateToProps = state => ({
  uploads: state.newsfeed.audienceTargeting.uploads,
  salesPostId: state.newsfeed.salesPostId,
  isLoading: state.newsfeed.loader.isLoading,
  resetFilter: state.newsfeed.audienceTargeting.resetFilter,
});

const mapDispatchToProps = dispatch => ({
  setRecommendationFilter: (tabName, values) => { dispatch(setRecommendationFilter(tabName, values)) },
  setRecommendationFilterUploads: (values) => { dispatch({ type: 'SET_RECOMMENDATION_FILTER_UPLOADS', payload: values }) },
  resetRecommendationFilter: (tabName) => { dispatch(resetRecommendationFilter(tabName)) },
  setResetTargetingFilter: (value) => { dispatch({ type: 'SET_TARGETING_RESET_FILTER', payload: value }) },
});

export default connect(mapStateToProps, mapDispatchToProps)(Uploads);
