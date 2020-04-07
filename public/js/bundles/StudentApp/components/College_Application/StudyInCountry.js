// /College_Application/StudyInCountry

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import { browserHistory } from 'react-router'
import DocumentTitle from 'react-document-title'

import SaveButton from './SaveButton'
import SelectField from './SelectField'
import SelectedItem from './SelectedItem'

import { STUDY_COUNTRIES } from './constants'
import { getCountries, updateProfile, saveApplication, resetSaved, clearChangedFields } from './../../actions/Profile'

let PAGE_DONE = '';

class StudyInCountry extends React.Component{
	constructor(props) {
		super(props)
		this.state = {
			open: false,
		}
		this._saveAndContinue = this._saveAndContinue.bind(this)
	}

	_saveAndContinue(e){
		e.preventDefault();

		let { dispatch, _profile, route } = this.props;
		let form = _.omitBy( _profile, (v, k) => k.includes('list') );

		form.page = route.id;

		dispatch( saveApplication(form, 'study', _profile.oneApp_step) );
	}

	componentWillMount(){
		let { dispatch, _profile, route } = this.props;

		PAGE_DONE = route.id+'_form_done';
		
		if( !_profile.init_countries_done ) dispatch( getCountries() );
		else STUDY_COUNTRIES.options = _profile.countries_list.slice();

		dispatch( updateProfile({page: 'contact'}) );
		dispatch( clearChangedFields());
	}

	componentWillReceiveProps(np){
		let { dispatch, _profile, route } = this.props;

		// once countries is initialized, update countryField.options w/countries
		if( _profile.init_countries_done !== np._profile.init_countries_done && np._profile.init_countries_done ){
			STUDY_COUNTRIES.options = np._profile.countries_list.slice();	
		}

		// after saving, reset saved and go to next route
		if( np._profile.save_success !== _profile.save_success && np._profile.save_success ){
			dispatch( resetSaved() );
			browserHistory.push('/college-application/'+route.next + window.location.search);
		}	
	}

	render(){
		let { _profile } = this.props,
			{ countries, open } = this.state;

		return (
			<DocumentTitle title="Plexuss | College Application | Study In Country">
				<div className="application-container">

					<div className="application-container">

						<form onSubmit={ this._saveAndContinue }>
							<div className="page-head">One of the things about Plexuss is you can be recruited by colleges around the world.</div>

							<SelectField field={ STUDY_COUNTRIES } {...this.props} />

							<div className="selected-countries-container">
								{ selectn('countries_to_study_in', _profile) && 
									_profile.countries_to_study_in.map((s) => 
										<SelectedItem 	
											key={s} 
											id={s} 
											name={'countries_to_study_in'}
											static_list={'countries_list'}
											init_name={'init_countries_done'}
											{...this.props} />) }
							</div>

							<SaveButton 
								page_done={PAGE_DONE} 
								_profile={_profile} />
						</form>

					</div>

				</div>
			</DocumentTitle>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		_profile: state._profile,
	};
};

export default connect(mapStateToProps)(StudyInCountry);