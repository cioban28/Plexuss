// index.js
import React from 'react'
import DocumentTitle from 'react-document-title'
import createReactClass from 'create-react-class'

import ProfileContainer from './../../profile_container'

export default createReactClass({
	render(){
		return (
			<DocumentTitle title="Admin Tools | Rep Profile">
				<div className="tools-section">
					<ProfileContainer hideHeader={true} />
				</div>
			</DocumentTitle>
		);
	}
});
