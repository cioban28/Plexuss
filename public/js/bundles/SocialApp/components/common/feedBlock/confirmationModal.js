import React from 'react';
class Confirmation extends React.Component{
    render(){
        const { closeModal, toggleHidePostFlag, forEditPost } = this.props;
        return(
            <div className={"confirmation_edit_modal "+ (forEditPost ? '' : 'dd-none')}>
                <div className="modal_heading">
                    Discard Changes?
                    <div className="modal_x" onClick={() => closeModal()}>&#10005;</div>
                </div>
                <div className="_block">
                    <div className="modal_message">
                        If you discard now, you'll lose any changes you've made this post.
                    </div>
                </div>
                <div className="action_btn">
                    <div className="Keep_editing" onClick={() => toggleHidePostFlag()}>Keep Editing</div>
                    <div className="discard" onClick={() => closeModal()}>Discard</div>
                </div>
            </div>
        )
    }
}

export default Confirmation;
