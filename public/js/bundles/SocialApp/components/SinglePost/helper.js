import React from 'react'
export function DeletedPost(){
    return (
        <div className="deleted_post_banner">
            <div className="deleted_post_content">
                <div className="dpc_header">
                    Attachment Unavailable
                </div>
                <div className="dpc_text">
                    This attachment may have been removed.
                </div>
            </div>
        </div>
    )
}