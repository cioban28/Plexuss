// TemplateContainer.js

import { connect } from 'react-redux'
import React, { Component } from 'react'

import EditorModal from './editorModal'
import SaveTemplateModal from './saveTemplateModal'
import EditTemplateModal from './editTemplateModal'
import AttachmentsButton from './../../../../../utilities/AttachmentsModal/attachmentsButton'

import { MAX_TEXT_CHAR_COUNT } from './constants'

import { 
	updateConvoActions,
	getTemplatesList, 
	loadTemplate } from './../../../../actions/messagesActions'

class TemplateContainer extends Component{
	constructor(props){
		super(props);

		this._loadTemplate = this._loadTemplate.bind(this);

		this.state = {
			saveOpen: false,
			editOpen: false,
			editorOpen: false,
			editName: false,
			areYouSureCheck: false,
		};
	}

	_loadTemplate(e){
		let { dispatch, messages: _m } = this.props,
			id = e.target.value,
			activeT = _.get(_m, 'activeThread'),
			already_loaded = _.find(_m.template_list.slice(), {id}),
			template_too_big = false;

		// if active thread is text and template selected length is larger than the max char count, don't set template content
		if( activeT.has_text && already_loaded.content.length > MAX_TEXT_CHAR_COUNT ) template_too_big = true;

		let msg = '';

		if(!template_too_big )
			msg = already_loaded.content;

		dispatch( updateConvoActions({ 
			template_too_big,
			send_field: msg,
			selected_template: already_loaded.id,
			attachmentNumber: 0 },

		));

		//due to contentEditable strange behavior with child components,  going append text to the div via Jquery
		$('#send_field').html(msg);
	}


	render(){
		let { dispatch, messages: _m } = this.props,
			{ saveOpen, editOpen, editorOpen, editName, areYouSureCheck } = this.state,
			templates = _m.template_list || [],
			_selected = _.get(_m, 'edit_template_selected', {});

		return (
			<div className="template-container">

				{ _m.template_too_big && <div className="templ-err">Template selected exceeds text message character count.</div> }

				{ !_.isEmpty(templates) && 
					<div className="action web_view">
						<select 
							value={ _m.selected_template || '' }
							onChange={ this._loadTemplate }>
								<option key={'disabled'} value="" disabled="disabled">Use a template</option>
								{ templates.map(t => <option key={t.id} value={t.id}>{t.name}</option>) }
						</select>
					</div> }

				<div className="action web_view">
					<label htmlFor="_saveTemp">
						<input 
							id="_saveTemp"
							name="saveTemplate"
							value="save"
							checked={ saveOpen }
							onChange={ e => this.setState({saveOpen: e.target.checked}) }
							type="checkbox" />

						{'Save as template'}
					</label>
				</div>

				{ !_.isEmpty(templates) && 
					<div className="action edit-temp-btn web_view" onClick={ e => this.setState({editOpen: true}) }>Edit Templates</div> }

				{ saveOpen && <SaveTemplateModal 
								{...this.props}
								_close={ e => this.setState({saveOpen: !saveOpen}) } /> }

				{ editOpen && <EditTemplateModal 
								{...this.props}
								_close={ e => this.setState({editOpen: !editOpen}) } 
								_openEditor={ e => this.setState({editOpen: false, editorOpen: true}) } /> }

				{ editorOpen && <EditorModal 
									{...this.props}
									_close={ e => this.setState({editorOpen: !editorOpen}) }
									_back={ e => this.setState({editorOpen: false, editOpen: true}) } /> }


				{ !_.isEmpty(templates) && <AttachmentsButton {...this.props} /> }

			</div>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		messages: state.messages,
	};
};

export default connect(mapStateToProps)(TemplateContainer);
