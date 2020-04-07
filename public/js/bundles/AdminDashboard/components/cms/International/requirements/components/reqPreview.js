// reqPreview.js

import React from 'react'
import selectn from 'selectn'
import createReactClass from 'create-react-class'

export default createReactClass({
	render(){
		let { intl, req } = this.props,
			_thisReq = intl[intl.activeProgram+'_'+req.name+'_requirements'];

		return selectn('list', _thisReq) ?
				<div className="p-list">
					<div className="p-head">{req.name} Requirements</div>
					{ _thisReq.list.map((rq) => <ReqPreviewRow key={rq.title} reqr={rq} />) }
				</div>
				: null
	}
});

const ReqPreviewRow = createReactClass({
	_getAttachment(){
		let { dispatch, reqr } = this.props;
		return selectn('attachment', reqr) ? URL.createObjectURL(reqr.attachment) : '';
	},

	_getAttachmentUrl(){
		let { dispatch, reqr } = this.props;
		return selectn('attachment_url', reqr) || '';
	},

	_showAttachment(){
		let { dispatch, reqr } = this.props;
		return selectn('attachment', reqr) || selectn('attachment_url', reqr);
	},

	render(){
		let { reqr } = this.props,
			align = reqr.attachment ? 'middle' : '',
			_attachment = selectn('attachment', reqr) ? this._getAttachment() : this._getAttachmentUrl(),
			_showAttachment = this._showAttachment();

		return (
			<div className="req-p-row">
				<div className={"req-name " + align}>
					<div>{ reqr.title || '' }</div>
					<div>{ reqr.description || '' }</div>
				</div>

				{ _showAttachment &&
					<div className="download">
						<a href={ _attachment } download>Download</a>
					</div> }
			</div>
		);
	}
});
