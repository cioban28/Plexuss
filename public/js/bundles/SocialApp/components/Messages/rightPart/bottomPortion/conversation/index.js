import React, { Component } from 'react'
import { connect } from 'react-redux'
import InfiniteScroll from 'react-infinite-scroller';
import { getThreadMessages } from './../../../../../api/messages'
import { InCommingWithImage, OutGoingWithImage} from './helper'
import './styles.scss'

class Conversation extends Component{
    constructor(props){
        super(props);
        this.state={
            hasMoreItems: true,
            _flag: false,
            loader: true,
        }
        this.scrollToBottom = this.scrollToBottom.bind(this);
        this.getMoreMessages = this.getMoreMessages.bind(this);
    }
    componentDidMount() {
        if(this.props.message){
            this.scrollToBottom();
        }
    }
    
    componentDidUpdate(prevProps) {
        const { scrollThread, currentThreadId, messagesPageNumber } = this.props;
        if(prevProps.messages != this.props.message && scrollThread[currentThreadId]){
            this.scrollToBottom();
        }
        if(messagesPageNumber[currentThreadId] > 1){
            if(this.state.loader){
                this.setState({loader: false})
            }
        }else{
            if(!this.state.loader){
                this.setState({loader: true})
            }
        }
    }
    scrollToBottom() {
        this.messagesEnd.scrollIntoView({ behavior: "smooth" });
    }
    getMoreMessages(page){
        const { currentThreadId, hasNextMessages, messagesPageNumber, messages } = this.props;
        if(currentThreadId && messagesPageNumber && messagesPageNumber[currentThreadId] > 1 && messages && messages.length > 0 && hasNextMessages && hasNextMessages[currentThreadId]){
            let data = {
                thread_id: currentThreadId,
                last_msg_id: messages[0].msg_id,
            }
            this.setState({hasMoreItems: false})
            getThreadMessages(data)
            .then(()=>{
                this.setState({hasMoreItems: hasNextMessages[currentThreadId]})
            })
        }
    }
    render(){
        let { messages, user } = this.props;
        let MESSAGES = [];
        let date = '';
        let flag = false;
        if(messages){
            MESSAGES = messages.map((message, index)=>{
                let arr = message.date.split(' ');
                if(arr[0] != date){
                    date = arr[0];
                    flag = true;
                }else{
                    flag = false;
                }
                if(message.user_id == user.user_id){
                    return <OutGoingWithImage message={message} key={index} date={message.date} flag={flag}/>
                }else{
                    return <InCommingWithImage message={message}key={index} date={message.date} flag={flag}/>
                }
            })
        }
        return(
            <div className="_converstaion">
                {
                    this.state.loader && (!messages || (messages && messages.length == 0)) &&
                    <div className="_convo_plexuss_loader">
                        <img src="/social/images/loader/plexuss-loader-test-2.gif" />
                    </div>
                }
                <InfiniteScroll
                    pageStart={1}
                    loadMore={this.getMoreMessages}
                    hasMore={this.state.hasMoreItems}
                    useWindow={false}
                    isReverse={true}
                >
                    {MESSAGES}
                </InfiniteScroll>
                <div style={{ float:"left", clear: "both" }}
                    ref={(el) => { this.messagesEnd = el; }}>
                </div>
            </div>
        )
    }
}

function mapStateToProps(state){
    return{
        user: state.user && state.user.data && state.user.data,
        currentThreadId: state.messages.currentThreadId,
        hasNextMessages: state.messages.hasNextMessages,
        scrollThread: state.messages.scrollThread,
        messagesPageNumber: state.messages.messagesPageNumber,
    }
}
export default connect(mapStateToProps, null)(Conversation);
