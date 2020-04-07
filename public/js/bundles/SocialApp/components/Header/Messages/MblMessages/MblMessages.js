import React, { Component } from 'react'
import './styles.scss';
import MblMessages from './index'
import CardMessages from './CardMessages';
import { connect } from 'react-redux'

class Messages extends Component{
    constructor(props){
        super(props);
        this.state = {
            convoId: '',
            convoIndex: '',
        }
        this.setConvoId = this.setConvoId.bind(this);
    }
    setConvoId(id){
        let convoIndex = this.props.conversations.findIndex(conversation => conversation.id === id);
        this.setState({
            convoIndex: convoIndex,
            convoId: id,
        })
    }
    render(){
        let { toggleShowConversation, showConversation, conversations } = this.props;
        return(
            <span>
                <span className="desktop_vanish">
                    { showConversation && <MblMessages toggleShowConversation={toggleShowConversation} messages={conversations[this.state.convoIndex].messages} convoId={this.state.convoId}/> }
                </span>
                {
                    !showConversation &&
                    <div className="rightbar messages_bar">
                        <ul className="rightbar-list">
                            <Header />
                            {
                                conversations.map((conversation, index)=>
                                <CardMessages key={index} conversation={conversation} toggleShowConversation={toggleShowConversation} setConvoId={this.setConvoId}/>)
                            }
                        </ul>
                    </div>
                }
            </span>
        )
    }
}
function Header(){
    return(
        <span>
            <li className="get_messages_head">Messages</li>
            <li className="form_banner">
                <form className="messages_seacrh">
                    <i className="fa fa-search search_icon"></i>
                    <input type="text" placeholder="Search Messages" className="search_messages" />
                </form>
            </li>
        </span>
    )
}
function mapStateToProps(state){
    return{
        conversations: state.conversations.conversations,
    }
}
function mapDispatchToProps(){
    return{

    }
}

export default connect(mapStateToProps, mapDispatchToProps)(Messages)