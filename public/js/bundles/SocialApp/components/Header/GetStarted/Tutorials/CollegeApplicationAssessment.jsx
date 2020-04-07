import React, { Component } from 'react';
import './styles.scss';
import $ from 'jquery';

class CollegeApplicationAssessment extends Component {
	goToLink(id){
		const container = $('.sic-tutorials-main');
		const targetEl = document.querySelector('.sic-tutorials-main').querySelector(`#${id}`);
    container.scrollTop(targetEl.offsetTop - 50);
	}

	render(){
		return(
			<div id='college-application-assessment'>
				<h5 className="text_underline">2. College Application Assessment</h5>
				<span>In this section we will go through the different parts of your College Application Assessment.</span>
				<table style={{width: '100%'}}>
					<tr>
						<td onClick={this.goToLink.bind(this, 'basic-info')}>1. Basic Info</td>
						<td onClick={this.goToLink.bind(this, 'planned-start')}>2. Planned Start</td>
					</tr>
					<tr>
						<td onClick={this.goToLink.bind(this, 'contact-info')}>3. Contact Info</td>
						<td onClick={this.goToLink.bind(this, 'citizenship-status')}>4. Citizenship</td>
					</tr>
					<tr>
						<td onClick={this.goToLink.bind(this, 'std-financials')}>5. Financials</td>
						<td onClick={this.goToLink.bind(this, 'std-GPA')}>6. GPA</td>
					</tr>
					<tr>
						<td onClick={this.goToLink.bind(this, 'std-test-scores')}>7. Scores</td>
						<td onClick={this.goToLink.bind(this, 'select-scholarships')}>8. Select Scholarships</td>
					</tr>
					<tr>
						<td onClick={this.goToLink.bind(this, 'select-colleges')}>9. Select Colleges</td>
						<td onClick={this.goToLink.bind(this, 'my-applications')}>10. My Applications</td>
					</tr>
					<tr>
						<td onClick={this.goToLink.bind(this, 'std-essays')}>11. Essay</td>
						<td onClick={this.goToLink.bind(this, 'std-demographics')}>12. Demographics</td>
					</tr>
					<tr>
						<td onClick={this.goToLink.bind(this, 'std-uploads')}>13. Uploads</td>
						<td onClick={this.goToLink.bind(this, 'std-sponsors')}>14. Sponsor</td>
					</tr>
				</table>
				<span className='hide-for-small-only'>Click on the Icon <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/sic_icon.PNG' className='sic-icon-img' /> on the navigation bar. Select College Application Assessment.</span>
				<span className='show-for-small-only'>Select <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Sic-mobile.svg' className='img_icons'/> then go to <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/sic_icon.PNG' className='sic-icon-img' /> on the navigation bar. Select College Application Assessment.</span>
				<div className="hide-for-small-only">
					<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/college-assessment-bar.png" />
				</div>
				<div className="show-for-small-only">
					<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/public_profile_clg_assessment_mbl/college_app_assessment_sic.jpg" />
				</div>
				<span>The SIC (Smart Interactive Column) will help you keep track of each section of your application. It helps you keep track of your completed and uncompleted sections.</span>

				<div id='basic-info' className='mtb-20'>
					<h5>1. Basic Info</h5>
					<div className="hide-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/basic_info_1.PNG" />
					</div>
					<span>Start your assessment by answering a few simple questions. Just click into the white fields and type your answer or select an option from the dropdown <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/dropdown.png' />.</span>
					<div className="show-for-small-only">
						<img className='mtb-20 full-width' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/public_profile_clg_assessment_mbl/Lets_begin_the _application_process.jpg" />
					</div>
				</div>

				<div id='planned-start' className='mb-20'>
					<h5>2. Planned Start</h5>
					<div className="hide-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/when_do_u_want_to_renrool.PNG" />
					</div>
					<span>Select an option from the dropdown <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/dropdown.png' />. Tell us what term and year you want to start and whether you are interested in campus only, online only, or both.</span>
					<div className="show-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/public_profile_clg_assessment_mbl/planned_start.jpg" />
					</div>
				</div>

				<div id='contact-info' className='mb-20'>
					<h5>3. Contact Info</h5>
					<div className="hide-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/colleges need a way to communicate with tyou.PNG" />
					</div>
					<span>In order for Colleges and Universities to get in touch with you, we ask you to share information such as your skype name, your phone number, and your address. Click in the white fields to type your information.</span>
					<div className="show-for-small-only">
						<img className='mtb-20 full-width' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/colleges-need-a-way-to-communicate-with-you.jpg" />
					</div>
				</div>

				<div id='citizenship-status' className='mb-20'>
					<h5>4. Citizenship Status</h5>
					<div className="hide-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/citizenship_status.PNG" />
					</div>
					<span>Universities require your citizenship status for their applications. Click in the white field to type your answer or select an option from the dropdown <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/dropdown.png' />.</span>
					<div className="show-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/public_profile_clg_assessment_mbl/citizenship_status.jpg" />
					</div>
				</div>

				<div id='std-financials' className='mb-20'>
					<h5>5. Financials</h5>
					<div className="hide-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/maximum_afford.PNG" />
					</div>
					<span>Indicate how much money you are able to pay for University per year.  Select the box <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/checkbox.png' /> if you are interested in financial aid, grants, and scholarships.</span>
					<div className="show-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/public_profile_clg_assessment_mbl/financials.jpg" />
					</div>
				</div>

				<div id='std-GPA' className='mb-20'>
					<h5>6. GPA (Grade Point Average)</h5>
					<div className="hide-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/GPA.PNG" />
					</div>
					<span>
						Universities need to know your Grade Point Average. If you have given us your GPA during the Plexuss sign-up process, we will automatically fill in this field for you. You just need to confirm.
						Not a student in the US? Select your country and enter your Grade Point Average, our converter will take care of the rest.
					</span>
					<div className="show-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/public_profile_clg_assessment_mbl/gpa.jpg" />
					</div>
				</div>

				<div id='std-test-scores' className='mb-20'>
					<h5>7. Test Scores</h5>
					<div className="hide-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/test_scores.PNG" />
					</div>
					<span>Enter your various Test Scores, so you application is as complete as possible for better assessment. Simply click on any of the buttons to enter your results.</span>
					<div className="show-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/public_profile_clg_assessment_mbl/scores.jpg" />
					</div>
				</div>

				<div id='select-scholarships' className='mb-20'>
					<h5>8. Select Scholarships</h5>
					<div className="hide-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Select_Scholarships.PNG" />
					</div>
					<span>Click <img className='view-details' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/scholarships_view_details.PNG' /> to see the details of each scholarship, i.e. if you qualify and what is required of you to apply for it. </span>
					<span>Click <img className='btn-img' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/scholarship_plus_sign.PNG' /> to add the scholarship to your list. <img className='btn-img' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/scholarship_added.png' /> scholarships will help you keep track of the ones you want to apply for.</span>
					<div className="show-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/public_profile_clg_assessment_mbl/scholarships.jpg" />
					</div>
				</div>

				<div id='select-colleges' className='mb-20'>
					<h5>9. Select Colleges</h5>
					<div className="hide-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Select_Colleges.PNG" />
					</div>
					<span>This section is your shortlist of Universities that you are seriously considering. This list also helps us make recommendations for you. </span>
					<span>You can add more colleges to your list. Click on <img className='btn-img' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/addmorecollegestransparent.png' /> to select more colleges. If you want to delete a university click on Your Colleges and select the <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/garbage_can.png' className='sic-icon-img' className='iw_30'/> next to the school you would like to delete. </span>
					<span>If you wish to apply to any of these schools click on <img className='btn-img' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/add_application.PNG' /> to add a University to My Applications.</span>
					<div className="show-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/public_profile_clg_assessment_mbl/selectcolleges2.jpg" />
					</div>
				</div>

				<div id='my-applications' className='mb-20'>
					<h5>10. My Applications</h5>
					<div className="hide-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/my_application.PNG" />
					</div>
					<span>This is the list of the Universities that you want to apply to. Click on <img className='btn-img' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/apply_button.png' /> to go to the application page of each University. (External link)</span>
					<div className="show-for-small-only">
						<img className='mtb-20 full-width' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/public_profile_clg_assessment_mbl/application.jpg" />
					</div>
				</div>

				<div id='std-essays' className='mb-20'>
					<h5>11. Essays</h5>
					<div className="hide-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Essay.PNG" />
					</div>
					<span>This section is optional. However, we highly recommend that you take the time to write an essay. You can type directly into the field or paste your essay into the text box.</span>
					<div className="show-for-small-only">
						<img className='mtb-20 full-width' src="/social/images/essay.jpg" />
					</div>
				</div>

				<div id='std-demographics' className='mb-20'>
					<h5>12. Demographics</h5>
					<div className="hide-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Demographics.PNG" />
					</div>
					<span>If you indicated that you are interested in scholarships, we recommend you enter the required information. Select an option from the dropdown <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/dropdown.png' />. If you are not interested, you can skip this step.</span>
					<div className="show-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/demographics.jpg" />
					</div>
				</div>

				<div id='std-uploads' className='mb-20'>
					<h5>13. Uploads</h5>
					<div className="hide-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Uploads.PNG" />
					</div>
					<span>To upload your Transcripts, Financial Documents, College Essays and other Documentation, simply click on the appropriate button and select the right file.</span>
					<div className="show-for-small-only">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/public_profile_clg_assessment_mbl/my_documents.jpg" />
					</div>
				</div>

				<div id='std-sponsors' className='hide-for-small-only'>
					<h5>14. Sponsor</h5>
					<div className="">
						<img className='mtb-20' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Sponsors.PNG" />
					</div>
					<span>This step is optional. Universities ask for your sponsor information so that they know who will be paying your tuition. Click the dropdown <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/dropdown.png' /> and enter the information of your sponsor. You can add additional sponsors by clicking Add Sponsor.</span>
				</div>
			</div>
			)
	}
}

export default CollegeApplicationAssessment;
