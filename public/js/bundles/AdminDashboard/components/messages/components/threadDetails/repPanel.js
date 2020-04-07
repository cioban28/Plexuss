// threadDetails.js

import React from 'react'
import createReactClass from 'create-react-class'

export default createReactClass({
	render(){
		let { _user } = this.props;

		return (
			<div id="_threadDetailsContainer">

				<div className="rep-youre-messaging clearfix">
					<div className="school-dets">
						<div className="bk-logo" style={{backgroundImage: 'url('+_user.profile_img_loc+')'}}></div>
						<div className="name-rank">
							<div><div className="rank">{'#' + (_user.rank || 'N/A') }</div></div>
							<div><div className="school-name">{_user.school_name || ''}</div></div>
						</div>
						<div className="detailed-info">{_user.address || 'Address: unavailable'}</div>
					</div>
					<div className="rep-dets text-center">
						<div className="name">{_user.name || 'College Representative'}</div>
						<div className="title">{_user.title || 'Title: Unavailable'}</div>
						<div className="since">{ _user.member_since ? 'Since ' + _user.member_since : 'N/A'}</div>
						<div className="pic" style={pic}></div>
						<div className="college">{_user.school_name || ''}</div>
					</div>
					<div className="go-to-col-btn text-center"><a href={_user.slug}>View College Stats</a></div>
				</div>

			</div>
		);
	}
});
