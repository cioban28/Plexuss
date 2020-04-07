import React, { Component } from 'react';
class CardMessages extends Component{
    render(){
        let { toggleShowConversation, conversation, setConvoId } = this.props;
        return(
            <li>
                <a className="row message_banner" onClick={() => {toggleShowConversation(); setConvoId(conversation.id) }} >
                    <div className="time">10:30 AM</div>
                    <div className="large-3 medium-3 small-2 columns">
                        <img src={conversation.friendImage} alt=""/>
                    </div>
                    <div className="large-9 medium-9 small-10 columns">
                        <div className="user_name">{conversation.friendName}</div>
                        <div className="message">{conversation.messages[1].text}</div>
                        <div className="message_number">1</div>
                    </div>
                </a>
            </li>
        )
    }
}
export default CardMessages;