// existingMemberReminderModal.js

import React from 'react'
import createReactClass from 'create-react-class'

export default createReactClass({
	render(){
		let { existing_client } = this.props;

		return (
			<div id="exist-member-reminder-modal" className="reveal-modal" data-reveal data-options="close_on_background_click: false"  data-remind={existing_client}>
				<div className="clearfix">
		  			<div className="right">
		       			<a className="close-reveal-modal help_helpful_videos_close_icon">&#215;</a>
		  			</div>
				</div>

				<div className="row reminder">
		  			<div className="column small-12 medium-7 large-7">
						<h3>You havent set your goals</h3>
						<br />
						<p>Colleges who set up their goal are 60% more successful.<br />
						     Please set up a time to set your enrollment application goal with your premiere support representative.
						</p>
		  			</div>

		  			<div className="column small-12 medium-5 large-5 text-center" style={{color:'#989898', padding: "20px"}}>
		  				<h5>Approved Goal</h5>
		       			<div className="medium-text-center">
		       				<h4 style={{color:"#787878"}}>30</h4>
		       			</div>

							<div className="row progress radius round">
							<div className="meter column small-6" style={{width: "50%"}}>
								<div className="progress-meter-text text-left" style={{paddingLeft:"0px", color: "#FFF", lineHeight: "1.2em"}}>
									<span style={{textDecoration: "underline"}}>15</span>
							  	</div>
							</div>

							<div className="column small-6">
								<div className="progress-meter-text text-right" style={{paddingLeft:"0px", lineHeight: "1.2em"}}>
									<span>50%</span>
							  	</div>
							</div>
						</div>

					</div>
				</div>

				<div className="row">
		  			<div className="medium-6 large-4 medium-offset-3 large-offset-4 text-center modal-btn">
		  				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/calendar_icon.png" alt="" />
		  				<a href="https://plexuss.youcanbook.me/service/jsps/cal.jsp?cal=TYajJNYkhp7FodzADaXv" target="_blank">Choose Date</a>
		  			</div>
		  			<div className="small-6 medium-5 large-offset-4 end"></div>
				</div>
			</div>
		);
	}
});
