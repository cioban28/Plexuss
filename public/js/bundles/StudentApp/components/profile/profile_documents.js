import React from 'react';
import './styles.scss';

import {connect} from 'react-redux';
import {getStudentData} from './../../actions/User';
import {uploadAFileMePage, getUploads, removeUploadMePage} from './../../actions/Profile';

import ProfileOption from './profileOption';
import Avatar from './../../../utilities/roundPortrait';
import CustomInputTFile from './../../../utilities/customInputTFile/customInputTFile';
import Loader from './../../../utilities/loader';

class Profile_documents extends React.Component{
	
	constructor(props){
		super(props);

		this.state = {
			checkOrUp: "check",
			transcript: false,
			financial: false,
			essay: false,
			other: false,
			clicked: '',
		}

		this._uploaded = this._uploaded.bind(this);
		this._uploadFile = this._uploadFile.bind(this);
		this._getUploads = this._getUploads.bind(this);
	}
	componentWillMount(){
		let {dispatch} = this.props;
		dispatch(getUploads());	
	}

	componentDiDMount(){
		let {dispatch} = this.props;

		dispatch(getStudentData());
		// _.find  (better run time, once at lesat two types of uploads are present)
		//otherwise, better performance to iterate once through whole array
		//check true when transcript type found: ex 'fincancial'
	}

	_uploadFile(e){
		let { dispatch } = this.props,
        formData = new FormData(),
        file = e.target.files[0],
        upload_type = e.target.name;

        // file.upload_time = moment().format('MM/D/YYYY hh:mma');
        file.upload_type = upload_type;

        formData.append(upload_type + '_1', file);
        dispatch(uploadAFileMePage(formData, this._uploaded ));
	}
	_uploaded(doc_type){
        let obj = {}; 
        obj[doc_type] = true;

		this.setState(obj);


	}
	 _renderIfDone(type) {
        const { _profile } = this.props,
            transcripts = _profile.transcript;
        let found =  null;

        if (!transcripts) return null;

        found = _.find(transcripts, (transcript) => {
            return transcript.transcript_type == type || transcript.doc_type == type;
        });

        return found 

            ? <span className='check'>&#10004;</span>

            : null;

    }
    _getUploads(){
    	let {dispatch} = this.props;

    	// dispatch(getUploads());
    	this.setState({checkOrUp: "upload"});
    }

	render(){
		let {checkOrUp, clicked, transcript, financial, essay, other} = this.state;
		let {_profile, dispatch} = this.props;

		let name = (_profile.fname || " ") + " " + (_profile.lname || " ");
		let percent = _profile.profile_percent + "% Complete" || "0% Complete"	

		return(
			<div className="_profile_docs">
				
				
					<div className="centering">
						<img className="upload-modal-image" src="/images/upload-icon.png" />
						<div className="big-header">My Documents</div>
						<div className="docs-details">
							Use the CHECKLIST tab to see what documents you need to upload. 
							Once your documents have been uploaded they will be in the UPLOADS tab.
						</div>

						<div className="mt50">
							<div className={checkOrUp === "check" ? "active docs-link" : "docs-link "} onClick={() => this.setState({checkOrUp: "check"})}>CHECKLIST</div>
							<div className={checkOrUp === "upload" ? "active docs-link" : "docs-link "}  onClick={this._getUploads} >UPLOADS</div>
						</div>


						{checkOrUp === "check" && <div className="mt30 upload-btn-container">
							<CustomInputTFile name="transcript" callback={this._uploadFile}> <div className="checklist-btn" >Upload Transcripts {this._renderIfDone('transcript')}</div> </CustomInputTFile>
							<CustomInputTFile name="financial" callback={this._uploadFile}> <div className="checklist-btn" >Upload Financial Documents {this._renderIfDone('financial')}</div></CustomInputTFile>
							<CustomInputTFile name="essay" callback={this._uploadFile}> <div className="checklist-btn" >Upload College Essay {this._renderIfDone('essay')}</div></CustomInputTFile>
							<CustomInputTFile name="other" callback={this._uploadFile}> <div className="checklist-btn" >Upload Other  {this._renderIfDone('other')} </div></CustomInputTFile>

						</div> }
						
						{checkOrUp === "upload" && <div>
							

							{_profile.getUploadsPending ? 

									<div className="mini-loader mt50"> </div>

								:

								_profile.transcript && _profile.transcript.length > 0 ? 

										<div className="docs-container">

											<div className="transcript-result mb10 underline">
												<div className="transcript-name-col"> File Name </div>
												<div className="transcript-date">Date Uploaded</div>
												<div className="transcript-rm">Remove</div>
											</div>


											{_profile.transcript.map((item, i) => {
													return (
														<div key={"up"+i} className="transcript-result">
															<div className="transcript-name-col">
																<div className={"doc-img " + item.doc_type}></div>
																<div className="transcript-name">{item.file_name || "not named"}</div>
															</div>
															<div className="transcript-date">{item.date || "unknown"}</div>
															<div className="transcript-rm"> <div className="rm" onClick={() => dispatch(removeUploadMePage(item.id))}>&times;</div></div>
														</div>	
													)
											})}

										</div>
										:
										<div className="no-docs">No uploads yet</div>}



						</div> }


					
					</div>

					{_profile.upload_pending == true &&  <Loader /> }

					{_profile.remove_pending == true && <Loader />}
					

			</div>
		);
	}
};

const mapStateToProps = (state, props) => {
	return {
		_profile: state._profile
	}
}

export default connect(mapStateToProps)(Profile_documents);