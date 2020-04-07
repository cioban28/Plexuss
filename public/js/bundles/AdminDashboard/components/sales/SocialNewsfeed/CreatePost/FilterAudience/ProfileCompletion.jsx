import React, { Component } from 'react';
import { connect } from 'react-redux';
import { SelectedFilter } from './SelectedFilter.jsx';
import RecommendationMeter from './RecommendationMeter.jsx';
import { setRecommendationFilter, resetRecommendationFilter } from '../../../../../actions/newsfeedActions';
import objectToFormData from 'object-to-formdata';


const tabName = 'profileCompletion';
class ProfileCompletion extends Component {
  constructor(props) {
    super(props);

    const state = (this.props.filterChanges.tabName === tabName && this.props.filterChanges.state) || this.props.profileCompletion;

    this.state = { ...state };

    this.profileCompletionPerc = [];
    for(let i=10; i<=100; i+=10) {
      this.profileCompletionPerc.push(`${i}`);
    }

    this.handleProfileCompletionChange = this.handleProfileCompletionChange.bind(this);
    this.handleRemoveFilter = this.handleRemoveFilter.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
    this.handleResetFilterClick = this.handleResetFilterClick.bind(this);
  }

  componentWillUnmount() {
    if(!_.isEqual(this.state, this.props.profileCompletion)) {
      const filter = {};
      filter.formData = this.buildFormData();
      filter.state = { ...this.state };
      filter.tabName = tabName;
      this.props.setFilterCurrentChanges(filter);
    }
  }

  componentWillReceiveProps(nextProps) {
    if(nextProps.resetFilter) {
      this.props.setResetTargetingFilter(false);
      this.setState({ ...nextProps.profileCompletion });
    }
  }

  handleProfileCompletionChange(e) {
    this.setState({ profileCompletion: e.target.value });
  }

  handleRemoveFilter(filer, filterName) {
    this.setState({ profileCompletion: '' });
  }

  buildFormData() {
    const values = {
      profileCompletion: this.state.profileCompletion,
      sales_pid: this.props.salesPostId,
    }
    return objectToFormData(values);
  }

  handleSubmit(e) {
    e.preventDefault();
    
    this.props.setRecommendationFilterProfileCompletion(this.state);
    this.props.setRecommendationFilter(tabName, this.buildFormData());
  }

  handleResetFilterClick() {
    this.props.resetRecommendationFilter(tabName);
  }

  render() {
    const { profileCompletion } = this.state;
    const { isLoading } = this.props;

    return (
      <form onSubmit={this.handleSubmit}>
				<div className='selected-filters-list'>
          <span className='filter-list-title'>Profile Completion: </span>
					{
						profileCompletion !== '' && <SelectedFilter
							filter='profileCompletion'
							filterName={`${profileCompletion}%`}
							handleRemoveFilter={this.handleRemoveFilter}
						/>
					}
        </div>
        <RecommendationMeter />
        <div className='filtering-cont'>
					<div className='row'>
            <div className='columns small-12 large-6'>
              <div className='row'>
                <div className='columns small-12 medium-9'>
                  <label htmlFor='profile-completion-filter'>Profile Completion:</label>
                  <select value={profileCompletion} onChange={this.handleProfileCompletionChange}>
                    <option defaultValue>Select...</option>
                    { this.profileCompletionPerc.map(pc => <option key={pc} value={pc}>{`${pc}%`}</option>) }
                  </select>
                </div>
              </div>
            </div>
            <div className='columns small-12 large-6'>
              <p className='description-para'>Select the minimum Profile Completion percentage that a student must reach to be considered a viable candidate for recruitment.</p>
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
  profileCompletion: state.newsfeed.audienceTargeting.profileCompletion,
  salesPostId: state.newsfeed.salesPostId,
  isLoading: state.newsfeed.loader.isLoading,
  resetFilter: state.newsfeed.audienceTargeting.resetFilter,
});

const mapDispatchToProps = dispatch => ({
  setRecommendationFilter: (tabName, values) => { dispatch(setRecommendationFilter(tabName, values)) },
  setRecommendationFilterProfileCompletion: (values) => { dispatch({ type: 'SET_RECOMMENDATION_FILTER_PROFILE_COMPLETION', payload: values }) },
  resetRecommendationFilter: (tabName) => { dispatch(resetRecommendationFilter(tabName)) },
  setResetTargetingFilter: (value) => { dispatch({ type: 'SET_TARGETING_RESET_FILTER', payload: value }) },
})

export default connect(mapStateToProps, mapDispatchToProps)(ProfileCompletion);
