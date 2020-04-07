import React from 'react'
import {researchUniversitiesSubHeadings} from "./constants.js"
export default function Major(){
	return(
		<div id="major">
			<h5>
				2. {researchUniversitiesSubHeadings.major}
			</h5>
			<span className="common">
				If you are unsure about what major to study, or you know what major to study but not which school fits your interests -you can read up on it in this section.
			</span>
			<br/ >
			<span >
				Click on Research Colleges & Universities in the SIC (Smart Interactive Column). Next select Majors.
			</span>
			<div className="common center hide-for-small-only">
				<img className="SIC_img" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Sic.PNG" />
			</div>
			<div className="common show-for-small-only">
				<img  src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/ResearchMobileImages/SicStep1.jpg" />
			</div>
			<span>
				Click on a major you’re interested in.
			</span>
			<div className="common hide-for-small-only">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Majors.PNG"/>
			</div>
			<div className="common show-for-small-only">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/ResearchMobileImages/majorshorter.jpg"/>
			</div>
			<span>
				Find more specific majors. Scroll to the bottom of the page to find schools that offer this major.
			</span>
			<div className="common hide-for-small-only">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/individual_major.PNG" />
			</div>
			<div className="common show-for-small-only">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/ResearchMobileImages/arts.jpg"/>
			</div>
		</div>
	)
}
