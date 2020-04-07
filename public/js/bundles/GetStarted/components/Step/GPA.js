// /College_Application/GPA

import React, {Component} from 'react'
import { connect } from 'react-redux'
import { withRouter } from 'react-router-dom'
import store from '../../../stores/getStartedStore'
import './gpa.scss'
import _ from 'lodash'
import Loader from '../Header/loader'
import TextField from './TextField'
import NumberField from './NumberField'
import SelectField from './SelectField'

/************************************************ below are fields for gpa page */
const GPA_FIELDS = [
	{name: 'gpa', label: '', display_label: 'United States Overall GPA', step: '0.01', placeholder: '', err: 'Only values between 0.1 and 4 are accepted.', min: 0.10, max: 4.00, type: 'text'},
];

const CONVERSION_GPA_FIELDS = [
	{name: 'gpa_applicant_country', label: '', alternative_label: 'Please select your Country', placeholder: 'Select an option', field_type: 'select', options: []},
	{name: 'gpa_applicant_scale', label: '', alternative_label: 'Select Grading Scale', placeholder: 'Select an option', field_type: 'select', options: []},
	{name: 'gpa_applicant_value', label: '', field_type: '', step: '0.01', placeholder: '', options: []},
	{name: 'gpa', label: '', field_type: 'text', step: '0.01', err: 'Only values between 0.1 and 4 are accepted.'}
];
import { updateProfile, saveApplication, resetSaved, getGPAGradingScales, convertToUnitedStatesGPA, clearChangedFields, getCountries } from './../../actions/Profile'

class GPA extends Component{
	constructor(props){
		super(props);

		this.setTimer = null;
        this._saveAndContinue = this._saveAndContinue.bind(this);
        this._getNodeName = this._getNodeName.bind(this);
        this._saveAndContinue = this._saveAndContinue.bind(this);
        this._getField = this._getField.bind(this);
        this._buildSelectField = this._buildSelectField.bind(this);
        this._buildQuestions = this._buildQuestions.bind(this);
        this._buildConversionInput = this._buildConversionInput.bind(this);
	}

	_getNodeName(id){
		if (!document.getElementById(id)) { return false; }

		return document.getElementById(id).nodeName.toLowerCase();
	}

	_saveAndContinue(e){
		e.preventDefault();
		
		let { _profile } = this.props;

		if( _profile['gpa_form_done'] ){
			let form = _.omitBy( _profile, (v, k) => k.includes('list') );
			store.dispatch( saveApplication(form, 'gpa') );
		}
	}

	// readOnly used for converted gpa input.
	_getField(field, readOnly){
		let { _profile } = this.props,
			field_type = field.field_type || field.type;

		switch (field_type) {
			case 'text': 
				return <TextField field={field} readOnly={readOnly} _profile={_profile} />;
			case 'select': 
				return <SelectField field={field} _profile={_profile} />;
			case 'number':
				return <NumberField field={field} readOnly={readOnly} disableFocus={true} _profile={_profile} />;

		}
	}

	_buildSelectField(field){
		return (
			<div key={ field.name } className="gpa-field mt20">
				<label className="mb20">{ field.alternative_label }</label>
				{ this._getField(field) }
			</div>
		);
	}

	_buildQuestions(){
		let { _profile } = this.props,
			Fields = JSON.parse(JSON.stringify(CONVERSION_GPA_FIELDS)),
			country_grading_scales = _profile.country_grading_scales,
			grading_scales_pending = _profile.grading_scales_pending,
			is_united_states = _profile.gpa_applicant_country == 1,
			grading_scale = null,
			render = null;

		render = Fields.map((field, index) => {
			// Inject country list into select field if it exists in state.
			if (field.name == 'gpa_applicant_country' && _profile.countries_list && _profile.countries_list.length > 0){
				field.options = _profile.countries_list;
				return this._buildSelectField(field);
			}

			if (field.name == 'gpa_applicant_scale') {
				// Only show if country is selected
				if (country_grading_scales != null && field.options && _profile.gpa_applicant_country != null) {
					if (country_grading_scales.length > 1) {
						field.options = country_grading_scales;
						return this._buildSelectField(field);
					}
				}
			}

			if (field.name == 'gpa_applicant_value') {
				if (grading_scales_pending) {
					return <span key="conversion-loader" className={ 'conversion-loader' }></span>;
				}
				// Only show if a grading scale is selected and grading scales are not null.
				else if (_profile.gpa_applicant_scale != null && _profile.country_grading_scales != null && _profile.country_grading_scales.length != 0) {
					grading_scale = _.find(country_grading_scales, { 'id': parseInt(_profile.gpa_applicant_scale) });
					if (grading_scale && grading_scale.conversion_type == 1) {	
						field.min = grading_scale.grading_scale_min;
						field.max = grading_scale.grading_scale_max;
						field.err = `Numbers between ${parseFloat(field.min).toFixed(2)} and ${parseFloat(field.max).toFixed(2)} allowed.`;
						field.field_type = 'number';
					} else if (grading_scale) {
						field.options = grading_scale.options,
						field.err = `You must select an option.`;
						field.placeholder = 'Select a Grade'
						field.field_type = 'select';
					}
					return this._buildConversionInput(field, Fields[index + 1]);
				}
			}

			return null;
		}); 

		// Special case, no conversion available. User must enter their converted US GPA.
		if ( Array.isArray(country_grading_scales) && country_grading_scales.length == 0 ) {
			let gpa_field = JSON.parse(JSON.stringify(GPA_FIELDS[0]));
			render.push(
				<div key='manual-gpa-entry-div'>
					{ !is_united_states && <label className='mt20 err-msg'>Unfortunately, we do not have a converter ready for your selected country.</label> }
					<label className='mt20'>{ gpa_field.display_label }</label> {/* Using variable name display label to keep it seperated from numberfield component*/}
					<div className='manual-gpa-entry'>
						<div className='gpa-field'>
							<span className="country_flag us"></span>
							{ this._getField(gpa_field) }
						</div>
					</div>
				</div>

			);
		}

		return render;
	}

