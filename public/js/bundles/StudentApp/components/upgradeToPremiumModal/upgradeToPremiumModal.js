import React, {Component} from 'react';
import './styles.scss';

export default class UpgradeToPermiumModal extends Component{
	
	render(){
		return(
			<div className="_upgradePremiumModal ">

				<div className="modalback"></div>

				<div className="premiumModal">

					<div id="upgradeModal_closeBtn" className="close-btn">&times;</div>

					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/plexuss-premium-icon.png" />

					<h1>Join Plexuss Premium Today</h1>
					<h2>By becoming premium you will have access to:</h2>

					<div className="darkbox">
						<div className="clearfix">
							<div className="lock-img">
								<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/unlock-icon.png" />
							</div>
							<div className="darkbox-title mb20">
								Unlock 50 College Essays That Got Students into Top Universities such as:
							</div>
						</div>	

						<div className="school-result clearfix">
							<div className="school-img-cont">
								<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/Harvard_University.png" />
							</div>
							<div className="school-name">Harvard University</div>
						</div>
						<div className="school-result clearfix">
							<div className="school-img-cont">
								<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/Massachusetts_Institute_of_Technology.png" />
							</div>
							<div className="school-name">Massachusetts Institute of Technology</div>
						</div>


						<div className="more-uni">+ More Universities</div>
						
					</div>

					<a href="/checkout/premium" className="goto-upgrade-btn">Upgrade to premium for $499</a>



				</div>


			</div>
		)
	}
}
