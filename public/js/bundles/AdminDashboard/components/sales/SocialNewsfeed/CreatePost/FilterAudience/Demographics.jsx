import React, { Component } from 'react';
import { connect } from 'react-redux';
import { SelectedFilter } from './SelectedFilter.jsx';
import RecommendationMeter from './RecommendationMeter.jsx';
import { getAllEthnicities, getAllReligionsCustom } from '../../../../../actions/scholarshipsActions';
import { setRecommendationFilter, resetRecommendationFilter } from '../../../../../actions/newsfeedActions';
import objectToFormData from 'object-to-formdata';
import _ from 'lodash';


const tabName = 'demographic'
class Demographics extends Component {
  constructor(props) {
    super(props);

    const state = (this.props.filterChanges.tabName === tabName && this.props.filterChanges.state) || this.props.demographics;

    this.state = { ...state };

    this.handleEthnicitySelectionChange = this.handleEthnicitySelectionChange.bind(this);
    this.handleReligionSelectionChange = this.handleReligionSelectionChange.bind(this);
    this.handleRemoveFilter = this.handleRemoveFilter.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
    this.handleResetFilterClick = this.handleResetFilterClick.bind(this);
  }

  componentDidMount() {
    this.props.getAllEthnicities();
    this.props.getAlReligions();
  }

  componentWillReceiveProps(nextProps) {
    if(nextProps.resetFilter) {
      this.props.setResetTargetingFilter(false);
      this.setState({ ...nextProps.demographics });
    }
  }

  componentWillUnmount() {
    if(!_.isEqual(this.state, this.props.demographics)) {
      const filter = {};
      filter.formData = this.buildFormData();
      filter.state = { ...this.state };
      filter.tabName = tabName;
      this.props.setFilterCurrentChanges(filter);
    }
  }

  handleInputValidate(inputName, e) {
    const age = { ...this.state.age };
    const inputValue = e.target.value;
    
    if(inputValue >= 0 && inputValue <= 100) {
      e.currentTarget.removeAttribute('data-invalid', '');
    } else {
      e.currentTarget.setAttribute('data-invalid', '');
    }

    inputName === 'minAge' ? age.min = inputValue : age.max = inputValue;

    this.setState({ age });
  }

  handleGenderFilterChange(selectedFilter) {
    const genderFilter = { ...this.state.genderFilter };
    
    Object.keys(genderFilter).forEach(filter => {
      if(filter === selectedFilter) {
        genderFilter[filter] = !genderFilter[filter];
      } else {
        genderFilter[filter] = false;
      }
    });
    
    if(!this.state.shouldShowGenderSelectedFilter) {
      this.setState({ genderFilter, shouldShowGenderSelectedFilter: true }); 
    }
    this.setState({ genderFilter }); 
  }

  handleEthnicityFilterChange(selectedFilter) {
    const ethnicityFilter = { ...this.state.ethnicityFilter };
    
    Object.keys(ethnicityFilter).forEach(filter => {
      if(filter === selectedFilter) {
        ethnicityFilter[filter] = !ethnicityFilter[filter];
      } else {
        ethnicityFilter[filter] = false;
      }
    });

    this.setState({ ethnicityFilter });
  }

  handleReligionFilterChange(selectedFilter) {
    const religionFilter = { ...this.state.religionFilter };
    
    Object.keys(religionFilter).forEach(filter => {
      if(filter === selectedFilter) {
        religionFilter[filter] = !religionFilter[filter];
      } else {
        religionFilter[filter] = false;
      }
    });

    this.setState({ religionFilter });
  }

  handleEthnicitySelectionChange(e) {
    const selectedEthnicites = [...this.state.selectedEthnicites];
    selectedEthnicites.push(e.target.value);
    this.setState({ selectedEthnicites });
  }

  handleReligionSelectionChange(e) {
    const selectedReligions = [...this.state.selectedReligions];
    selectedReligions.push(e.target.value);
    this.setState({ selectedReligions });
  }

  buildFormData() {
    const { age, genderFilter, ethnicityFilter, religionFilter, selectedEthnicites, selectedReligions } = this.state;
    const values = {
      ageMax_filter: age.max,
      ageMin_filter: age.min,
      all_eth_filter: ethnicityFilter.all,
      all_gender_filter: genderFilter.all,
      all_rgs_filter: religionFilter.all,
      ethnicity: selectedEthnicites,
      exclude_eth_filter: ethnicityFilter.exclude,
      exclude_rgs_filter: religionFilter.exclude,
      female_only_filter: genderFilter.females_only,
      include_eth_filter: ethnicityFilter.include,
      include_rgs_filter: religionFilter.include,
      male_only_filter: genderFilter.males_only,
      religion: selectedReligions,
      sales_pid: this.props.salesPostId,
    };
    return objectToFormData(values);
  }

  handleSubmit(e) {
    e.preventDefault();

    this.props.setRecommendationFilterDemographics({ ...this.state });
    this.props.setRecommendationFilter(tabName, this.buildFormData());
  }

