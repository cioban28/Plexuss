// /Application/CustomQuestions.js

import React from 'react'
import { connect } from 'react-redux'
import createReactClass from 'create-react-class'

import Heading from './Heading'
import Question from './Question'
import TitleField from './TitleField'
import RequireSection from './RequireSection'

import { updateSimpleProp } from './../../../actions/overviewActions'

const CUSTOM_Q = 'custom_questions';

const ANSWER = {answer: ''};

const QUESTION = {
	question: '',
	answer_type: '',
	answers: [
		{...ANSWER, id: 1},
		{...ANSWER, id: 2}
	],
};

const CustomQuestions = createReactClass({
	componentWillMount(){
		let { dispatch, route, overview } = this.props;

		if( !_.get(overview, CUSTOM_Q+'.length') ){
			dispatch( updateSimpleProp({[CUSTOM_Q]: [{...QUESTION, id: 1}]}) );
		}

		dispatch( updateSimpleProp({page: route.id}) );
	},

	_addQuestion(){
		let { dispatch, overview } = this.props,
			nextId = overview[CUSTOM_Q].length + 1;

		dispatch( updateSimpleProp({
			[CUSTOM_Q]: [...overview[CUSTOM_Q], {...QUESTION, id: nextId}]
		}) );
	},

	render(){
		let { route, overview, college } = this.props;

		return (
			<div>
				<Heading {...this.props} />

				<br />

				{ overview['require_'+route.id] &&
					<div>
						<TitleField college={college} />

						{ _.get(overview, CUSTOM_Q+'.length', 0) > 0 &&
							overview[CUSTOM_Q].map((p) => <Question key={p.id} quest={p} _name={CUSTOM_Q} {...this.props} />) }

						<div>
							<div className="add-another" onClick={ this._addQuestion }>Add another question</div>
						</div>
					</div>
				}

				<RequireSection {...this.props} />
			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		college: state.college,
		overview: state.overview,
	};
};

export default connect(mapStateToProps)(CustomQuestions);
