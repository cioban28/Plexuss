import React from 'react';
import createReactClass from 'create-react-class'

export default createReactClass({

	render() {
		var { conference } = this.props;

		return (
			<div className="row" style={{margin: '0 0 20px'}}>
				<div className="column small-2">
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/conference-icon.png" className="conference"></img>
				</div>
				<div className="column small-1 conf-text date">
					{conference.date || ''}
				</div>
				<div className="column small-5 conf-text name text-center">
					{conference.name || ''} - {conference.location || ''}
				</div>
				<div className="column small-2 conf-text booth text-center">
					{conference.booth_num || ''}
				</div>
				<div className="column small-1 rsvp text-center">
					<a href="http://sinashayesteh.youcanbook.me/" target="_blank">RSVP</a>
				</div>
				<div className="column small-1 conf-text text-center">
					<a href="/conferences" target="_blank">View all</a>
				</div>
			</div>
		);
	}
});
