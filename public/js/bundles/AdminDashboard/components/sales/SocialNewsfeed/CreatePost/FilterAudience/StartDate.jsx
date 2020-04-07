import React, { Component } from 'react';
import { connect } from 'react-redux';
import RecommendationMeter from './RecommendationMeter.jsx';
import { SelectedFilter } from './SelectedFilter.jsx';
import { setRecommendationFilter, resetRecommendationFilter } from '../../../../../actions/newsfeedActions';
import objectToFormData from 'object-to-formdata';
import _ from 'lodash';


const tabName = 'startDateTerm';
class StartDate extends Component {
  constructor(props) {
    super(props);

    this.termStartDateYears = [];
    for(let i=new Date().getFullYear(); i<new Date().getFullYear()+7; i++) {
      this.termStartDateYears.push(`Fall ${i}`);
      this.termStartDateYears.push(`Spring ${i}`);
    }

    const state = (this.props.filterChanges.tabName === tabName && this.props.filterChanges.state) || this.props.startDate;

    this.state = { ...state };

    this.handleTermYearChange = this.handleTermYearChange.bind(this);
    this.handleRemoveFilter = this.handleRemoveFilter.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this); 
    this.handleResetFilterClick = this.handleResetFilterClick.bind(this);
  }

  componentWillReceiveProps(nextProps) {
    if(nextProps.resetFilter) {
      this.props.setResetTargetingFilter(false);
      this.setState({ ...nextProps.startDate });
    }
  }

  componentWillUnmount() {
    if(!_.isEqual(this.state, this.props.startDate)) {
      const filter = {};
      filter.formData = this.buildFormData();
      filter.state = { ...this.state };
      filter.tabName = tabName;
      this.props.setFilterCurrentChanges(filter);
    }
  }

  handleTermYearChange(e) {
    const selectedTerms = [...this.state.selectedTerms];
    selectedTerms.push(e.target.value);
    this.setState({ selectedTerms });
  }

  handleRemoveFilter(filter, filterName) {
    const { selectedTerms } = this.state;
    const itemIndex = selectedTerms.findIndex(term => term === filterName);
    if(itemIndex === -1) return;
    selectedTerms.splice(itemIndex, 1);
    this.setState({ selectedTerms });
  }

  buildFormData() {
    const values = {
      startDateTerm: this.state.selectedTerms,
      sales_pid: this.props.salesPostId,
    }

    return objectToFormData(values);
  }

  handleSubmit(e) {
    e.preventDefault();

    this.props.setRecommendationFilterStartDateTerm({ ...this.state });
    this.props.setRecommendationFilter(tabName, this.buildFormData());
  }

  handleResetFilterClick() {
    this.props.resetRecommendationFilter(tabName);
  }

  render() {
    const { selectedTerms } = this.state;
    const { isLoading } = this.props;

    return (
      <form onSubmit={this.handleSubmit}>
        <div className='selected-filters-list'>
          <span className='filter-list-title'>StartDateTerm: </span>
          {
            selectedTerms.map((term, index) => <SelectedFilter
              filter='term'
              key={`${term}-${index}`}
              filterName={term}
              handleRemoveFilter={this.handleRemoveFilter}
            />)
          }
        </div>
        <RecommendationMeter />
        <div className='filtering-cont'>
          <div className='row'>
            <div className='column small-12 large-6'>
              <label htmlFor='termStartDateFilter'>You can select multiple options, just click to add.</label>
              <select id='termStartDateFilter' onChange={this.handleTermYearChange}>
                <option>Select...</option>
                {
                  this.termStartDateYears.map((term, index) => <option key={`${term}-${index}`} value={term}>{term}</option>)
                }
              </select>
            </div>
            <div className='column small-12 large-6'>
              <p className='description-para'>Each student on Plexuss tell us when they intend to start school. Select the term(s) you want students you're targeting to apply for.</p>
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
  startDate: state.newsfeed.audienceTargeting.startDate,
  salesPostId: state.newsfeed.salesPostId,
  isLoading: state.newsfeed.loader.isLoading,
  resetFilter: state.newsfeed.audienceTargeting.resetFilter,
});

const mapDispatchToProps = dispatch => ({
  setRecommendationFilter: (tabName, values) => { dispatch(setRecommendationFilter(tabName, values)) },
  setRecommendationFilterStartDateTerm: (values) => { dispatch({ type: 'SET_RECOMMENDATION_FILTER_START_DATE_TERM', payload: values }) },
  resetRecommendationFilter: (tabName) => { dispatch(resetRecommendationFilter(tabName)) },
  setResetTargetingFilter: (value) => { dispatch({ type: 'SET_TARGETING_RESET_FILTER', payload: value }) },
});

export default connect(mapStateToProps, mapDispatchToProps)(StartDate);
