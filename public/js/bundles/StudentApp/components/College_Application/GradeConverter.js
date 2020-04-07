// /College_Application/GradeConverter.js

import React from 'react'
import selectn from 'selectn'

import { getGradeConversionCountries } from './../../actions/Profile'

export default class extends React.Component{
	constructor(props) {
		super(props)
		this.state = {
			converterOpen: false,
		}
	}

	componentWillReceiveProps(np){
		let { dispatch, _profile } = this.props;

		if( np._profile.init_done && !np._profile.init_grade_conversion_countries_done ) 
			dispatch( getGradeConversionCountries(np._profile.country_name) );
	}

	render(){
		let { _profile } = this.props,
			{ converterOpen } = this.state;

		return (
			<div className="grade-converter">
				<div className="converter-btn" onClick={ () => this.setState({converterOpen: !converterOpen}) }>
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/calc_icon.png" alt="Calculator Icon" />
					<div>Not from the United States?</div>
					<div><u>Use this GPA Converter</u></div>
				</div>

				<div className={"converter "+(converterOpen ? '' : 'hide')}>
					<div>{ selectn('country_name', _profile) }</div>

					<div className="cols">
						<div>Scale</div>
						<div>Description</div>
						<div>U.S. Grade</div>
					</div>

					<div className="container">
						{ selectn('grade_conversion_countries_list', _profile) && 
							_profile.grade_conversion_countries_list.map((g, i, arr) => {
								if( i === 0 || arr[i-1].grading_scale !== arr[i].grading_scale ) 
									return <div className="title" key={g.grading_scale+i}>{g.grading_scale}</div>;
								
								return <GradeConversion key={g.grading_scale+i} con={g} />;
							}) }
					</div>
				</div>
			</div>
		);
	}
}

class GradeConversion extends React.Component{
	constructor(props) {
		super(props)
	}

	render(){
		let { con } = this.props;

		return (
			<div className="cols">
				<div>{ con.scale }</div>
				<div>{ con.description }</div>
				<div>{ con.us_grade }</div>
			</div>
		);
	}
}