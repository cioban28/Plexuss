import React, { Component } from 'react'
import { connect } from 'react-redux'
import { ConnectedUser } from './helper'
import { getThreaData } from './../../../api/messages'
import { setThread, setThreadInfo, sendMessageAction, readThreadMsg } from '../../../actions/messages'
class SingleCard extends Component{
    constructor(props){
        super(props);
        this.clickHandler = this.clickHandler.bind(this);
    }
    clickHandler(){
        let { thread, mobileHandle, removeMT, messagesPageNumber } = this.props;
        let threadId = thread && thread.thread_id ? thread.thread_id : -1;
        let threadData = {
            currentThreadId: threadId,
            friendId: thread.id ? thread.id : thread.thread_type_id,
        }
        this.props.setThread(threadData);
        this.props.setThreadInfo(thread);
        mobileHandle();
        removeMT();
        let data ={
            thread_id: thread.thread_id
        }
        this.props.readThreadMsg(data);
        if(messagesPageNumber && thread && messagesPageNumber[thread.thread_id] == 1 && threadId != -1){
            let _data = {
                id: threadId,
            }
            getThreaData(_data);
        }
    }
    render(){
        let { thread, userInfo } = this.props;
        return(
            thread && 
            <div className={"row single_message_banner "+ (thread.thread_id == userInfo.thread_id ? 'selected_card' : '')} onClick={this.clickHandler}>    
                <ConnectedUser thread={thread}/>
            </div>
        )
    }
}

function mapDispatchToProps(dispatch) {
    return({
        setThread: (threadData) => {dispatch(setThread(threadData))},
        setThreadInfo: (data) => {dispatch(setThreadInfo(data))},
        sendMessage: (data) => {dispatch(sendMessageAction(data))},
        readThreadMsg: (data) => {dispatch(readThreadMsg(data))},
    })
}
function mapStateToProps(state){
    return{
        user: state.user && state.user.data && state.user.data,
        userInfo: state.messages && state.messages.threadData && state.messages.threadInfo,
        messagesPageNumber: state.messages.messagesPageNumber,
    }
}
export default connect(mapStateToProps, mapDispatchToProps)(SingleCard);