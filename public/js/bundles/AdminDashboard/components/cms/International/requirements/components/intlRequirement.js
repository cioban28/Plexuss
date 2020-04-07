// intlRequirement.js

import React from 'react'
import selectn from 'selectn'
import ReactSpinner from 'react-spinjs-fix'
import createReactClass from 'create-react-class'

import { spinjs_config } from './../../../../common/spinJsConfig'
import { REMOVE_REQ_ROUTE, REMOVE_REQ_ATTACHMENT_ROUTE } from './../../constants'
import { editActiveReq, removeItem, saveIntlDataWithFile, resetSaved } from './../../../../../actions/internationalActions'

export default createReactClass({
	getInitialState(){
		return {
			editMode: false,
			title: '',
			pdf: null,
			id: null,
		};
	},

	_setActiveReq(){
		let { dispatch, req } = this.props;
		dispatch( editActiveReq({type: req.name, req_action: 'set'}) );
	},

	_editActiveReq(e){
		let { dispatch, req } = this.props;
		dispatch( editActiveReq({type: req.name, req_action: 'edit', title: e.target.value}) );
	},

	_editActiveReqDescription(e){
		let { dispatch, req } = this.props;
		dispatch( editActiveReq({type: req.name, req_action: 'edit', description: e.target.value}) );
	},

	_addAttachment(e){
		let { dispatch, req } = this.props;
		dispatch( editActiveReq({type: req.name, req_action: 'edit', attachment: e.target.files[0]}) );
	},

	_saveReq(e){
		e.preventDefault();

		let { dispatch, intl, req } = this.props,
			_thisReq = intl[intl.activeProgram+'_'+req.name+'_requirements'].active_req,
			form = new FormData(e.target);

		form.append(intl.activeProgram+'_'+"attachement", _thisReq.attachment);

		if( this._formValid() ){
			dispatch( saveIntlDataWithFile(form, 'EDIT_ACTIVE_REQ', {type: req.name, req_action: 'save'}) );
		}
	},

	_formValid(){
		let { intl, req } = this.props,
			_thisReq = intl[intl.activeProgram+'_'+req.name+'_requirements'];

		return !!selectn('active_req.title', _thisReq);
	},

	_getAttachment(){
		let { dispatch, intl, req } = this.props,
			_thisReq = intl[intl.activeProgram+'_'+req.name+'_requirements'];

		return (selectn('active_req.attachment.name', _thisReq) || '') || (selectn('active_req.attachment_url', _thisReq) || '');
	},

	render(){
		let { dispatch, intl, req } = this.props,
			_thisReq = intl[intl.activeProgram+'_'+req.name+'_requirements'],
			req_id = intl.activeProgram+'_'+req.name+'_attachement',
			formValid = this._formValid(),
			_attachment = this._getAttachment();

		let attachmentNameClass = !_attachment ? 'hide' : '';
		let attachmentLinkClass = attachmentNameClass ? '' : 'hide';
		let modeClass = !selectn('active_req', _thisReq) ? 'hide' : '';

		return (
			<div className="req">
				<div className="clearfix section-head">
					<div className="left name">{ req.name || '' } Requirements</div>
					<div className="right add" onClick={ this._setActiveReq }>{'+ add'}</div>
				</div>

				<div className={"edit-mode " + modeClass}>
					<form onSubmit={ this._saveReq } ref="_form">

						<input type="hidden" name="tab" value="requirements" />
						<input type="hidden" name="view_type" value={ intl.activeProgram } />
						{ selectn('active_req.id', _thisReq) &&
							<input type="hidden" name="id" value={_thisReq.active_req.id} /> }

						<input
							id=""
							name={ intl.activeProgram+'_'+req.name }
							type="text"
							value={ selectn('active_req.title', _thisReq) || '' }
							onChange={ this._editActiveReq }
							placeholder="Enter name of requirement" />

						<input
							id=""
							name={ intl.activeProgram+'_'+req.name+'_description' }
							type="text"
							value={ selectn('active_req.description', _thisReq) || '' }
							onChange={ this._editActiveReqDescription }
							placeholder="Enter description of requirement" />

						<div className={"attachment " + attachmentNameClass}>{ _attachment }</div>

						{ !_attachment && <div className="actions clearfix">
											<div className="left">
												<label htmlFor={ req_id }>Add Attachment</label>
												<input
													id={ req_id }
													style={{display: 'none'}}
													name={ intl.activeProgram+'_'+"attachement" }
													onChange={ this._addAttachment }
													type="file" />
											</div>
										</div> }

						<div className="text-right new-req-actions">
							<div
								onClick={ this._setActiveReq }
								className="cancel">
									Cancel
							</div>

							<button
								disabled={ !formValid || intl.pending }
								className="button save">
									{ intl.pending ? <ReactSpinner config={spinjs_config} /> : 'Save' }
							</button>
						</div>

					</form>
				</div>

				{ selectn('list', _thisReq) ?
					_thisReq.list.map((r) => <SingleReq
												key={r.title}
												reqr={r}
												{...this.props} />) : null }

			</div>
		);
	}
});

