// /Intl_Resources/Prep.js

import React from 'react'

import ListGenerator from './ListGenerator'
import ResourceHeader from './ResourceHeader'

import { RESOURCES, IELTS, TOEFL, GENERAL } from './constants'

const HEADER = _.find(RESOURCES, {icon: 'prep'});

class Prep extends React.Component {
	render(){
		return (
			<section>
				<ResourceHeader header={HEADER} />

				<div className="content">Research both <a href="https://www.ets.org/toefl/" target="_blank">TOEFL</a> and <a href="https://www.ielts.org/" target="_blank">IELTS</a> to see which English Proficiency Exam best fits your test-taking skill set</div>

				<br />

				<div className="row collapse">
					<div className="columns medium-6">
						{ IELTS.map((i) => <ListGenerator key={i.title} chklist={i} />) }
					</div>

					<div className="columns medium-6">
						{ TOEFL.map((i) => <ListGenerator key={i.title} chklist={i} />) }
					</div>
				</div>

				<br />

				<div className="content">
					<h6 className="section-head">General Tips</h6>
					<ul>
						<li className="content">Don’t wait until the last minute to study. "Cramming" will not help you learn the material you need to pass the exam.</li>
						<li className="content">Try not to only "study" English, but actively use it every day, whether it’s speaking to a friend, using flashcards, watching English television, and so on.</li>
						<li className="content">Along with speaking English, try to think in English as well so that the words and phrases become more natural</li>
						<li className="content">Read books, magazines, articles, and more in English instead of in your native language</li>
						<li className="content">Write in English frequently</li>
						<li className="content">Familiarize yourself with what will be on the exam and how the exam is structured</li>
						<li className="content">Get lots of sleep, give yourself study breaks and go outside, and try to relax.</li>
					</ul>
				</div>

			</section>
		);
	}
}

export default Prep;