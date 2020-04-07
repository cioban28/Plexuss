import React from 'react'
import DocumentTitle from 'react-document-title'
import { connect } from 'react-redux'
import SaveButton from './SaveButton'
import SelectField from './SelectField'
import { browserHistory } from 'react-router'
import { updateProfile, saveApplication, resetSaved, clearChangedFields } from './../../actions/Profile'
import { SPONSOR_SELECT, SPONSOR_AGREEMENT } from './questionConstants'

import TextField from './TextField'
import CheckboxForm from './CheckboxForm'
import PhoneNumberVerifier from './../common/PhoneNumberVerifier'

class Sponsor extends React.Component {
	constructor(props) {
		super(props);

		this.page_done = '';

		this._saveAndContinue = this._saveAndContinue.bind(this);
	}

	componentWillReceiveProps(np) {
		let { _profile, dispatch, route } = this.props;

		if( np._profile.save_success !== _profile.save_success && np._profile.save_success ){
			this._clearRemovedSponsors();
			dispatch( resetSaved() );

			if( np._profile.coming_from ) browserHistory.goBack();
			else { this.props.location.pathname.includes("social") ? this.props.history.push('/social/one-app/'+route.next + window.location.search) : browserHistory.push('/college-application/'+route.next + window.location.search)};
		}
	}

	componentWillMount() {
		let { _profile, dispatch, route } = this.props;

		this.page_done = route.id + '_form_done';

		dispatch( updateProfile({ page: route.id }) );
		dispatch( clearChangedFields());
	}

	_clearRemovedSponsors() {
		let { _profile, dispatch } = this.props,
			number_of_entries = _profile.sponsor_number_of_entries,
			option = _profile.sponsor_will_pay_option,
			field = SPONSOR_SELECT[0],
			dependents = field['dependents_' + option],
			nullified_sponsors = {},
			index = null;

		_.forIn(_profile, (val, key) => {

			if (key.includes('sponsor_will_pay_id_')) {
				index = parseInt(key.replace('sponsor_will_pay_id_', ''));
				if (number_of_entries < index + 1) {
					nullified_sponsors[key] = null;
				}
			}

			_.each(dependents, field => {
				if (key.includes(field.name)) {
					index = parseInt(key.replace(field.name + '_', ''));
					if (number_of_entries < index + 1) {
						nullified_sponsors[key] = null;
					}
				}
			});
		});

		dispatch( updateProfile(nullified_sponsors) );
	}

	_saveAndContinue(event) {
		event.preventDefault();

		let { _profile, dispatch } = this.props;

		if (_profile[this.page_done]) {
			let form = this._buildFormData();
			dispatch( saveApplication(form, 'sponsor', _profile.oneApp_step) );
		}
	}

	_buildFormData() {
		let { _profile, dispatch } = this.props,
			option = _profile.sponsor_will_pay_option,
			number_of_entries = _profile.sponsor_number_of_entries,
			raw_form = _.pickBy( _profile, (val, key) => _profile[key] &&
														 key.includes('sponsor_will_pay') &&
														!key.includes('valid') &&
														!key.includes('optin') &&
														!key.includes('option')
			),
			result_obj = {},
			index = 0,
			found = true;

		result_obj.number_of_entries = number_of_entries;
		result_obj.page = _profile.page;
		result_obj.option = option;
		result_obj[option] = [];

		while (found) {
			let tmpObj = {};

			found = false;

			_.forIn(raw_form, (val, key) => {
				if (key.endsWith('_' + index)) {
					let newKey = key.replace('sponsor_will_pay_', '').replace('_' + index, '');
					tmpObj[newKey] = val;
					found = true;
					delete raw_form[key];
				} else if (key.endsWith('_' + index + '_code')) {
					tmpObj['phone_code'] = val;
					found = true;
					delete raw_form[key];
				}
			});

			if (found) {
				result_obj[option].push(tmpObj);
			}

			index++;
		}

		if (_profile['impersonateAs_id']) {
			result_obj.impersonateAs_id = _profile['impersonateAs_id'];
		}

		result_obj.optin = _profile.sponsor_will_pay_optin;

		return result_obj;
	}

	render() {
		let { _profile, dispatch, route } = this.props;
		return (
			<DocumentTitle title={ "Plexuss | College Application | " + route.name }>
				<div className="application-container sponsor-container">
					<form onSubmit={ this._saveAndContinue } className="sponsor-form">
						<div className="app-form-container">
							<div className="page-head head-line-height">Sponsors (optional)</div>
							<div>Attending university in the US requires you to meet your financial obligations. You have indicated that you can afford <b>{ _profile.financial_firstyr_affordibility || '0' }</b> per year.</div>
							<br />
							<div>In order to receive an I-20, universities want to know how you plan to pay these fees annually.</div>
							<br />
							{ _profile &&
								<SponsorForm
									_profile={ _profile }
									dispatch={ dispatch }
									route={ route }
								/>
							}
						</div>
					</form>
				</div>
			</DocumentTitle>
		);
	}
}

