import React, { Component } from 'react'
import ReactDom from 'react-dom'
import CollegeNav from '../CollegeNav'
import CollegeRanking from './CollegeRanking'
import MainContent from './MainContent'
import { connect } from 'react-redux'
import '../styles.scss'
import {Helmet} from 'react-helmet'

class Ranking extends Component {
	render() {
		return (
			<div id="ranking_main_div">
				<Helmet>
		          <title>College Ranking News | College Recruiting Academic Network</title>
		        </Helmet>
				<div className = 'ranking_banner_top'>
					<CollegeRanking />
					<MainContent />
				</div>
			</div>
		);	
	}
}

export default Ranking
