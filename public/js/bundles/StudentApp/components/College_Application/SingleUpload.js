// /College_Application/Uploads

import React, { Component } from 'react'

import CustomModal from './../common/CustomModal'

import { updateProfile, removeUpload } from './../../actions/Profile'

import { UPLOAD_Q as UploadLabels } from './constants'

import Tip from './../../../utilities/tip'

export default class SingleUpload extends Component{
	constructor(props){
		super(props);

		this._getDoc = this._getDoc.bind(this);
		this._getExt = this._getExt.bind(this);
		this._getDate = this._getDate.bind(this);
		this._getName = this._getName.bind(this);
		this._removeFile = this._removeFile.bind(this);

		this.state = {
			open: false,
			upload_hovered: false,
			gview_start: 'https://docs.google.com/gview?url=',
			gview_end: '&embedded=true',
		};
	}

	_removeFile(){
		let { dispatch, _profile, file } = this.props;

		let fileFound = _.find(_profile.transcripts.slice(), file),
			newList = null,
			tmpObj = {};

		if( fileFound ) tmpObj['transcripts'] = _.reject(_profile.transcripts.slice(), fileFound);
		else return;

		if ( _profile.uploaded_file_names && file.name ) {
			tmpObj['uploaded_file_names'] = _.reject(_profile.uploaded_file_names, (file_name) => file.name == file_name);
		}

		// Do not allow file removal if another removal is currently pending. Causes sync issues when creating new lists.
		if ( !_profile.remove_pending ) {
			// if file has transcript_id, means it's already been saved and have to make ajax call to remove from db
			// else, hasn't been saved yet and just needs to be removed locally
			if( fileFound.transcript_id ) dispatch( removeUpload(fileFound.transcript_id, tmpObj) );
			else dispatch( updateProfile(tmpObj) );
		}
	}

	_getDate(){
		let { file } = this.props;
		return file.transcript_date || file.upload_time;
	}

	_getDoc(){
		let { file } = this.props,
			{ gview_start, gview_end } = this.state,
			ext = this._getExt(),
			url = file.transcript_url || file.url;

		if( ext === 'image' && ( file.ext_type || file.mime_type ) ) return url; // if has ext_type, came from backend
		else if( ext === 'image' && file.type ) return window.URL.createObjectURL(file); //if has type, create locally on frontend

		// if here, means file is not an image, so it's either a txt, pdf, or something else
		return (url && gview_start+url+gview_end ) || window.URL.createObjectURL(file);
	}

	_getType(){
		let { file } = this.props;
		return file.transcript_type || file.upload_type || file.doc_type;
	}

	_getName(){
		let { file } = this.props,
			url = file.transcript_url || file.url;
		return ( url && url.split('/').pop() ) || file.name;
	}

	_getExt(){
		let { file } = this.props,
			type = file.type || file.mime_type;

		if( file.ext_type === 'img' || ( type && type.split('/')[0] === 'image' ) ) return 'image';
		return 'txt';
	}

	render(){
		let { _profile, file } = this.props,
			{ open } = this.state,
			remove_pending = _profile.remove_pending,
			upload_pending = _profile.upload_pending,
			_type = this._getType(),
			_name = this._getName(),
			_date = this._getDate(),
			_doc = this._getDoc(),
			_ext = this._getExt(),
			toolTipContent = _.find(UploadLabels, ['name', _type]);

		// Invalid file type. Do not render.
		if (!toolTipContent) { return null; }

		toolTipContent = toolTipContent.label;

		return (
			<div className="single-upload">
				<div onClick={ e => this.setState({open: true}) } >
					<div className={"doc " + _type} 
						onMouseEnter={ () => { this.setState({upload_hovered: true}) } } 
						onMouseLeave={ () => { this.setState({upload_hovered: false}) } } >
					</div> 
					{ this.state.upload_hovered && <Tip classes="upload-type-tip" styling={{marginLeft: '-7.2em', paddingTop: 0, paddingBottom: 0}}>{ toolTipContent }</Tip> }
					<div title={ _name || '' }>{ _name || '' }</div>
				</div>
				<div>{ _date || '' }</div>
				
				{ file.transcript_id && !remove_pending && !upload_pending && <div onClick={ this._removeFile }>&times;</div> }
                
                { ( !file.transcript_id || remove_pending || upload_pending ) && <div>&nbsp;</div> }

				{ open && <CustomModal
							closeMe={ e => this.setState({open: false}) }>
							<div className="modal upload-modal">
								<div className="closeMe" onClick={ e => this.setState({open: false}) }>&times;</div>

								{
									_ext === 'image' ?
									<img src={ _doc } alt="Upload Preview" />
									:
									<iframe width="500" height="500" frameBorder="0" src={ _doc }>
										<p>The uploaded document could not be displayed due to unsupported browser. Please upgrade your browser or use Google Chrome or FireFox.</p>
									</iframe>
								}
								
							</div>
						</CustomModal> }
			</div>
		);
	}
}