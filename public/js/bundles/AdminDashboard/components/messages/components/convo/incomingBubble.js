// incomingBubble.js

import React, { Component } from 'react'

export default class IncomingBubble extends Component{
	constructor(props){
		super(props);
		this._toHTML = this._toHTML.bind(this);
		this._formatTime = this._formatTime.bind(this);	
	}	

	_toHTML(){
		let { convo } = this.props,
			msg = convo.msg || '';

		return {__html: msg};
	}

	_formatTime(){
		let { convo } = this.props;
		return moment(convo.date).format('h:mm a');
	}	

	render(){
		let { convo, name } = this.props;

		return (
			<div className="incoming-bubble">

				<div className="msg">
					<div dangerouslySetInnerHTML={ this._toHTML() } />
					<div className="arrow" />
				</div>

				<div className="pic-wrapper" style={{backgroundImage: 'url('+convo.img+')'}}>
					<div className="pic-name">{ convo.Name || 'No name' }</div>
				</div>

				<div className="time">{ this._formatTime() }</div>	

			</div>
		);
	}
}
