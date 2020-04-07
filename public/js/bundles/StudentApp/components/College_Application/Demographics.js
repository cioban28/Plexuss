import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import { browserHistory } from 'react-router'
import DocumentTitle from 'react-document-title'
import find from 'lodash/find';
import omitBy from 'lodash/omitBy';

import SaveButton from './SaveButton'
import DraftEditor from './../common/DraftEditor'

import { DEMOGRAPHIC_FIELDS }   from './constants'

import {updateProfile, getAllReligions, getAllEthnicities, resetSaved, clearChangedFields } from './../../actions/Profile'
import SelectField from './SelectField';
import { APP_ROUTES } from '../../../SocialApp/components/OneApp/constants';

var PAGE_DONE = '';

const _ = {
	find: find,
	omitBy: omitBy,
}
class Demographics extends React.Component {
	  state = {
		}

constructor(props){
	super(props);
	this._saveAndContinue = this._saveAndContinue.bind(this);
	this._fillOptionsInFields = this._fillOptionsInFields.bind(this);
	}

	componentWillMount(){
		let { dispatch, route } = this.props;

		PAGE_DONE = route.id+'_form_done';
		dispatch( updateProfile({page: route.id}) );

		dispatch(clearChangedFields());
		dispatch(getAllReligions());
		dispatch(getAllEthnicities());
	}

	componentDidMount(){
		let { dispatch, route } = this.props;

		PAGE_DONE = route.id+'_form_done';
		dispatch( updateProfile({page: route.id}) );
	}



	componentWillReceiveProps(np){
		let { dispatch, route, _profile } = this.props;
		// after saving, reset saved and go to next route
		if( np._profile.save_success !== _profile.save_success && np._profile.save_success ){
			dispatch( resetSaved() );

			if (np._profile.coming_from ) browserHistory.goBack();
			else {
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
			}
		}
	}

    _onSkip() {
        const { _profile, route } = this.props;
        const required_route = _.find(_profile.req_app_routes.slice(), {id: route.id});

        if(required_route)
        {

         	if ( window.location.pathname.includes('social')  )
					{
						this.props.history.push('/social/one-app/'+required_route.next + window.location.search);
		       }
		    	else
		    	{
         		browserHistory.push('/college-application/'+required_route.next + window.location.search);
		    	}
        }
    }

	_saveAndContinue(e){
		e.preventDefault();

		let { dispatch, _profile } = this.props;
		let form = _.omitBy( _profile, (v, k) => k.includes('list') );

		dispatch( saveApplication(form, 'demographics', _profile.oneApp_step) );
	}

	_fillOptionsInFields() {
		let { dispatch, _profile } = this.props;
		ethnicity_field = _.find(DEMOGRAPHIC_FIELDS, {name: 'ethnicity'});
		ethnicity_field.options = this.props._profile.ethnicities_list.slice();

		religion_field = _.find(DEMOGRAPHIC_FIELDS, {name: 'religion'});
		religion_field.options = this.props._profile.religions_list.slice();

	}

	render(){
		let { _profile, _user, route } = this.props;

		return (
			<DocumentTitle title={"Plexuss | College Application | "+route.name}>
				<div className="application-container">

						<form onSubmit={ this._saveAndContinue }>
							<div className="app-form-container">
								<div className="page-head head-line-height">Demographics (optional)</div>
								<div className="dir">Some scholarships need this information</div>
								<div>
										{ DEMOGRAPHIC_FIELDS.map((d) => {
											if (d.name == "ethnicity" && !!_profile.ethnicities_list[0] && _profile.ethnicities_list[0].length > 0) {
												d.options = _profile.ethnicities_list[0];
											}
											else if (d.name == "religion" && !!_profile.religions_list[0] && _profile.religions_list[0].length > 0) {
												d.options = _profile.religions_list[0].slice(0, -1);
											}
											return <SelectField key={d.name} field={d} {...this.props} />;
										}) }
									</div>
							</div>
						</form>


				</div>
			</DocumentTitle>
		);
	}
}

const mapStateToProps = (state,  props) => {
	return {
		_user: state._user,
		_profile: state._profile,
	}
}

export default connect(mapStateToProps)(Demographics);
