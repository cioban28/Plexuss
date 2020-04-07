// /College_Application/Scores

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import { browserHistory } from 'react-router'
import DocumentTitle from 'react-document-title'
import ExamForm from './ExamForm'
import SaveButton from './SaveButton'
import RadioField from './RadioField'
import ScoresInputted from './ScoresInputted'
import CustomModal from './../common/CustomModal'

import { REPORT, US_BTNS, INTL_BTNS } from './constants'
import { getPrioritySchools } from './../../actions/Intl_Students'
import { updateProfile, saveApplication, resetSaved, toggleExamReporting, clearChangedFields } from './../../actions/Profile'
import BottomBar from './BottomBar'
import { APP_ROUTES } from '../../../SocialApp/components/OneApp/constants';

var PAGE_DONE = '';

const error_msg = 'To continue, please ensure all changes to exams have valid entries and are completely filled out.';

class Scores extends React.Component {
	constructor(props) {
		super(props)
		this.state = {
			show_modal: true,
		}
		this._closeModal = this._closeModal.bind(this)
		this._saveAndContinue = this._saveAndContinue.bind(this)
	}

	_closeModal(e){
		this.setState({ show_modal: false });
	}

	_saveAndContinue(e){
		e.preventDefault();

		let { dispatch, _profile, route } = this.props;
		console.log(_profile[PAGE_DONE])
		console.log("props", this.props);

		if( _profile[PAGE_DONE] && !_profile.skip_score_saving ){
			let form = _.omitBy( _profile, (v, k) => k.includes('list') && !k.includes('listening') );
			dispatch( saveApplication(form, 'scores', _profile.oneApp_step) );
		}else if(_profile[PAGE_DONE]){
			if ( window.location.pathname.includes('social')  )
			{
				this.props.history.push('/social/one-app/'+route.next + window.location.search);
       }
    	else
    	{
				browserHistory.push('/college-application/' + route.next + window.location.search);
			}
		}
	}

	componentWillMount(){
		let { dispatch, _profile, route } = this.props;

		PAGE_DONE = route.id+'_form_done';
		dispatch( updateProfile({ page: route.id }) );
		dispatch( clearChangedFields());

		if ( _profile.invalid_exam_validation_modal ) {
			this.setState({ show_modal: true });
		}
	}

	componentWillReceiveProps(np){
		let { dispatch, route, _profile } = this.props;

		// after saving, reset saved and go to next route
		if( np._profile.save_success !== _profile.save_success && np._profile.save_success ){
			dispatch( resetSaved() );

			// get new set of priority schools after saving
			dispatch( getPrioritySchools() );

			if (window.location.pathname.includes('social')) {
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
    		if( np._profile.coming_from ) browserHistory.goBack();
				else browserHistory.push('/college-application/'+route.next  + window.location.search);
    	}
		}

		if ( np._profile.self_report && np._profile.self_report !== _profile.self_report ){
			let skip_score_saving = np._profile.self_report == 'no' ? true : false;
			dispatch( toggleExamReporting({ skip_score_saving, route }) );
		}
	}

	_closeModal(e){
		this.setState({ show_modal: false });
	}

	_saveAndContinue(e){
		e.preventDefault();

		let { dispatch, _profile, route } = this.props;

		if( _profile[PAGE_DONE] && !_profile.skip_score_saving ){
			let form = _.omitBy( _profile, (v, k) => k.includes('list') && !k.includes('listening') );
			dispatch( saveApplication(form, 'scores', _profile.oneApp_step) );
		}else if(_profile[PAGE_DONE]){
			if ( window.location.pathname.includes('social')  )
			{
				this.props.history.push('/social/one-app/'+route.next + window.location.search);
       }
    	else
    	{
				browserHistory.push('/college-application/' + route.next + window.location.search);
			}
		}

	}

