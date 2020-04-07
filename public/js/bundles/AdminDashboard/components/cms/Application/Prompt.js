// /Application/Family.js

import React from 'react'
import createReactClass from 'create-react-class'

import { updateSimpleProp } from './../../../actions/overviewActions'

export default createReactClass({
	_editPrompt(e){
		let { dispatch, overview, essay, _name } = this.props,
			val = e.target.value,
			copy = overview[_name].slice();

		let updatedPrompts = copy.map((p) => p.id === essay.id ? {...p, topic: val} : p);

		dispatch( updateSimpleProp({[_name]: updatedPrompts}) );
	},

	_deletePrompt(){
		let { dispatch, overview, essay, _name } = this.props,
			copy = overview[_name].slice();

		let found = _.find(copy, {id: essay.id});

		if( found ){
			let newPrompts = _.reject(copy, {id: found.id});
			dispatch( updateSimpleProp({[_name]: newPrompts}) );
		}
	},

	render(){
		let { overview, essay } = this.props;

		return (
			<div className="prompt">
				<div className="prompt-head">
					<div>Prompt #{essay.id || ''}</div>
					{ essay.id !== 1 && <div><span onClick={ this._deletePrompt }>Delete Prompt</span></div> }
				</div>

				<div className="prompt-body">
					<label htmlFor={'_'+essay.name}>Topic</label>
					<input
						id={'_'+essay.name}
						type="text"
						name={ essay.name || '' }
						value={ essay.topic || '' }
						placeholder="Essay Topic"
						onChange={ this._editPrompt } />
				</div>
			</div>
		);
	}
});
