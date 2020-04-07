import React, { Component } from 'react'
import { connect } from 'react-redux'
import FriendsList from './leftPart/index'
import RightPart from './rightPart/index'
import { setThreadInfo, setThread } from './../../actions/messages'
import { Helmet } from 'react-helmet';
class MessagesContainer extends Component{
    constructor(props){
        super(props);
        this.state={
            mobileHandleFlag: (window.innerWidth < 767 && window.location.pathname.split("/")[3] !== undefined) ? true : false,
            newMessageThread: false,
        }
        this.mobileHandle = this.mobileHandle.bind(this);
        this.handler = this.handler.bind(this);
        this.removeMT = this.removeMT.bind(this);
    }
    mobileHandle(){
        this.setState({
            mobileHandleFlag: !this.state.mobileHandleFlag,
        })
    }
    handler(){
        this.setState({newMessageThread: !this.state.newMessageThread})
    }
    removeMT(){
        if(this.state.newMessageThread != false){
            this.setState({newMessageThread: false})
        }
    }
    render(){
        return(
            <div className="messages_container">
                <Helmet>
                    <title>College Admin Dashboard | Student Recruitment | Plexuss.com</title>
                </Helmet>
                <FriendsList
                    mobileHandleFlag={this.state.mobileHandleFlag}
                    mobileHandle={this.mobileHandle}
                    handleNewMessagesThread={this.handler}
                    removeMT={this.removeMT}
                   />
                <RightPart messages={this.props.messages} mobileHandleFlag={this.state.mobileHandleFlag} mobileHandle={this.mobileHandle} handleNewMessagesThread={this.handler} newMessageThread={this.state.newMessageThread} />
            </div>
        )
    }
}
function mapStateToProps(state){
    return{
        messages: state.messages && state.messages.allThreadMessages && state.messages.allThreadMessages[state.messages.currentThreadId],
    }
}
function mapDispatchToProps(dispatch) {
    return({
        setThreadInfo: (data) => {dispatch(setThreadInfo(data))},
        setThread: (threadData) => {dispatch(setThread(threadData))},
    })
}
export default connect(mapStateToProps, mapDispatchToProps)(MessagesContainer);
