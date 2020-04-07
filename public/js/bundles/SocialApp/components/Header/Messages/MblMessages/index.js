import React, { Component  } from 'react'
import InCommingBubble from './Components/InCommingBubble'
import MeBubble from './Components/MeBubble'
import AttcahFile from './Components/AttachFile'

import './styles.scss'

class MblMessages extends Component{
    render(){
        let { toggleShowConversation, messages, convoId } = this.props;
        return(
            <div className="con_container">
                <div className="con_header">
                    <div className="back_btn" onClick={() => toggleShowConversation()}> <span className="left_arrow">‹ </span>Back</div>
                    <div className="name">James vanderbilt</div>
                </div>
                <div className="pre_msgs">
                    PREVIOUS MESSAGES
                </div>
                <div className="convo">
                    {
                        messages.map((message, index) =>
                                (message.type == 'inComming') ?
                                    <InCommingBubble key={index} message={message.text} date={message.date}/>
                                :
                                    <MeBubble key={index} message={message.text} date={message.date}/>
                        )
                    }
                </div>
                <AttcahFile convoId={convoId}/>
            </div>
        )
    }
}
export default MblMessages