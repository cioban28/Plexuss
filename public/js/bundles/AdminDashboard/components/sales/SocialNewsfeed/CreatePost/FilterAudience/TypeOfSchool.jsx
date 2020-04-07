import React, { Component } from 'react';
import { connect } from 'react-redux';
import RecommendationMeter from './RecommendationMeter.jsx';
import { SelectedFilter } from './SelectedFilter.jsx';
import { setRecommendationFilter, resetRecommendationFilter } from '../../../../../actions/newsfeedActions';
import objectToFormData from 'object-to-formdata';


const tabName = 'typeofschool';
class TypeOfSchool extends Component {
  constructor(props) {
    super(props);

    const state = (this.props.filterChanges.tabName === tabName && this.props.filterChanges.state) || this.props.typeOfSchool;

    this.state = { ...state };

    this.handleSubmit = this.handleSubmit.bind(this); 
    this.handleResetFilterClick = this.handleResetFilterClick.bind(this);
  }

  componentWillReceiveProps(nextProps) {
    if(nextProps.resetFilter) {
      this.props.setResetTargetingFilter(false);
      this.setState({ ...nextProps.typeOfSchool });
    }
  }

  componentWillUnmount() {
    if(!_.isEqual(this.state, this.props.typeOfSchool)) {
      const filter = {};
      filter.formData = this.buildFormData();
      filter.state = { ...this.state };
      filter.tabName = tabName;
      this.props.setFilterCurrentChanges(filter);
    }
  }

  handleSchoolTypeChange(typeOfSchool) {
    this.setState({ typeOfSchool: typeOfSchool });
  }

  buildFormData() {
    const { typeOfSchool } = this.state;
    const values = {
      both_typeofschool: typeOfSchool === 'Both',
      online_only_typeofschool: typeOfSchool === 'Online Only',
      campus_only_typeofschool: typeOfSchool === 'Campus Only',
      sales_pid: this.props.salesPostId,
    };
    return objectToFormData(values);
  }
  
  handleSubmit(e) {
    e.preventDefault();

    this.props.setRecommendationFilterTypeOfSchool({ ...this.state });
    this.props.setRecommendationFilter(tabName, this.buildFormData());
  }

  handleResetFilterClick() {
    this.props.resetRecommendationFilter(tabName);
  }

  render() {
    const { typeOfSchool } = this.state;
    const { isLoading } = this.props;

    return (
      <form onSubmit={this.handleSubmit}>
        <div className='selected-filters-list'>
          <span className='filter-list-title'>Type of School: </span>
          <SelectedFilter
            filter='typeOfSchool'
            filterName={typeOfSchool}
            noRemoveBtn={true}
          />
        </div>
        <RecommendationMeter />
        <div className='filtering-cont'>
          <div className='row'>
            <div className='column small-12 large-6'>
              <input type='radio' id='both-type-of-school' name='type-of-school-filter' checked={typeOfSchool === 'Both'} onChange={this.handleSchoolTypeChange.bind(this, 'Both')} />
              <label htmlFor='both-type-of-school'>Both</label>
              <input type='radio' id='online-only-type-of-school' name='type-of-school-filter' checked={typeOfSchool === 'Online Only'} onChange={this.handleSchoolTypeChange.bind(this, 'Online Only')} />
              <label htmlFor='online-only-type-of-school'>Online Only</label>
              <input type='radio' id='campus-only-type-of-school' name='type-of-school-filter' checked={typeOfSchool === 'Campus Only'} onChange={this.handleSchoolTypeChange.bind(this, 'Campus Only')} />
              <label htmlFor='campus-only-type-of-school'>Campus Only</label>
            </div>
            <div className='column small-12 large-6'>
              <p className='description-para'>By default, we will recommend students who are interested in both online and on-campus education. If you'd like to limit your recommendations to only online or on-campus, select one of these options.</p>
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
  typeOfSchool: state.newsfeed.audienceTargeting.typeOfSchool,
  salesPostId: state.newsfeed.salesPostId,
  isLoading: state.newsfeed.loader.isLoading,
  resetFilter: state.newsfeed.audienceTargeting.resetFilter,
});

const mapDispatchToProps = dispatch => ({
  setRecommendationFilter: (tabName, values) => { dispatch(setRecommendationFilter(tabName, values)) },
  setRecommendationFilterTypeOfSchool: (values) => { dispatch({ type: 'SET_RECOMMENDATION_FILTER_TYPEOFSCHOOL', payload: values }) },
  resetRecommendationFilter: (tabName) => { dispatch(resetRecommendationFilter(tabName)) },
  setResetTargetingFilter: (value) => { dispatch({ type: 'SET_TARGETING_RESET_FILTER', payload: value }) },
});

export default connect(mapStateToProps, mapDispatchToProps)(TypeOfSchool);
