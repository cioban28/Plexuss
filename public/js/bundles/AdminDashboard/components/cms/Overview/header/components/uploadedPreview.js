// uploadedPreview.js

import React from 'react'
import createReactClass from 'create-react-class'

export default createReactClass({
	render(){
		let { item } = this.props;

		return(
			<div
				className="img-preview"
				style={ item.bg || {} } >
				{ item.video_id && <iframe
									width="100%"
									height="100%"
									src={ item.source }
									frameBorder="0"
									allowFullScreen /> }
			</div>
		);
	}
});
