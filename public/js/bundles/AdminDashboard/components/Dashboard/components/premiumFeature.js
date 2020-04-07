// /Dashboard/components/premiumFeature.js

import React, { Component } from 'react'
import VideoModal from './videoModal'

export default class PremiumFeature extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            showModal: false,
        }
    }

    render() {
        const { isSelected, name, label, description, pricing, _onSelect, videoLink } = this.props;

        return (
            <div className='premium-feature-container'>
                <div onClick={_onSelect} className={'premium-feature-select-toggle' + (isSelected ? ' active' : '')}></div>
                <div className={'premium-feature-icon ' + name}></div>
                <div className='premium-feature-label'>{label}</div>
                <div className='premium-feature-description'>{description}</div>
                { videoLink &&
                    <div 
                        className='premium-feature-play-button' 
                        onClick={() => this.setState({ showModal: true })}>
                            <div className='play-button-icon'></div>
                    </div> }
                <div className='premium-feature-label'>{pricing}</div>

                { this.state.showModal && 
                    <VideoModal
                        name={name}
                        label={label} 
                        videoLink={videoLink}
                        description={description}
                        closeMe={() => this.setState({ showModal: false })} /> }
            </div>
        )
    }
}