import React, { Component } from 'react';
import { connect } from 'react-redux';
import { CountryDropdown, RegionDropdown } from 'react-country-region-selector';
import axios from 'axios';
import { SelectedFilter } from './SelectedFilter.jsx';
import RecommendationMeter from './RecommendationMeter.jsx';
import { setRecommendationFilter, resetRecommendationFilter } from '../../../../../actions/newsfeedActions';
import objectToFormData from 'object-to-formdata';
import _ from 'lodash';

const tabName = 'location';
class Location extends Component {
  constructor(props) {
    super(props);

    const state = (this.props.filterChanges.tabName === tabName && this.props.filterChanges.state) || this.props.locationData;

    this.state = { ...state };

    this.handleRemoveFilter = this.handleRemoveFilter.bind(this);
    this.handleCityChange = this.handleCityChange.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
    this.handleResetFilterClick = this.handleResetFilterClick.bind(this);
  }

  componentWillReceiveProps(nextProps) {
    if(nextProps.resetFilter) {
      this.props.setResetTargetingFilter(false);
      this.setState({ ...nextProps.locationData });
    }
  }

  componentWillUnmount() {
    if(!_.isEqual(this.state, this.props.locationData)) {
      const filter = {};
      filter.formData = this.buildFormData();
      filter.state = { ...this.state };
      filter.tabName = tabName;
      this.props.setFilterCurrentChanges(filter);
    }
  }

  handleCountriesSelectorChange(selectedFilter) {
    const countriesFilter = { ...this.state.countriesFilter };
    Object.keys(countriesFilter).forEach(filter => {
      if(filter === selectedFilter) {
        countriesFilter[filter] = !countriesFilter[filter];
      } else {
        countriesFilter[filter] = false;
      }
    });
    this.setState({ countriesFilter });
  }

  handleStatesSelectorChange(selectedFilter) {
    const statesFilter = { ...this.state.statesFilter };
    Object.keys(statesFilter).forEach(filter => {
      if(filter === selectedFilter) {
        statesFilter[filter] = !statesFilter[filter];
      } else {
        statesFilter[filter] = false;
      }
    });
    this.setState({ statesFilter });
  }

  handleCitiesSelectorChange(selectedFilter) {
    const citiesFilter = { ...this.state.citiesFilter };
    Object.keys(citiesFilter).forEach(filter => {
      if(filter === selectedFilter) {
        citiesFilter[filter] = !citiesFilter[filter];
      } else {
        citiesFilter[filter] = false;
      }
    });
    this.setState({ citiesFilter });
  }

  handleCountryChange(val) {
    const selectedCountries = [...this.state.selectedCountries];
    selectedCountries.push(val);

    if(val === 'United States') {
      this.setState({ selectedCountry: val, isUSSelected: true, selectedCountries });
    }
    this.setState({ selectedCountry: val, selectedCountries });
  }

  handleRegionChange(val) {
    const selectedUSStates = [...this.state.selectedUSStates];
    selectedUSStates.push(val);
    this.setState({ selectedRegion: val, selectedUSStates });
    axios.get(`http://plexuss.local:8000/ajax/homepage/getCityByState/${val}`)
      .then(res => {
        if(res.statusText === 'OK') {
          this.setState({ stateCities: res.data });
        }
      })
      .catch(function (error) {
      });
  }

  handleCityChange(e) {
    const selectedUSCities = [...this.state.selectedUSCities];
    selectedUSCities.push(e.target.value)
    this.setState({ selectedUSCities })
  }

  handleRemoveFilter(filter, filterName) {
    if(filter === 'country') {
      const selectedCountries = [...this.state.selectedCountries];
      const itemIndex = selectedCountries.findIndex(country => country === filterName);
      if(itemIndex === -1) return;
      selectedCountries.splice(itemIndex, 1);
      this.setState({ selectedCountries });
    } else if (filter === 'state') {
      const selectedUSStates = [...this.state.selectedUSStates];
      const itemIndex = selectedUSStates.findIndex(state => state === filterName);
      if(itemIndex === -1) return;
      selectedUSStates.splice(itemIndex, 1);
      this.setState({ selectedUSStates });
    } else {
      const selectedUSCities = [...this.state.selectedUSCities];
      const itemIndex = selectedUSCities.findIndex(city => city === filterName);
      if(itemIndex === -1) return;
      selectedUSCities.splice(itemIndex, 1);
      this.setState({ selectedUSCities });
    }
  }

