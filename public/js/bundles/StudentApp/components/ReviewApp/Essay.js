// /ReviewApp/Basic.js

import React from 'react'
import selectn from 'selectn'

import SectionHeader from './SectionHeader'

class ReviewEssay extends React.Component {
	constructor(props) {
		super(props)
		this._getMarkup = this._getMarkup.bind(this)
	}
	_getMarkup(){
		let { _profile } = this.props;
		return {__html: _profile.essay_content || 'No essay added'};
	}

	render(){
		let { dispatch, _profile, _route, noEdit } = this.props;

		return (
			<div className="section">

				<div className="inner">
				
					<div className="arrow" />

					<SectionHeader route={_route} dispatch={dispatch} noEdit={noEdit} />

					<div dangerouslySetInnerHTML={ this._getMarkup() } />

				</div>

			</div>
		);
	}
}
export default ReviewEssay;