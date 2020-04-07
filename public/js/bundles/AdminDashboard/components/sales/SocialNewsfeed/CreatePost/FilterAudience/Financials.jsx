import React, { Component } from 'react';
import { connect } from 'react-redux';
import RecommendationMeter from './RecommendationMeter.jsx';
import { SelectedFilter } from './SelectedFilter.jsx';
import { setRecommendationFilter, resetRecommendationFilter } from '../../../../../actions/newsfeedActions';
import objectToFormData from 'object-to-formdata';


const tabName = 'financial';
class Financials extends Component {
  constructor(props) {
    super(props);

    const state = (this.props.filterChanges.tabName === tabName && this.props.filterChanges.state) || this.props.financials;

    this.state = { ...state };

    this.handleFinancialsChange = this.handleFinancialsChange.bind(this);
    this.handleCheckboxChange = this.handleCheckboxChange.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
    this.handleResetFilterClick = this.handleResetFilterClick.bind(this);
  }

  componentWillReceiveProps(nextProps) {
    if(nextProps.resetFilter) {
      this.props.setResetTargetingFilter(false);
      this.setState({ ...nextProps.financials });
    }
  }

  componentWillUnmount() {
    if(!_.isEqual(this.state, this.props.financials)) {
      const filter = {};
      filter.formData = this.buildFormData();
      filter.state = { ...this.state };
      filter.tabName = tabName;
      this.props.setFilterCurrentChanges(filter);
    }
  }

  handleFinancialsChange(e) {
    const selectedOption = e.target.value;
    const selectedFinancialIndex = this.state.financials.findIndex(financial => financial === selectedOption);
    this.setState({ selectedFinancialRange: selectedOption, selectedFinancialIndex });
  }

  handleCheckboxChange() {
    this.setState((prevState) => ({ studentNotInterestedInAid: !prevState.studentNotInterestedInAid }));
  }

  buildFormData() {
    const { studentNotInterestedInAid, selectedFinancialIndex, selectedFinancialRange } = this.state;
    const values = {
      financial: this.state.financials.slice(selectedFinancialIndex, this.state.financials.length),
      interested_in_aid: studentNotInterestedInAid,
      selectedFinancialIndex,
      selectedFinancialRange,
      sales_pid: this.props.salesPostId,
    };
    return objectToFormData(values);
  }

  handleSubmit(e) {
    e.preventDefault();

    this.props.setRecommendationFilterFinancials({ ...this.state });
    this.props.setRecommendationFilter(tabName, this.buildFormData());
  }

  handleResetFilterClick() {
    this.props.resetRecommendationFilter(tabName);
  }

  render() {
    const { financials, studentNotInterestedInAid, selectedFinancialRange, selectedFinancialIndex } = this.state;
    const { isLoading } = this.props;

    const renderFiancialValue = (financialRange) => {
      if(this.state.financials.length - 1 !== selectedFinancialIndex) {
        return financialRange.split(' - ').map((financial, index) => {
          return financialRange !== '50,000' ? financial.replace(/^/, '$') : `$${financial}+`;
        }).join(' - ');
      } else {
        return `$${financialRange}+`;
      }
    }

    return (
      <form onSubmit={this.handleSubmit}>
        <div className='selected-filters-list'>
          <span className='filter-list-title'>Financials: </span>
          {
            !!financials && financials.slice(selectedFinancialIndex, financials.length).map((term, index) => <SelectedFilter
              filter='term'
              key={`${term}-${index}`}
              filterName={renderFiancialValue(term)}
              noRemoveBtn={true}
            />)
          }
        </div>
        <RecommendationMeter />
        <div className='filtering-cont'>
          <div className='row'>
            <div className='column small-12 large-6'>
              <label>Select a minimum range.</label>
              <select value={selectedFinancialRange} onChange={this.handleFinancialsChange}>
              {
                !!financials && financials.map((finacialRange, index) => <option key={`${finacialRange}-${index}`} value={finacialRange}>{renderFiancialValue(finacialRange)}</option>)
              }
              </select>
              <div>
                <input id='interested-in-aid' type='checkbox' checked={studentNotInterestedInAid} onChange={this.handleCheckboxChange} />
                <label htmlFor='interested-in-aid'>Filter by students who are NOT interested in financial aid, grants, and scholarships</label>
              </div>
            </div>
            <div className='column small-12 large-6'>
             <p className='description-para'>If you would like to target students that are able to contribute financially to their college education, select the minimum amount that they might expect to contribute. These amounts are from the same list we give students to choose from on their profiles.</p>
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
  financials: state.newsfeed.audienceTargeting.financials,
  salesPostId: state.newsfeed.salesPostId,
  isLoading: state.newsfeed.loader.isLoading,
  resetFilter: state.newsfeed.audienceTargeting.resetFilter,
});

const mapDispatchToProps = dispatch => ({
  setRecommendationFilter: (tabName, values) => { dispatch(setRecommendationFilter(tabName, values)) },
  setRecommendationFilterFinancials: (values) => { dispatch({ type: 'SET_RECOMMENDATION_FILTER_FINANCIAL', payload: values }) },
  resetRecommendationFilter: (tabName) => { dispatch(resetRecommendationFilter(tabName)) },
  setResetTargetingFilter: (value) => { dispatch({ type: 'SET_TARGETING_RESET_FILTER', payload: value }) },
});

export default connect(mapStateToProps, mapDispatchToProps)(Financials);
