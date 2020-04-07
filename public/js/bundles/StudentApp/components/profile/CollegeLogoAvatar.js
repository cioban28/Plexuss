import React from 'react';
import Tooltip from 'react-tooltip';
import { isEmpty } from 'lodash';

export default ({ url, school_name, slug, onDelete = undefined }) => {
    const openCollegePage = () => window.open('/college/' + slug, '_blank');

    // Circular shaped
    const STANDARD_MODE_STYLES = {
        background: 'url(' + url + ')',
        borderRadius: '50%',
    }

    // Square shaped to allow for close button
    const EDIT_MODE_STYLES = {
        background: 'url(' + url + ')',
        borderRadius: '50%',
        
    }

    const avatarStyles = onDelete ? EDIT_MODE_STYLES : STANDARD_MODE_STYLES;

    const id = `college-logo-school-name-${school_name}`;

    const onClick = onDelete ? onDelete : openCollegePage;

    return (
        <div className='college-logo-avatar'>
            <a onClick={onClick} style={{display: 'inline-block'}} data-tip data-for={id}>
                <div className='college-logo-image' style={avatarStyles}>
                    { onDelete && 
                        <div className='remove-liked-college-container'>
                            <span className='remove-liked-college-x'>&times;</span>
                        </div> }
                </div>
            </a>

            <Tooltip id={id} effect='solid'>
              <span>{school_name}</span>
            </Tooltip>

        </div>
    );
}