const SingleReq = createReactClass({
	_editReqr(){
		let { dispatch, reqr, req } = this.props;
		reqr.req_action = 'edit';
		reqr.type = req.name;
		dispatch( editActiveReq(reqr) );
	},

	_addingAttachment(e){
		let { dispatch, reqr, req, intl } = this.props;

		reqr.req_action = 'save';
		reqr.attachment = e.target.files[0];
		reqr.type = req.name;

		var form = new FormData(this.refs.attachment_form),
			pre = intl.activeProgram+'_'+intl.program;

		form.append(intl.activeProgram+'_'+req.name, reqr.title);
		form.append(intl.activeProgram+'_'+req.name+'_description', reqr.description);

		dispatch( saveIntlDataWithFile(form, 'EDIT_ACTIVE_REQ', reqr) );
	},

	_removeReqr(){
		let { dispatch, reqr, req } = this.props;
		reqr.req_action = 'remove';
		reqr.type = req.name;
		dispatch( removeItem(reqr, REMOVE_REQ_ROUTE, 'EDIT_ACTIVE_REQ') );
	},

	_removeAttachment(){
		let { dispatch, reqr, req } = this.props;
		reqr.req_action = 'remove_attachment';
		reqr.attachment = null;
		reqr.attachment_url = null;
		reqr.type = req.name;
		dispatch( removeItem(reqr, REMOVE_REQ_ATTACHMENT_ROUTE, 'EDIT_ACTIVE_REQ') );
	},

	_getAttachment(){
		let { dispatch, reqr } = this.props;
		return selectn('attachment', reqr) ? reqr.attachment.name : (reqr.attachment_url || '');
	},

	render(){
		let { intl, reqr } = this.props,
			reqr_id = reqr.title+'_attachement',
			_attachment = this._getAttachment();

		return (
			<div className="saved-req">

				<input type="hidden" name="visa_doc[]" value="" />
				<input type="hidden" name="visa[]" value={ reqr.title || '' } />

				<div className="title">{ reqr.title || '' }</div>
				<div className="title">{ reqr.description || '' }</div>
				{ _attachment && <div className="attachment">{ _attachment || '' }</div> }

				<div className="actions clearfix">
					<div className="left" onClick={ this._editReqr }>Edit</div>
					<div className="left">{'|'}</div>
					<div className="left" onClick={ this._removeReqr }>Remove</div>
					<div className="left">{'|'}</div>
					{
						!_attachment ?
						<div className="left">
							<form ref="attachment_form">
								<input type="hidden" name="tab" value="requirements" />
								<input type="hidden" name="view_type" value={ intl.program } />
								<input type="hidden" name="id" value={ reqr.id } />

								<label htmlFor={ reqr_id }>Add Attachment</label>
								<input
									id={ reqr_id }
									style={{display: 'none'}}
									name={intl.activeProgram+'_attachement'}
									onChange={ this._addingAttachment }
									type="file" />
							</form>
						</div>
						:
						<div className="left" onClick={ this._removeAttachment }>Remove Attachment</div>
					}
				</div>
			</div>
		);
	}
});
