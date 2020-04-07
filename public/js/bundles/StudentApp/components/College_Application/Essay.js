// /College_Application/Essay

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import { browserHistory } from 'react-router'
import DocumentTitle from 'react-document-title'

import SaveButton from './SaveButton'
import RadioField from './RadioField'
import DraftEditor from './../common/DraftEditor'

import { ESSAY_TOPICS, ESSAY_MIN, ESSAY_MAX } from './constants'
import { updateProfile, saveApplication, resetSaved, clearChangedFields } from './../../actions/Profile'
import { APP_ROUTES } from '../../../SocialApp/components/OneApp/constants';

var PAGE_DONE = '';

class Essay extends React.Component {
	constructor(props) {
		super(props)
		this._onSkip = this._onSkip.bind(this)
		this._saveAndContinue = this._saveAndContinue.bind(this)
	}

	_onSkip() {
        const { _profile, route } = this.props;
        const required_route = _.find(_profile.req_app_routes.slice(), {id: route.id});

        if(required_route) browserHistory.push('/college-application/'+required_route.next + window.location.search);
    }

	_saveAndContinue(e){
		e.preventDefault();

		let { dispatch, _profile } = this.props;
		let form = _.omitBy( _profile, (v, k) => k.includes('list') );

		dispatch( saveApplication(form, 'essay', _profile.oneApp_step) );
	}

	componentWillMount(){
		let { dispatch, route } = this.props;

		PAGE_DONE = route.id+'_form_done';
		dispatch( updateProfile({page: route.id}) );
		dispatch(clearChangedFields());
	}

	componentWillReceiveProps(np){
		let { dispatch, route, _profile } = this.props;

		// after saving, reset saved and go to next route
		if( np._profile.save_success !== _profile.save_success && np._profile.save_success ){
			dispatch( resetSaved() );

			if( np._profile.coming_from ) browserHistory.goBack();
			else{
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
	}

	_onSkip() {
		const { _profile, route } = this.props;
		const required_route = _.find(_profile.req_app_routes.slice(), {id: route.id});

		if(required_route) { this.props.location.pathname.includes("social") ? this.props.history.push('/social/one-app/'+route.next + window.location.search) : browserHistory.push('/college-application/'+route.next + window.location.search)};
	}

	_saveAndContinue(e){
		e.preventDefault();

		let { dispatch, _profile } = this.props;
		let form = _.omitBy( _profile, (v, k) => k.includes('list') );

		dispatch( saveApplication(form, 'essay', _profile.oneApp_step) );
	}

	render(){
		let { _profile, route } = this.props;
		return (
			<DocumentTitle title={"Plexuss | College Application | "+route.name}>
				<div className="application-container">

					<form onSubmit={ this._saveAndContinue }>
						<div className="app-form-container">
							<div className="page-head head-line-height">Personal Essay (optional)</div>

							<div className="essay-line-height">
								<p>Universities want to get to know who you are outside of the classroom. Tell us how your everyday life and experiences have helped you to develop personal values and goals, and how you continue to work toward achieving those goals. Be sure to also include what you do to overcome any obstacles that may impede your journey to success.</p>
								<p>Write an essay of no more than 650 words, using the prompt to inspire and structure your response. Remember: 650 words is your limit, not your goal. Use the full range if you need it, but don’t feel obligated to do so. <b>(The application won’t accept a response shorter than 250 words.)</b></p>
							</div>

							<br />

							<div>
								<p>You can type directly into the box, or you can paste text from another source.</p>

								{ _profile.init_done && (_profile.essay_content === null ? true : _profile.essay_content)  && <DraftEditor
															name={'essay_content'}
															action={updateProfile}
															minLen={ESSAY_MIN}
															maxLen={ESSAY_MAX}
															_state={_profile}
															editorState={ selectn('editorState', _profile) } /> }
							</div>

													{/* <div className='next-section-buttons'>
									<SaveButton
										_profile={_profile}
										page_done={PAGE_DONE} />

															<div className='section-skip-button' onClick={this._onSkip}>Skip</div>
													</div> */}
						</div>

					</form>

				</div>
			</DocumentTitle>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		_user: state._user,
		_profile: state._profile,
	};
};

export default connect(mapStateToProps)(Essay);
