import React from 'react';
import './styles.scss';
import {promoteYourselfSubHeadings} from './constants.js'
export default function MyArticles(){

	return(
		<div id="my_articles">
			<h5> 4. {promoteYourselfSubHeadings.myArticles} </h5>
			<span>
				My Articles gives you the opportunity to write articles to convey your ideas and talk about your extracurricular activities.
			</span>
			<br />
			<span>
				To publish an article either go home and select Write an Article or go to My Articles.
			</span>
			<ul>
				<li>Click <img className="article_img" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/write_an_article.PNG" /></li>
				<li>Enter a Title</li>
				<li>Select an Article Image. This can be your own image or a free stock images</li>
				<ul>
					<li className="sub_li">
						The article image should appropriately represent the subject/content of your article
					</li>
				</ul>
				<li>Either copy/paste your article into the text box or write your article in the text box</li>
				<li>Tag your articles subject with one of the options: News, Ranking, Admissions, Sports, Campus Life, Paying for College, Financial Aid</li>
				<li>Preview your article</li>
				<li>Click <img className="publish_img" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/publish.PNG" /></li>
			</ul>
			<div className="common hide-for-small-only">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/WriteAnArticle.jpg" />
			</div>
			<div className="common show-for-small-only">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/PromoteYourself/WriteAnArticle_Mobile.jpg" />
			</div>
			<span>
				You can see your articles stats -views, likes, comments, shares- in your Dashboard.
			</span>
			<div className="common hide-for-small-only">
				<img className="dashboard_img" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Dashboard.jpg" />
			</div>
			<div className="common show-for-small-only">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/PromoteYourself/Dashboard_Mobile.jpg" />
			</div>
		</div>
	)
}
