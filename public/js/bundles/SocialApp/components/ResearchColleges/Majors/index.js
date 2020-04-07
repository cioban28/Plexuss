import React, { Component } from 'react'
import ReactDom from 'react-dom'
import CollegeNav from '../CollegeNav'
import FindMajor from './FindMajor'
import DecideMajor from './DecideMajor'

import { connect } from 'react-redux'
import '../styles.scss'

class Majors extends Component {
	render(){
		return (
			<div id="majors_main_div" className='inner-padding'>
				<div className = 'ranking_banner_top'>
					<div className="right-bar-department-info">
						<FindMajor />
						<DecideMajor />
					</div>
				</div>
			</div>
		)
	}
}

export default Majors