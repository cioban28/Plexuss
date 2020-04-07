// /Dashboard/components/premiumPlan.js
import React, { Component } from 'react'

export default ({ label, description, _onRequest }) => (
    <div className='premium-plan-container'>
        <div className='plan-label'>{label}</div>
        <div className='details-container'>
            <div className='premium-plan-description'>{description}</div>
            <div className='request-premium-plan-button' onClick={_onRequest}>Request Proposal</div>
        </div>
    </div>
);