	render(){
		let { _profile, route, dispatch } = this.props,
			show_exams = _profile.self_report == 'no' ? false : true,
			show_modal = this.state.show_modal;

		return (
			<DocumentTitle title={"Plexuss | College Application | "+route.name}>
				<div className="application-container full scores-container">
					<div className="scores-left-side">
						<form onSubmit={ this._saveAndContinue }>
							<div className="app-form-container">
								<div className="page-head head-line-height">Scores</div>

								<div className="new-text">In addition to sending official score reports as required by colleges, do you wish to self-report scores or future test dates for any of the following standardized tests: ACT, SAT/SAT Subject, AP, IB, TOEFL, PTE Academic, and IELTS?</div>

								{ REPORT.map((r) => <RadioField key={r.id} field={r} {...this.props} />) }
								{ show_exams &&
									<div className='score-container'>

										<div className="sub-head">College Entrance Exams</div>
										<div className="exam-btns">
											{ US_BTNS.map((b) => <ExamBtn key={b.name} type='us' btn={b} {...this.props} />) }
										</div>

										{ selectn('us_active_exam', _profile) && <ExamForm type='us' {...this.props} /> }
										{ !selectn('us_active_exam', _profile) && <br />}

										<div className="sub-head">International Students</div>
										<div className="exam-btns">
											{ INTL_BTNS.map((ib) => <ExamBtn key={ib.name} type='intl' btn={ib} {...this.props} />) }
										</div>

										{ selectn('intl_active_exam', _profile) && <ExamForm type='intl' {...this.props} /> }
										{ !selectn('intl_active_exam', _profile) && <br />}

									</div>
								}
							</div>
						</form>
					</div>
					<div className="scores-right-side">
							<ScoresInputted _profile={ _profile } dispatch={ dispatch } route={ route } />
					</div>
					{ show_modal &&
						<CustomModal closeMe={ () => { this._closeModal() } }>
							<div className="modal invalid-exam-modal">
								<div className="closeMe" onClick={ () => { this._closeModal() } }>&times;</div>
								<h4>It looks like you have invalid exam scores. Please read the instructions and examples on how to continue your application.</h4>
								<br />
								<h3 className="instructions-text">Instructions</h3>
								<ul className="modal-exam-instructions">
									<li><b>1.</b> Click on a <span className="red-btns-text">red</span> exam button. (<span className="red-btns-text">red</span> indicates the exam has invalid entries)</li>
									<li><img src="/images/example-invalid-btn.png" /></li>
									<li><b>2.</b> Replace invalid entries with valid scores <b>OR</b> click the <span className="clear-btn-text">clear</span> button to clear the exam entries.</li>
									<li><img src="/images/example-invalid-field.png" /></li>
								</ul>
							</div>
						</CustomModal>
					}
				</div>
			</DocumentTitle>
		);
	}
}

class ExamBtn extends React.Component{
	constructor(props) {
		super(props)
		this._examOn = this._examOn.bind(this)
	}

	_examOn(){
		let { _profile, dispatch, type, btn } = this.props,
			new_active_exam = {};

		if (type) {
			// Toggle off if same exam selected
			if (btn == _profile[type + '_active_exam']) {
				new_active_exam[type + '_active_exam'] = null;
				dispatch( updateProfile(new_active_exam) );
			}
			// Else change active exam
			else {
				new_active_exam[type + '_active_exam'] = btn;
				dispatch( updateProfile(new_active_exam) );
			}
		}
	}

	render(){
		let { _profile, btn, type } = this.props,
			is_active = selectn(type + '_active_exam.name', _profile) === btn.name,
			is_valid = _profile[btn.name + '_validated'] != null && _profile[btn.name + '_validated'],
			complete_check_mark = is_valid ? '\u2714' : '';

		let cssClasses = (is_active ? 'active' : '') +
						 (is_valid ? ' complete' : _profile[btn.name + '_validated'] != null ? ' invalid' : '');

		return (
			<div onClick={ this._examOn } className={ cssClasses }>{ btn.name }<span>{ complete_check_mark }</span> </div>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		_profile: state._profile,
	};
};

export default connect(mapStateToProps)(Scores);
