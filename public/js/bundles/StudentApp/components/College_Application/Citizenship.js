// /College_Application/Citizenship

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import { browserHistory } from 'react-router'
import DocumentTitle from 'react-document-title'

import TextField from './TextField'
import SaveButton from './SaveButton'
import NumberField from './NumberField'
import SelectField from './SelectField'
import SelectedItem from './SelectedItem'
import { CTZENSHIP, DUAL_CTZN, CTZN_YRS, LANG } from './constants'
import { getCountries, getLanguages, updateProfile, saveApplication, resetSaved, clearChangedFields } from './../../actions/Profile'
import BottomBar from './BottomBar';
import { APP_ROUTES } from '../../../SocialApp/components/OneApp/constants';

var PAGE_DONE = '';
var CTZN = [...CTZENSHIP, ...CTZN_YRS];

class Citizenship extends React.Component{
	constructor(props) {
		super(props)
		this.state = {
			countries: [],
			open: false,
		}
		this._saveAndContinue = this._saveAndContinue.bind(this)
	}

	_saveAndContinue(e){
		e.preventDefault();

		let { dispatch, _profile } = this.props;

		if( _profile[PAGE_DONE] ){
			let form = _.omitBy( _profile, (v, k) => k.includes('list') );
			dispatch( saveApplication(form, 'citizenship', _profile.oneApp_step) );
		}
	}

	componentWillMount(){
		let { dispatch, _profile, route } = this.props;

		if( _profile.init_countries_done ){
			let field = _.find(CTZENSHIP, {name: 'country_of_birth'}),
				copy = _profile.countries_list.slice();

			field.options = copy;
			DUAL_CTZN.options = copy;
		}

		if( _profile.init_languages_done ){
			let field = LANG;
			field.options = _profile.languages_list.slice();
		}

		PAGE_DONE = route.id+'_form_done';
		dispatch( updateProfile({page: route.id}) );
		dispatch(clearChangedFields());
	}

	componentWillReceiveProps(np){
		let { dispatch, route, _profile } = this.props,
			{ countries } = this.state;

		if( _profile.init_countries_done !== np._profile.init_countries_done && np._profile.init_countries_done ){
			let field = _.find(CTZENSHIP, {name: 'country_of_birth'}),
				copy = np._profile.countries_list.slice();

			field.options = copy;
			DUAL_CTZN.options = copy;
		}

		if( _profile.init_languages_done !== np._profile.init_languages_done && np._profile.init_languages_done ){
			let field = LANG;
			field.options = np._profile.languages_list.slice();
		}

		// if/when citizenship_status is changed, if 3, add dual citizenship field to form
		// else don't include it
		if( np._profile.citizenship_status && np._profile.citizenship_status == 2 ) CTZN = [...CTZENSHIP, DUAL_CTZN];
		else CTZN = [...CTZENSHIP];

		// after saving, reset saved and go to next route
		if( np._profile.save_success !== _profile.save_success && np._profile.save_success ){
			dispatch( resetSaved() );

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
			{ countries, open } = this.state;

		return (
			<DocumentTitle title={"Plexuss | College Application | "+route.name}>
				<div className="application-container">

					<form onSubmit={ this._saveAndContinue }>
						<div className="app-form-container">
							<div className="page-head">Citizenship Status</div>

							<div className="dir head-line-height">Colleges need additional information in order to make more informed decisions. On average colleges receive about 500 applications.</div>

							{ CTZN.map((c) => {
								if( c.options ) return <SelectField key={c.name} field={c} {...this.props} />;
								return <TextField key={c.name} field={c} {...this.props} />;
							}) }

							<SelectField field={ LANG } {...this.props} />

							<div className="selected-countries-container atLanguages">
								{ _.get(_profile, 'languages.length', 0) > 0 &&
									_profile.languages.map((m) =>
										<SelectedItem
											key={m}
											id={m}
											name={'languages'}
											static_list={'languages_list'}
											init_name={'init_languages_done'}
											{...this.props} />) }
							</div>

							<div className="err-container">
								{ CTZN_YRS.map((c) => <NumberField key={c.name} field={c} disableFocus={true} {...this.props} />) }
							</div>
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

export default connect(mapStateToProps)(Citizenship);