  buildFormData() {
    const { countriesFilter, statesFilter, citiesFilter, selectedCountries, selectedUSStates, selectedUSCities, isUSSelected, stateCities, selectedCountry, selectedRegion } = this.state;
    const values = {
      all_country_filter: countriesFilter.all,
      include_country_filter: countriesFilter.include,
      exclude_country_filter: countriesFilter.exclude,
      all_state_filter: statesFilter.all,
      include_state_filter: statesFilter.include,
      exclude_state_filter: statesFilter.exclude,
      all_city_filter: citiesFilter.all,
      include_city_filter: citiesFilter.include,
      exclude_city_filter: citiesFilter.exclude, 
      country: selectedCountries,
      state: selectedUSStates,
      city: selectedUSCities,
      sales_pid: this.props.salesPostId,
    };
    return objectToFormData(values);
  }

  handleSubmit(e) {
    e.preventDefault();

    this.props.setRecommendationFilterLocation({ ...this.state });
    this.props.setRecommendationFilter(tabName, this.buildFormData());
  }

  handleResetFilterClick() {
    this.props.resetRecommendationFilter(tabName);
  }

  render() {
    const { countriesFilter, statesFilter, citiesFilter, selectedCountry, selectedCountries, selectedRegion, isUSSelected, stateCities, selectedUSStates, selectedUSCities } = this.state;
    const { isLoading } = this.props;

    return (
      <form onSubmit={this.handleSubmit}>
        <div className='selected-filters-list'>
          <span className='filter-list-title'>Location: </span>
          {
            selectedCountries.map((country, index) => <SelectedFilter
              filter='country'
              key={`${country}-${index}`}
              filterName={country}
              include={countriesFilter.include}
              exclude={countriesFilter.exclude}
              handleRemoveFilter={this.handleRemoveFilter}
            />)
          }
          {
            selectedUSStates.map((state, index) => <SelectedFilter
              filter='state'
              key={`${state}-${index}`}
              filterName={state}
              include={statesFilter.include}
              exclude={statesFilter.exclude}
              handleRemoveFilter={this.handleRemoveFilter}
            />)
          }
          {
            selectedUSCities.map((city, index) => <SelectedFilter
              filter='city'
              key={`${city}-${index}`}
              filterName={city}
              include={citiesFilter.include}
              exclude={citiesFilter.exclude}
              handleRemoveFilter={this.handleRemoveFilter}
            />)
          }
        </div>
        <RecommendationMeter />
        <div className='filtering-cont location-cont'>
          <div className='row'>
            <div className='column small-12 large-6'>
              <div className='row'>
                <h4 className='filter-heading'>Country:</h4>
                <input id='all_countries_filter' type='radio' name='country' checked={countriesFilter.all} onChange={this.handleCountriesSelectorChange.bind(this, 'all')} />
                <label htmlFor='all_countries_filter' className='filter-label'>All</label>
                <input id='include_countries_filter' type='radio' name='country' checked={countriesFilter.include} onChange={this.handleCountriesSelectorChange.bind(this, 'include')} />
                <label htmlFor='include_countries_filter' className='filter-label'>Include</label>
                <input id='exclude_countries_filter' type='radio' name='country' checked={countriesFilter.exclude}  onChange={this.handleCountriesSelectorChange.bind(this, 'exclude')} />
                <label htmlFor='exclude_countries_filter' className='filter-label'>Exclude</label>
                {
                  (!!countriesFilter.include || !!countriesFilter.exclude) && <div>
                    <div>
                    {
                      <CountryDropdown
                        priorityOptions={['US']}
                        value={selectedCountry}
                        onChange={this.handleCountryChange.bind(this)} />
                    }
                    </div>
                    <div>
                    {
                      selectedCountries.map((country, index) => <SelectedFilter
                        key={`${index}-${country}`}
                        filterName={country}
                        filter='country'
                        selectedListUnderFilter={true}
                        handleRemoveFilter={this.handleRemoveFilter}
                      />)
                    }
                    </div>
                  </div>
                }
                {
                  !!countriesFilter.include && isUSSelected && <div>
                    <h4 className='filter-heading'>States:</h4>
                    <input id='all_states_filter' type='radio' name='states' checked={statesFilter.all} onChange={this.handleStatesSelectorChange.bind(this, 'all')} />
                    <label htmlFor='all_states_filter' className='filter-label'>All</label>
                    <input id='include_states_filter' type='radio' name='states' checked={statesFilter.include} onChange={this.handleStatesSelectorChange.bind(this, 'include')} />
                    <label htmlFor='include_states_filter' className='filter-label'>Include</label>
                    <input id='exclude_states_filter' type='radio' name='states' checked={statesFilter.exclude}  onChange={this.handleStatesSelectorChange.bind(this, 'exclude')} />
                    <label htmlFor='exclude_states_filter' className='filter-label'>Exclude</label>
                    {
                      (!!statesFilter.include || !!statesFilter.exclude) && <div>
                          <div>
                            <RegionDropdown
                              defaultOptionLabel='Select state(s)'
                              country={'United States'}
                              value={selectedRegion}
                              onChange={this.handleRegionChange.bind(this)} />
                          </div>
                          <div>
                          {
                            selectedUSStates.map((state, index) => <SelectedFilter
                              key={`${index}-${state}`}
                              filterName={state}
                              filter='state'
                              selectedListUnderFilter={true}
                              handleRemoveFilter={this.handleRemoveFilter}
                            />)
                          }
                          </div>
                        </div>
                    }
                    <h4 className='filter-heading'>City:</h4>
                    <input id='all_cities_filter' type='radio' name='cities' checked={citiesFilter.all} onChange={this.handleCitiesSelectorChange.bind(this, 'all')} />
                    <label htmlFor='all_cities_filter' className='filter-label'>All</label>
                    <input id='include_cities_filter' type='radio' name='cities' checked={citiesFilter.include} onChange={this.handleCitiesSelectorChange.bind(this, 'include')} />
                    <label htmlFor='include_cities_filter' className='filter-label'>Include</label>
                    <input id='exclude_cities_filter' type='radio' name='cities' checked={citiesFilter.exclude}  onChange={this.handleCitiesSelectorChange.bind(this, 'exclude')} />
                    <label htmlFor='exclude_cities_filter' className='filter-label'>Exclude</label>
                    {
                      (!!citiesFilter.include || !!citiesFilter.exclude) && <div>
                        <select onChange={this.handleCityChange}>
                        <option>Select city(s)</option>
                        {
                          stateCities.map((city, index) => (
                            <option key={`select-${city}-${index}`} value={city}>{city}</option>
                          ))
                        }
                        </select>
                        <div>
                        {
                          selectedUSCities.map((city, index) => <SelectedFilter
                            key={`${index}-${city}`}
                            filterName={city}
                            filter='city'
                            selectedListUnderFilter={true}
                            handleRemoveFilter={this.handleRemoveFilter}
                          />)
                        }
                        </div>
                      </div>
                    }
                  </div>
                }
                </div>
            </div>
            <div className='column small-12 large-6 filtering-description'>
              <p>Choose if you would like to receive students from the USA and/or International students.</p>
              <p>If you would like to include or exclude students from a certain State or City, select your desired location. You can select more than one.</p>
            </div>
          </div>
        </div>
        <div className='btn-submit-cont mt-10'>
          <span className='reset-filter-btn' onClick={this.handleResetFilterClick}>Reset this filter</span>
          <button type='submit' className='btn-submit' disabled={isLoading} onSubmit={this.handleSubmit}>{isLoading ? 'Saving...' : 'Save'}</button>
        </div>
      </form>
    )
  }
}

const mapStateToProps = state => ({
  locationData: state.newsfeed.audienceTargeting.location,
  isLoading: state.newsfeed.loader.isLoading,
  salesPostId: state.newsfeed.salesPostId,
  resetFilter: state.newsfeed.audienceTargeting.resetFilter,
});

const mapDispatchToProps = dispatch => ({
  setRecommendationFilter: (tabName, values) => { dispatch(setRecommendationFilter(tabName, values)) },
  resetRecommendationFilter: (tabName) => { dispatch(resetRecommendationFilter(tabName)) },
  setRecommendationFilterLocation: (values) => { dispatch({ type: 'SET_RECOMMENDATION_FILTER_LOCATION', payload: values }) },
  setResetTargetingFilter: (value) => { dispatch({ type: 'SET_TARGETING_RESET_FILTER', payload: value }) },
});

export default connect(mapStateToProps, mapDispatchToProps)(Location);