// Private classes
class SponsorForm extends React.Component {
	constructor(props) {
		super(props);

		this.page_done = '';

		this._getField = this._getField.bind(this);
		this._incrementEntries = this._incrementEntries.bind(this);
		this._decrementEntries = this._decrementEntries.bind(this);
	}

	componentWillMount() {
		let { _profile, dispatch, route } = this.props;

		this.page_done = route.id + '_form_done';

		if (_profile && !_profile.sponsor_number_of_entries) {
			dispatch( updateProfile({ sponsor_number_of_entries: 1 }) );
		}
	}

	componentWillReceiveProps(newProps) {
		let { _profile, dispatch } = this.props,
			{ _profile: _newProfile } = newProps;
	}

	_buildAgreement() {
		let field = SPONSOR_AGREEMENT[0];
		return this._getField(field);
	}

	_incrementEntries(event) {
		event.preventDefault();
		let { _profile, dispatch } = this.props,
			number_of_entries = _profile.sponsor_number_of_entries;

		dispatch( updateProfile({ sponsor_number_of_entries: ++number_of_entries }) );
	}

	_decrementEntries(event) {
		event.preventDefault();
		let { _profile, dispatch } = this.props,
			number_of_entries = _profile.sponsor_number_of_entries;

		dispatch( updateProfile({ sponsor_number_of_entries: --number_of_entries }) );
	}

	_getFieldDependents(option, entry_number) {
		let { _profile } = this.props,
			field = JSON.parse(JSON.stringify(SPONSOR_SELECT[0])), // Deep cloning object
			dependents = field['dependents_' + option].slice(),
			number_of_entries = _profile.sponsor_number_of_entries;

		dependents = dependents.map((dependent, index) => {
			dependent.name = dependent.name + '_' + entry_number;

			dependent.alternate_name
				? dependent.alternate_name = ( dependent.alternate_name + '_' + entry_number )
				: null;

			 number_of_entries > 1
				? dependent.label = (entry_number + 1) + '. ' + dependent.label
				: null;

			return (
				<div key={index} className='sponsor-field'>
					{ this._getField(dependent) }
				</div>
			)
		});

		return dependents;
	}

	_buildDependents() {
		let { _profile } = this.props,
			option = _profile && _profile.sponsor_will_pay_option,
			number_of_entries = _profile.sponsor_number_of_entries || 1;

		if (!option) { return null; }

		const dependents = [];

		for (let entry_number = 0; entry_number < number_of_entries; entry_number++) {

			let classes = 'sponsor-dependents ' + ( entry_number > 0 ? 'mt40' : 'mt30' );

			dependents.push(
				<div key={ entry_number } className={ classes }>
					{ this._getFieldDependents(option, entry_number) }
				</div>
			);

		}

		dependents.push(
			<div key='-1' className='alter-entry-number-btns mt15'>
				{ number_of_entries > 1 &&
					<div onClick={ this._decrementEntries } className='remove-entries-btn'>
						{ 'Remove ' + ( option[0].toUpperCase() + option.slice(1) ) }
					</div>
				}
				<div onClick={ this._incrementEntries } className='add-entries-btn'>
					{ 'Add ' + ( option[0].toUpperCase() + option.slice(1) ) }
				</div>
			</div>
		);

		return dependents;
	}

	_getField(field) {
		let { _profile, dispatch } = this.props;

		switch (field.field_type) {
			case 'text':
				return <TextField field={field} _profile={_profile} dispatch={dispatch} />;
			case 'checkbox':
				return <CheckboxForm checkb={field} _profile={_profile} dispatch={dispatch} />;
			case 'phone':
				return <div>{field.label || ''}<PhoneNumberVerifier alternate={field.alternate_name} /></div>;
			case 'email':
				return <TextField field={field} _profile={_profile} dispatch={dispatch} />;
		}
	}


    _onSkip = () => {
        const { _profile, route } = this.props;
        const required_route = _.find(_profile.req_app_routes.slice(), {id: route.id});

        if(required_route) browserHistory.push('/college-application/'+required_route.next + window.location.search);
    }

	render() {
		let { _profile, dispatch } = this.props,
			field = SPONSOR_SELECT[0],
			option = _profile && _profile.sponsor_will_pay_option,
			agreement = option ? this._buildAgreement() : null;

		return (
			<div className='sponsor-options'>
				{ field && <SelectField key={field.name} field={field} _profile={_profile} dispatch={dispatch} /> }
				{ this._buildDependents() }
				<div className='sponsor-agreement mt50 mb10'>
					{ agreement }
				</div>

                { /*<div className='next-section-buttons'>
                    <SaveButton
                        page_done={ this.page_done }
                        _profile={ _profile }
                    />

                    <div className='section-skip-button' onClick={this._onSkip}>Skip</div>
                </div>*/ }
			</div>
		);
	}
}
// End private classes

const mapStateToProps = (state, props) => {
	return {
		_profile: state._profile,
	};
};

export default connect(mapStateToProps)(Sponsor);
