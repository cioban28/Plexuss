import React from 'react'
import {researchUniversitiesSubHeadings} from "./constants.js"
import './styles.scss';
export default function FindColleges(){
	return(
			<div id="find_colleges">
			<h5> 1. {researchUniversitiesSubHeadings.findColleges}</h5>
				<span className="span_pad">
					There are two ways for you to find college pages on Plexuss.
				</span>
				<br/>
				<span>
					You can click into the search bar on top and enter the name of the school you are looking for.
				</span>
				<div className="section hide-for-small-only">
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Searchbar.PNG" />
				</div>
				<div className="section show-for-small-only">
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/ResearchMobileImages/topsearchbar.jpg" />
				</div>
			<span>
				Or click on Research Colleges & Universities in the SIC (Smart Interactive Column).
				Select Find Colleges.
			</span>
			<div className="section center hide-for-small-only">
				<img className="SIC_img" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Sic.PNG" />
			</div>
			<div className=" section show-for-small-only">
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/ResearchMobileImages/SicStep1.jpg" />
			</div>
			<span className="span_pad">
				Next, on the world map, click on a country.
			</span>
			<div className="section hide-for-small-only">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Find_colleges_all_over_the_world.PNG" />
			</div>
			<div className="section show-for-small-only">
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/ResearchMobileImages/worldmap.jpg" />
			</div>
			<span>
				Select a state.
			</span>
			<div className="section hide-for-small-only">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/US_MAP.PNG" />
			</div>
			<div className="section show-for-small-only">
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/ResearchMobileImages/USAMap.jpg" />
			</div>
			<span>
				Go through the list of Universities of that state and click on the ones you’re interested in for more information.
			</span>
			<div className="section hide-for-small-only">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Best_of_California.jpg"/>
			</div>
			<div className="section show-for-small-only">
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/ResearchMobileImages/bestcollegesinca.jpg" />
			</div>
			<span className="hide-for-small-only">
				The college page overview presents helpful information.
			</span>
			<div className="section hide-for-small-only">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Overview_UCLA.gif"/>
			</div>
			<span className="section show-for-small-only" >
				Check out the college page overview for helpful information.
			</span>
			<div className=" section show-for-small-only">
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/ResearchMobileImages/gif-overview.gif" />
			</div>
			<span>
				The college page also informs you on stats, ranking, tuition, news, and current students and alumni.
			</span>
			<div className="section hide-for-small-only">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Stats_and_other_menu_items.gif"/>
			</div>
			<div className="section show-for-small-only">
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/ResearchMobileImages/gif-tabs.gif" />
			</div>
			<div>
				<span>
					On the college pages you will see a floating action button: <img className="floating_img hide-for-small-only" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/star.svg"/>
					<img className="floating_img_mbl show-for-small-only" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/ResearchMobileImages/floatingactionbutton.jpg" />
				</span>
			</div>
			<span className="span_pad"> If you’re interested in a University click on it to expand.</span>
			<div className="section hide-for-small-only">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/open.PNG"/>
			</div>
			<div className="section show-for-small-only">
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/ResearchMobileImages/modalapplynow.jpg" />
			</div>
			<span>
				Click on Get Recruited! to add this school to your favorites.
				Click on Apply Now! to work on your College Application Assessment.
			</span>
		</div>
	)
}
