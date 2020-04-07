// /Application/CustomQuestionsSequel.js

import React from 'react'
import { connect } from 'react-redux'
import createReactClass from 'create-react-class'

import Heading from './Heading'
import RequireSection from './RequireSection'
import Tooltip from './../../../../utilities/tooltip'
import ProgramHeader from './../International/components/programHeader'

import { updateSimpleProp } from './../../../actions/overviewActions'

import { ADDTL_IMMIGRATION, ADDTL_LEGAL, ADDTL_FORMS, ADDTL_CONTACT, ADDTL_FINANCES, ADDTL_ENGLISH_EXAMS,
	ADDTL_EDU, ADDTL_GRAD, ADDTL_ETHNICITY, ADDTL_HEALTH } from './../../../../StudentApp/components/College_Application/constants'

import * as Q from './../../../../StudentApp/components/College_Application/questionConstants'

var ALL_ADDTL_OBJ = {...Q._getCommonQuestionsAsObject()},
	ALL_ADDTL_ARR = []; // specific questions added after initProfile

const CustomQuestionsSequel = createReactClass({
	getInitialState(){
		return {
			categories: [],
			all_count: 0,
		};
	},

	componentWillMount(){
		let { dispatch, route, overview, user } = this.props;

		dispatch( updateSimpleProp({page: route.id}) ); // update page on willMount
		if( _.isEmpty(overview.custom_fields) ) dispatch( updateSimpleProp({custom_fields: {}}) ); // set custom_fields if empty

		// init specific questions for this school, if user's profile is already initialized
		if( user.initProfile ) this._getSpecificQuestions(user.aor_id, user.org_school_id);
	},

	componentWillReceiveProps(np){
		let { dispatch, overview: _o, user: _u, route } = this.props,
			{ overview: _no, user: _nu, intl: _ni } = np;

		// once initProfile is done, if user has an aor_id, add specific question for this aor
		if( _u.initProfile !== _nu.initProfile && _nu.initProfile ) this._getSpecificQuestions(_nu.aor_id, _nu.org_school_id);
	},

	_getSpecificQuestions(aor_id, school_id){
		if( aor_id && Q['SPECIFIC_FOR_AOR_'+aor_id] ) ALL_ADDTL_ARR = [...Q['SPECIFIC_FOR_AOR_'+aor_id]]; // if school is aor, show aor specific questions
		else if( Q['SPECIFIC_FOR_SCHOOL_'+school_id] ) ALL_ADDTL_ARR = [...Q['SPECIFIC_FOR_SCHOOL_'+school_id]]; // if we have school specific questions, show
	},

	render(){
		let { dispatch, route, overview, intl } = this.props,
			{ categories, all_count } = this.state;

		return (
			<div>
				<ProgramHeader />
				<Heading {...this.props} />
				<br />

				<div className="cat-top">
					&nbsp;

					<div className="col-lab opt">Optional</div>
					<div className="col-lab req">Required</div>
				</div>

				<br />

				<div className="question-list">
					{
						ALL_ADDTL_ARR.length > 0 ?
						ALL_ADDTL_ARR.map(q => <QuestionSequel key={q.name} question={q} {...this.props} />)
						: <span>No custom questions</span>
					}
				</div>

				<br />

				<RequireSection noRequire={true} {...this.props} />

				<br />
			</div>
		);
	}
});

const QuestionSequel = createReactClass({
	_update(e){
		let { dispatch, overview, question, intl, route } = this.props,
			section_name = intl.activeProgram+'_require_'+route.id,
			question_name = intl.activeProgram+'_'+question.name,
			checked = e.target.checked,
			custom_fields = {
				...overview.custom_fields,
				[question_name]: checked,
			};

		if( checked ) custom_fields[question_name+'_optional'] = !checked;

		dispatch( updateSimpleProp({custom_fields}) );
	},


	_updateOptional(e){
		let { dispatch, overview, question, intl, route } = this.props,
			section_name = intl.activeProgram+'_require_'+route.id,
			question_name = intl.activeProgram+'_'+question.name,
			checked = e.target.checked,
			custom_fields = {
				...overview.custom_fields,
				[question_name+'_optional']: checked,
			};

		if( checked ) custom_fields[question_name] = !checked;

		dispatch( updateSimpleProp({custom_fields}) );
	},

	_updateOptional(e){
		let { question } = this.props;
		this._updateFields(e.target.checked, true);
	},

	render(){
		let { dispatch, overview, question, user, intl } = this.props,
			name = intl.activeProgram+'_'+question.name;

		return (
			<label>

				<span>{ question.label || 'no label?' }</span>

				{ question.tip &&
					<Tooltip toolTipStyling={styles.tt} tipId={'addtl_tip'}>
						{ question.tip.map(t => <div key={t}>{t}</div>) }
					</Tooltip> }

				<input
					id={name+'_optional'}
					type="checkbox"
					name={name+'_optional'}
					checked={ !!overview.custom_fields[name+'_optional'] }
					value={ overview.custom_fields[name+'_optional'] || '' }
					onChange={ this._updateOptional } />

				<input
					id={name}
					type="checkbox"
					name={name}
					checked={ !!overview.custom_fields[name] }
					value={ overview.custom_fields[name] || '' }
					onChange={ this._update } />

			</label>
		);
	}
});

const styles = {
	tt: {
		color: '#797979',
		border: '1px solid #797979',
	}
};

const mapStateToProps = (state, props) => {
	return {
		intl: state.intl,
		user: state.user,
		overview: state.overview,
	};
};

export default connect(mapStateToProps)(CustomQuestionsSequel);
