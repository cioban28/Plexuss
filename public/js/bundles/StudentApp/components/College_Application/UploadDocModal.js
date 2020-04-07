// /College_Application/UploadDocModal.js

import React, { Component } from 'react'

import CustomModal from './../common/CustomModal'

// import { UPLOAD_Q } from './constants'
import { updateProfile, updateFileUploads } from './../../actions/Profile'

export default class UploadDocModal extends Component{
	constructor(props){
		super(props);
		this._upload = this._upload.bind(this);
	}

	_upload(e){
		let { dispatch, _profile, close } = this.props,
			file = e.target.files[0],
			name = e.target.id;

		file.upload_type = name; // add prop to know what type of upload this file belongs to
		file.upload_time = moment().format('MM/D/YYYY hh:mma');

		let files = _profile.transcripts ? [..._profile.transcripts, file] : [file];

		// this.state.openModal = false; // no need to set state here, on dispatch complete, it'll re-render
		close();
		dispatch( updateFileUploads({transcripts: files}) );
	}

	render(){
		let { close, docs } = this.props;
		return (
			<CustomModal closeMe={ close }>
				<div className="modal upload-modal">
					<div className="closeMe" onClick={ close }>&times;</div>
					<div className="modal-head text-center">Upload files</div>
					<div className="modal-sub text-center">Choose which type of file you would like to upload</div>
					<div className="file-opts">
						{ docs.map((u) => <Doc key={u.name} doc={u} upload={this._upload} />) }
					</div>
				</div>
			</CustomModal>
		);
	}
};

const Doc = (props) => {
	let { doc, upload } = props;

	return (
		<div>
			<div className={"doc "+doc.name}><label htmlFor={doc.name} /></div>
			<input
				type="file"
				id={ doc.name }
				name={ doc.name }
				onChange={ upload } />
			<div>{ doc.label }</div>
		</div>
	);
}