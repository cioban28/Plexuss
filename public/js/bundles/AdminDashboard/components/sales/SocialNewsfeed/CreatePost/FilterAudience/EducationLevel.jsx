import React, { Component } from 'react';
import { connect } from 'react-redux';
import { SelectedFilter } from './SelectedFilter.jsx';
import RecommendationMeter from './RecommendationMeter.jsx';
import { setRecommendationFilter, resetRecommendationFilter } from '../../../../../actions/newsfeedActions';
import objectToFormData from 'object-to-formdata';
import _ from 'lodash';


const tabName = 'educationLevel';
class EducationLevel extends Component {
  constructor(props) {
    super(props);

    const state = (this.props.filterChanges.tabName === tabName && this.props.filterChanges.state) || this.props.educationLevel;

    this.state = { ...state };

    this.handleSubmit = this.handleSubmit.bind(this);
    this.handleResetFilterClick = this.handleResetFilterClick.bind(this);
  }

  componentWillReceiveProps(nextProps) {
    if(nextProps.resetFilter) {
      this.props.setResetTargetingFilter(false);
      this.setState({ ...nextProps.educationLevel });
    }
  }

  componentWillUnmount() {
    if(!_.isEqual(this.state, this.props.educationLevel)) {
      const filter = {};
      filter.formData = this.buildFormData();
      filter.state = { ...this.state };
      filter.tabName = tabName;
      this.props.setFilterCurrentChanges(filter);
    }
  }

  handleCheckboxChange(propertyName) {
    if(propertyName === 'highSchool') {
      this.setState(prevState => ({ highSchool: !prevState.highSchool }));
    } else {
      this.setState(prevState => ({ college: !prevState.college }));
    }
  }

  buildFormData() {
    const { highSchool, college } = this.state;
    const values = {
      hsUsers_filter: highSchool,
      collegeUsers_filter: college,
      sales_pid: this.props.salesPostId,
    };
    return objectToFormData(values);
  }

  handleSubmit(e) {
    e.preventDefault();

    this.props.setRecommendationFilterEducationLevel({ ...this.state });
    this.props.setRecommendationFilter(tabName, this.buildFormData());
  }

  handleResetFilterClick() {
    this.props.resetRecommendationFilter(tabName);
  }

  render() {
    const { highSchool, college } = this.state;
    const { isLoading } = this.props;

    return (
      <form onSubmit={this.handleSubmit}>
        <div className='selected-filters-list'>
          <span className='filter-list-title'>Education Level: </span>
          {
            highSchool && <SelectedFilter
              filter='highSchool'
              filterName='High School'
              handleRemoveFilter={this.handleCheckboxChange.bind(this)}
            />
          }
          {
            college && <SelectedFilter
              filter='college'
              filterName='College'
              handleRemoveFilter={this.handleCheckboxChange.bind(this)}
            />
          }
        </div>
        <RecommendationMeter />
        <div className='filtering-cont'>
          <div className='row'>
            <div className='columns small-12 large-6'>
              <div className='row'>
                <div className='columns small-12'>
                  <input type='checkbox' id='high-school-edu' checked={highSchool} onChange={this.handleCheckboxChange.bind(this, 'highSchool')} />
                  <label htmlFor='high-school-edu'>High school</label>
                </div>
              </div>
              <div className='row'>
              <div className='columns small-12'>
                  <input type='checkbox' id='college-edu' checked={college} onChange={this.handleCheckboxChange.bind(this, 'college')} />
                  <label htmlFor='college-edu'>College</label>
                </div>
              </div>
            </div>
            <div className='columns small-12 large-6'>
              <p className='description-para'>By default, we show you students at all education levels, but if you are interested in students who have completed some college, you can select "College" here.</p>
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
  educationLevel: state.newsfeed.audienceTargeting.educationLevel,
  salesPostId: state.newsfeed.salesPostId,
  isLoading: state.newsfeed.loader.isLoading,
  resetFilter: state.newsfeed.audienceTargeting.resetFilter,
});

const mapDispatchToProps = dispatch => ({
  setRecommendationFilter: (tabName, values) => { dispatch(setRecommendationFilter(tabName, values)) },
  setRecommendationFilterEducationLevel: (values) => { dispatch({ type: 'SET_RECOMMENDATION_FILTER_EDUCATION_LEVEL', payload: values }) },
  resetRecommendationFilter: (tabName) => { dispatch(resetRecommendationFilter(tabName)) },
  setResetTargetingFilter: (value) => { dispatch({ type: 'SET_TARGETING_RESET_FILTER', payload: value }) },
})

export default connect(mapStateToProps, mapDispatchToProps)(EducationLevel);
