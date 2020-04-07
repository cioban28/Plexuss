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
	ALL_ADDTL_ARR = [...Q._getCommonQuestionsAsArray()]; // specific questions added after initProfile

const CustomQuestionsSequel = createReactClass({
	getInitialState(){
		return {
			categories: [],
			all_count: 0,
		};
	},

	componentWillMount(){
		let { dispatch, route, overview } = this.props;

		this._getCategories();

		dispatch( updateSimpleProp({page: route.id}) ); // update page on willMount
		if( _.isEmpty(overview.additional_fields) ) dispatch( updateSimpleProp({additional_fields: {}}) ); // set additional_fields if empty
	},

	_getCategories(){
		var cats = [];

		_.each(ALL_ADDTL_OBJ, (arr, val) => {
			let name = val.split('_').slice(1).join(' ').toLowerCase(),
				count = arr.length;

			cats.push({name, count, val});
		});

		this.state.categories = cats;
		this._buildAllCount();
	},

	_buildAllCount(){
		this.state.all_count = ALL_ADDTL_ARR.length || 0;
	},

	_getQuestions(){
		let { custom_category: cat } = this.props.overview,
			{ user } = this.props;

		if( !cat ) return [];
		if( cat === 'all' ) return [...ALL_ADDTL_ARR]; // else just return all questions without specific ones
		return ALL_ADDTL_OBJ[cat];
	},

	render(){
		let { dispatch, route, overview, intl } = this.props,
			{ categories, all_count } = this.state,
			questions = this._getQuestions();

		return (
			<div>
				<ProgramHeader />
				<Heading {...this.props} />
				<br />

				<div className="cat-top">
					<select
						className="custom-category"
						value={ overview.custom_category || '' }
						onChange={ e => dispatch( updateSimpleProp({custom_category: e.target.value}) ) }>
							<option value="" disabled="disabled">Select a category</option>
							{ categories.map(c => <option key={c.name} value={c.val}>{c.name} ({c.count})</option>) }
							<option value="all">All Categories ({all_count})</option>
					</select>

					<div className="col-lab opt">Optional</div>
					<div className="col-lab req">Required</div>
				</div>

				<div className="question-list">
					{ questions && questions.map(q => <QuestionSequel key={q.name} question={q} {...this.props} />) }
				</div>

				<br />

				<RequireSection noRequire={true} {...this.props} />

				<br />
				<div>Have another question thats not listed? Submit your question <a>here</a>.</div>
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
			additional_fields = {
				...overview.additional_fields,
				[question_name]: checked,
			};

		if( checked ) additional_fields[question_name+'_optional'] = !checked;

		dispatch( updateSimpleProp({additional_fields}) );
	},

	_updateOptional(e){

		let { dispatch, overview, question, intl, route } = this.props,
			section_name = intl.activeProgram+'_require_'+route.id,
			question_name = intl.activeProgram+'_'+question.name,
			checked = e.target.checked,
			additional_fields = {
				...overview.additional_fields,
				[question_name+'_optional']: checked,
			};

		if( checked ) additional_fields[question_name] = !checked;


		dispatch( updateSimpleProp({additional_fields}) );
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
					checked={ !!overview.additional_fields[name+'_optional'] }
					value={ overview.additional_fields[name+'_optional'] || '' }
					onChange={ this._updateOptional } />

				<input
					id={name}
					type="checkbox"
					name={name}
					checked={ !!overview.additional_fields[name] }
					value={ overview.additional_fields[name] || '' }
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
