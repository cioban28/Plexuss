// /Intl_Resources/Work_In_US.js

import React from 'react'

import ListGenerator from './ListGenerator'
import ResourceHeader from './ResourceHeader'

import { RESOURCES } from './constants'

const HEADER = _.find(RESOURCES, {icon: 'work'});

class WorkUS extends React.Component {
	render(){
		return (
			<section>
				<ResourceHeader header={HEADER} />

				<div className="row">

					<div className="column medium-6">
						<div className="content details">F1 Students are not permitted to work off-campus during their first academic year, but are allowed to accept on-campus employment (subject to certain standards). After the first academic year, F-1 students may begin 3 types of off-campus employment:</div>

						<div className="content">
							<ul>
								<li className="content">
									Curricular Practical Training (CPT)
									<ul>
										<li className="content">Any type of required internship offered by employers in connection with the student’s institution</li>
									</ul>
								</li>
								<li className="content"><a href="https://www.uscis.gov/working-united-states/students-and-exchange-visitors/students-and-employment/optional-practical-training" target="_blank">Optional Practical Training (OPT)</a></li>
								<li className="content"><a href="https://www.uscis.gov/working-united-states/students-and-exchange-visitors/students-and-employment/stem-opt" target="_blank">Science, Technology, Engineering, and Mathematics (STEM) Optional Practical Training Extension (OPT)</a></li>
								<li className="content">Any off-campus employment must be related to area of study and authorized prior to starting any work by the Designated School Official</li>
							</ul>
						</div>
					</div>

					<div className="column medium-6">
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/resource_work.png" alt="International Resources - Working in the US" />
					</div>
					
				</div>
			</section>
		);
	}
}
export default WorkUS;