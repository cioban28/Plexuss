import React, { Component } from 'react'

const openLink = (link) => {
    window.open(link, '_blank');
}

export default ({ name, imageURL, link }) => (
    <div className='edx-college-link-container'>
        <img className='edx-college-image' src={imageURL} alt={name} />
        <div className='edx-register-button-container'>
            <div className='edx-register-button' onClick={() => openLink(link)}>Register</div>
        </div>
    </div>
);