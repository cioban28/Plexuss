import React from 'react'
import CustomModal from './../../../../StudentApp/components/common/CustomModal'

export default ({ closeMe }) => (
    <CustomModal closeMe={ closeMe }>
        <div className="modal premium-request-success-modal">
            <div className="closeMe" onClick={ closeMe }>&times;</div>
            <div className='header-container'>
                <div className='header-text'>Thank you for expressing interest in our features. Our team will contact you shortly in regards to your selected services or plans.</div>
            </div>
            <div className='ok-button' onClick={ closeMe }>OK</div>
        </div>
    </CustomModal>
);
