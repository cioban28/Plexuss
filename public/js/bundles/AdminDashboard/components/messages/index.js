// index.js

import io from 'socket.io-client'
import { connect } from 'react-redux'
import React, { Component } from 'react'

import ThreadList from './components/threadList'
import Convo from './components/convo'
import ThreadDetails from './components/threadDetails'

import { getProfile } from './../../actions/profileActions'
import { 
	setSocket, 
	openThread,
	updateThreads, 
	addNewMessage,
	updateSingleThread,
	updateThreadOfPersonYouAreMessaging,
} from './../../actions/messagesActions'

import './styles.scss'

let socket;

class Messages extends Component{
	constructor(props){
		super(props);	

		socket = io(`${window.location.host}:3001`, {
			secure: window.location.protocol.includes('https')
		});
		this.setShowConversation = this.setShowConversation.bind(this);
		this.unsetShowConversation = this.unsetShowConversation.bind(this);

		this._connect = this._connect.bind(this);
		this._disconnect = this._disconnect.bind(this);

		this._joinRoom = this._joinRoom.bind(this);
		this._joinedRoom = this._joinedRoom.bind(this);
		this._joinThread = this._joinThread.bind(this);
		this._joinedThread = this._joinedThread.bind(this);
		this._updatedThread = this._updatedThread.bind(this);
		this._joinedAllThreads = this._joinedAllThreads.bind(this);

		this._sentMessage = this._sentMessage.bind(this);
		this._typedMessage = this._typedMessage.bind(this);
		this._doneTypingMessage = this._doneTypingMessage.bind(this);

		this.state = {
			showConversation: false
		};

		socket.on('connect', this._connect);
		socket.on('disconnect', this._disconnect);
		socket.on('joined:room', this._joinedRoom);
		socket.on('joined:thread', this._joinedThread);
		socket.on('joined:all_threads', this._joinedAllThreads);
		socket.on('updated:thread', this._updatedThread);

		socket.on('sent:message', this._sentMessage);
		socket.on('typed:message', this._typedMessage);	
	}

	// built-in methods
	componentWillMount(){
		// console.log('socket: ', socket);

		const { dispatch, user } = this.props;

		dispatch( getProfile() ); // get user data
		dispatch( setSocket(socket) ); // save socket to store

		// if we already have profile data, immediately join room
		if( user.initProfile ) this._joinRoom(user);
	}	

	componentDidMount(){
		// if chatbar or webinarbar is visible, push message window down
		const chatbar = document.getElementById('_chat_bar'),
			webinarbar = document.getElementById('_webinar_bar');

		if( chatbar || webinarbar ) 
			document.getElementById('_messagesContainer').style.top = '133px';	

        mixpanel.track('admin-messages', { Location: 'Dashboard' });
	}

	componentWillReceiveProps(np){
		const { dispatch, user: _u, messages: _m } = this.props,
			{ user: _nu, messages: _nm } = np;

		// once profile is done initializing, join room - need user id
		if( _u.initProfile !== _nu.initProfile && _nu.initProfile ) this._joinRoom(_nu);	

		// join all threads whenever thread list length has increased from previous state
		if( _.get(_nm, 'threads.length', 0) > _.get(_m, 'threads.length', 0) ){
			// console.log('--------------- thread list length has increased! ---------------');
			this._joinAllThreads(_nm.threads);
		}
	}

	// connect/disconnect
	_connect(){
		const { messages: _m, user: _u } = this.props;
		// console.log('is admin currently in thread room? ', _m.current_thread_room);
		if( _m.current_thread_room ) this._joinThread();
	}

	_disconnect(){
		// console.log('bruh - just disconnected from nodejs - wtf');
	}

	// room related methods
	_joinRoom({org_school_id, agency_id, id, fname}){
	 	let representative_type = window.location.href.includes('admin') ? 'admin' : 'agency';

	 	let room = representative_type == 'agency' ? agency_id  : org_school_id;

		socket.emit('join:room', {
			room: room, 
			user_id: id, 
			name: fname
		});
	}

	_joinedRoom({ online }){
		// console.log('admin is online!');
	}

	// thread related methods
	_joinThread(){
		// console.log('admin is joining thread using active thread data');

		const representative_type = window.location.href.includes('admin') ? 'admin' : 'agency',
			{ user, messages: _m } = this.props,
			prefix = representative_type == 'admin' ? user.org_school_id : user.agency_id;

		let thread_room = prefix+':'+_m.activeThread.thread_id;

		socket.emit('join:thread', {
			name: user.fname, 
			user_id: user.id, 
			thread_room,
		});
	}	

	_joinedThread({ thread_room }){
		// console.log('admin joined thread!');
		
		const { dispatch } = this.props;
		dispatch( openThread({ thread_room }) );
	}

	_joinAllThreads(threads){
		// console.log('joining all threads');

		const { user } = this.props;

		socket.emit('join:all_threads', {
			threads,
			name: user.fname, 
			user_id: user.id, 
			college_id: user.org_school_id,
			agency_id: user.agency_id,
		});	
	}

	_joinedAllThreads(all_rooms){
		// console.log('joined all threads: ', all_rooms);
	}

	_updatedThread(thread){
		// console.log('got update thread! ', thread);
		const { dispatch, user: _u } = this.props;

		// console.log('new thread coming in: ', thread);
		
		// only need to update thread if this thread is intended for this user
		if( _u.id == thread.user_id ) dispatch( updateSingleThread(thread) );
	}

	// message related methods
	_sentMessage(message){
		const { dispatch } = this.props;

		// console.log('new message', message);

		// add new message to convo
		dispatch( addNewMessage( message ) );

		// disable ellipsis
		this._doneTypingMessage();
	}	

	_typedMessage({ user_id, thread_room, code, name }){
		// console.log(`admin:${user_id} - ${name} - is typing...`);
		const { dispatch, messages: _m } = this.props;

		dispatch( updateThreads({
			show_ellipsis: (!!code && _m.current_thread_room == thread_room),
			ellipsis_name: name,
		}) );
	}	

	setShowConversation() {
		if(this.state.showConversation == false){
			this.setState({showConversation: true});
		}
	}
	unsetShowConversation() {
		if(this.state.showConversation == true){
			this.setState({showConversation: false});
		}
	}
	_doneTypingMessage(){
		// console.log('admin is done typing!');
		const { messages: _m } = this.props;
		_m._socket.emit('typing:message', {thread_room: _m.current_thread_room});
	}

	// render: built-in
	render(){
		const { params, user } = this.props,
			{ whosOnline, _threads, thread_room_id, msg, messageList, show_ellipsis } = this.state,
			_currentThread = thread_room_id ? thread_room_id.split(':')[1] : '';

		return (
			<div id="_messagesContainer" className="clearfix">
				<ThreadList showConversation={this.state.showConversation} unsetShowConversation={this.unsetShowConversation} setShowConversation={this.setShowConversation} params={params} />
				<Convo showConversation={this.state.showConversation} unsetShowConversation={this.unsetShowConversation} setShowConversation={this.setShowConversation}/>
				<ThreadDetails />
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

export default connect(mapStateToProps)(Messages);
