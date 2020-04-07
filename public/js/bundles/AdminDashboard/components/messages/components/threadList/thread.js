// thread.js

import { connect } from 'react-redux'
import React, { Component } from 'react'

import UserPic from './userPic'
import Subject from './subject'
import Notifiers from './notifiers'

import { 
	getConvo,
	messageRead,
	updateThreads,
} from './../../../../actions/messagesActions'

class Thread extends Component{
	constructor(props){
		super(props);

		this._isActive = this._isActive.bind(this);
		this._joinThread = this._joinThread.bind(this);

		this.state = {};
	}	

	componentWillMount(){
		let { dispatch, thread, index, messages: _m } = this.props,
			activeT = _.get(_m, 'activeThread.thread_id');

		// if we have socket saved, no active thread has been set and it's the first thread in the list, make active
		if( _m._socket && !activeT && index === 0 ) this._joinThread(); // join first thread

		// if thread does not have convo, fetch it
		if( !thread.convo ) dispatch( getConvo(thread.thread_id) );	
	}

	componentWillReceiveProps(np){
		const { dispatch, messages: _m, thread: _t } = this.props,
			{ messages: _nm, thread: _nt } = np;

		const { activeThread: _at } = _m,
			{ activeThread: _nat } = _nm;

		if( _.get(_nat, 'thread_id') == _nt.thread_id && _nt.thread_id == _nm.undeterminedThreadNowHasId ){
			console.log('undeterminedThreadNowHasId!!');
			dispatch( updateThreads({undeterminedThreadNowHasId: null}) ); // reset 
			this._joinThread(_nt.thread_id); // join new thread to listen for others in this thread room
		}
	}

	componentDidUpdate(pp){
		const { dispatch, messages: _m, thread: _t } = this.props,
			{ messages: _nm } = pp;

		const this_thread_is_active = _.get(_m, 'activeThread.thread_id') == _t.thread_id;

		// if this thread is active, thread has unread msgs and message read is not currently pending, set read.
		if( this_thread_is_active && _t.num_unread_msg > 0 && !_m.read_pending ){
			dispatch( messageRead(_t.thread_id) );
		}
	}

	_joinThread(){
	 	const representative_type = window.location.href.includes('admin') ? 'admin' : 'agency';

		let { user, messages: _m, thread } = this.props,
			prefix = representative_type == 'admin' ? user.org_school_id : user.agency_id,
			thread_room = prefix+':'+thread.thread_id,

			data = {
				name: user.fname, 
				user_id: user.id, 
				thread_room,
			};

		_m._socket.emit('join:thread', data);
	}	

	_isActive(){
		let { messages: m, thread } = this.props;

		if( m.activeThread && (m.activeThread.thread_id === thread.thread_id) ) return 'thread active';
		return 'thread';
	}

	threadClickHandler = () => {
		this._joinThread();
		this.props.setShowConversation();
	}

	render(){
		let { messages, thread } = this.props,
			is_active = this._isActive();

		return (
			<div className={ is_active } onClick={ this.threadClickHandler }>
				<UserPic {...thread} messages={messages} />
				<Subject {...thread} />
				<Notifiers {...thread} />
				<span><img className="show-for-small-only mobile-menu-arrow img-right" src="/images/mobile_menu_arrow.png"/></span>
			</div>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		user: state.user,
		messages: state.messages,
	};
};

export default connect(mapStateToProps)(Thread);
