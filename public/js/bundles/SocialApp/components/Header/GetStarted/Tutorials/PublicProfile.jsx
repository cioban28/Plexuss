import React, { Component } from 'react';
import './styles.scss';
import $ from 'jquery';


class PublicProfile extends Component {
	goToLink(id){
		const container = $('.sic-tutorials-main');
		const targetEl = document.querySelector('.sic-tutorials-main').querySelector(`#${id}`);
    container.scrollTop(targetEl.offsetTop - 50);
	}

	render(){

		return (
			<div id="public_profile">
				<h5 className="text_underline">Step 1 Public Profile & College Application Assessment</h5>
				<h5 className="text_underline">Public Profile</h5>
				<table style={{width: '100%'}}>
					<tr>
						<td onClick={this.goToLink.bind(this, 'user-information')}>1. User Information</td>
						<td onClick={this.goToLink.bind(this, 'claim-to-fame')}>2. Claim to Faim</td>
					</tr>
					<tr>
						<td onClick={this.goToLink.bind(this, 'user-objectives')}>3. Objective</td>
						<td onClick={this.goToLink.bind(this, 'skills-endorsement')}>4. Skills & Endorsement</td>
					</tr>
					<tr>
						<td onClick={this.goToLink.bind(this, 'projects-n-publications')}>5. Project & Publications</td>
						<td onClick={this.goToLink.bind(this, 'liked-college')}>6. Liked Colleges</td>
					</tr>
				</table>
				<div className='span_margin hide-for-small-only'>
					<span>Your Public Profile is visible to your connections and colleges. An indicator on your homepage helps you keep track of the completeness of your profile.</span>
				</div>
				<div className='span_margin show-for-small-only'>
					<span>Your Public Profile is visible to your connections and colleges. The more complete your profile the easier it will be for college reps, alumni, and peers to get a full picture of your accomplishments.</span>
				</div>
				<div className='hide-for-small-only'>
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/public_profile.PNG" />
				</div>
				<div className='show-for-small-only'>
					<img className='full-width' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/public_profile_clg_assessment_mbl/me.jpg" />
				</div>
				<div className='span_margin_top hide-for-small-only'>
					<span>Click on the next step to continue editing. Alternatively, click on edit Profile above this section (below your profile picture).</span>
				</div>
				<div className='span_margin show-for-small-only'>
					<div className='span_margin_bottom'>
						<span>Click on Edit Profile.</span>
					</div>
					<div>
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/public_profile_clg_assessment_mbl/me_edit.jpg" />
					</div>
				</div>

				<div id='user-information'>
					<h5>1. User Information</h5>
					<div className='span_margin_bottom'>
						<span>Click on the edit icon <img src="/social/images/edit_icon.png" /> on the right to edit your Information.</span>
					</div>
					<div className='hide-for-small-only'>
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/user_Information.jpg" />
					</div>
					<div className='show-for-small-only'>
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/public_profile_clg_assessment_mbl/edit_user_info.jpg" />
					</div>
					<div className='span_margin_top'>
						<span>Either click into the fields and enter your information or select an option from the dropdown <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/dropdown.png" />.</span>
					</div>
				</div>

				<div id='claim-to-fame'>
					<h5>2. Claim to Fame</h5>
					<div className='span_margin_bottom'>
						<span>Click on the edit icon <img src="/social/images/edit_icon.png" /> on the right to edit your information.</span>
					</div>
					<div className='hide-for-small-only'>
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/claim_fame.PNG" />
					</div>
					<div className='show-for-small-only'>
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/public_profile_clg_assessment_mbl/claim_to_fame.jpg" />
					</div>
					<div className='span_margin_top'>
						<span>Claim to Fame lets you talk about yourself, your interests, and your goals. This is a section that you can use to highlight your skills and passions.</span>
					</div>
				</div>

				<div id='user-objectives'>
					<h5>3. Objective</h5>
					<div className='span_margin_bottom'>
						<span>Click on the edit icon <img src="/social/images/edit_icon.png"/> on the right to edit your information.</span>
					</div>
					<div className='hide-for-small-only'>
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/objective.PNG" />
					</div>
					<div className='show-for-small-only'>
						<img className='full-width' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/public_profile_clg_assessment_mbl/edit_objective.jpg" />
					</div>
					<div className='span_margin_top'>
						<span>Your objective is your study goals. What degree you are seeking, what major you want to study, and when you want to start. You can add your dream occupation.</span>
					</div>
					<div className='span_margin_bottom'>
						<span>Either click into the fields and enter your information or select an option from the dropdown <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/dropdown.png" />.</span>
					</div>
				</div>

				<div id='skills-endorsement'>
					<h5>4. Skills & Endorsements</h5>
					<div className='span_margin_bottom'>
						<span>Click on the edit icon <img src="/social/images/edit_icon.png"/> on the right to edit your information.</span>
					</div>
					<div className='hide-for-small-only'>
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/skills_endorsement.PNG" />
					</div>
					<div className='show-for-small-only'>
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/public_profile_clg_assessment_mbl/skills_and_endorsements.jpg" />
					</div>
					<div className='span_margin_top'>
						<span>In this section you can add your skills -soft and hard skills alike. Your peers are later able to endorse these.</span>
					</div>
				</div>

				<div id='projects-n-publications'>
					<h5>5. Projects & Publications</h5>
					<div className='span_margin_bottom'>
						<span>Click on the edit icon on <img src="/social/images/edit_icon.png" /> the right to edit your information.</span>
					</div>
					<div className='hide-for-small-only'>
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/publications.PNG" />
					</div>
					<div className='show-for-small-only'>
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/public_profile_clg_assessment_mbl/projects_and_publications.jpg" />
					</div>
					<div className='span_margin_top'>
						<span>Projects & Publications is your portfolio. You can add Articles, Essays, Publications, and links to other work.</span>
					</div>
				</div>

				<div id='liked-college'>
					<h5>6. Liked College</h5>
					<div className='span_margin_bottom'>
						<span>Click on the edit icon <img  src="/social/images/edit_icon.png" /> on the right to edit your information.</span>
					</div>
					<div className='hide-for-small-only'>
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/liked_college.PNG" />
					</div>
					<div className='show-for-small-only'>
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/public_profile_clg_assessment_mbl/liked_colleges.jpg" />
					</div>
					<div className='span_margin'>
						<span>Click on Add+ to add Universities that you are interested in. This helps you connect with like-minded people.</span>
					</div>
				</div>
			</div>
			)
	}
}

export default PublicProfile;
