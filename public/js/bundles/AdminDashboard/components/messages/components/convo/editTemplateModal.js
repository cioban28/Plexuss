// /convo/editTemplateModal.js

import React, { Component } from 'react'

import CustomModal from './../../../../../utilities/customModal'

import { updateConvoActions, deleteTemplate } from './../../../../actions/messagesActions'

export default class SaveTemplateModal extends Component{
	constructor(props){
		super(props);

		this._deleteTemplate = this._deleteTemplate.bind(this);
		this.state = { areYouSureCheck: false };
	}

	_deleteTemplate(){
		let { dispatch, messages: _m } = this.props;
		this.state.areYouSureCheck = false;
		dispatch( deleteTemplate(_m.edit_template_selected.id) );
	}

	render(){
		let { dispatch, messages: _m, _close, _openEditor } = this.props,
			{ areYouSureCheck } = this.state,
			templates = _m.template_list || [],
			_selected = _.get(_m, 'edit_template_selected', {});

		return (
			<CustomModal backgroundClose={ _close }>
				<div className="edit-template-modal">
					<div className="tclose">
						<span onClick={ _close }>&times;</span>
					</div>

					<div className="tname">Edit templates</div>

					<ul className="template-list">
						{ templates.map(te => 
							<li key={ te.id }>
								<input
									id={ te.id }
									name={'edit_temp_selection'}
									type="radio"
									value={ te.id || '' }
									checked={ _selected.id === te.id }
									onChange={ e => dispatch( updateConvoActions({edit_template_selected: te}) ) }
									placeholder="Enter template name" />

								<label htmlFor={ te.id }>{ te.name || '' }</label>
							</li>) }
					</ul>

					{ !areYouSureCheck && 
						<div className="tactions">
							<button 
								onClick={ _openEditor }
								disabled={ !_m.edit_template_selected } 
								className="tedit">Edit</button>

							<button 
								onClick={ e => this.setState({areYouSureCheck: true}) }
								disabled={ !_m.edit_template_selected } 
								className="tdelete">Delete</button>
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