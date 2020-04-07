// /ReviewApp/Colleges.js

import React from 'react'
import selectn from 'selectn'
import Link from 'react-router'

import SectionHeader from './SectionHeader'
import MyCollege from './../College_Application/MyCollege'

class ReviewColleges extends React.Component {
	constructor(props) {
		super(props)
		this._buildCollegeLogos = this._buildCollegeLogos.bind(this)
	}

	_buildCollegeLogos() {
		let { _profile } = this.props,
			MyCollegeList = _profile.MyCollegeList,
			completed_colleges = _profile.completed_colleges,
			render = null,
			classes = null,
			finished = null;

		render = MyCollegeList.map((college, i) => {
			finished = ( completed_colleges && !!_.find(completed_colleges, ['school_name', college.school_name]) ) 
				? 'done' : '';

			return ( 
				<MyCollege key={college.college_id} i={i+1} college={college} classes={'submit-college-logo ' + finished} />
			);
		});

		return render;
	}

	render(){
		let { dispatch, _profile, _route, noEdit } = this.props;

		return (
			<div className="section">

				<div className="inner">
				
					<div className="arrow" />

					<SectionHeader route={_route} dispatch={dispatch} noEdit={noEdit} />

					{ _.get(_profile, 'MyCollegeList.length', 0) > 0 && 
						this._buildCollegeLogos() }

					{ _.get(_profile, 'MyCollegeList.length', 0) === 0 && <div>No schools selected to apply to</div> }

				</div>

			</div>
		);
	}
}

export default ReviewColleges;