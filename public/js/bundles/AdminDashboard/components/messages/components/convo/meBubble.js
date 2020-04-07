// meBubble.js

import React from 'react'
import createReactClass from 'create-react-class'

export default createReactClass({
	_toHTML(){
		let { convo } = this.props,
			msg = convo.msg || '';

		return {__html: msg};
	},

	_formatTime(){
		let { convo } = this.props;
		return moment(convo.date).format('h:mm a');
	},

	render(){
		let { user, convo } = this.props;

		return (
			<div className="me-bubble">

				<div className="pic-wrapper" style={{backgroundImage: 'url('+(convo.img || user.profile_pic)+')'}}>
					<div className="pic-name">{ convo.Name || 'No name' }</div>
				</div>

				<div className="msg">
					<div dangerouslySetInnerHTML={ this._toHTML() } />
					<div className="arrow" />
				</div>

				<div className="time">{ this._formatTime() }</div>

			</div>
		);
	}
});
