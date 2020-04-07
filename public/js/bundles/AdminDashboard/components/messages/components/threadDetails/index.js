// threadDetails.js

import { connect } from 'react-redux'
import React, { Component } from 'react'

import RepPanel from './repPanel'
import StudentPanel from './studentPanel'

import './styles.scss'

class ThreadDetails extends Component{
	constructor(props){
		super(props);
	}

	shouldComponentUpdate(np){
		let { messages: _m } = this.props,
			{ messages: _nm } = np,
			this_details = _.get(_m, 'activeThread.user_info.name'),
			next_details = _.get(_nm, 'activeThread.user_info.name');

		return this_details !== next_details;
	}

	render(){
		let { messages } = this.props,
			_user = _.get(messages, 'activeThread.user_info', {}),
			classes = _.isEmpty(_user) ? 'close' : '';

		return (
			<div id="_threadDetailsContainer" className={ classes }>
				{ (_user && !_.isEmpty(_user)) && <StudentPanel _user={_user} /> }
			</div>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		messages: state.messages,
	};
};

export default connect(mapStateToProps)(ThreadDetails);