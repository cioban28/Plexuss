// Single score input to ScoresInputted.js

import React from 'react'

export default class SingleScoreInput extends React.Component{
	constructor(props) {
		super(props);

		this._buildSingleScore = this._buildSingleScore.bind(this);
	}

	_buildSingleScore(segment, index) {
		let { _profile } = this.props,
			name = segment.name,
			label = segment.label,
			score = _profile[name],
			score_invalid = _profile[segment.name + '_valid'] != null && !_profile[segment.name + '_valid']

		// Do not show if no score or score is invalid
		if (!score || score_invalid) { return null; }

		return (
			<div key={ index } className='single-exam-scores'>
				<div className='single-exam-score'>{ score }</div>
				<div title={ label } className='single-exam-label'>{ label }</div>
			</div>
		);
	}

	_isExamEmpty(exam){
		let { _profile } = this.props,
			is_empty = true;

		_.each(exam.segments, segment => {
			if (_profile[segment.name]) { 
				is_empty = false;
				return false; 
			} 
		});

		return is_empty;
	}

	render() {
		let { exam, _profile } = this.props;
		
		if (this._isExamEmpty(exam)) { return; }
		
		return (
			<div className='single-exam-score-container'>
				<div className='single-exam-title'>{ exam.title }</div>
				<div className='single-exam-scores-list'>
					{ exam.segments.map(this._buildSingleScore) }
				</div>
			</div>
		);
	}
}