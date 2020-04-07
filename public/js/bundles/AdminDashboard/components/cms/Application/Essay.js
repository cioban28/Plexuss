// /Application/Essay.js

import React from 'react'
import { connect } from 'react-redux'
import createReactClass from 'create-react-class'

import Prompt from './Prompt'
import Heading from './Heading'
import TitleField from './TitleField'
import RequireSection from './RequireSection'
import ProgramHeader from './../International/components/programHeader'

import { updateSimpleProp } from './../../../actions/overviewActions'

const ESSAY = {
	name: 'essay_prompt',
	topic: '',
}

const ESSAY_PROMPTS = 'essay_prompts';

const Essay = createReactClass({
	componentWillMount(){
		let { dispatch, route, overview } = this.props;

		if( !_.get(overview, ESSAY_PROMPTS+'.length') ){
			dispatch( updateSimpleProp({[ESSAY_PROMPTS]: [{...ESSAY, id: 1}]}) );
		}

		dispatch( updateSimpleProp({page: route.id}) );
	},

	_addPrompt(){
		let { dispatch, overview } = this.props,
			nextId = overview[ESSAY_PROMPTS].length + 1;

		dispatch( updateSimpleProp({
			[ESSAY_PROMPTS]: [...overview[ESSAY_PROMPTS], {...ESSAY, id: nextId}]
		}) );
	},

	render(){
		let { route, overview, college } = this.props;

		return (
			<div>
				<ProgramHeader />
				<Heading {...this.props} />

				<br />

				{/* overview['require_'+route.id] &&
					<div>
						<TitleField college={college} />

						{ _.get(overview, ESSAY_PROMPTS+'.length', 0) > 0 &&
							overview[ESSAY_PROMPTS].map((p) => <Prompt key={p.id} essay={p} _name={ESSAY_PROMPTS} {...this.props} />) }

						<div>
							<div className="add-another" onClick={ this._addPrompt }>Add another prompt</div>
						</div>
					</div>
				*/}

				<RequireSection {...this.props} />
			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		intl: state.intl,
		college: state.college,
		overview: state.overview,
	};
};

export default connect(mapStateToProps)(Essay);
