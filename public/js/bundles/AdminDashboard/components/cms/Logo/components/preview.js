// preview.js

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import createReactClass from 'create-react-class'

const LogoPreview = createReactClass({
	getInitialState(){
		return {
			social: {
				linkedin: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/icons/linkedin_64_black.png',
				pinterest: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/icons/pinterest_64_black.png',
				twitter: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/icons/twitter_64_black.png',
				facebook: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/icons/facebook_64_black.png'
			},
			icons: {
				overview: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/icons/overview.png',
				stats: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/icons/stats.png',
				ranking: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/icons/ranking.png',
				admissions: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/icons/admission.png',
				chat: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/icons/chat.png',
			},
		};
	},

	render(){
		let { logo, college } = this.props,
			{ social, icons } = this.state,
			img = '';

		//if logo.image is set, then user has updated logo so use that, else use saved logo
		if( logo && logo.fileURL ) img = logo.fileURL;
		else if( college && college.logo_url ) img = college.logo_url;

		return (
			<div className="logo-preview-container left">
				<div className="outer">
					<div className="head">
						<div className="clearfix">
							<div className="right rank">#{ selectn('plexuss_ranking', college) || '' }</div>
						</div>
						<div className="clearfix">
							<div className="left img"><img src={ img } alt="School Logo" /></div>
							<div className="right info">
								<h4 className="text-right">{ selectn('school_name', college) || '' }</h4>
								<div className="addr text-right">{ selectn('contact_info', college) || '' }</div>
								<div className="clearfix social">
									<div className="right">
										<img src={ social.linkedin } alt="linkedin share" />
									</div>
									<div className="right">
										<img src={ social.pinterest } alt="pinterest share" />
									</div>
									<div className="right">
										<img src={ social.twitter } alt="twitter share" />
									</div>
									<div className="right">
										<img src={ social.facebook } alt="linkedin share" />
									</div>
									<div className="right share"><b>SHARE:</b></div>
								</div>
							</div>
						</div>

						<div className="nav-items">
							<ul className="small-block-grid-3 medium-block-grid-4 large-block-grid-6">
								<li className="text-center">
									<img src={ icons.overview } alt="nav icon" />
									<span>Overview</span>
								</li>
								<li className="text-center">
									<img src={ icons.stats } alt="nav icon" />
									<span>Stats</span>
								</li>
								<li className="text-center">
									<img src={ icons.ranking } alt="nav icon" />
									<span>Ranking</span>
								</li>
								<li className="hide-for-small-only text-center">
									<img className="admissions" src={ icons.admissions } alt="nav icon" />
									<span>Admissions</span>
								</li>
								<li className="show-for-large-up text-center">
									<img src={ icons.chat } alt="nav icon" />
									<span>Chat</span>
								</li>
								<li className="show-for-large-up text-center">
									<span>More</span>
									<span className="dropdown"></span>
								</li>
							</ul>
						</div>
					</div>
					<div className="media">
						<div className="toggle-btns"><span>PICS</span> | VIDEO | TOUR</div>
						<img src={ selectn('overview_image', college) || college.default_overview } alt="Slider image" />
						<div className="arrow left"></div>
						<div className="arrow right"></div>
					</div>
				</div>
				<div className="text-center preview-text">Preview</div>
			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		logo: state.logo,
		college: state.college,
	};
};

export default connect(mapStateToProps)(LogoPreview);
