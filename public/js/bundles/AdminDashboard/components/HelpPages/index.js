import React from 'react'
import './styles.scss'
import ImageBanner from './../Base/ImageBannerWOverlay/imageBannerWOverlay'
import HelpBody from './components/helpBody'
import createReactClass from 'create-react-class'

export default createReactClass({

	render(){

		return (
			<div className="helpPages-container">
				<div className="banner-wrapper clearfix">
					<ImageBanner min="1" max="2"></ImageBanner>

					<div className="row dashboard-stats-container">
						<div className="dash-centered-row">
							<div className="column help-title">
								<span className="help-faq"></span>
								Help/FAQs
							</div>
							<div className="column help-searchBar"></div>
						</div>
					</div>
				</div>


				<HelpBody></HelpBody>
			</div>
		);
	}


});
