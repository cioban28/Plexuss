// /College_Application/Contact_Info

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import { browserHistory } from 'react-router'
import DocumentTitle from 'react-document-title'

import TextField from './TextField'
import RadioField from './RadioField'
import SaveButton from './SaveButton'
import VerifyCode from './VerifyCode'
import SelectField from './SelectField'
import CheckboxField from './CheckboxField'
import PhoneNumberVerifier from './../common/PhoneNumberVerifier'
import { CONTACT, PREFERRED_PHONE, PREFERRED_ALTERNATE_PHONE, ALTERNATE_ADDRESS_INTL, ALTERNATE_ADDRESS,
		 ALTERNATE_ADDRESS_PREFERRED, ADDRESS, ADDRESS_INTL, TOS } from './constants'

import { getStates, getCountries, updateProfile, saveApplication,
	resetSaved, sendConfirmationCode, clearChangedFields, getProfileData } from './../../actions/Profile'
import BottomBar from './BottomBar';
import { APP_ROUTES } from '../../../SocialApp/components/OneApp/constants';

var PAGE_DONE = '';

var ADDR = ADDRESS,
	ALT_ADDR = ALTERNATE_ADDRESS;

class Contact_Info extends React.Component{
	constructor(props) {
		super(props)
		this.state = {
			firstHalfDone: false,
		}
		this._initCountries = this._initCountries.bind(this)
		this._initStates = this._initStates.bind(this)
		this._saveAndContinue = this._saveAndContinue.bind(this)
	}

	_initStates(list){
		let stateField = _.find(ADDRESS, {name: 'state_id'});
		let alt_stateField = _.find(ALTERNATE_ADDRESS, {name: 'alternate_state_id'});

		stateField.options = list;
		alt_stateField.options = list;
	}

	_initCountries(list){
		// if user is intl, set countries list to ADDRESS_INTL options, else set it to just ADDRESS options
		var countryField = _.find(ADDRESS, {name: 'country_id'}),
			alt_countryField = _.find(ALTERNATE_ADDRESS, {name: 'alternate_country_id'}),
			countryField_intl = _.find(ADDRESS_INTL, {name: 'country_id'}),
			alt_countryField_intl = _.find(ALTERNATE_ADDRESS_INTL, {name: 'alternate_country_id'});

		countryField.options = list;
		alt_countryField.options = list;
		countryField_intl.options = list;
		alt_countryField_intl.options = list;
	}

	_saveAndContinue(e){
		e.preventDefault();
		let { dispatch, _profile } = this.props;

		if( _profile[PAGE_DONE] ){
			let form = _.omitBy( _profile, (v, k) => k.includes('list') );

			// if user has opted in for texting, send confirmation code
			if( _profile.txt_opt_in === 1 ) dispatch( sendConfirmationCode({..._profile}) );
			dispatch( saveApplication(form, 'contact', _profile.oneApp_step) );
		}
	}

	componentWillMount(){
		let { dispatch, _profile, route } = this.props;

		// init states
		if( _profile.init_states_done ) this._initStates( _profile.states_list.slice() );

		// init countries
		if( _profile.init_countries_done ) this._initCountries( _profile.countries_list.slice() );

		PAGE_DONE = route.id+'_form_done';
		dispatch( updateProfile({page: route.id}) );
		dispatch(clearChangedFields());
	}

