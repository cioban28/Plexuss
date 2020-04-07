import React, { Component } from 'react'

export default class DashMessage extends Component{
	render(){
		let { body } = this.props;

		return (
			<div className="dash-message-container clearfix">
				<div className="dash-msg-body" dangerouslySetInnerHTML={{__html: body || ''}} />
			</div>
		);
	}
}