import React from 'react'
import {researchUniversitiesSubHeadings} from "./constants.js"
export default function Ranking(){
	return(
		<div id="ranking">
			<h5>
				3. {researchUniversitiesSubHeadings.ranking}
			</h5>
			<span className="common">
				Research Universities by the Plexuss College Rank.
			</span>
			<br />
			<span>
				Click on Research Colleges & Universities in the SIC (Smart Interactive Column). Next select Ranking.
			</span>
			<div className="common center hide-for-small-only">
				<img className="SIC_img" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Sic.PNG" />
			</div>
			<div className="common show-for-small-only">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/ResearchMobileImages/SicStep1.jpg" />
			</div>
			<span>
				Click on this button <img className="full_ranking" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/button-1.PNG" /> to expand the list.
			<br/>
				Or explore Other Ranking Lists. Click on this button <img className="all_ranking" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/button2.png" /> to see all other ranking lists.
			</span>
			<div className="common hide-for-small-only">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/ranking.PNG"/>
			</div>
			<div className="common show-for-small-only">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/ResearchMobileImages/rankingmobile.jpg" />
			</div>
		</div>
	)
}
