import React, { Component } from 'react';
import { isEmpty } from 'lodash';
import CustomModal from './../common/CustomModal';

export default class CollegeLearnMoreModal extends Component {
    constructor(props) {
        super(props);
    }

    openUrl = () => {
        const { _profile, onClose } = this.props;
        const { selectCollegeLearnMoreResponse: learnMore } = _profile;

        if (isEmpty(learnMore.url)) return;

        const url = learnMore.url;

        window.open(url, '_blank');

        onClose();
    }

    render() {
        const { _profile, onClose } = this.props;
        const { selectCollegeLearnMoreResponse: learnMore, list } = _profile;
        const { college } = learnMore;
        const { ro_type } = college;

        return (
            <CustomModal>
                <div className="modal learn-more-modal">
                    <div className="closeMe" onClick={onClose}>&times;</div>
                    <div className='learn-more-modal-header'>
                        <img src={college.logo_url} className="learn-more-college-image" />
                        <h4>{college.school_name}</h4>
                    </div>

                    { (ro_type == 'linkout' || ro_type == 'click') && 
                        <div className='learn-more-content-container'>
                            <p>is part of our network. We would like to provide you with more information. You can stay on Plexuss or visit the school site for more information.</p>
                            <div className='learn-more-modal-button' onClick={this.openUrl}>
                                Learn more
                            </div>
                            <div className='learn-more-modal-stay-button' onClick={onClose}>
                                Stay on Plexuss
                            </div>

                        </div> }

                    { (ro_type == 'post') &&
                        <div className='learn-more-content-container'>
                            <p>is part of our network. We will notify this college and they will get back to you.</p> 

                            <div className='learn-more-ok-button' onClick={onClose}>OK</div>

                        </div> }

                    <div></div>
                </div>
            </CustomModal>

        );
    }
}