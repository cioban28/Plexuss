import React from 'react'
import { getSchoolAdditionalQuestions, updateProfile, saveAdditionalInfoPage, changeCollegeAdditionalPage, clearChangedFields } from '../../actions/Profile'
import TextField from './TextField'
import RadioForm from './RadioForm'
import TextForm from './TextForm'
import SelectField from './SelectField'
import UploadForm from './UploadForm'
import CheckboxForm from './CheckboxForm'
import DatePickr from './../common/DatePickr'
import PhoneNumberVerifier from './../common/PhoneNumberVerifier'

export default class SingleCollegeAdditionalInfo extends React.Component {
	constructor(props) {
		super(props);

		this._goBack = this._goBack.bind(this);
	}

	componentWillMount() {
		let { dispatch, route, _profile, college } = this.props;

		dispatch( updateProfile({ page: route.id }) );
		// dispatch( clearChangedFields());
	}

	componentWillReceiveProps(newProps) {
		let { dispatch, college, _profile } = this.props;

		if ( newProps.college && ( !college || ( college && college.school_name != newProps.college.school_name ) ) ) {
			dispatch( getSchoolAdditionalQuestions(newProps.college) );
		}
	}

	// Replacing spaces with _ and all letters lowercased.
	_getFormatedName(){
		let { college } = this.props;
		return college ? college.school_name.split(/\s+/).join('_').toLowerCase() : null;
	}

	_getField(field) {
		let { _profile } = this.props;

		switch(field.field_type) {
			case 'text': return <TextField key={field.name} field={field} {...this.props} />;
			case 'radio': return <RadioForm key={field.name} radio={field} {...this.props} />;
			case 'textform': return <TextForm key={field.name} field={field} {...this.props} />;
			case 'select': return <SelectField key={field.name} field={field} {...this.props} />;
			case 'redirect': return <UploadForm key={field.name} field={field} {...this.props} />;
			case 'checkbox': return <CheckboxForm key={field.name} checkb={field} {...this.props} />;
			case 'phone': return <div key={field.name}>{field.label || ''}<PhoneNumberVerifier alternate={field.alternate_name} /></div>;
			case 'date': return <DatePickr
									key={field.name}
									_label={field.label}
									_side="_left"
									_action={ updateProfile }
									_state={ _profile }
									_format={'YYYY-MM-DD'}
									_name={field.name} />;
		}
	}

	_goBack(event) {
		event.preventDefault();

		let { dispatch, college } = this.props;

		dispatch( changeCollegeAdditionalPage(college, 'back') );
	}

	_buildSectionHeader() {
		let { _profile, college } = this.props,
			name = this._getFormatedName(),
			disabled = _profile.save_pending,
			additional_questions = _profile[name + '_school_additional_questions'];

		if (this._undefinedCheck()) {
			return null;
		}

		// Truncating if school_name is over 31 characters
		let title = college.school_name.length > 31 ? college.school_name.slice(0,32) + '...' : college.school_name;

		let current_page = additional_questions.current_page + 1;
		let page_count = additional_questions.questions.length;

		return (
			<div className="page-head">
				<span className='school-title' title={ college.school_name }>{ title }</span>
				<span style={{float: 'right'}}>
					{ current_page > 1 && <span onClick={ disabled ? () => false : this._goBack } className="additional-questions-back-button">Back</span> }
					<span>{'(' + current_page + '/' + page_count + ')'}</span>
				</span>
			</div>
		)
	}

	_buildQuestions() {
		if (this._undefinedCheck()) { return null; }

		let { _profile } = this.props,
			name = this._getFormatedName(),
			additional_questions = _profile[name + '_school_additional_questions'],
			questions = additional_questions.questions,
			current_page = additional_questions.current_page;

		return questions[current_page].map((field) => {
			if (_profile.religion_list && field.name === 'addtl__are_you_christian' && field.dependents_no && field.dependents_no.length > 0) {
				field.dependents_no = field.dependents_no.map(d => { return {...d, options: _profile.religion_list}; })
			}
			return this._getField(field);
		});
	}

	_undefinedCheck() {
		let { _profile, college } = this.props,
			applyTo_schools = _profile.applyTo_schools,
			name = this._getFormatedName(),
			additional_questions = _profile[name + '_school_additional_questions'];

		return ( !college || !additional_questions || ( college && !_.find(applyTo_schools, ['school_name', college.school_name]) ) );
	}

	render() {
		let { _profile, college } = this.props;
		return (
			<div className='questions-container' ref={(div) => { this.container = div; }}>
				{ this._buildSectionHeader() }
				<div className="questions mt10">
					{ this._buildQuestions() }
				</div>
			</div>
		);
	}

}

