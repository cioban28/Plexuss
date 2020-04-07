// /ReviewApp/AdditionalInfo.js

import React from 'react'

import SectionHeader from './SectionHeader'

import {REVIEW_APP} from './constants'

class ReviewAdditionalInfo extends React.Component {
	constructor(props) {
		super(props)
		this._buildAdditionalInformationTables = this._buildAdditionalInformationTables.bind(this)
		this._verifyPremierStatus = this._verifyPremierStatus.bind(this)
	}

	_buildAdditionalInformationTables() {
		const tables = [];
		let { _profile, _superUser: _su } = this.props,
			has_premier_status = this._verifyPremierStatus();

		_.each(REVIEW_APP.additional_information, (value, key) => {
			let school_ids = REVIEW_APP.additional_information[key].school_ids;

			// If viewing a student app, only show specific college additional questions for their organization.
			if (!has_premier_status && _su && _su.is_imposter && !_su.is_plexuss) {

				// Case 1: Organization school id does not match school id
				if (_su.org_school_id && school_ids && school_ids.indexOf(_su.org_school_id) == -1) { return; }

				// Case 2: Organization aor_id is not 5, skip ELS schools.
				if (key == 'ELS' && ( !_su.is_aor || _su.aor_id != 5 )) { return; }
			} 

			tables.push(<AdditionalInfoTable key={key} _profile={_profile} record={REVIEW_APP.additional_information[key]} />);
		});

		return tables;
	}

	_verifyPremierStatus() {
		let { _profile, _superUser: _su } = this.props;
		
		if (_su && _su.org_plan_status == 'Bachelor') { return true; }

		// Check trial dates
		if (!_su || !_su.premier_trial_begin_date_ACTUAL || !_su.premier_trial_end_date_ACTUAL) { return false; }

		// Converts current date into YYYY/MM/DD format
		const date_string = new Date().toISOString().split('T')[0],
			  current_date = new Date(date_string.split('-').join('/')),
			  begin_date = new Date(_su.premier_trial_begin_date_ACTUAL.split('-').join('/')),
			  end_date = new Date(_su.premier_trial_end_date_ACTUAL.split('-').join('/'));

		if (current_date >= begin_date && current_date <= end_date) { return true; }

		return false;
	}

	render(){
		let { dispatch,  _profile, _user, _route, noEdit } = this.props,
			loading = _user.init_imposter_pending || _user.init_student_data_pending;

		return (
			<div className="section">

				<div className="inner">
				
					<div className="arrow" />

					<SectionHeader route={_route} dispatch={dispatch} noEdit={noEdit} customName={'Additional Questions'} />

					<div className="additional-questions-container">
						{ !loading && 
							this._buildAdditionalInformationTables() }
						{ loading && 
						  <div className='additional-questions-loader'></div> }
					</div>

				</div>

			</div>
		);
	}
}

class AdditionalInfoTable extends React.Component{

	constructor(props) {
		super(props)
		this.state = {
			hidden: true,
			arrow: '\u25b7',
		}
		this._toggleHide = this._toggleHide.bind(this)
		this._verifyInformationExists = this._verifyInformationExists.bind(this)
		this._formatTableID = this._formatTableID.bind(this)
		this._capitalizeFirstLetter = this._capitalizeFirstLetter.bind(this)
		this._getTableRowValue = this._getTableRowValue.bind(this)
		this._buildTableRow = this._buildTableRow.bind(this)
	}

	_toggleHide() {
		// toggle arrow
		let arrow = this.state.arrow == '\u25b7'
				 	? '\u25bd'  // Down arrow unicode
				 	: '\u25b7'; // Right arrow unicode 

		this.setState({arrow: arrow, hidden: !this.state.hidden});
	}

	_formatTableID(string) {
		return string.replace(/\s+/, '-').toLowerCase() + '-table';
	}

	_capitalizeFirstLetter(string) {
		if (string == null) { return false; }

		string = string.toString();

		if (!isNaN(parseFloat(string[0]))) { return string; }

		return string[0].toUpperCase() + string.slice(1);
	}

	_buildTableRow(row){
		let { _profile } = this.props,
			value = null;

		value = this._getTableRowValue(row);

		if (!value || value.toString().trim() == 'null') { return; }

		return (
			<tr key={row.id}>
				<th>{row.label}</th>
				<th>{this._capitalizeFirstLetter(value)}</th>
			</tr>
		);
	}

	_getTableRowValue(row) {
		let { _profile } = this.props;
		
		// Undefined cases, do not get anything if null value.
		if (_profile[row.id] == null || _profile[row.id] == '' || _profile[row.id] == '0000-00-00')
			return false;

		// If row has options, use options to obtain value 		
		if (row.options != null && row.options.length > 0) {
			let option = null;
			for (let i = 0; i < row.options.length; i++) {
				option = row.options[i];
				if (_profile[row.id] == option.id) {
					return option.name;
				}
			}
		}

		// If row has fields, use fields to obtain value 		
		if (row.fields != null && row.fields.length > 0) {
			let values = [],
				field = null;
			for (let i = 0; i < row.fields.length; i++) {
				field = row.fields[i];

				// Radio case, no need to continue looping. Value's in original row.id
				if (row.id == field.name) { return _profile[row.id]; }

				if (_profile[field.name] != '0' && _profile[field.name] != 'null' && _profile[field.name] != '0000-00-00') {
					values.push(field.label.replace('on this date:', ''));
				}
			}
			return values.join(', ');
		}

		// else use switch to determine special case or default
		switch(row.id) {
			case 'religious_affiliation':
				return _profile.religion_list 
					? _.find(_profile.religion_list, [ 'id', parseInt(_profile[row.id]) ]).name 
					: _profile[row.id];
			case 'liberty_housing_requirements__off_campus': 
			case 'liberty_housing_requirements__residence_hall':
				return ( !_profile[row.id] || _profile[row.id] == 'null' )
					? "No"
					: "Yes";
			case 'have_any_of_the_following_conditions':
				return _profile.illnesses;
			case 'emergency_contact_relationship':
				return _profile[row.id] == '0'
					? false
					: _profile[row.id];
			default: 
				return _profile[row.id];
		}
	}

	// Checks if at least one answer exists in the state for each record table
	_verifyInformationExists(record) {
		if (!record) { return false; }

		let { _profile } = this.props,
			value = null;

		for (let i = 0; i < record.answers.length; i++) {
			value = this._getTableRowValue(record.answers[i]);
			if (value != null && value.toString().trim() != 'null' && value != '0000-00-00' && value != '') {
				return true;
			}
		}

		return false;
	}

	render(){
		let record = this.props.record;

		// If no answer information exist for table, do not build table
		if (!this._verifyInformationExists(record)) { return null; }

		return (

			<div key={this._formatTableID(record.title)}>
				<div className="heading mt15" onClick={this._toggleHide}>{this.state.arrow} {record.title}</div>
				{ !this.state.hidden &&
					<table className={this._formatTableID(record.title)}>
						<tbody>
							{ record.answers.map(this._buildTableRow) }
						</tbody>
					</table>
				}

			</div>	

		);
	}
}

export default ReviewAdditionalInfo;