	componentWillReceiveProps(np){
		let { dispatch, _profile, route } = this.props;

		// once states is initialized, update countryField.options wstates/
		if( _profile.init_states_done !== np._profile.init_states_done && np._profile.init_states_done ){
			this._initStates( np._profile.states_list.slice() );
		}

		// once countries is initialized, update countryField.options w/countries
		if( _profile.init_countries_done !== np._profile.init_countries_done && np._profile.init_countries_done ){
			this._initCountries( np._profile.countries_list.slice() );
		}

		// after saving, reset saved and go to next route
		if( np._profile.save_success !== _profile.save_success && np._profile.save_success ){
			dispatch( resetSaved() );

			// if opting in, show verifyCode, else skip that and go to next route
			if( np._profile.coming_from ) browserHistory.goBack();
			else {
				if(this.props.location.pathname.includes('social')) {
					let nextIncompleteStep = '';
					let formDonePropertyName = '';
					const routeIndex = APP_ROUTES.findIndex(r => r.id === route.id);
					if(routeIndex !== -1) {
						const nextStepsRoutes =  APP_ROUTES.slice(routeIndex+1);
						for(let nextRoute of nextStepsRoutes) {
							formDonePropertyName = `${nextRoute.id}_form_done`;
							if(_profile.hasOwnProperty(formDonePropertyName) && !_profile[formDonePropertyName]) {
								nextIncompleteStep = nextRoute;
								break;
							}
						}
					}
					const nextRoute = !!nextIncompleteStep ? nextIncompleteStep.id : route.next;
					this.props.history.push('/social/one-app/'+nextRoute + window.location.search)
				} else {
					browserHistory.push('/college-application/'+route.next + window.location.search)
				}
			};
			// else browserHistory.push('/college-application/'+route.next + window.location.search);
		}
	}

	render(){
		let { _profile, route } = this.props,
			{ firstHalfDone } = this.props;

		if( _.get(_profile, 'country_id') === 1 ){
			ADDR = ADDRESS;
			ALT_ADDR = ALTERNATE_ADDRESS;
		}else{
			ADDR = ADDRESS_INTL;
			ALT_ADDR = ALTERNATE_ADDRESS_INTL;
		}
		return (
			<DocumentTitle title={"Plexuss | College Application | "+route.name}>
					<div className="application-container">
						<form onSubmit={ this._saveAndContinue }>
							<div className="app-form-container">
								<div className="page-head head-line-height">Colleges need a way to communicate with you</div>

								{/* contact info */}
								{ CONTACT.map((n) => <TextField key={n.name} field={n} {...this.props} />) }

								{/* phone info */}
								<div>Preferred Phone Number</div>
								<div>
									{ PREFERRED_PHONE.map((pp) => <RadioField key={pp.id} field={pp} {...this.props} />) }
								</div>
								<PhoneNumberVerifier />
								<CheckboxField field={TOS} {...this.props} />

								{/* alternate phone info */}
								<div>Alternate Phone Number</div>
								<div>
									{ PREFERRED_ALTERNATE_PHONE.map((ap) => <RadioField key={ap.id} field={ap} {...this.props} />) }
								</div>
								{ (selectn('preferred_alternate_phone', _profile) && _profile.preferred_alternate_phone !== 'none') &&
									<PhoneNumberVerifier alternate={'alternate_phone'} /> }

								{/* address info */}
								<div className="sub-head">Address</div>
								<div>Permanent Home Address</div>
								{ ADDR.map((a) => {
									if( a.options ) return <SelectField key={a.name} field={a} {...this.props} />;
									return <TextField key={a.name} field={a} {...this.props} />;
								}) }

								{/* alternate address info */}
								<div>Alternate Address</div>
								<div>
									{ ALTERNATE_ADDRESS_PREFERRED.map((aa) => <RadioField key={aa.id} field={aa} {...this.props} />) }
								</div>
								{ selectn('alternate_address', _profile) === 'send' &&
									<div className="alternate-fields">
										{ ALT_ADDR.map((a) => {
											if( a.options ) return <SelectField key={a.name} field={a} {...this.props} />;
											return <TextField key={a.name} field={a} {...this.props} />;
										}) }
									</div>
								}

								{ (_.isBoolean(_profile.save_success) && !_profile.save_success) &&
									<div className="error-msg">{_profile.save_err_msg || 'Error.'}</div> }
							</div>
						</form>
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

export default connect(mapStateToProps)(Contact_Info);
