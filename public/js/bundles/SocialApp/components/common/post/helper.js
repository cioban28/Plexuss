import React from 'react'
export function AttachmentUnavailable(){
    return(
        <div className="unavailable_attachment_banner">
            <div className="content_banner">
                <div className="av_header">
                    Attachment Unavailable
                </div>
                <div className="av_text">
                    This attachment may have been removed.
                </div>
            </div>
        </div>
    )
}