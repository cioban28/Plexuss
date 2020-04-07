import React from 'react'
import Tooltip from 'react-tooltip';

export default () => (
    <div className='skills-section-tooltip-content'>
        <div className='skills-section-tooltip-header'><span className='info-icon'>i</span> How do I fill out my Skills & Endorsements</div>
        
        <div className='skills-section-tooltip-detail'>
            <span className='group-icon' /><b>Group</b>&nbsp;- This could be either your team, company, or group you were a part of.
        </div>

        <div className='skills-section-tooltip-detail'>
            <span className='position-icon' /><b>Position</b>&nbsp;- The position you held, ie: for sports it could be Goalie/ Center Forward, for work it could be Accountant/Intern
        </div>

        <div className='skills-section-tooltip-detail'>
            <span className='awards-icon' /><span><b>Awards</b>&nbsp;- Any awards/medals/merit you received.</span>
        </div>
    </div>
);