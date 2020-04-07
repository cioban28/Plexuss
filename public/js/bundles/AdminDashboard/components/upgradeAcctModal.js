// upgradeAcctModal.js

import React from 'react'
import createReactClass from 'create-react-class'

export default createReactClass({
	render(){
		return (
			<div id="upgrade-acct-modal" className="reveal-modal" data-reveal="" aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
                <div className="row">
                    <div className="column small-12 text-right">
                        <a className="close-reveal-modal help_helpful_videos_close_icon" aria-label="Close">&#215;</a>
                    </div>
                </div>

                <div className="row upgrade-msg-row">
                    <div className="column small-12 text-center">
                        Upgrade your account to filter your daily student recommendations
                    </div>
                </div>

                <div className="row filter-intro-container" data-equalizer>
                    <div className="column small-12 medium-4">
                        <div className="filter-intro-step" data-equalizer-watch>
                            <div className="text-center">1</div>
                            <div className="text-center">
                                <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/step-1-filter.png" alt="Plexuss" />
                            </div>
                            <div>
                                You receive student recommendations daily, but youre looking for certain kinds of students
                            </div>
                        </div>
                    </div>
                    <div className="column small-12 medium-4">
                        <div className="filter-intro-step" data-equalizer-watch>
                            <div className="text-center">2</div>
                            <div className="text-center">
                                <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/step-2-filter.png" alt="Plexuss" />
                            </div>
                            <div>
                                {"Choose what you'd like to filter by and save your changes (menu on the left)"}
                            </div>
                        </div>
                    </div>
                    <div className="column small-12 medium-4">
                        <div className="filter-intro-step" data-equalizer-watch>
                            <div className="text-center">3</div>
                            <div className="text-center">
                                <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/step-3-filter.png" alt="Plexuss" />
                            </div>
                            <div>
                                Based on your filters, you will receive recommendations that may be a better fit for your school
                            </div>
                        </div>
                    </div>
                </div>

                <div className="row upgrade-or-naw-btn-row">
                    <div className="column small-12 medium-6 large-5 large-offset-1 text-right">
                        <a href="" data-reveal-id="thankyou-for-upgrading-modal" className="radius button">{"I'd like to upgrade my account"}</a>
                    </div>
                    <div className="column small-12 medium-6 large-5 end">
                        <a href="" className="radius button secondary close-reveal-modal" aria-label="Close">{"I'll think about it"}</a>
                    </div>
                </div>
            </div>
		);
	}
});
