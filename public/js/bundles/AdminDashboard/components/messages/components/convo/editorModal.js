// /convo/editorModal.js

import TinyMCE from 'react-tinymce';
import React, { Component } from 'react'

import CustomModal from './../../../../../utilities/customModal'

import { 
	updateConvoActions, 
	saveTemplate,
	deleteTemplate } from './../../../../actions/messagesActions'

const PLUGINS = {
	plugins: 'autolink link image lists print preview',
	toolbar: 'undo redo | bold italic | alignleft aligncenter alignright',
};

export default class SaveTemplateModal extends Component{
	constructor(props){
		super(props);

		this._saveTemplate = this._saveTemplate.bind(this);
		this._deleteTemplate = this._deleteTemplate.bind(this);
		this._confirmNameChange = this._confirmNameChange.bind(this);

		this.state = {
			editName: false,
			areYouSureCheck: false,
			new_template_name: '',
			new_content: '',
			err: false,
		};
	}

	componentWillMount(){
		let { messages: _m } = this.props;

		this.state = {
			...this.state,
			new_content: _.get(_m, 'edit_template_selected.content', ''),
			new_template_name: _.get(_m, 'edit_template_selected.name', ''),
		}
	}

	_saveTemplate(e){
		e.preventDefault();
		let { dispatch, messages: _m, _back } = this.props,
			{ new_template_name: name, new_content: content } = this.state,
			id = _.get(_m, 'edit_template_selected.id', '');

		if( name && content ){
			_back();
			dispatch( saveTemplate(name, content, id) );

		}else this.setState({err: true});
	}

	_deleteTemplate(){
		let { dispatch, messages: _m, _back } = this.props;

		this.state.areYouSureCheck = false;
		_back();

		dispatch( deleteTemplate(_m.edit_template_selected.id) );
	}

	_confirmNameChange(e){
		e.preventDefault();

		let { dispatch, messages: _m } = this.props,
			{ new_template_name } = this.state;

		this.state = {
			...this.state,
			editName: false,
		};

		dispatch( updateConvoActions({
			edit_template_selected: {
				..._m.edit_template_selected,
				name: new_template_name,
			}
		}) );
	}

	render(){
		let { dispatch, messages: _m, _close, _back } = this.props,
			{ editName, areYouSureCheck, new_template_name, new_content, err } = this.state,
			_selected = _.get(_m, 'edit_template_selected', {});

		return (
			<CustomModal backgroundClose={ _close }>
				<div className="editor-modal">
					<div className="tclose">
						<span onClick={ _close }>&times;</span>
					</div>

					{ !editName && 
						<div className="tname">
							<span>{_selected.name}</span>
							<img 
								onClick={ e => this.setState({editName: true}) }
								src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/edit_icon_sm.png" />
						</div> }

					{ editName && 
						<form className="name-editor" onSubmit={ this._confirmNameChange }>
							<input
								className="save-temp-name"
								name="save-temp-name"
								type="text"
								value={ new_template_name || '' }
								onChange={ e => this.setState({new_template_name: e.target.value}) }
								placeholder="Enter template name" />

							<div onClick={ this._confirmNameChange }>Ok</div>
						</form> }

					<br />

					<TinyMCE
						content={ new_content || ''}
						config={ PLUGINS }
						onChange={ e => this.setState({new_content: e.target.getContent()}) } />

					<br />

					{ err && <div className="err">In order to save, name and content cannot be empty.</div> }

					{ !areYouSureCheck && 
						<div className="clearfix editor-actions">
							<div className="tback left" onClick={ _back }>Back</div>
							<div className="tsave right" onClick={ this._saveTemplate }>Save</div>
							<div className="tdelete right" onClick={ e => this.setState({areYouSureCheck: true}) }>Delete</div>
						</div> }

					{ areYouSureCheck && 
						<div className="tactions">
							<div className="areYouSure">Are you sure you want to permanently delete <b>{_selected.name}</b>?</div>

							<button 
								onClick={ e => this.setState({areYouSureCheck: false}) }
								className="tedit">No</button>

							<button 
								onClick={ this._deleteTemplate }
								className="tdelete">Yes</button>
						</div> }
				</div>
			</CustomModal>
		);
	}
}