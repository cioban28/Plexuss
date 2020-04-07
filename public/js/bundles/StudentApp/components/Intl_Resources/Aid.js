// /Intl_Resources/Aid.js

import React from 'react'

import ResourceHeader from './ResourceHeader'

import { RESOURCES } from './constants'

const HEADER = _.find(RESOURCES, {icon: 'aid'});

class Aid extends React.Component {
	render(){
		return (
			<section>
				<ResourceHeader header={HEADER} />

				<div className="row collapse">

					<div className="column medium-6">
						<div className="content details">The United States government does not assist international students with loans or grants. If you cannot cover the entirety of your tuition, you can look for other ways to fund your education:</div>

						<ul>
							<li className="content">Some countries may offer foreign study funding for their own citizens who qualify for assistance once they are admitted to an approved program or school abroad.</li>
							<li className="content">It is possible to get scholarships and grants from non-governmental organizations, businesses, and private foundations. There are numerous websites where you can find scholarships you may be eligible for, including <a href="https://www.careeronestop.org/toolkit/training/find-scholarships.aspx" target="_blank">careeronestop.org</a>, <a href="http://www.topuniversities.com/student-info/scholarship-advice/international-scholarships-study-us" target="_blank">topuniversities.com</a>, and <a href="http://www.collegeweeklive.com/scholarships" target="_blank">collegeweeklive.com</a>.</li>
							<li className="content">Many universities and colleges offer assistance to international students, which can be found by contacting their international admissions offices.</li>
						</ul>
					</div>

					<div className="column medium-6">
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/resource_aid.png" alt="International Resources - Finding the Right School" />
					</div>
				</div>
			</section>
		);
	}
}

export default Aid;