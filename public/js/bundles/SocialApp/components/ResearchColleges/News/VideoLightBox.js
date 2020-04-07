import React from 'react';
import './styles.scss'

export function VideoLightBox({ newsId, closeModal }) {
  return (
    <div id="lightbox" style={{display: 'block', opacity: '1', visibility: 'visible', top: '0px'}}>
      <div className='lightbox-inner-cont'>
        <div className="clearfix close-lightbox">
            <div className="right">
                <a className="close-reveal-modal" onClick={closeModal.bind(this, newsId)}>×</a>
            </div>
        </div>
        <div className="iframe-container"><iframe src={`/lightbox/${newsId}`} frameBorder="0"></iframe>
            <img src="/social/images/plexuss-loader-test-2.gif" alt="Loading gif" />
        </div>
      </div>
    </div>
  )
}
