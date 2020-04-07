import React, { Component } from 'react'
import MessagesContainer from './messagesContainer'
import axios from 'axios'
import './styles.scss'
import { connect } from 'react-redux'
import { getThreaData } from './../../api/messages'
import { setThreadInfo, setThread } from './../../actions/messages'
class Messages extends Component{
    constructor(props){
        super(props);
        this.state={
            flag: true,
        }
    }
    componentDidMount(){
        axios({
            method: 'get',
            url: '/ajax/notifications/setRead?type=msg',
        })
    }
    componentWillReceiveProps(nextProps){
        let path = window.location.pathname.split('/')
        const { messagesPageNumber } = nextProps;
        if(this.state.flag && nextProps.messageThreads && path[3] !== undefined){
            const { messageThreads } = nextProps;
            let thread_id = path[3]
            let thread_index = null
            Object.values(messageThreads).map((item, index) => {
                if(item.thread_id == thread_id) {
                    thread_index = index
                }
            })

            let threadData = {
                currentThreadId: messageThreads[thread_index].thread_id,
                friendId:  messageThreads[thread_index].thread_type_id,
            }
            this.props.setThread(threadData);
            this.props.setThreadInfo(messageThreads[thread_index]);
            this.setState({flag: false})
            let _id = messageThreads[thread_index].thread_id;
            let _data = {
                id: messageThreads[thread_index].thread_id
            }
            if(messagesPageNumber && messagesPageNumber[_id] == 1){
                getThreaData(_data);
            }
        }
        else if(this.state.flag && nextProps.messageThreads && nextProps.messageThreads[0]){
            const { messageThreads } = nextProps;
            let threadData = {
                currentThreadId: messageThreads[0].thread_id,
                friendId:  messageThreads[0].thread_type_id,
            }
            this.props.setThread(threadData);
            this.props.setThreadInfo(messageThreads[0]);
            this.setState({flag: false});
            let _id = messageThreads[0].thread_id;
            let _data = {
                id: messageThreads[0].thread_id
            }
            if(messagesPageNumber && messagesPageNumber[_id] == 1){
                getThreaData(_data);
            }
        }
    }
    render(){
        return(
            <div>
                <MessagesContainer />
            </div>
        )
    }
}
function mapStateToProps(state){
    return{
        messageThreads: state.messages.messageThreads && state.messages.messageThreads.topicUsr,
        messagesPageNumber: state.messages.messagesPageNumber,
    }
}
function mapDispatchToProps(dispatch) {
    return({
        setThreadInfo: (data) => {dispatch(setThreadInfo(data))},
        setThread: (threadData) => {dispatch(setThread(threadData))},
    })
}
export default connect(mapStateToProps, mapDispatchToProps)(Messages);
