// /Intl_Resources/Find_School.js

import React from 'react'

import ResourceHeader from './ResourceHeader'

import { RESOURCES } from './constants'

const HEADER = _.find(RESOURCES, {icon: 'find'});

class FindSchool extends React.Component {
	render(){
		return (
			<section>
				<ResourceHeader header={HEADER} />

				<div className="row collapse">

					<div className="column medium-6">
						<div className="content details">When you are looking for colleges to apply to, it can be difficult to find a school that has everything you are looking for. Even harder can be figuring out what you should consider before applying. That’s why Plexuss came up with this quick list to help you make a decision and save time and money while you’re at it. Check out <a href="/news/article/choosing-a-college-9-factors-to-consider" target="_blank">9 Factors to Consider When Choosing a College</a>.</div>

						<div className="content">For additional help picking universities in the United States for international students, visit the <a href="/college" target="_blank">Find a College page</a> to search for schools that match your needs and interests. You can filter your results to a specific location, degree type, tuition amount, average test scores, etc. You can also check out the <a href="/comparison" target="_blank">Plexuss College Comparison Tool</a>. You can use this tool to “battle” as many colleges as you want side by side to compare things like college rank, size, tuition, acceptance rate, and more. This is very helpful when you’re deciding between schools.</div>
					</div>

					<div className="column medium-6">
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/things_to_consider_when_choosing_a_college_lg.png" alt="International Resources - Finding the Right School" />
					</div>
				</div>

			</section>
		);
	}
}

export default FindSchool;