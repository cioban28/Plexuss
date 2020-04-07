import React, { Component } from 'react'
import { connect } from 'react-redux'
import Thread from './Messages/Thread/index'
import { makeThreadApi } from './../../api/messages'
import { addInConversationArray, removeThreadAction } from './../../actions/messages'

class ConversationArr extends Component{
    constructor(props){
        super(props);
        this.state={friendsState: []}
        this.switchTread = this.switchTread.bind(this);
    }
    componentDidMount(){
        const { friends } = this.props;
        if(friends){
            let newFriends = [];
            friends.map(friend =>{
              let obj = {
                value: friend.user_id, label: friend.fname+' '+friend.lname,
              };
              newFriends.push(obj);
            })
            this.setState({friendsState: newFriends})
        }
    }
    componentDidUpdate(prevProps){
        if(prevProps.friends != this.props.friends){
            const { friends } = this.props;
            if(friends){
                let newFriends = [];
                friends.map(friend =>{
                  let obj = {
                    value: friend.user_id, label: friend.fname+' '+friend.lname,
                  };
                  newFriends.push(obj);
                })
                this.setState({friendsState: newFriends})
            }
        }
    }
    switchTread(id){
        const { messageThreads, user } = this.props;
        let index = messageThreads.findIndex(thread => thread.thread_type_id == id);
        if(index == -1){
            let data = {
                user_id: id,
                thread_room: 'post:room:'+id,
                user_thread_room: 'post:room:'+user.user_id,
            }
            makeThreadApi(data)
            .then(res => {
                let t_id = {
                    thread_id: res.data.thread_id,
                }
                this.props.addInConversationArray(t_id);
            })
        }else{
            this.props.addInConversationArray(messageThreads[index]);
        }
    }
    render(){
        const { messageThreads, user, showThreadArr, nmFlag } = this.props;
        const { friendsState } = this.state;
        let arr = [];
        let thread={
            "thread_id": -99,
            "Name": "New Message",
        }
        return(
            <div className="converstaion_array">
                {
                    nmFlag &&
                    <Thread key={thread.thread_id} messages={arr} thread={thread} removeThread={this.props.removeThreadAction} user={user} friendsState={friendsState} switchTread={this.switchTread}/>                        
                }
                {
                    // messageThreads && messageThreads.map((thread, index) =>
                    //     (showThreadArr.includes(thread.thread_id)) ?
                    //       <Thread key={thread.thread_id} messages={this.props.allThreadMessages[thread.thread_id]} thread={thread} removeThread={this.props.removeThreadAction} user={user} friendsState={friendsState}/>
                    //     :
                    //         ''
                    // )
                    messageThreads && showThreadArr && 
                    showThreadArr.map((thread_id) => {
                        let index = messageThreads.findIndex(thread => thread.thread_id == thread_id);
                        if(index != -1){
                            return <Thread key={thread_id} messages={this.props.allThreadMessages[thread_id]} thread={messageThreads[index]} removeThread={this.props.removeThreadAction} user={user} friendsState={friendsState}/>
                        }
                    })
                }
            </div>
        )
    }
}
function mapStateToProps(state){
    return{
        messageThreads: state.messages.messageThreads && state.messages.messageThreads.topicUsr,
        user: state.user && state.user.data,
        showThreadArr: state.messages && state.messages.showThreadArr,
        nmFlag: state.messages && state.messages.nmFlag,
        allThreadMessages: state.messages && state.messages.allThreadMessages && state.messages.allThreadMessages,
        friends: state.user && state.user.networkingDate && state.user.networkingDate.friends,
    }
}
function mapDispatchToProps(dispatch) {
    return({
        removeThreadAction: (id) => { dispatch(removeThreadAction(id))},
        addInConversationArray: (thread) => { dispatch(addInConversationArray(thread))},
    })
}
export default connect(mapStateToProps, mapDispatchToProps)(ConversationArr);