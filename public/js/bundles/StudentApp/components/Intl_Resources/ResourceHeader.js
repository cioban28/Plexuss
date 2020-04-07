// /Intl_Resources/ResourceHeader.js

import React from 'react'

class ResourceHeader extends React.Component {
	render(){
		let { header } = this.props;

		return (
			<h4 className="resource-header">
				<div className={'icon sm '+header.icon} />
				{ header.title }
			</h4>
		);
	}
}

export default ResourceHeader;