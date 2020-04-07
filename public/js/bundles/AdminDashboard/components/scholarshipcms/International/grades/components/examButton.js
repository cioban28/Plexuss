// examButton.js

import React from 'react'

import { editHeaderInfo } from './../../../../../actions/internationalActions'
import * as constants from './../../constants'
import createReactClass from 'create-react-class'

export default createReactClass({
	_toggleExams(){
		let { dispatch, intl, exam } = this.props,
			toggle = {},
			prop = exam.title+'_btn';

		toggle[prop] = intl[prop] ? '' : 'show';

		dispatch( editHeaderInfo(toggle) );
	},

	_complete(){
		let { intl, exam } = this.props,
			exam_fields = _.filter(constants[exam.title], {exam: exam.title}),
			program = intl.activeProgram,
			complete = '';

		// all of the fields must be entered in order to be considered complete
		_.each(exam_fields, (ex) => {
			if( intl[program+'_'+ex.name] ) complete = 'complete';
			else{
				complete = '';
				return false;
			}
		});

		return complete;
	},

	render(){
		let { intl, exam } = this.props,
			name = exam.title ? 'ADD ' + exam.title : '',
			btnClass = intl[exam.title+'_btn'],
			complete = this._complete();

		return (
			<div className={"exam-btn "+btnClass} onClick={ this._toggleExams }>
				{ name || '' }
				<div className={"checkmark "+complete}>&#10003;</div>
			</div>
		);
	}
});
