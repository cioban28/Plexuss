// Renders the scores the user inputs into a view on the right side.

import React from 'react'

import SingleScoresInput from './SingleScoresInput'

import { updateProfile } from './../../actions/Profile'

import { ACT, PRE_16_SAT, POST_16_SAT, PRE_16_PSAT, POST_16_PSAT,
		GED, AP, TOEFL, IELTS, PTE, LSAT, GMAT, GRE, OTHER, iBT, PBT } from './../College_Application/constants'

const EXAMS = {
	ACT,
	'SAT Before 2016': PRE_16_SAT,
	'SAT After 2016': POST_16_SAT,	
	'PSAT Before 2016': PRE_16_PSAT,
	'PSAT After 2016': POST_16_PSAT,	
	GED,
	AP,
	TOEFL,
	iBT,
	PBT,
	IELTS,
	PTE,
	LSAT,
	GMAT,
	GRE,
	OTHER
};

export default class ScoresInputted extends React.Component {
	constructor(props) {
		super(props);
	}

	_getExams() {
		const { _profile } = this.props,
			_exams = [],
			skip = [];

		_profile.is_pre_2016_sat ? skip.push('SAT After 2016') : skip.push('SAT Before 2016');
		_profile.is_pre_2016_psat ? skip.push('PSAT After 2016') : skip.push('PSAT Before 2016');

		_.forIn(EXAMS, (val, key) => {
			if (skip.indexOf(key) > -1) { return; }

			let obj = {};
			obj.title = key;
			obj.segments = [];

			_.each(val, (ex) => {
				let seg = {
					name: ex.name,
					label: ex.label.split(' ')[0].replace(':', '')
				};

				obj.segments.push(seg);
			});

			_exams.push(obj);
		});

		return _exams;
	}

	_buildExamSections() {
		let { _profile } = this.props,
			exams = this._getExams();

		return exams.map(exam => {
			if (this._isExamEmpty(exam)) { return; }
			return <SingleScoresInput key={ exam.title } exam={ exam } _profile={ _profile } />
		});
	}

	_isExamEmpty(exam){
		let { _profile } = this.props,

			is_exam_empty = true;

		_.each(exam.segments, score => { 
			if (_profile[score.name]) { 
				is_exam_empty = false;
				return false; 
			}
		});

		return is_exam_empty;
	}

	_allExamsEmpty(){
		const all_exams = this._getExams();

		let exams_empty = true;

		_.each(all_exams, exam => {
			if (!this._isExamEmpty(exam)) {
				exams_empty = false;
				return false;
			}
		});

		return exams_empty;
	}

	render() {
		let { _profile } = this.props,
			no_scores = _profile.self_report == 'no' ? true : false;

		if (this._allExamsEmpty() || no_scores) { return null; }

		return (
			<div>
				<div className="page-head">My Scores</div>
				{ this._buildExamSections() }
			</div>
		)
	}
}