  handleRemoveFilter(filter, filterName) {
    switch(filter) {
      case 'age':
        this.setState({ age: { min: '', max: '' } });
        break;
      
      case 'gender': 
        this.handleGenderFilterChange('all');
        this.setState({ shouldShowGenderSelectedFilter: false });
        break;
      
      case 'ethnicity':
        let selectedEthnicites = [...this.state.selectedEthnicites];
        selectedEthnicites = selectedEthnicites.filter(ethnicity => ethnicity !== filterName);
        this.setState({ selectedEthnicites });
        break;

      case 'religion':
        let selectedReligions = [...this.state.selectedReligions];
        selectedReligions = selectedReligions.filter(religion => religion !== filterName);
        this.setState({ selectedReligions });
        break;
      
      default: 
        break;
    }
  }

  handleResetFilterClick() {
    this.props.resetRecommendationFilter(tabName);
  }

  render() {
    const { age, genderFilter, ethnicityFilter, religionFilter, selectedEthnicites, selectedReligions, shouldShowGenderSelectedFilter } = this.state;
    const { ethnicities, religions, isLoading } = this.props;

    const formatAgeFilterText = obj => {
      if((obj.min === 0 || (obj.min && !!obj.min.toString().trim())) && (obj.max !== 0 && !obj.max)) {
        return `Age: ${obj.min} + `;
      } else if((obj.max === 0 || (obj.max && !!obj.max.toString().trim())) && (obj.min !== 0 && !obj.min)) {
        return `Age: - ${obj.max} `;
      } else {
        return `Age: ${obj.min} - ${obj.max}`;
      }
    }

    const capitalize = str => str[0].toUpperCase() + str.slice(1);

    const formatGenderFilterText = () => (
      Object.keys(genderFilter).filter(key => genderFilter[key] === true).toString().split('_').map(word => capitalize(word)).join(' ')
    )

    return (
      <form onSubmit={this.handleSubmit}>
        <div className='selected-filters-list'>
          <span className='filter-list-title'>Demographics: </span>
          {
            (!!age.min || !!age.max || age.min === 0 || age.max === 0) && <SelectedFilter
              filter='age'
              key='age'
              filterName={formatAgeFilterText(age)}
              handleRemoveFilter={this.handleRemoveFilter}
            />
          }
          {
            shouldShowGenderSelectedFilter && <SelectedFilter
              filter='gender'
              filterName={`Gender: ${formatGenderFilterText()}`}
              handleRemoveFilter={this.handleRemoveFilter}
            />
          }
          {
            !!selectedEthnicites && !!selectedEthnicites.length && selectedEthnicites.map((ethnicity, index) => <SelectedFilter
              filter='ethnicity'
              key={`${ethnicity}-selected-filter-${index}`}
              filterName={ethnicity}
              include={ethnicityFilter.include}
              exclude={ethnicityFilter.exclude}
              handleRemoveFilter={this.handleRemoveFilter}
            />)
          }
          {
            !!selectedReligions && !!selectedReligions.length && selectedReligions.map((religion, index) => <SelectedFilter
              filter='religion'
              key={`${religion}-selected-filter-${index}`}
              filterName={religion}
              include={religionFilter.include}
              exclude={religionFilter.exclude}
              handleRemoveFilter={this.handleRemoveFilter}
            />)
          }
        </div>

        <RecommendationMeter />
        
        <div className='filtering-cont'>
          <div className='row'>
            <div className='columns small-12 large-6'>
              <div className='row'>
                <div className='columns small-3 medium-2 scores-desc'>
                  <label htmlFor='' className='score-input-label'>Age: </label>
                </div>
                <div className='columns small-3 medium-3'>
                  <input type='text' className='score-input' placeholder='Min' value={age.min} onChange={this.handleInputValidate.bind(this, 'minAge')} />
                  <small className='error-text'>Incorrect values. Make sure Min age isn't greater than Max age.</small>
                </div>
                <div className='columns small-3 medium-1 text-center scores-desc'>to</div>
                <div className='columns small-3 medium-3'>
                  <input type='text' className='score-input' placeholder='Max' value={age.max} onChange={this.handleInputValidate.bind(this, 'maxAge')} />
                  <small className='error-text'>Incorrect values. Ex: 16 - 30.</small>
                </div>
                <div></div>
              </div>
              <div className='row mb-10'>
                <div className='columns small-12'>
                  <h4 className='filter-heading'>Gender</h4>
                  <input id='all_gender_filter' type='radio' name='gender' checked={genderFilter.all} onChange={this.handleGenderFilterChange.bind(this, 'all')} />
                  <label htmlFor='all_gender_filter' className='filter-label'>All</label>
                  <input id='males_only_filter' type='radio' name='gender' checked={genderFilter.males_only} onChange={this.handleGenderFilterChange.bind(this, 'males_only')} />
                  <label htmlFor='males_only_filter' className='filter-label'>Males Only</label>
                  <input id='females_only_filter' type='radio' name='gender' checked={genderFilter.females_only}  onChange={this.handleGenderFilterChange.bind(this, 'females_only')} />
                  <label htmlFor='females_only_filter' className='filter-label'>Females Only</label>
                </div>
              </div>
              <div className='row mb-10'>
                <div className='columns small-12'>
                  <h4 className='filter-heading'>Ethnicity</h4>
                  <input id='all_ethnicities_filter' type='radio' name='ethnicity' checked={ethnicityFilter.all} onChange={this.handleEthnicityFilterChange.bind(this, 'all')} />
                  <label htmlFor='all_ethnicities_filter' className='filter-label'>All</label>
                  <input id='include_ethnicities_filter' type='radio' name='ethnicity' checked={ethnicityFilter.include} onChange={this.handleEthnicityFilterChange.bind(this, 'include')} />
                  <label htmlFor='include_ethnicities_filter' className='filter-label'>Include</label>
                  <input id='exclude_ethnicites_filter' type='radio' name='ethnicity' checked={ethnicityFilter.exclude}  onChange={this.handleEthnicityFilterChange.bind(this, 'exclude')} />
                  <label htmlFor='exclude_ethnicites_filter' className='filter-label'>Exclude</label>
                </div>
              </div>
              {
                (ethnicityFilter.include || ethnicityFilter.exclude) && <div className='row'>
                  <p className='description-para'>You can select multiple options, just click to add</p>
                  <select onChange={this.handleEthnicitySelectionChange}>
                    <option defaultValue>Select...</option>
                    {
                      !!ethnicities && !!ethnicities.length && ethnicities.map((ethnicity, index) => <option 
                        key={`${ethnicity.name}-${index}`} 
                        value={ethnicity.name}
                      >
                        {ethnicity.name}
                      </option>)
                    }
                  </select>
                  {
                    !!selectedEthnicites && !!selectedEthnicites.length && selectedEthnicites.map((ethnicity, index) => <SelectedFilter
                      filter='ethnicity'
                      key={`${ethnicity}-bottom-filter-${index}`}
                      filterName={ethnicity}
                      selectedListUnderFilter={true}
                      handleRemoveFilter={this.handleRemoveFilter}
                    />)
                  }
                </div>
              }
              <div className='row mb-10'>
                <div className='columns small-12'>
                  <h4 className='filter-heading'>Religion</h4>
                  <input id='all_religions_filter' type='radio' name='religion' checked={religionFilter.all} onChange={this.handleReligionFilterChange.bind(this, 'all')} />
                  <label htmlFor='all_religions_filter' className='filter-label'>All</label>
                  <input id='include_religions_filter' type='radio' name='religion' checked={religionFilter.include} onChange={this.handleReligionFilterChange.bind(this, 'include')} />
                  <label htmlFor='include_religions_filter' className='filter-label'>Include</label>
                  <input id='exclude_religions_filter' type='radio' name='religion' checked={religionFilter.exclude}  onChange={this.handleReligionFilterChange.bind(this, 'exclude')} />
                  <label htmlFor='exclude_religions_filter' className='filter-label'>Exclude</label>
                </div>
              </div>
              {
                (religionFilter.include || religionFilter.exclude) && <div className='row'>
                  <p className='description-para'>You can select multiple options, just click to add</p>
                  <select onChange={this.handleReligionSelectionChange}>
                    <option defaultValue>Select...</option>
                    {
                      !!religions && !!religions.length && religions.map((religion, index) => <option 
                        key={religion.name} 
                        value={religion.name}
                      >
                        {religion.name}
                      </option>)
                    }
                  </select> 
                  {
                    !!selectedReligions && !!selectedReligions.length && selectedReligions.map((religion, index) => <SelectedFilter
                      filter='religion'
                      key={`${religion}-bottom-filter-${index}`}
                      filterName={religion}
                      selectedListUnderFilter={true}
                      handleRemoveFilter={this.handleRemoveFilter}
                    />)
                  } 
                </div>
              }
            </div>
            <div className='columns small-12 large-6 filtering-description'>
              <p>Choose an age range for students you are interested in. Students must be at least 13 years old to create an account on Plexuss.</p>
              <p>By default we will recommend you all ethnicities, but you can select which ethnicities you would like to give priority to include or exclude from your daily recommendations.</p>
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
  ethnicities: state.scholarships.ethnicities,
  religions: state.scholarships.religions,
  demographics: state.newsfeed.audienceTargeting.demographics,
  salesPostId: state.newsfeed.salesPostId,
  isLoading: state.newsfeed.loader.isLoading,
  resetFilter: state.newsfeed.audienceTargeting.resetFilter,
});

const mapDispatchToProps = dispatch => ({
  getAllEthnicities: () => { dispatch(getAllEthnicities()) },
  getAlReligions: () => { dispatch(getAllReligionsCustom()) },
  setRecommendationFilter: (tabName, values) => { dispatch(setRecommendationFilter(tabName, values)) },
  resetRecommendationFilter: (tabName) => { dispatch(resetRecommendationFilter(tabName)) },
  setRecommendationFilterDemographics: (values) => { dispatch({ type: 'SET_RECOMMENDATION_FILTER_DEMOGRAPHIC', payload: values }) },
  setResetTargetingFilter: (value) => { dispatch({ type: 'SET_TARGETING_RESET_FILTER', payload: value }) },
})

export default connect(mapStateToProps, mapDispatchToProps)(Demographics);
