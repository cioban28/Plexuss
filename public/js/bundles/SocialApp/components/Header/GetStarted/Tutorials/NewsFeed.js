import React from 'react';
import './styles.scss';
import {promoteYourselfSubHeadings} from './constants.js'
export default function NewsFeed(){

	return(
		<div id="news_feed">
			<h5> 3. {promoteYourselfSubHeadings.newsFeed} </h5>
			<span sp>On your News Feed you can share content that expresses your interests, ask questions and get answers from your connections. </span>
			<br />
			<span>
				To post go Home <img className="img_icons_trans" src="/social/images/newhome.png" /> Select one of the options:
			</span>
			<div className="common hide-for-small-only">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Make_a_Post.PNG" />
			</div>
			<div className="common show-for-small-only">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/PromoteYourself/MakePost.jpg" style={{ border: '1px solid #000' }} />
			</div>
		</div>
	)
}
