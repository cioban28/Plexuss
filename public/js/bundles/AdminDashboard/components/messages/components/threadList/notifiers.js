// thread.js

import moment from 'moment'
import React, { Component } from 'react'

export default class Notifiers extends Component{
	constructor(props){
		super(props);	
		this._formatTime = this._formatTime.bind(this);
		this._formatUnread = this._formatUnread.bind(this);
	}

	shouldComponentUpdate(np){
		let { num_unread_msg } = this.props,
			{ num_unread_msg: next_unread } = np;

		// only update if there is a new msg
		return num_unread_msg !== next_unread;
	}

	_formatUnread(){
		let { num_unread_msg } = this.props,
			unread = +num_unread_msg || 0,
			max = 10;

		if( unread > max ) return max+'+';
		return unread;
	}

	_formatTime(){
		let { date } = this.props,
			today = moment().format('YYYY-MM-DD'),
			date_split = date.split(' ')[0];

		// if last message was sent today, show time
		if( moment(today).isSame(date_split) ) return moment(date).format('h:mm A');

		// if last message was 1 day ago, show "Yesterday" 
		if( moment().subtract(1, 'day').isSame(date_split.date, 'day') ) return 'Yesterday';

		// else just show time ago
		let time_ago = moment(date).fromNow();

		if( time_ago.includes('ago') ){
			time_ago = time_ago.split(' ').slice(0, 2).join(' ');
			time_ago = time_ago.split(' ')[0] + ' ' + time_ago.split(' ')[1][0]; // example result: 2 d (2 days)
		}

		// if time ago = 'a year ago', transform to '1 year ago'
		if( time_ago[0] === 'a' ) return '1 '+time_ago.split(' ')[1];

		return time_ago;
	}

	render(){
		let { formatted_date } = this.props,
			unread = this._formatUnread(),
			time = this._formatTime();

		return (
			<div className="notifier-wrapper">
				<div className="time">{ time }</div>
				<div>&nbsp;</div>
				{ unread ? 
					<div className="notification">{ unread }</div> : 
					<div className="viewed">
						<div className="viewed-date">{ formatted_date }</div>
					</div> }
			</div>
		);
	}
}
