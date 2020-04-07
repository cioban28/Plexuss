import React from 'react'
import { bindActionCreators } from 'redux'
import CustomModal from './../common/CustomModal'
import { connect } from 'react-redux'
import * as profileActions from './../../actions/Profile'
import SingleUpload from './SingleUpload'
import Uploads from './../ReviewApp/Uploads'

class UploadsModal extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            activeTab: 'checklist',
        }

        this._renderIfDone = this._renderIfDone.bind(this);
        this._toggleTabs = this._toggleTabs.bind(this);
        this._uploadDoc = this._uploadDoc.bind(this);
    }

    _toggleTabs(tab) {
        if (this.state.activeTab !== tab) {
            this.setState({
                activeTab: tab,
            });
        }
    }

    _uploadDoc(e) {
        const { saveUploadedFiles } = this.props,
            formData = new FormData(),

            upload_type = e.target.getAttribute('name'),

            file = e.target.files[0];

        file.upload_time = moment().format('MM/D/YYYY hh:mma');
        file.upload_type = upload_type;

        formData.append(upload_type + '_1', file);

        saveUploadedFiles(formData);
    }

    _renderIfDone(type) {
        const { _profile } = this.props,
            transcripts = _profile.transcripts;
        
        let found =  null;

        if (!transcripts) return null;

        found = _.find(transcripts, (transcript) => {
            return transcript.transcript_type == type || transcript.doc_type == type;
        });

        return found 

            ? <span className='green-check-mark'>&#10003;</span>

            : null;

    }

    render() {
        const { closeMe, _profile } = this.props,
            activeTab = this.state.activeTab;
        return (
            <CustomModal closeMe={ () => null }>
                <div className="modal initial-uploads-modal">
                    <div className="closeMe" onClick={ () => closeMe() }>&times;</div>
                    <img className='upload-modal-image' src="/images/upload-icon.png" />
                    <h4>My Documents</h4>
                    <p>Use the CHECKLIST tab to see what documents you need to upload. Once your documents have been uploaded they will be in the UPLOADS tab.</p>
                    <div className='uploads-modal-option-tabs'>
                        <div 
                            onClick={ () => this._toggleTabs('checklist') }
                            className={ 'tab-option ' + (activeTab == 'checklist' ? 'active' : null) }>CHECKLIST</div>
                        <div 
                            onClick={ () => this._toggleTabs('uploads') }
                            className={ 'tab-option ' + (activeTab == 'uploads' ? 'active' : null) }>UPLOADS</div>
                    </div>                    

                    { activeTab == 'checklist' &&
                        <div>
                            <label className='upload-modal-button' htmlFor={'transcript'}>Upload Transcript {this._renderIfDone('transcript')}</label>
                            <input 
                                type="file"
                                id={ 'transcript' }
                                name={ 'transcript' }
                                onChange={ this._uploadDoc } />

                            <label className='upload-modal-button' htmlFor={'financial'}>Upload Financial Documents {this._renderIfDone('financial')}</label>
                            <input 
                                type="file"
                                id={ 'financial' }
                                name={ 'financial' }
                                onChange={ this._uploadDoc } /> 

                            <label className='upload-modal-button' htmlFor={'essay'}>Upload College Essay {this._renderIfDone('essay')}</label>
                            <input 
                                type="file"
                                id={ 'essay' }
                                name={ 'essay' }
                                onChange={ this._uploadDoc } />

                            <label className='upload-modal-button' htmlFor={'other'}>Upload Other {this._renderIfDone('other')}</label>
                            <input
                                type="file"
                                id={ 'other' }
                                name={ 'other' }
                                onChange={ this._uploadDoc } />
                        </div> }

                    { activeTab == 'uploads' &&
                        <div>
                            { (_.get(_profile, 'transcripts.length') > 0) 

                                ?

                                <div className="all-uploads">
                                    <br />

                                    <div className="single-upload hd">
                                        <div>File Name</div>
                                        <div>Date Uploaded</div>
                                        <div>Remove</div>
                                    </div>
                                    { _profile.transcripts.map((au, i) => <SingleUpload key={au.transcript_id || (au.name || '')+i} file={au} {...this.props} />) }
                                </div>

                                : 

                                <div>
                                    { _profile.init_profile_pending == true
                                        ? <div className='spin-loader'></div>
                                        : <div>No uploads yet</div> }
                                </div>
                            }
                        </div>
                    }
                </div>
            </CustomModal>
        );
    }

}


const mapStateToProps = (state, props) => {
    return {
        _user: state._user,
        _profile: state._profile,
    };
};

const mapDispatchToProps = (dispatch) => {
    return { ...bindActionCreators(profileActions, dispatch), dispatch };
}

export default connect(mapStateToProps, mapDispatchToProps)(UploadsModal);