	_buildConversionInput(field1, field2){
		let { _profile } = this.props,
			country_id = _profile.gpa_applicant_country,
			valid = _profile.gpa_applicant_value_valid != null ? _profile.gpa_applicant_value_valid : true,
			countries_list = _profile.countries_list,
			selected_country = _.find(countries_list, { 'id': parseInt(country_id) }),
			abbr = selected_country.abbr.toLowerCase(),
			conversion_pending = _profile.grading_conversion_pending,

			// Smaller flag to avoid rendering more than the flag itself.
			tiny_flag = field1.field_type == 'select' ? 'tiny' : '',
			label_verb = field1.field_type == 'select' ? 'select' : 'enter',
			label = `Please ${ label_verb } your GPA`;

		return (
			<div key={ field1.name + field2.name } className="mt20">
				<label>{ label }</label>
				<div className="gpa-conversion-values">
					<div className='gpa-field'>
						<span className={ "country_flag " + abbr + " " + tiny_flag }></span>
						{ this._getField(field1) }
					</div>
					<div className='gpa-field'>
						<span className="country_flag us"></span>
						{ conversion_pending && <span className={ 'conversion-loader' }></span> }
						{ this._getField(field2) }
					</div>
				</div>
			</div>
		);
    }

    componentWillMount(){
	}

	componentDidMount(){
		let { _profile } = this.props;

		if ( _profile && _profile.country_id != null && _profile.gpa_applicant_country == null ) {
			store.dispatch( updateProfile({ gpa_applicant_country: _profile.country_id }) );
		}
	}

	componentWillReceiveProps(np){
		let { _profile } = this.props;

		// after saving, reset saved and go to next route
		if ( np._profile.save_success !== _profile.save_success && np._profile.save_success ) {
			store.dispatch( resetSaved() );
			window.location.href = '/get_started';
		}

		if ( ( (!_profile && np._profile) || (_profile.country_id !== np._profile.country_id) ) && np._profile.country_id != null ) {
			store.dispatch( updateProfile({ gpa_applicant_country: np._profile.country_id }) );
		}

		if ( np._profile.gpa_applicant_country !== _profile.gpa_applicant_country && np._profile.gpa_applicant_country != null ) {
			store.dispatch( getGPAGradingScales(np._profile.gpa_applicant_country) );
		}

		if ( np._profile.country_grading_scales !== _profile.country_grading_scales && np._profile.country_grading_scales != null ) {
			if ( np._profile.country_grading_scales.length == 1 ) {
				store.dispatch( updateProfile({ gpa_applicant_scale: np._profile.country_grading_scales[0].id }) );
			}
		}

		if ( np._profile.gpa_applicant_scale && np._profile.gpa_applicant_scale != _profile.gpa_applicant_scale ) {
			let country_grading_scales = np._profile.country_grading_scales,
				grading_scale = _.find(country_grading_scales, { 'id': parseInt(np._profile.gpa_applicant_scale) });
			store.dispatch( updateProfile({ selected_grading_scale: grading_scale, gpa_applicant_value: null, gpa_applicant_value_valid: false }))
		}

		if ( ( Number.isFinite(parseFloat(np._profile.gpa_applicant_value)) || this._getNodeName('gpa_applicant_value') == 'select' ) && np._profile.gpa_applicant_value !== _profile.gpa_applicant_value ) {
			if ( np._profile.selected_grading_scale != null && np._profile.gpa_applicant_value !== '' ) {
				let grading_scale = np._profile.selected_grading_scale,
					gch_id = grading_scale.id,
					old_value = np._profile.gpa_applicant_value,
					conversion_type = grading_scale.conversion_type;

				clearInterval(this.setTimer);

				if (np._profile.gpa_applicant_value_valid) {
					store.dispatch( updateProfile({ grading_conversion_pending: true }));
					this.setTimer = setTimeout(() => {
						store.dispatch( convertToUnitedStatesGPA(gch_id, old_value, conversion_type) );
					}, 500)
				} else {
					store.dispatch( updateProfile({ grading_conversion_pending: false }));
				}
			}
		}
	}

	render(){
		return (
			<div>
				<div className="intro" style={{'font-weight':'bold'}}>Grade Point Average (GPA)</div>
				<div className="intro" style={{'font-size':'14px'}}>In order to qualify to apply to a university in the United States, it is critical that we have the right GPA. Please enter your current grades based on your county's grading scale; we will then convert it to a U.S. GPA.</div>
				<br /><br />
				<div className="gpa-converter confirm-gpa">
					{ this._buildQuestions() }
					<div className="right btn submit-btn text-center" onClick={this._saveAndContinue}>Next</div>
					{ this.props._profile.save_pending ? <Loader /> : null }
				</div>
			</div>
		);
	}
}

const mapStateToProps = (state) => {
	return {
		_profile: state.steps,
	};
};

export default withRouter(connect(mapStateToProps)(GPA));