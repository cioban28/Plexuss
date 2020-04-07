import React from 'react';
import Tooltip from 'react-tooltip'

export default ({ content, id, textColor = '#fff', place = 'top', type = 'info' }) => (
    <div className='info-tooltip-container' style={{color: textColor}} data-tip data-for={id}>
        <span className='info-tooltip-icon' style={{color: textColor}}>i</span>

        <Tooltip id={id} effect='solid' place={place} type={type}>
            <span>{content}</span>
        </Tooltip>
    </div>
)