import React, {Component} from 'react';
import { isEmpty } from 'lodash';

import {searchForMajors} from './../../actions/Profile';

import Edit_section from './profile_edit_section';

import Profile_edit_privacy_settings from './profile_edit_privacy_settings'

import InfoTooltip from './InfoTooltip';

const YOUTUBE_PLACEHOLDER_EXAMPLE = 'www.youtube.com/embed/cISL9w0c8fo'
const VIMEO_PLACEHOLDER_EXAMPLE = 'www.player.vimeo.com/video/167976518'

const TOOLTIP_VIDEO_CONTENT = (URL) => (
    <span>The link must be a valid URL and should include the protocol, for example: <b>https://</b>{URL}</span>
);

export default class Profile_edit_claim_to_fame extends Component{
    
    constructor(props){
        super(props);

        const { _profile } = props;
        const { claimToFameDescription, claimToFameYouTubeVideoUrl, claimToFameVimeoUrl, claimToFameVideoUrl } = _profile;

        this.state = {
            submittable: false,
            editedDescription: '',
            editedYouTube: '',
            editedVimeo: '',
            editedDescriptionValid: null,
            editedYouTubeValid: null,
            editedVimeoValid: null,
        }
    }

    _onEnableEditing = () => {
        const { _profile } = this.props;
        const { claimToFameDescription, claimToFameYouTubeVideoUrl, claimToFameVimeoUrl, claimToFameVideoUrl } = _profile;

        const newState = {...this.state};

        newState['submittable'] = claimToFameDescription ? true : false;

        newState['editedDescription'] = claimToFameDescription || '';

        newState['editedYouTube'] = claimToFameYouTubeVideoUrl || '';

        newState['editedVimeo'] = claimToFameVimeoUrl || '';

        this.setState(newState);
    }

    _validate = () => {
        const potentialKeys = ['editedDescription', 'editedYouTube', 'editedVimeo'];
        const validation = {};

        let submittable = true;
        
        let valid = null;

        potentialKeys.forEach((key) => {
            switch (key) {
                case 'editedDescription':
                    valid = !!this.state[key];
                    break;

                default: // Default checks for url links
                    valid = true;

                    if (this.state[key]) {
                        try {
                            new URL(this.state[key]);
                            if (key === 'editedYouTube') {
                                valid = this.state[key].startsWith('https://www.youtube.com/embed/');
                            } else if (key === 'editedVimeo') {
                                valid = this.state[key].startsWith('https://player.vimeo.com/video/');
                            } else {
                                value = false;
                            }
                        } catch(exception) {
                            valid = false;
                        }
                    }

                    break;
            }

            validation[`${key}Valid`] = valid;

        });

        for (let key in validation) {
            if (validation[key] === false) {
                submittable = false;
            }
        }

        validation['submittable'] = submittable;

        this.setState(validation);
        return submittable;
    }

    _onChange = (event) => {
        this.setState({ [event.target.id]: event.target.value }, this._validate);
    }

    _save = (callback) => {
        const { editedDescription, editedYouTube, editedVimeo } = this.state;
        const { save } = this.props;

        const data = {
            description: editedDescription,
            youtube_url: editedYouTube,
            vimeo_url: editedVimeo,
        }

        save(data, callback);
    }

    render(){
        const {
            submittable,
            editedDescription,
            editedVimeo,
            editedYouTube,
            editedDescriptionValid,
            editedVimeoValid,
            editedYouTubeValid
        } = this.state;

        const { _profile } = this.props;

        const { claimToFameDescription, claimToFameVideoUrl } = _profile;
        return(
            <Edit_section autoOpenEdit={this.props.autoOpenEdit} editable={true} saveHandler={this._save} onEnableEditing={this._onEnableEditing} section={'claim-to-fame'} submittable={submittable}>
                {/* Preview section */}
                <div>
                    <div className="green-title">
                        Claim to Fame
                        <Profile_edit_privacy_settings section="claim_to_fame" initial={!!_profile.public_profile_settings ? !!_profile.public_profile_settings.claim_to_fame ? _profile.public_profile_settings.claim_to_fame : null : null}/>
                    </div>

                    <div className='claim-to-fame-preview-container'>
                        { isEmpty(claimToFameDescription) && <div style={{color: '#d3d3d3'}}>No claim to fame content added yet.</div> }
                        <div className='claim-to-fame-detail'>{claimToFameDescription}</div>
                        {/* Hide till url converter function made
                        <div className='claim-to-fame-video'>
                            { claimToFameVideoUrl &&
                                <iframe width="350" height="250" src={claimToFameVideoUrl}></iframe> }
                        </div>
                        */}
                    </div>
                </div>

                {/* Edit section */}
                <div>
                    <div className="green-title">Claim to Fame</div>

                    <div className='claim-to-fame-edit-container'>
                        <div className='about-yourself-container'>
                            <div>Tell us about yourself</div>
                            <textarea id='editedDescription' className={editedDescriptionValid === false ? 'invalid-field' : ''} value={editedDescription} onChange={this._onChange} placeholder="Write about your claim to fame here. You may also provide video links from YouTube or Vimeo below."/>
                        </div>
                   
                        <div className='video-link-input-container'>
                        {/* Hide till url converter function made
                            <div>
                                <div>YouTube Link <InfoTooltip type='dark' id='info-tooltip-youtube-link' content={TOOLTIP_VIDEO_CONTENT(YOUTUBE_PLACEHOLDER_EXAMPLE)} /></div>
                                <div className='claim-to-fame-input-container'>
                                    <div className='claim-to-fame-youtube-icon' />
                                    <input id='editedYouTube' className={editedYouTubeValid === false ? 'invalid-field' : ''} value={editedYouTube} onChange={this._onChange} placeholder="e.g., https://www.youtube.com/embed/cISL9w0c8fo" />
                                </div>
                                { editedYouTubeValid === false && <div className='url-input-error'>URL must be in this format: <b>https://www.youtube.com/embed/[YOUR_YOUTUBE_ID]</b></div> }
                            </div>
                            <div>
                                <div>Vimeo Link <InfoTooltip type='dark' id='info-tooltip-vimeo-link' content={TOOLTIP_VIDEO_CONTENT(VIMEO_PLACEHOLDER_EXAMPLE)} /></div>
                                <div className='claim-to-fame-input-container'>
                                    <div className='claim-to-fame-vimeo-icon' />
                                    <input id='editedVimeo' className={editedVimeoValid === false ? 'invalid-field' : ''} value={editedVimeo} onChange={this._onChange} placeholder="e.g., https://player.vimeo.com/video/167976518" />
                                </div>
                                { editedVimeoValid === false && <div className='url-input-error'>URL must be in this format: <b>https://player.vimeo.com/video/[YOUR_VIMEO_ID]</b></div> }
                            </div>
                        */}
                        </div>
                    </div>
                </div>
            </Edit_section>
        );

    }
} 