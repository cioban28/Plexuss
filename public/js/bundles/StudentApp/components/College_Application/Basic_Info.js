// /College_Application/Basic_Info

import $ from 'jquery'
import React from 'react'
import { connect } from 'react-redux'
import { browserHistory } from 'react-router'
import DocumentTitle from 'react-document-title'

import TextField from './TextField'
import SaveButton from './SaveButton'
import SelectField from './SelectField'
import SelectedItem from './SelectedItem'
import NameConfirmation from './NameConfirmation'

import { getPrioritySchools } from './../../actions/Intl_Students'
import { IN_COLLEGE, SCHOOL_NAME, BASIC_SELECT_FIELDS_1, BASIC_SELECT_FIELDS_2 } from './constants'
import { getMajors, getSchoolsBasedOnSchoolType, updateProfile, saveApplication, resetSaved, AmplitudeSaveApplication, clearChangedFields } from './../../actions/Profile'
import leftPart from '../../../SocialApp/components/Messages/leftPart';
import BottomBar from './BottomBar';
import { APP_ROUTES } from '../../../SocialApp/components/OneApp/constants';
var PAGE_DONE=''

var ALL_FIELDS = [
	IN_COLLEGE,
	SCHOOL_NAME,
	...BASIC_SELECT_FIELDS_1,
	BASIC_SELECT_FIELDS_2,
];

class Basic_Info extends React.Component {
	constructor(props) {
		super(props)
		this.state = {
			selected: true,
		}
		this._verifyMajorsList = this._verifyMajorsList.bind(this)
		this._saveAndContinue = this._saveAndContinue.bind(this)
		this._closeDropdown = this._closeDropdown.bind(this)
	}

	_closeDropdown(e){
		let { selected } = this.state,
			target = $(e.target);

		if( target.closest('.dropdown').length === 0 && !selected ) this.setState({selected: true});
	}

	_saveAndContinue(e=undefined, callback=undefined){
		!!e && e.preventDefault();

		let { dispatch, _profile } = this.props;
		let currentPage = _profile.oneApp_step;
		if( !_profile[PAGE_DONE] ){
			_.each(ALL_FIELDS, (field) => {
				// if this page is NOT done, loop through each field finding the invalid fields and triggering blur which will show the error
				if( _.isNull(_profile[field.name]) || _.isUndefined(_profile[field.name]) ){
					let tm = document.getElementById(field.name);
					tm.focus();
					tm.blur();
				}
			} );
		}else{
			let form = _.omitBy( _profile, (v, k) => k.includes('list') );
			dispatch( saveApplication(form, 'basic', currentPage) );
			this.props.unsetSubmit();
		}
	}

	// Checks to ensure major list options are set and available
	_verifyMajorsList() {
		let props = this.props, { dispatch } = props,
			majors_field = _.find(BASIC_SELECT_FIELDS_1, {name: 'majors_arr'});

		if ( props._profile.init_majors_done && props._profile.majors_list ) {
			majors_field.options = props._profile.majors_list.slice();
		} else {
			dispatch ( getMajors() );
		}
	}

	componentWillMount(){
		let { dispatch, _profile, route } = this.props;

		PAGE_DONE = route.id+'_form_done';
		dispatch( updateProfile({page: route.id}) );
		dispatch(clearChangedFields());

		document.addEventListener('click', this._closeDropdown);
	}

	componentWillUnmount(){
		document.removeEventListener('click', this._closeDropdown);
	}

	componentWillReceiveProps(np){
		this.props.submitClicked && this._saveAndContinue()
		let { dispatch, _profile, route } = this.props;

		if( _profile.init_majors_done !== np._profile.init_majors_done && np._profile.init_majors_done ){
			let majors_field = _.find(BASIC_SELECT_FIELDS_1, {name: 'majors_arr'});
			majors_field.options = np._profile.majors_list.slice();
		}

		// after saving, reset saved and go to next route
		if( np._profile.save_success !== _profile.save_success && np._profile.save_success ){
			dispatch( resetSaved() );

			// get new set of priority schools after saving
			dispatch( getPrioritySchools() );

			// if coming_from is set, means redirected from an unnatural path so go back where we came from

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
		}

		if( np._profile.schoolName !== _profile.schoolName && np._profile.schoolName ){
			this.state.selected = false;
			dispatch( getSchoolsBasedOnSchoolType({
				search_for_school: np._profile.schoolName,
				in_college: +np._profile.in_college,
			}) );
		}

		// if current state of schoolName is undefined, but next state is not undefined, then make selected true to close the dropdown
		if( _.isUndefined(_profile.schoolName) && !_.isUndefined(np._profile.schoolName) ) this.state.selected = true;
	}

	render(){
		let { _profile, route } = this.props,
			{ selected } = this.state;
		this._verifyMajorsList();
		return (
			<DocumentTitle title={"Plexuss | College Application | "+route.name}>
				<div>
					<div className="application-container">
						<form >
							<div className="app-form-container">
								<div className="page-head head-line-height">Let's begin the Application Process</div>

								<SelectField field={ IN_COLLEGE } {...this.props} />

								<div className="school-name-container">
									<TextField field={ SCHOOL_NAME } {...this.props} />
									{ !selected && <div className="dropdown">
														{ (_.get(_profile, 'list_of_schools_for_schoolName.length', 0) > 0 &&
															_.isArray(_profile.list_of_schools_for_schoolName)) &&
															_profile.list_of_schools_for_schoolName.map((s) => <SchoolOption
																													key={s.id}
																													school={s}
																													{...this.props}
																													selected={() => this.setState({selected: true})} />) }
													</div> }
								</div>

								<div>
									{ BASIC_SELECT_FIELDS_1.map((b) => {
										if( b.name === 'schoolName' ) return <TextField key={b.name} field={b} {...this.props} />;
										return <SelectField key={b.name} field={b} {...this.props} />;
									}) }
								</div>

								<div className="selected-countries-container atMajors">


									{  _.get(_profile, 'majors_arr.length', 0) > 0 && !_.isEmpty(_profile.majors_list) &&
											_profile.majors_arr.map((m) => {
												return(
													<SelectedItem
														key={m}
														id={m}
														name={'majors_arr'}
														static_list={'majors_list'}
														init_name={'init_majors_done'}
														item = {m}
														{...this.props} />)}
												)
										}
								</div>

								<SelectField field={BASIC_SELECT_FIELDS_2} {...this.props} />
							</div>
						</form>
					</div>
				</div>
			</DocumentTitle>
		);
	}
}

class SchoolOption extends React.Component{
	constructor(props) {
		super(props)
		this._setSchoolName = this._setSchoolName.bind(this)
	}
	_setSchoolName(){
		let { dispatch, school, selected } = this.props;

		selected(); // closes the dropdown on selection
		dispatch( updateProfile({schoolName: school.name}) );
	}

	render(){
		let { school } = this.props;

		return (
			<div onClick={ this._setSchoolName }>{ school.name }</div>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		_user: state._user,
		_profile: state._profile,
	};
};

export default connect(mapStateToProps)(Basic_Info);
