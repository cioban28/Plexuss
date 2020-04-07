// /convo/sendMessageField.js

import { connect } from 'react-redux'
import React, { Component } from 'react'

import SendErrorModal from './sendErrorModal'

import { MAX_TEXT_CHAR_COUNT } from './constants'
import { updateConvoActions, 
		 sendMessage, 
		 setAttachmentNumber } from './../../../../actions/messagesActions'

class SendMessageField extends Component{
	constructor(props){
		super(props);

		this._send = this._send.bind(this);
		this._keyUp = this._keyUp.bind(this);
		this._keyPress = this._keyPress.bind(this);
		this._updateMsg = this._updateMsg.bind(this);
		this.sendCallback = this._sendCallback.bind(this);
		this._getLineCount = this._getLineCount.bind(this);
		this._getCharCount = this._getCharCount.bind(this);
		this._typingMessage = this._typingMessage.bind(this);
		this._doneTypingMessage = this._doneTypingMessage.bind(this);

		this.state = {
			typing: false,
		};
	}

	componentWillReceiveProps(np){
		let { dispatch, messages: _m } = this.props,
			{ messages: _nm } = np,
			activeT = _.get(_m, 'activeThread', {}),
			nextActiveT = _.get(_nm, 'activeThread', {});

		// when active thread has changed, clear send field
		if( activeT.thread_id !== nextActiveT.thread_id ) 
			dispatch( updateConvoActions({send_field: '', template_too_big: false}) );
	}

	_getLineCount(){
		let { messages } = this.props;

		if( !messages.send_field ) return 1;
		return messages.send_field.split('\n').length;
	}

	_getCharCount(){
		let { messages } = this.props;
		return _.get(messages, 'send_field.length', 0);
	}

	_updateMsg(e){
		
		let { dispatch, messages } = this.props,
			thread = messages.activeThread || {},
			val = e.target.innerHTML,
			count = val.length,
			template_too_big = false;

		// thread has_text, then msg length cannot exceed MAX_TEXT_CHAR_COUNT 
		if( thread.has_text && count <= MAX_TEXT_CHAR_COUNT ) template_too_big = true;

		dispatch( updateConvoActions({send_field: val, template_too_big}) );
	}

	_keyPress(e){
		const { nativeEvent: event } = e;
		const { typing } = this.state;
		const { dispatch, messages: _m } = this.props;

		// if the enter key is pressed without the shiftkey, send message
		if( !e.shiftKey && e.keyCode === 13 ){
			e.preventDefault();
			this._send();

		}else if( !typing ){
			// only need to emit typing event once - no need for every char entered
			this._typingMessage(e.keyCode);
			this.setState({typing: true})
		}
	}

	_typingMessage(code){
		// console.log('typing!');
		const { user: _u, messages: _m } = this.props;

		_m._socket.emit('typing:message', {
			code,
			user_id: _u.id,
			thread_room: _m.current_thread_room,
			name: _u.fname,
		});
	}	

	_doneTypingMessage(){
		// console.log('no longer typing b/c of blur!');
		const { messages: _m } = this.props;

		// emit to typing:message without code to signal that user is done typing
		_m._socket.emit('typing:message', {thread_room: _m.current_thread_room});

		// reset typing to false to trigger _typingMessage again on next char entered
		this.setState({typing: false});
	}

	_keyUp(){
		const { dispatch } = this.props;
		
		if( $('#send_field').find('.upload-file-wrapper').length === 0 )
			dispatch( setAttachmentNumber(0) );
	}

	_send(e){
		e && e.preventDefault();

		var { dispatch, messages: _m, user } = this.props,
			{ current_thread_room: thread_room } = _m,
			{ id: user_id } = user,
			message = _m.send_field;

		var thread = _m.activeThread || {},
			{ thread_id, thread_type, thread_type_id: to_user_id } = thread;

		if( thread_id && message ){
			let data = { 
				user_id,
				message, 
				thread_id, 
				to_user_id,
				thread_type, 
				thread_room,
			};

			dispatch( sendMessage(data, this._sendCallback) );
		}
	}

	_sendCallback(){
		//React creates warning with contentEditable if components or React values are children of
		// using html and JQUery to append, ect... (maybe till finding a better solution)
		$('#send_field').html('');
	}

	render(){
		let { dispatch, messages: _m } = this.props,
			thread = _m.activeThread || {},
			line_count = this._getLineCount(),
			count = this._getCharCount(),
			disabler = _m.send_pending || _.isEmpty(thread);

		return (
			<form className="send-container" onSubmit={ this._send }>

				{ !!thread.has_text && <div className="text-icon" /> }

				<div
					contentEditable="true"
					id="send_field"
					name="send_field"
					className={ thread.has_text ? 'is-text' : '' }
					value={ _m.send_field || '' }
					onKeyDown={ this._keyPress }
					onKeyUp={ this._keyUp }
					onChange={ this._updateMsg }
					onInput={ this._updateMsg }
					onBlur={ this._doneTypingMessage }
					disabled={ disabler }
					placeholder={ thread.has_text ? 'Text Message' : 'Send message' }/>

				{ !!thread.has_text && <div className="text-count">{count}/{MAX_TEXT_CHAR_COUNT}</div> }

				<button 
					disabled={ disabler }
					className="button send">
						{ (!_m.send_pending && _m.activeThread) && <span>Send</span> }
						{ disabler &&
							<div>
								&nbsp;
								<div className="send-loader" />
							</div> }
				</button>

				{ _m.send_err && <SendErrorModal {...this.props} /> }

			</form>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		user: state.user,
		messages: state.messages,
	};
};

export default connect(mapStateToProps)(SendMessageField);