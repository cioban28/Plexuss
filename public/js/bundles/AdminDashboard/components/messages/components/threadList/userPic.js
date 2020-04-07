// userPic.js

import React, { Component } from 'react'

export default class UserPic extends Component{
	constructor(props){
		super(props);
		this.state = {did_init: false}
	}

	shouldComponentUpdate(np){
		var { messages: _m, thread_id } = this.props,
			{ messages: _nm } = np,
			{ did_init } = this.state,
			activeT = _.get(_m, 'activeThread', {}),
			nextActiveT = _.get(_nm, 'activeThread', {}),
			id = _.get(activeT, 'thread_id'),
			next_id = _.get(nextActiveT, 'thread_id'),
			update = false;

		// only need to check for update userPic if this thread is active	
		if( next_id === thread_id ){

			// if activeThread is diff, convo is pending, and have not init, update
			if( (id !== next_id) && _nm.init_convo_pending) update = true;
			else if( (id === next_id) && !_nm.init_convo_pending) update = true;
			
		}

		return update;
	}

	render(){
		let { messages: _m, img, thread_id, has_text, is_campaign } = this.props,
			thread = _.get(_m, 'activeThread', {}),
			_thread_id = thread.thread_id || '';

		return (
			<div className="pic-wrapper">
				<div className="pic" style={{backgroundImage: 'url('+img+')'}}>
					{ !!has_text && 
						<div className="type text">
							<div className="type-tip">Text Message</div>
						</div> }

					{ !!is_campaign && 
						<div className="type campaign">
							<div className="type-tip">Campaign</div>
						</div> }

					{ (_thread_id === thread_id && _m.init_convo_pending) && <div className="convo-loader" /> }
				</div>
			</div>
		);
	}
}