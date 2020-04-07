// convo.js

import React from 'react'
import { connect } from 'react-redux'
import createReactClass from 'create-react-class'

import ConvoWindow from './convoWindow'
import ConvoActionsContainer from './convoActionsContainer'

import './styles.scss'

const Convo = createReactClass({
	render(){
		let { messages, showConversation, unsetShowConversation} = this.props,
			thread_info = _.get(messages, 'activeThread.user_info', {}),
			expand_if_no_details = _.isEmpty(thread_info) ? 'full' : '';

		return (
			<div id="_convoContainer" className={expand_if_no_details, showConversation ? "show_magic" : "vanish_magic"}>
				<ConvoWindow unsetShowConversation={unsetShowConversation}/>
				<ConvoActionsContainer />
			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		messages: state.messages,
	};
};

export default connect(mapStateToProps)(Convo);
