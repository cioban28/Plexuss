// /Intl_Resources/Student_Visa.js

import React from 'react'

import ResourceHeader from './ResourceHeader'

import { RESOURCES } from './constants'

const HEADER = _.find(RESOURCES, {icon: 'visa'});

class StudentVisa extends React.Component {
	render(){
		return (
			<section>
				<ResourceHeader header={HEADER} />

				<div className="row collapse">

					<div className="column medium-6">
						<div className="content">In order to study in the United States, you must obtain an F-1 Student Visa issued by the US.</div>
						<br />
						<div className="content">Before you can apply for the F-1 visa, you must first apply to and be accepted by a SEVP (Student and Exchange Visitor Program) approved school.</div>

						<br />	
						
						<div className="content">
							<h6 className="section-head">To Apply For An American Student Visa, You Must Complete the Student Visa Requirements</h6>
							<ul>
								<li className="content">You must complete the online study visa application, which consists of <a href="https://ceac.state.gov/genniv/" target="_blank">Form DS-160</a> and a <a href="https://travel.state.gov/content/visas/en/general/photos.html" target="_blank">photo of yourself that you will need to upload</a>.</li>
								<li className="content">You must schedule an interview, usually at the U.S. Embassy or Consulate in the country you reside in. Wait time for an interview can be long and will vary depending on a number of factors, so it is best to apply for your visa early.</li>
							</ul>
						</div>
					</div>

					<div className="column medium-6">
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/resource_visa.png" alt="International Resources - Finding the Right School" />
					</div>

				</div>

				<br />

				<div className="content">
					<h6 className="section-head">Prepare For Your Interview</h6>
					<ul>
						<li className="content">There is a non-refundable visa application fee of $160</li>
						<li className="content">
							You will need to bring the Required Documentation:
							<ul>
								<li className="content">Passport: Your passport must be valid for at least 6 months beyond your period of stay in the United States</li>
								<li className="content">Nonimmigrant Visa Application, Form DS-160 confirmation page</li>
								<li className="content">Application fee payment receipt if you are instructed to pay before your interview</li>
								<li className="content">Photo: You will need to <a href="https://travel.state.gov/content/visas/en/general/photos.html" target="_blank">upload your photo</a> when completing Form DS-160. If you cannot upload your photo at that time, you will have to bring in one printed photo</li>
								<li className="content">
									Certificate of Eligibility for Nonimmigrant (F-1) Student Status - Form I-20
									<ul>
										<li className="content">This will be sent to you from your school once they have entered your information into the Student and Exchange Visitor Information System (SEVIS) database.</li>
									</ul>
								</li>
							</ul>
						</li>
					</ul>
				</div>

			</section>
		);
	}
}

export default StudentVisa;