// /ReviewApp/Basic.js

import React from 'react'
import selectn from 'selectn'

import SectionHeader from './SectionHeader'
import { ACT, PRE_16_SAT, POST_16_SAT, PRE_16_PSAT, POST_16_PSAT,
		GED, AP, TOEFL, IELTS, PTE, LSAT, GMAT, GRE, OTHER } from './../College_Application/constants'

const EXAMS = {
	ACT,
	'SAT Before 2016': PRE_16_SAT,
	'SAT After 2016': POST_16_SAT,
	'PSAT Before 2016': PRE_16_PSAT,
	'PSAT After 2016': POST_16_PSAT,
	GED,
	AP,
	TOEFL,
	IELTS,
	PTE,
	LSAT,
	GMAT,
	GRE,
	OTHER
};

class ReviewScores extends React.Component {
	constructor(props) {
		super(props)
		this.state = {
			all_exams: [],
		}
		this._allExamsEmpty = this._allExamsEmpty.bind(this)
		this._isExamEmpty = this._isExamEmpty.bind(this)
		this._buildExams = this._buildExams.bind(this)
	}

	_allExamsEmpty(){
		let { all_exams } = this.state;

		for (let i = 0; i < all_exams.length; i++) {
			let exam = all_exams[i];

			if (!this._isExamEmpty(exam)) {
				return false;
			}

		}

		return true;
	}

	_isExamEmpty(exam){
		let { _profile } = this.props,

			is_exam_empty = true;

		exam.segments.forEach(score => {
			if (_profile[score.name]) { is_exam_empty = false; }
		});

		return is_exam_empty;
	}

	_buildExams(){
		var _exams = [];

		_.forIn(EXAMS, (val, key) => {
			let obj = {};

			obj.title = key;
			obj.segments = [];

			_.each(val, (ex) => {
				let seg = {
					name: ex.name,
					label: ex.label.split(' ')[0],
				};

				obj.segments.push(seg);
			});

			_exams.push(obj);
		});

		this.state.all_exams = _exams;
	}

	componentWillMount(){
		this._buildExams();
	}
	
	render(){
		let { dispatch, _profile, _route, noEdit } = this.props,
			{ all_exams } = this.state;

		return (
			<div className="section">

				<div className="inner">

					<div className="arrow" />

					<SectionHeader route={_route} dispatch={dispatch} noEdit={noEdit} />

					{ _profile.in_college === 0 && ( _profile.gpa || _profile.weighted_gpa ) &&
					<div>
						<div className="heading">High School Scores</div>
						{ _profile.gpa &&<div className="gpa">High School GPA: {_profile.gpa} </div> }
						{ _profile.weighted_gpa && <div className="gpa">Weighted GPA: {_profile.weighted_gpa} </div> }
						<hr />
					</div>
					}

					{ !this._allExamsEmpty() &&
					<div>
						<div className="heading">College Entrance Exams</div>

						<div className="scores-container">
							{ all_exams.map((e) => <ExamReview key={e.title} exam={e} {...this.props} _isExamEmpty={this._isExamEmpty} />) }
						</div>

						<br />
					</div>
					}

					{ _profile.in_college === 1 && _profile.gpa &&
					<div>
						<div className="heading">College GPA</div>
						<div className="gpa">Overall GPA: { _profile.gpa }</div>
						<br />
					</div>
					}

				</div>

			</div>
		);
	}
}

class ExamReview extends React.Component{
	constructor(props) {
		super(props)
	}

	render(){
		let { _profile, exam } = this.props;
		if ( !this.props._isExamEmpty(exam) ) {

			return (
				<div className="exam">
					<div className="title">{ exam.title }</div>
					<div className="score">
						{ exam.segments.map((s, i) => !!_profile[s.name] && <div key={s.name+i}>{s.label} {_profile[s.name]}</div>) }
					</div>
				</div>
			);

		} else { return null; }
	}
}

export default ReviewScores;