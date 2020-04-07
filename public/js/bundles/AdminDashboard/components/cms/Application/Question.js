// /Application/Question.js

import React from 'react'
import createReactClass from 'create-react-class'

import { updateSimpleProp } from './../../../actions/overviewActions'

const FIELD_TYPE_MAP = {
	'short answer': 'text',
	'paragraph': 'textarea',
	'multiple choice 1': 'radio',
	'multiple choice n': 'checkbox',
};

const QUESTION_TYPES = [
	{name: 'short answer', label: 'Short Answer'},
	{name: 'paragraph', label: 'Paragraph'},
	{name: 'multiple choice 1', label: 'Multiple Choice (Select One)'},
	{name: 'multiple choice n', label: 'Multiple Choice (Select Multiple)'},
];

export default createReactClass({
	_editQuestion(e){
		let { dispatch, overview, quest, _name } = this.props,
			val = e.target.value,
			name = e.target.getAttribute('name'),
			copy = overview[_name].slice();

		let updatedPrompts = copy.map((p) => p.id === quest.id ? {...p, [name]: val} : p);

		dispatch( updateSimpleProp({[_name]: updatedPrompts}) );
	},

	_editType(e){
		let { dispatch, overview, quest, _name } = this.props,
			val = e.target.value,
			name = e.target.getAttribute('name'),
			copy = overview[_name].slice();

		let updatedPrompts = copy.map((p) => p.id === quest.id ? {...p, [name]: val, element: FIELD_TYPE_MAP[val]} : p);

		dispatch( updateSimpleProp({[_name]: updatedPrompts}) );
	},

	_deleteQuestion(){
		let { dispatch, overview, quest, _name } = this.props,
			copy = overview[_name].slice();

		let found = _.find(copy, {id: quest.id});

		if( found ){
			let newQuestions = _.reject(copy, {id: found.id});
			dispatch( updateSimpleProp({[_name]: newQuestions}) );
		}
	},

	_addAnswer(e){
		let { dispatch, overview, quest, _name } = this.props,
			copy = [...overview[_name]];

		let newAnswers = copy.map((qu) => {
			if( qu.id === quest.id ){
				let id = qu.answers.length + 1;
				return {...qu, answers: [...qu.answers, {id, answer: ''}]};
			}

			return qu;
		});

		dispatch( updateSimpleProp({[_name]: newAnswers}) );
	},

	render(){
		let { overview, quest } = this.props;

		return (
			<div className="prompt">
				<div className="prompt-head">
					<div>Question #{quest.id || ''}</div>
					{ quest.id !== 1 && <div><span onClick={ this._deleteQuestion }>Delete Question</span></div> }
				</div>

				<div className="prompt-body question">
					<label htmlFor={'_'+quest.name}>Question</label>
					<input
						id={'_'+quest.name}
						type="text"
						name="question"
						value={ quest.question || '' }
						placeholder="Question Text"
						onChange={ this._editQuestion } />
				</div>

				<div className="prompt-body question">
					<label htmlFor={'_'+quest.name+'_type'}>Answer Type</label>
					<select
						name="answer_type"
						value={ quest.answer_type || '' }
						onChange={ this._editType }>
						<option value='' disabled="disabled">Select type...</option>
						{ QUESTION_TYPES.map((q) => <option key={q.name} value={q.name}>{q.label}</option>) }
					</select>
				</div>

				{ quest.answer_type.includes('multiple') &&
					quest.answers.map((a) => <MultipleChoice key={a.id} answer={a} {...this.props} />) }

				{ quest.answer_type.includes('multiple') && <div className="add-answer" onClick={ this._addAnswer }>+ Add another answer</div> }
			</div>
		);
	}
});

const MultipleChoice = createReactClass({
	_editAnswer(e){
		let { dispatch, overview, quest, _name, answer } = this.props,
			val = e.target.value,
			name = e.target.getAttribute('name'),
			copy = overview[_name].slice();

		let newAnswers = copy.map((qu) => {
			if( qu.id === quest.id ){
				let answer_copy = qu.answers.slice(),
					found = _.find(answer_copy, {id: answer.id});

				return {
					...qu,
					answers: qu.answers.map((an) => an.id === answer.id ? {...an, answer: val} : an)
				};
			}

			return qu;
		});

		dispatch( updateSimpleProp({[_name]: newAnswers}) );
	},

	_deleteAnswer(){
		let { dispatch, overview, quest, _name, answer } = this.props,
			copy = [...overview[_name]];

		let newAnswers = copy.map((qu) => {
			if( qu.id === quest.id ){
				let answer_copy = qu.answers.slice(),
					found = _.find(answer_copy, {id: answer.id});

				return found ? {...qu, answers: _.reject(answer_copy, {id: found.id})} : qu;
			}

			return qu;
		});

		dispatch( updateSimpleProp({[_name]: newAnswers}) );
	},

	render(){
		let { answer, quest } = this.props;

		return (
			<div className="prompt-body answer question">
				{ answer.id === 1 && <label>Answer</label> }

				<input
					name={ (quest.id || '')+(quest.element || '') }
					type={ quest.element || 'text' } />

				<input
					type={'text'}
					name="answer"
					value={ answer.answer || '' }
					placeholder={ "Answer #"+answer.id }
					onChange={ this._editAnswer } />

				{ answer.id > 2 && <div className="remove" onClick={ this._deleteAnswer }>&times;</div> }
			</div>
		);
	}
});
