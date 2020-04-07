import React from 'react'
import CustomModal from './../../../../StudentApp/components/common/CustomModal'

export default ({ name, label, videoLink, description, closeMe }) => (
    <CustomModal closeMe={ closeMe }>
        <div className="modal dashboard-video-modal">
            <div className="closeMe" onClick={ closeMe }>&times;</div>
            <div className='header-container'>
                <div className={ 'video-icon ' + name }></div>
                <div className='header-text'>{ label }</div>
            </div>
            
            { videoLink 
                ? 
                <iframe 
                    src={videoLink}
                    width="560" 
                    height="349"
                    frameBorder="no"
                    allowFullScreen></iframe>

                :
                <div className='bold-text'>We are currently working on a video. Please try again later.</div>
            }

            <div className='description-text'>{description}</div>
        </div>
    </CustomModal>
);
