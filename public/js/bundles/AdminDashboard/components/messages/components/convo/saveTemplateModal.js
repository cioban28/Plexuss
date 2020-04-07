// /convo/saveTemplateModal.js

import React, { Component } from 'react'

import CustomModal from './../../../../../utilities/customModal'

import { 
	updateConvoActions, 
	saveTemplate } from './../../../../actions/messagesActions'

export default class SaveTemplateModal extends Component{
	constructor(props){
		super(props);

		this._saveTemplate = this._saveTemplate.bind(this);

		this.state = {err: false};
	}

	_saveTemplate(e){
		e.preventDefault();
		let { dispatch, messages: _m, _close } = this.props,
			name = _m.save_template_name,
			content = _m.send_field;

		if( name && content ){
			_close();
			dispatch( saveTemplate(name, content) );

		}else this.setState({err: true});
	}

	render(){
		let { dispatch, messages: _m, _close } = this.props,
			{ err } = this.state;

		return (
			<CustomModal backgroundClose={ _close }>
				<div className="save-template-modal">
					<div className="tclose">
						<span onClick={ _close }>&times;</span>
					</div>

					<div className="tname">Template Name:</div>

					<form onSubmit={ this._saveTemplate }>
						<input
							name="save-temp-name"
							type="text"
							value={ _m.save_template_name || '' }
							onChange={ e => dispatch( updateConvoActions({save_template_name: e.target.value}) ) }
							placeholder="Enter template name" />
					</form>

					{ err && <div className="err">Cannot save a template with empty name or content.</div> }

					<div className="tsave" onClick={ this._saveTemplate }>Save</div>
				</div>
			</CustomModal>
		);
	}
}