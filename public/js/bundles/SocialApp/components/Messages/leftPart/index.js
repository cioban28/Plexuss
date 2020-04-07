import React, { Component } from 'react'
import { connect } from 'react-redux'
import InfiniteScroll from 'react-infinite-scroller';
import { getMessagesThreads, getThreaData } from './../../../api/messages'
import { setThread, setThreadInfo, sendMessageAction, unsetNextTopicUsr } from '../../../actions/messages'
import SingleCard from './singleCard'
import './styles.scss'
import cloneDeep from 'lodash/cloneDeep'
const _ = {
    cloneDeep: cloneDeep
}

class FriendsList extends Component{
    constructor(props){
        super(props);
        this.state={
            messageThreads: [],
            items: [],
            hasMoreItems: true,
            searchText: '',
        }
        this.filterList = this.filterList.bind(this);
        this.clickHandler = this.clickHandler.bind(this);
        this.getMoreTopicUsr = this.getMoreTopicUsr.bind(this);
    }
    componentDidMount(){
        this.setState({
            messageThreads: _.cloneDeep(this.props.messageThreads),
            items: _.cloneDeep(this.props.messageThreads),
        })
    }
    componentDidUpdate(prevProps){
        if(prevProps.messageThreads != this.props.messageThreads){
            const { searchText } = this.state;
            this.setState({
                messageThreads: _.cloneDeep(this.props.messageThreads),
            })
            var updatedList = _.cloneDeep(this.props.messageThreads);
            updatedList = updatedList.filter(function(item){
                let fullName = item.Name;
                return fullName.toLowerCase().search(searchText) !== -1;
            });
            this.setState({items: _.cloneDeep(updatedList)});
        }
    }
    clickHandler(thread){
        let threadId = thread && thread.thread_id ? thread.thread_id : -1;
        let threadData = {
            currentThreadId: threadId,
            friendId: thread.id ? thread.id : thread.thread_type_id,
        }
        this.props.setThread(threadData);
        this.props.setThreadInfo(thread);
    }
    filterList(event){
        let { messageThreads } = this.props;
        this.setState({searchText: event.target.value.toLowerCase()})
        if(messageThreads){
            var updatedList = _.cloneDeep(messageThreads);
            updatedList = updatedList.filter(function(item){
                let fullName = item.Name;
                return fullName.toLowerCase().search( event.target.value.toLowerCase()) !== -1;
            });
            this.setState({items: _.cloneDeep(updatedList)});
        }
    }
    getMoreTopicUsr(page){
        const { topicUsrPageNumber , nextTopicUser, user, unsetNextTopicUsr } = this.props;
        let data = { user_id: user.user_id, pageNumber: topicUsrPageNumber };
        if(user &&  user.user_id && nextTopicUser && topicUsrPageNumber > 1){
            this.setState({hasMoreItems: false})
            unsetNextTopicUsr();
            getMessagesThreads(data)
            .then((res)=>{
                this.setState({hasMoreItems: nextTopicUser});
            })
        }
    }
    render(){
        let { items } = this.state;
        let { mobileHandleFlag, mobileHandle, handleNewMessagesThread, removeMT } = this.props;
        return(
            <span className={"left_part "+ (mobileHandleFlag ? 'mobile_handler': '')}>
                <div className="header" onClick={()=>handleNewMessagesThread()}>
                    <div className="heading">
                        <div className="title">Start a new message</div>
                        <div className="_img-parent"><img src="/social/images/compose message.svg" /></div> 
                    </div>
                    <div className="search_bar">
                        <form className="messages_seacrh">
                            <i className="fa fa-search search_icon"></i>
                            <input type="text" placeholder="Search Messages" className="search_messages" onChange={(e)=>this.filterList(e)}/>
                        </form>
                    </div>
                </div>
                <div className="friends_list">
                    <InfiniteScroll
                        pageStart={1}
                        loadMore={this.getMoreTopicUsr}
                        hasMore={this.state.hasMoreItems}
                        useWindow={false}
                    >
                        {
                            items && items.map((thread, index) =>{
                                if(thread.thread_id !== -1){
                                    return <SingleCard key={index} thread={thread} type={'connected'} mobileHandle={mobileHandle} removeMT={removeMT}/>
                                }
                            })
                        }
                    </InfiniteScroll>
                </div>
            </span>
        )
    }
}
function mapStateToProps(state){
    return{
        messageThreads: state.messages.messageThreads && state.messages.messageThreads.topicUsr,
        user: state.user && state.user.data && state.user.data,
        allThreadMessages: state.messages && state.messages.allThreadMessages && state.messages.allThreadMessages,
        topicUsrPageNumber: state.messages && state.messages.topicUsrPageNumber,
        nextTopicUser: state.messages && state.messages.nextTopicUser
    }
}
function mapDispatchToProps(dispatch) {
    return({
        setThread: (threadData) => {dispatch(setThread(threadData))},
        setThreadInfo: (data) => {dispatch(setThreadInfo(data))},
        sendMessage: (data) => {dispatch(sendMessageAction(data))},
        unsetNextTopicUsr: () => { dispatch(unsetNextTopicUsr())},
    })
}
export default connect(mapStateToProps, mapDispatchToProps)(FriendsList);