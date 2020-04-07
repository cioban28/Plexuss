import React from 'react'
import { Link } from 'react-router'
import createReactClass from 'create-react-class'

export default createReactClass({
	render() {
		let { func, funcid, url, revealid, hasRoute } = this.props, name = '';

		if( funcid === 'dash_cms' ) name = 'Add Ranking, update logo';

		return (
			<div id={funcid} className="medium-3 column end dash_indicator">
				<div className='indicator_feed text-center'>{name}</div>
				{
					hasRoute ?
					<Link to="/admin/tools"><div className='indicator_link'>{func}</div></Link>
					: <a href={url} data-reveal-id={revealid}></a>
				}
			</div>
		);
	}
});
