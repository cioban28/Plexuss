// convoActionsContainer.js

import { connect } from 'react-redux'
import React, { Component } from 'react'

import SendMessageField from './sendMessageField'
import TemplateContainer from './templateContainer'


export default class ConvoActionsContainer extends Component{
	render(){
		return (
			<div id="_actionsContainer">
        <div className="web_view"><TemplateContainer /></div>
				<SendMessageField />
        <div className="mobile_view"><TemplateContainer /></div>
			</div>
		);
	}
}
