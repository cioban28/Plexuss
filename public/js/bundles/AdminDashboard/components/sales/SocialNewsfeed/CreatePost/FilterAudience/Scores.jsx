import React, { Component } from 'react';
import { connect } from 'react-redux';
import RecommendationMeter from './RecommendationMeter.jsx';
import { SelectedFilter } from './SelectedFilter.jsx';
import { setRecommendationFilter, resetRecommendationFilter } from '../../../../../actions/newsfeedActions';
import objectToFormData from 'object-to-formdata';


const tabName = 'scores';
class Scores extends Component {
  constructor(props) {
    super(props);

    const state = (this.props.filterChanges.tabName === tabName && this.props.filterChanges.state) || this.props.scores;

    this.state = { ...state };

    this.handleInputValidate = this.handleInputValidate.bind(this);
    this.handleRemoveFilter = this.handleRemoveFilter.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this); 
    this.handleResetFilterClick = this.handleResetFilterClick.bind(this);
  }

  componentWillReceiveProps(nextProps) {
    if(nextProps.resetFilter) {
      this.props.setResetTargetingFilter(false);
      this.setState({ ...nextProps.scores });
    }
  }

  componentWillUnmount() {
    if(!_.isEqual(this.state, this.props.scores)) {
      const filter = {};
      filter.formData = this.buildFormData();
      filter.state = { ...this.state };
      filter.tabName = tabName;
      this.props.setFilterCurrentChanges(filter);
    }
  }

  handleInputValidate(inputFieldName, e) {
    const inputValue = e.target.value.toString().trim() === '' || isNaN(e.target.value) ? e.target.value : parseFloat(e.target.value);
    const SAT = { ...this.state.SAT };
    const ACT = { ...this.state.ACT };
    const TOEFL = { ...this.state.TOEFL };
    const IELTS = { ...this.state.IELTS };

    switch(inputFieldName) {
      case 'minGPA':
        let GPA = { ...this.state.GPA };
        if(inputValue >= 0 && inputValue <= 4.0) {
          e.currentTarget.removeAttribute('data-invalid', '');
        } else {
          e.currentTarget.setAttribute('data-invalid', '');
        }
        GPA.min = inputValue;
        this.setState({ GPA });
        break;

      case 'maxGPA':
        GPA = { ...this.state.GPA };
        if(inputValue >= 0 && inputValue <= 4.0) {
          e.currentTarget.removeAttribute('data-invalid', '');
        } else {
          e.currentTarget.setAttribute('data-invalid', '');
        }
        GPA.max = inputValue;
        this.setState({ GPA });
        break;

      case 'minSAT':
        let SAT = { ...this.state.SAT };
        if(inputValue >= 600 && inputValue <= 2400) {
          e.currentTarget.removeAttribute('data-invalid', '');
        } else {
          e.currentTarget.setAttribute('data-invalid', '');
        }
        SAT.min = inputValue;
        this.setState({ SAT });
        break;

      case 'maxSAT':
        SAT = { ...this.state.SAT };
        if(inputValue >= 600 && inputValue <= 2400) {
          e.currentTarget.removeAttribute('data-invalid', '');
        } else {
          e.currentTarget.setAttribute('data-invalid', '');
        }
        SAT.max = inputValue;
        this.setState({ SAT });
        break;

      case 'minACT':
        let ACT = { ...this.state.ACT };
        if(inputValue >= 0 && inputValue <= 36) {
          e.currentTarget.removeAttribute('data-invalid', '');
        } else {
          e.currentTarget.setAttribute('data-invalid', '');
        }
        ACT.min = inputValue;
        this.setState({ ACT });
        break;

      case 'maxACT':
        ACT = { ...this.state.ACT };
        if(inputValue >= 0 && inputValue <= 36) {
          e.currentTarget.removeAttribute('data-invalid', '');
        } else {
          e.currentTarget.setAttribute('data-invalid', '');
        }
        ACT.max = inputValue;
        this.setState({ ACT });
        break;

      case 'minTOEFL':
        let TOEFL = { ...this.state.TOEFL };
        if(inputValue >= 0 && inputValue <= 120) {
          e.currentTarget.removeAttribute('data-invalid', '');
        } else {
          e.currentTarget.setAttribute('data-invalid', '');
        }
        TOEFL.min = inputValue;
        this.setState({ TOEFL });
        break;

      case 'maxTOEFL':
        TOEFL = { ...this.state.TOEFL };
        if(inputValue >= 0 && inputValue <= 120) {
          e.currentTarget.removeAttribute('data-invalid', '');
        } else {
          e.currentTarget.setAttribute('data-invalid', '');
        }
        TOEFL.max = inputValue;
        this.setState({ TOEFL });
        break;

      case 'minIELTS':
        let IELTS = { ...this.state.IELTS };
        if(inputValue >= 0 && inputValue <= 9) {
          e.currentTarget.removeAttribute('data-invalid', '');
        } else {
          e.currentTarget.setAttribute('data-invalid', '');
        }
        IELTS.min = inputValue;
        this.setState({ IELTS });
        break;

      case 'maxIELTS':
        IELTS = { ...this.state.IELTS };
        if(inputValue >= 0 && inputValue <= 9) {
          e.currentTarget.removeAttribute('data-invalid', '');
        } else {
          e.currentTarget.setAttribute('data-invalid', '');
        }
        IELTS.max = inputValue;
        this.setState({ IELTS });
        break;

      default:
        break;
    }
  }

  handleRemoveFilter(filter, filterName) {
    switch(filter) {
      case 'GPA':
        this.setState({ GPA: {min: '', max: ''} });
        break;
      case 'SAT':
        this.setState({ SAT: {min: '', max: ''} });
        break;
      case 'ACT':
        this.setState({ ACT: {min: '', max: ''} });
        break;
      case 'TOEFL':
        this.setState({ TOEFL: {min: '', max: ''} });
        break;
      case 'IELTS':
        this.setState({ IELTS: {min: '', max: ''} });
        break;
    }
  }

  buildFormData() {
    const { GPA, SAT, ACT, TOEFL, IELTS } = this.state;
    const values = {
      gpaMin_filter: GPA.min,
      gpaMax_filter: GPA.max,
      satMin_filter: SAT.min,
      satMax_filter: SAT.max,
      actMin_filter: ACT.min,
      actMax_filter: ACT.max,
      toeflMin_filter: TOEFL.min,
      toeflMax_filter: TOEFL.max,
      ieltsMin_filter: IELTS.min,
      ieltsMax_filter: IELTS.max,
      sales_pid: this.props.salesPostId,
    }
    return objectToFormData(values);
  }

  handleSubmit(e) {
    e.preventDefault();

    this.props.setRecommendationFilterScore({...this.state});
    this.props.setRecommendationFilter(tabName, this.buildFormData());
  }

  handleResetFilterClick() {
    this.props.resetRecommendationFilter(tabName);
  }

  render() {
    const { GPA, SAT, ACT, TOEFL, IELTS } = this.state;
    const { isLoading } = this.props;

    const renderFilterText = obj => {
      if((obj.min === 0 || (obj.min && !!obj.min.toString().trim())) && (obj.max !== 0 && !obj.max)) {
        return `${obj.min} + `;
      } else if((obj.max === 0 || (obj.max && !!obj.max.toString().trim())) && (obj.min !== 0 && !obj.min)) {
        return ` - ${obj.max} `;
      } else {
        return `${obj.min} - ${obj.max}`;
      }
    }

    return (
      <form onSubmit={this.handleSubmit}>
        <div className='selected-filters-list'>
          <span className='filter-list-title'>Scores: </span>
          {
            (!!GPA.min || !!GPA.max || GPA.min === 0 || GPA.max === 0) && <SelectedFilter
              filter='GPA'
              key='GPA'
              filterName={`Hs gpa: ${renderFilterText(GPA)}`}
              handleRemoveFilter={this.handleRemoveFilter}
            />
          }
          {
            (!!SAT.min || !!SAT.max || SAT.min === 0 || SAT.max === 0) && <SelectedFilter
              filter='SAT'
              key='SAT'
              filterName={`SAT: ${renderFilterText(SAT)}`}
              handleRemoveFilter={this.handleRemoveFilter}
            />
          }
          {
            (!!ACT.min || !!ACT.max || ACT.min === 0 || ACT.max === 0) && <SelectedFilter
              filter='ACT'
              key='ACT'
              filterName={`ACT: ${renderFilterText(ACT)}`}
              handleRemoveFilter={this.handleRemoveFilter}
            />
          }
          {
            (!!TOEFL.min || !!TOEFL.max || TOEFL.min === 0 || TOEFL.max === 0) && <SelectedFilter
              filter='TOEFL'
              key='TOEFL'
              filterName={`TOEFL: ${renderFilterText(TOEFL)}`}
              handleRemoveFilter={this.handleRemoveFilter}
            />
          }
          {
            (!!IELTS.min || !!IELTS.max || IELTS.min === 0 || IELTS.max === 0) && <SelectedFilter
              filter='IELTS'
              key='IELTS'
              filterName={`IELTS: ${renderFilterText(IELTS)}`}
              handleRemoveFilter={this.handleRemoveFilter}
            />
          }
        </div>
        <RecommendationMeter />
        <div className='filtering-cont'>
          <div className='row'>
            <div className='columns small-12 large-6'>
              <p className='description-para mb-10'>We will recommend students to you within the score ranges you set here.</p>
              <div className='row'>
                <div className='columns small-3 medium-2 scores-desc'>
                  <label htmlFor='none' className='score-input-label'>GPA:</label>
                </div>
                <div className='columns small-3 medium-3 large-4'>
                  <input type='number' className='score-input error' placeholder='Min: 0' value={GPA.min} onChange={this.handleInputValidate.bind(this, 'minGPA')} />
                  <small className='error-text'>Incorrect values. Make sure Min GPA isn't greater than Max GPA.</small>
                </div>
                <div className='columns small-3 medium-1 text-center scores-desc'>to</div>
                <div className='columns small-3 medium-4 large-4 end'>
                  <input type='number' className='score-input' placeholder='Max: 4.0' value={GPA.max} onChange={this.handleInputValidate.bind(this, 'maxGPA')} />
                  <small className='error-text'>Incorrect values. Ex: 2.5 - 4.0</small>
                </div>
              </div>
              <div className='row'>
                <div className='columns small-3 medium-2 scores-desc'>
                  <label htmlFor='none' className='score-input-label'>SAT:</label>
                </div>
                <div className='columns small-3 medium-3 large-4'>
                  <input type='number' className='score-input' placeholder='Min: 600' value={SAT.min} onChange={this.handleInputValidate.bind(this, 'minSAT')} />
                  <small className='error-text'>Incorrect values. Make sure Min SAT isn't greater than Max SAT.</small>
                </div>
                <div className='columns small-3 medium-1 text-center scores-desc'>to</div>
                <div className='columns small-3 medium-4 large-4 end'>
                  <input type='number' className='score-input' placeholder='Max: 2400' value={SAT.max} onChange={this.handleInputValidate.bind(this, 'maxSAT')} />
                  <small className='error-text'>Incorrect values. Ex: 1200 - 2200</small>
                </div>
              </div>
              <div className='row'>
                <div className='columns small-3 medium-2 scores-desc'>
                  <label htmlFor='none' className='score-input-label'>ACT:</label>
                </div>
                <div className='columns small-3 medium-3 large-4'>
                  <input type='number' className='score-input' placeholder='Min: 0' value={ACT.min} onChange={this.handleInputValidate.bind(this, 'minACT')} />
                  <small className='error-text'>Incorrect values. Make sure Min ACT isn't greater than Max ACT.</small>
                </div>
                <div className='columns small-3 medium-1 text-center scores-desc'>to</div>
                <div className='columns small-3 medium-4 large-4 end'>
                  <input type='number' className='score-input' placeholder='Max: 36' value={ACT.max} onChange={this.handleInputValidate.bind(this, 'maxACT')} />
                  <small className='error-text'>Incorrect values. Ex: 20 - 32</small>
                </div>
              </div>
              <div className='row'>
                <div className='columns small-3 medium-2 scores-desc'>
                  <label htmlFor='none' className='score-input-label'>TOEFL:</label>
                </div>
                <div className='columns small-3 medium-3 large-4'>
                  <input type='number' className='score-input' placeholder='Min: 0' value={TOEFL.min} onChange={this.handleInputValidate.bind(this, 'minTOEFL')} />
                  <small className='error-text'>Incorrect values. Make sure Min TOEFL isn't greater than Max TOEFL.</small>
                </div>
                <div className='columns small-3 medium-1 text-center scores-desc'>to</div>
                <div className='columns small-3 medium-4 large-4 end'>
                  <input type='number' className='score-input' placeholder='Max: 120' value={TOEFL.max} onChange={this.handleInputValidate.bind(this, 'maxTOEFL')} />
                  <small className='error-text'>Incorrect values. Ex: 50 - 100</small>
                </div>
              </div>
              <div className='row'>
                <div className='columns small-3 medium-2 scores-desc'>
                  <label htmlFor='none' className='score-input-label'>IELTS:</label>
                </div>
                <div className='columns small-3 medium-3 large-4'>
                  <input type='number' className='score-input' placeholder='Min: 0' value={IELTS.min} onChange={this.handleInputValidate.bind(this, 'minIELTS')} />
                  <small className='error-text'>Incorrect values. Make sure Min IELTS isn't greater than Max IELTS.</small>
                </div>
                <div className='columns small-3 medium-1 text-center scores-desc'>to</div>
                <div className='columns small-3 medium-4 large-4 end'>
                  <input type='number' className='score-input' placeholder='Max: 9' value={IELTS.max} onChange={this.handleInputValidate.bind(this, 'maxIELTS')} />
                  <small className='error-text'>Incorrect values. Ex: 5 - 9</small>
                </div>
              </div>
            </div>
            <div className='columns small-12 large-6'></div>
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
  scores: state.newsfeed.audienceTargeting.scores,
  salesPostId: state.newsfeed.salesPostId,
  isLoading: state.newsfeed.loader.isLoading,
  resetFilter: state.newsfeed.audienceTargeting.resetFilter,
});

const mapDispatchToProps = dispatch => ({
  setRecommendationFilter: (tabName, values) => { dispatch(setRecommendationFilter(tabName, values)) },
  setRecommendationFilterScore: (values) => { dispatch({ type: 'SET_RECOMMENDATION_FILTER_SCORES', payload: values }) },
  resetRecommendationFilter: (tabName) => { dispatch(resetRecommendationFilter(tabName)) },
  setResetTargetingFilter: (value) => { dispatch({ type: 'SET_TARGETING_RESET_FILTER', payload: value }) },
});

export default connect(mapStateToProps, mapDispatchToProps)(Scores);
