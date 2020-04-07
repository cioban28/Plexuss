// /College_Application/Scores

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import { browserHistory } from 'react-router'
import DocumentTitle from 'react-document-title'
import SaveButton from './SaveButton'
import SelectField from './SelectField'
import { START_TERM, START_YR, GET_YEARS, INTERESTED_SCHOOL_TYPE } from './constants'
import { updateProfile, saveApplication, resetSaved, clearChangedFields } from './../../actions/Profile'
import BottomBar from './BottomBar';
import { APP_ROUTES } from '../../../SocialApp/components/OneApp/constants';
var PAGE_DONE = '';

class StartingWhen extends React.Component{
	constructor(props){
		super(props)
		this.state = {
			converterOpen: false,
		}
		this._saveAndContinue = this._saveAndContinue.bind(this)
	}

	_saveAndContinue(e){
		e.preventDefault();

		let { dispatch, _profile } = this.props;

		if( _profile[PAGE_DONE] ){
			let form = _.omitBy( _profile, (v, k) => k.includes('list') );
			dispatch( saveApplication(form, 'start', _profile.oneApp_step) );
		}
	}

	componentWillMount(){
		let { dispatch, route } = this.props;

		PAGE_DONE = route.id+'_form_done';
		dispatch( updateProfile({page: route.id}) );
		dispatch( clearChangedFields());

		START_YR.options = GET_YEARS(10, 20);
	}

	componentWillReceiveProps(np){
		let { dispatch, route, _profile } = this.props;

		// after saving, reset saved and go to next route
		if( np._profile.save_success !== _profile.save_success && np._profile.save_success ){
			dispatch( resetSaved() );
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
		}
	}

	render(){
		let { _profile, route } = this.props;

		return (
			<DocumentTitle title={"Plexuss | College Application | "+route.name}>
				<div className="application-container">
					<form onSubmit={ this._saveAndContinue }>
						<div className="app-form-container">

							<div className="page-head head-line-height">When do you plan to enroll?</div>

							<div className="start-wrapper new-starting-when-wrapper">
								<SelectField field={ START_TERM } {...this.props} />
								<SelectField field={ START_YR } {...this.props} />
								<SelectField field={ INTERESTED_SCHOOL_TYPE } {...this.props} />
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

export default connect(mapStateToProps)(StartingWhen);
