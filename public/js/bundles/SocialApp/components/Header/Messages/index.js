import React, { Component } from 'react'
 import axios from 'axios'
import InfiniteScroll from 'react-infinite-scroller';
import CardMessages from './CardMessages';
import Header from './Header'
import { Link } from 'react-router-dom'
import { connect } from 'react-redux'
import cloneDeep from 'lodash/cloneDeep'
import { getMessagesThreads } from './../../../api/messages'
import { addInConversationArray, setNmFlag, unsetNextTopicUsr } from './../../../actions/messages'
import './styles.scss';
const _ = {
    cloneDeep: cloneDeep
}

class Messages extends Component{
    constructor(props){
        super(props);
        this.state={
            apiCallFlag: false,
            messageThreads: [],
            items: [],
            hasMoreItems: true,
            searchText: "",
        }
        this.filterList = this.filterList.bind(this);
        this.getMoreTopicUsr = this.getMoreTopicUsr.bind(this);
    }
    componentDidMount(){
        this.setState({
            messageThreads: _.cloneDeep(this.props.messageThreads),
            items: _.cloneDeep(this.props.messageThreads),
        })
        axios({
          method: 'get',
          url: '/ajax/notifications/setRead?type=msg',
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
        let data ={ user_id: user.user_id, pageNumber: topicUsrPageNumber };
        if(user && user.user_id && nextTopicUser && topicUsrPageNumber > 1){
            this.setState({hasMoreItems: false})
            unsetNextTopicUsr();
            getMessagesThreads(data)
            .then((res)=>{
                this.setState({hasMoreItems: nextTopicUser});
            })
        }
    }
    render(){
        let { user, messageThreads } = this.props;
        let { items } = this.state;
        return(
            <span>
            {this.props.user.signed_in == 1 ? (
                <div className="rightbar messages_bar">
                    <Header filterList={this.filterList} handleNewmsg={this.props.setNmFlag}/>
                    <ul className="rightbar-list">
                        {
                            !messageThreads &&
                            <div className="_sic_messages_plexuss_loader">
                                <img src="/social/images/loader/plexuss-loader-test-2.gif" />
                            </div> 
                        }
                        <InfiniteScroll
                            pageStart={1}
                            loadMore={this.getMoreTopicUsr}
                            hasMore={this.state.hasMoreItems}
                            useWindow={false}
                        >
                            {
                                items && items.map((thread, index) =>{
                                    if(thread.thread_id !== -1){
                                        return <CardMessages key={index} thread={thread} addConversationThread={this.props.addInConversationArray} user={user}/>
                                    }
                                })
                            }
                        </InfiniteScroll>
                        {
                            items && items.length > 0 && <Footer />
                        }
                    </ul>
                </div>
            ) : (
                <div className="rightbar message-preview">
                    <div className="right-circle"><img className="img-circle" src="/images/frontpage/messages-circle.png"/></div>
                    <div className="right-text"><span className="desc-text">Stay in touch with classmates, college students, alumni and university representatives</span></div>
                    <div className="right-login"><a href="/signup?utm_source=SEO&utm_medium=frontPage" className="btn-login">Login or Signup</a></div>
                </div>
            )}
            </span>
        )
    }
}

function Footer(){
    return(
        <li className="footer_area">
            <Link to={"/social/messages"} className="view_all">View All</Link>
        </li>
    )
}

function mapStateToProps(state){
    return{
        messageThreads: state.messages.messageThreads && state.messages.messageThreads.topicUsr,
        threadData: state.messages && state.messages.threadData && state.messages.threadData,
        allThreadMessages: state.messages && state.messages.allThreadMessages && state.messages.allThreadMessages,
        user: state.user && state.user.data,
        topicUsrPageNumber: state.messages && state.messages.topicUsrPageNumber,
        nextTopicUser: state.messages && state.messages.nextTopicUser

    }
}
function mapDispatchToProps(dispatch) {
    return({
        addInConversationArray: (thread) => { dispatch(addInConversationArray(thread))},
        setNmFlag: () => { dispatch(setNmFlag())},
        unsetNextTopicUsr: () => { dispatch(unsetNextTopicUsr())},
    })
}
export default connect(mapStateToProps, mapDispatchToProps)(Messages)
