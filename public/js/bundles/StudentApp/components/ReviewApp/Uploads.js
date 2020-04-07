// /ReviewApp/Basic.js

import React from 'react'
import selectn from 'selectn'

import SectionHeader from './SectionHeader'

import CustomModal from './../common/CustomModal'

import { UPLOAD_Q as UploadLabels } from '../College_Application/constants'

class ReviewUploads extends React.Component {
	constructor(props) {
		super(props)
		this.state = {
			showModal: false,
			activeTranscript: null
		}
		this._viewUploadPreview = this._viewUploadPreview.bind(this)
		this._buildUpload = this._buildUpload.bind(this)
	}

	_viewUploadPreview(url){
	    let filetype = url.split(".").pop(), 
	    	showPreview = null;

	    if( filetype == 'jpeg' || filetype == 'jpg' || filetype == 'png' || filetype == 'gif' || filetype == 'bmp') {
	        showPreview = <img src={ url } alt="Uploaded Transcript" />
	    } else {
	    	url = "https://docs.google.com/gview?url=" + url + "&embedded=true";
	        showPreview = <iframe src={ url } style={{ width: '100%', height: '500px' }} frameBorder="0"></iframe>;
	    }

	    this.setState({	activeTranscript: showPreview, showModal: true })
	}

	_buildUpload(transcript){
		let doc_type = transcript.transcript_type || transcript.upload_type || transcript.doc_type,
			fileLabel = transcript.transcript_url ? transcript.transcript_url.split('/').pop() : transcript.url ? transcript.url.split('/').pop() : transcript.name,
			docLabel = _.find(UploadLabels, ['name', doc_type]);

		// Invalid file type. Do not render.
		if (!docLabel) { return null; }

		docLabel = docLabel.label;

		return (
			<div onClick={() => this._viewUploadPreview(transcript.transcript_url || transcript.url)} 
				 className="item col upload" 
				 key={transcript.transcript_id || transcript.upload_time}>
				<div title={docLabel} className={"doc " + doc_type} />
				<span title={fileLabel}>{fileLabel}</span>
			</div>
		);
	}

	render(){
		let { dispatch, _profile, _route, noEdit } = this.props;

		return (
			<div className="section">

				<div className="inner">
				
					<div className="arrow" />

					<SectionHeader route={_route} dispatch={dispatch} noEdit={noEdit} />

					{ _.get(_profile, 'transcripts.length', 0) > 0 && _profile.transcripts.map(this._buildUpload)}

					{ _.get(_profile, 'transcripts.length', 0) === 0 && <div>No uploads added</div> }

					{ this.state.showModal && 
						<CustomModal closeMe={ () => this.setState({ showModal: false }) }>
							<div className="modal preview-uploads-modal">
								{ this.state.activeTranscript }
							</div>
						</CustomModal> 
					}
				</div>

			</div>
		);
	}
}

export default ReviewUploads;