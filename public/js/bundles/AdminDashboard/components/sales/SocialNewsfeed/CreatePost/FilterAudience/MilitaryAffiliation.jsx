import React, { Component } from 'react';
import { connect } from 'react-redux';
import { SelectedFilter } from './SelectedFilter.jsx';
import RecommendationMeter from './RecommendationMeter.jsx';
import { getAllMilitaries } from '../../../../../actions/scholarshipsActions';
import { setRecommendationFilter, resetRecommendationFilter } from '../../../../../actions/newsfeedActions';
import objectToFormData from 'object-to-formdata';


const tabName = 'militaryAffiliation';
class MilitaryAffiliation extends Component {
	constructor(props) {
		super(props);

		const state = (this.props.filterChanges.tabName === tabName && this.props.filterChanges.state) || this.props.militaryAffiliations;

    this.state = { ...state };

		this.handleInMilitaryChange = this.handleInMilitaryChange.bind(this);
		this.handleMiliataryAffiliationChange = this.handleMiliataryAffiliationChange.bind(this);
		this.handleRemoveFilter = this.handleRemoveFilter.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
		this.handleResetFilterClick = this.handleResetFilterClick.bind(this);
	}

	componentDidMount() {
		this.props.getAllMilitaries();
	}

	componentWillReceiveProps(nextProps) {
    if(nextProps.resetFilter) {
      this.props.setResetTargetingFilter(false);
      this.setState({ ...nextProps.militaryAffiliations });
    }
	}
	
	componentWillUnmount() {
		if(!_.isEqual(this.state, this.props.militaryAffiliations)) {
			const filter = {};
			filter.formData = this.buildFormData();
			filter.state = { ...this.state };
			filter.tabName = tabName;
			this.props.setFilterCurrentChanges(filter);
		}
  }

	handleInMilitaryChange(e) {
		const value = e.target.value;
		this.setState({ inMilitary: (value === 'true' || value === 'false') ? JSON.parse(value) : '', selectedMilitaryAffiliations: [] });
	}

	handleMiliataryAffiliationChange(e) {
		const selectedMilitaryAffiliations = [...this.state.selectedMilitaryAffiliations];
		selectedMilitaryAffiliations.push(e.target.value);
		this.setState({ selectedMilitaryAffiliations });
	}

	handleRemoveFilter(filter, filterName) {
		if(filter === 'inMilitary') {
			this.setState({ inMilitary: '', selectedMilitaryAffiliations: [] });
		} else if(filter === 'selectedMilitaryAffiliations') {
			const selectedMilitaryAffiliations = [...this.state.selectedMilitaryAffiliations];
			this.setState({ selectedMilitaryAffiliations: selectedMilitaryAffiliations.filter(sma => sma !== filterName) });
		}
	}

	buildFormData() {
		const { inMilitary, selectedMilitaryAffiliations } = this.state;
		const values = {
			inMilitary: inMilitary,
			militaryAffiliation: selectedMilitaryAffiliations,
			sales_pid: this.props.salesPostId,
		}
		return objectToFormData(values);
	}

	handleSubmit(e) {
		e.preventDefault();
		
		this.props.setRecommendationFilterMilitaryAffiliation({...this.state});
		this.props.setRecommendationFilter(tabName, this.buildFormData());
	}

	handleResetFilterClick() {
		this.props.resetRecommendationFilter(tabName);
	}

	render() {
		const { inMilitary, selectedMilitaryAffiliations } = this.state;
		const { militaries, isLoading } = this.props;

		return (
			<form onSubmit={this.handleSubmit}>
				<div className='selected-filters-list'>
          <span className='filter-list-title'>Military Affiliation: </span>
					{
						inMilitary !== '' && <SelectedFilter
							filter='inMilitary'
							filterName={inMilitary ? 'Yes' : 'No'}
							handleRemoveFilter={this.handleRemoveFilter}
						/>
					}
          {
            !!selectedMilitaryAffiliations.length && selectedMilitaryAffiliations.map(sma => <SelectedFilter
							key={`${sma}-filter`}
              filter='selectedMilitaryAffiliations'
              filterName={sma}
              handleRemoveFilter={this.handleRemoveFilter}
            />) 
          }
        </div>
        <RecommendationMeter />
        <div className='filtering-cont'>
					<div className='row'>
						<div className='columns small-12'>
							<h4 className='filter-heading mb-20'>Military Affiliation</h4>
						</div>
						<div className='columns small-12'>
							<div className='row'>
								<div className='columns small-12 medium-9'>
									<label htmlFor='inMillitary-filter' className='bold'>In Military?</label>
									<select value={inMilitary} id='inMillitary-filter' onChange={this.handleInMilitaryChange}>
										<option defaultValue>Select...</option>
										<option value={false}>No</option>
										<option value={true}>Yes</option>
									</select>
								</div>
							</div>
							{
								!!inMilitary && <div className='row'>
									<div className='columns small-12 medium-9'>
										<label htmlFor='military-affiliation-filter' className='bold'>Military Affiliation:</label>
										<select id='military-affiliation-filter' onChange={this.handleMiliataryAffiliationChange}>
											<option defaultValue>Select...</option>
											{
												!!militaries && !!militaries.length && militaries.map((ma, index) => <option key={`${ma.name}-index`} value={ma.name}>{ma.name}</option>)
											}
										</select>
									</div>
								</div>
							}
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

const mapStateToProps = state => {
	return {
		militaries: state.scholarships.militaries,
		militaryAffiliations: state.newsfeed.audienceTargeting.militaryAffiliations,
		salesPostId: state.newsfeed.salesPostId,
		isLoading: state.newsfeed.loader.isLoading,
		resetFilter: state.newsfeed.audienceTargeting.resetFilter,
	}
}

const mapDispatchToProps = dispatch => ({
	getAllMilitaries: () => { dispatch(getAllMilitaries()) },
	setRecommendationFilter: (tabName, values) => { dispatch(setRecommendationFilter(tabName, values)) },
	setRecommendationFilterMilitaryAffiliation: (values) => { dispatch({ type: 'SET_RECOMMENDATION_FILTER_MILITARY_AFFILIATION', payload: values }) },
	resetRecommendationFilter: (tabName) => { dispatch(resetRecommendationFilter(tabName)) },
  setResetTargetingFilter: (value) => { dispatch({ type: 'SET_TARGETING_RESET_FILTER', payload: value }) },
})

export default connect(mapStateToProps, mapDispatchToProps)(MilitaryAffiliation);
