// sendErrorModal.js

import React, { Component } from 'react'

import CustomModal from './../../../../../utilities/customModal'

import { updateConvoActions } from './../../../../actions/messagesActions'

export default class SaveTemplateModal extends Component{
	constructor(props){
		super(props);
	}

	render(){
		let { dispatch, messages: _m } = this.props;

		return (
			<CustomModal backgroundClose={ e => dispatch( updateConvoActions({send_err: false}) ) }>
				<div className="plex-err-modal">
					<div className="tclose">
						<span onClick={ e => dispatch( updateConvoActions({send_err: false}) ) }>&times;</span>
					</div>

					<br />
					<div className="err-title">{ _m.send_err }</div>

					<br />
					<div>Because of this error, you unfortunately will not be able to message this user at the moment, but if you contact us at support@plexuss.com and tell us about this error that you are receiving, we will be sure to fix the problem asap.</div>

					<br />
					<div 
						className="tsave"
						onClick={ e => dispatch( updateConvoActions({send_err: false}) ) }>Close</div>
				</div>
			</CustomModal>
		);
	}
}