// thankyouForUpgradingModal.js

import React from 'react'
import createReactClass from 'create-react-class'

export default createReactClass({
	render(){
		return (
			<div id="thankyou-for-upgrading-modal" className="reveal-modal" data-reveal="" aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
                <div className="row close-modal-x">
                    <div className="column small-12 text-right">
                        <a className="close-reveal-modal help_helpful_videos_close_icon" aria-label="Close">&#215;</a>
                    </div>
                </div>
                <div className="row">
                    <div className="column small-12 text-center thankyou-msg-col">
                        <div>Thank you!</div>
                        <div>Sina or Molly will contact you very soon to get you set up with your new account.</div>
                        <div>{"(We're working on giving you a place to manage upgrading your account in the future, so thank you for your patience.)"}</div>
                    </div>
                </div>
                <div className="row">
                    <div className="column medium-8 large-6 medium-centered text-center">
                        <a href="" className="radius button secondary close-reveal-modal" aria-label="Close">Looking forward to it ;)</a>
                    </div>
                </div>
            </div>
		);
	}
});
