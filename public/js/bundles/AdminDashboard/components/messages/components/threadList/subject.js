// thread.js

import React, { Component } from 'react'

const MAX_NAME_LEN = 24;
const MAX_NAME_LEN_WITH_OTHERS = 11;

export default class Subject extends Component{
	constructor(props){
		super(props);
		this._getThreadMembers = this._getThreadMembers.bind(this);
	}

	shouldComponentUpdate(np){
		let { num_unread_msg, msg } = this.props,
			{ num_unread_msg: next_unread, msg: next_msg } = np;

		// only update if there is a new msg
		return num_unread_msg !== next_unread || msg !== next_msg;
	}

	_getThreadMembers(){
		let { Name, thread_members } = this.props,
			members_without_me = null;

		if( _.get(thread_members, 'length', 0) > 1 ) 
			members_without_me = _.filter(thread_members, m => m.Name !== Name);

		return members_without_me;
	}

	render(){
		let { Name, msg } = this.props,
			members = this._getThreadMembers(),
			others = '+'+(members && members.length)+' others';

		// add ellipsis to names that are too long - doing this instead of using css cause it's required to put overflow: hidden which cuts off the tooltip also
		if( members && Name.length > MAX_NAME_LEN_WITH_OTHERS ) Name = Name.slice(0, MAX_NAME_LEN_WITH_OTHERS) + '...';
		else if( Name.length > MAX_NAME_LEN ) Name = Name.slice(0, MAX_NAME_LEN) + '...';

		return (
			<div className="subject-wrapper">
				<div className="name">
					{ Name || 'No Name' }
					{ members && 
						<span className="tipper">
							{ others }
							<div>{ members.map(m => <div key={m.Name}>{m.Name}</div>) }</div>
						</span> }
				</div>
				
				<div className="msg-preview">{ msg || <span>&nbsp;</span> }</div>
			</div>
		);
	}
}