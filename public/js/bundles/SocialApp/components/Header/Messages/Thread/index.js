import React, { Component } from 'react';
import Select from 'react-select';
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import InfiniteScroll from 'react-infinite-scroller';
import './styles.scss';
import CommingBubble from './CommingBubble';
import OutBubble from './OutBubble';
import { saveMessage, typeMessageApi, cancelTypeMessageApi, getThreadMessages, setMsgReadTime, addUserInConversation, getThreaData } from './../../../../api/messages'
import { BubblesForMsg, ThreadMsgsBubble } from './../../../common/loader/loader'

class Thread extends Component{
    constructor(props){
        super(props);
        this.state = {
            text: '',
            selectedOption: null,
            placeholder: 'Type a message',
            ellipseFlag: true,
            msgFlag: false,
            timer: null,
            hasMoreItems: false,
            _flag: true,
            viewTimeFlag: true,
            scrollTimer: null,
            loader: true,
        }
        this.keyPress = this.keyPress.bind(this);
        this.onChange = this.onChange.bind(this);
        this.scrollToBottom = this.scrollToBottom.bind(this);
        this.sendMessage = this.sendMessage.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.keyUp = this.keyUp.bind(this);
        this.doneTyping = this.doneTyping.bind(this);
        this.getMoreMessages = this.getMoreMessages.bind(this);
        this._addUserInConvo = this._addUserInConvo.bind(this);
        this.setHasMoreFlag = this.setHasMoreFlag.bind(this);
    }
    setHasMoreFlag(){
        this.setState({hasMoreItems: true})
    }
    _addUserInConvo(){
        const { thread } = this.props;
        let data = {
            user_id: "v20qjMVWPe9pKNPdLywr4pRnJ",
            thread_id: thread.thread_id,
        }
        addUserInConversation(data);
    }
    keyPress(event){
        if(event.shiftKey && event.keyCode === 13) {
            this.setState({ text: event.target.value + "\n" });
        }
        else if(!event.shiftKey && event.keyCode == 13){
            if(this.state.text !== ''){
                this.sendMessage();
            }
            this.setState({text: ''})
            event.preventDefault()
        }
    }
    sendMessage(){
        let { thread, user } = this.props;
        let { text } = this.state;
        let thread_room_list = ['post:room:'+user.user_id];
        thread.thread_members.map((member) => {
            thread_room_list.push('post:room:'+member.user_id);
        })
        let msg = {
            message: text,
            thread_id: thread.thread_id,
            to_user_id: thread.thread_type_id ? thread.thread_type_id : '',
            thread_type: 'users',
            user_id: user.user_id,
            thread_room: thread.thread_id && thread.thread_id,
            thread_room_list: thread_room_list
        };
        saveMessage(msg)
        .then(()=>{
            this.setState({viewTimeFlag: true})
        })
        this.setState({
            message: '',
        })
    }
    onChange(event){
        this.setState({
            text: event.target.value,
        })
        if(event.target.value == ""){
            this.setState({viewTimeFlag: true})
        }
    }
    scrollToBottom() {
        if(this.messagesEnd){
            this.messagesEnd.scrollIntoView({ behavior: "smooth" });
        }
    }
    componentDidMount() {
        const { messages, thread, typingMsgArr, messagesPageNumber } = this.props;
        if(messages){
            this.state.scrollTimer = setTimeout(() => this.scrollToBottom(),500)
        }
        if(messagesPageNumber && thread && messagesPageNumber[thread.thread_id] == 1){
            let _data = {
                id: thread.thread_id,
            }
            getThreaData(_data)
            .then(() => {
                this.setState({loader: false})
                this.scrollToBottom();
                setTimeout(() => this.setHasMoreFlag(),1000)
            })
        }else{
            this.setState({loader: false})
            setTimeout(() => this.setHasMoreFlag(),1000)
        }
        if(messages.length == 0){
            this.setState({
                placeholder: 'Say hello to your new Plexuss connection '+thread.Name,
            })
        }
        this.setState({msgFlag: typingMsgArr.includes(thread.thread_id)})
    }

    componentDidUpdate(prevProps) {
        const { thread, typingMsgArr, messages } = this.props;
        if(prevProps.typingMsgArr != typingMsgArr){
            this.setState({msgFlag: typingMsgArr.includes(thread.thread_id)})
        }
        if(prevProps.messages != messages){
            this.setState({
                placeholder: 'Type a message',
            })
            if(this.state.viewTimeFlag){
                this.setState({viewTimeFlag: true})
            }
        }
    }
    componentWillReceiveProps(nextProps){
        const { thread, messages, scrollThread } = this.props;
        if(nextProps.messages && nextProps.scrollThread && nextProps.scrollThread[thread.thread_id] && nextProps.messages != messages){
            this.state.scrollTimer = setTimeout(() => this.scrollToBottom(),400)
        }
    }
    componentWillUnmount(){
        clearInterval(this.state.scrollTimer);
    }
    handleChange(selectedOption){
        const { removeThread, switchTread } = this.props;
        removeThread(-99);
        this.setState({selectedOption: selectedOption})
        switchTread(selectedOption.value);
    }
    keyUp(event){
        const { thread, user_id, messages } = this.props;
        const { ellipseFlag, viewTimeFlag } = this.state;
        let data ={
            thread_room: 'post:room:'+thread.thread_type_id,
            user_id: user_id,
            thread_id: thread.thread_id,
        }
        if(event.target.value && ellipseFlag){
            typeMessageApi(data);
            this.setState({ellipseFlag: false})
        }else if(!event.target.value && !ellipseFlag){
            this.doneTyping(data)
        }
        clearTimeout(this.state.timer);
        this.state.timer=setTimeout(() => this.doneTyping(data),800);
        if(messages && messages[messages.length-1] && user_id != messages[messages.length-1].user_id && viewTimeFlag && !messages[messages.length-1].read_time){
            let setReadTimeData = {
                msg_id: messages[messages.length-1].msg_id,
                thread_id: thread.thread_id,
                thread_room: 'post:room:'+messages[messages.length-1].user_id,
            }
            setMsgReadTime(setReadTimeData)
            this.setState({viewTimeFlag: false})
        }
    }
    doneTyping(data){
        cancelTypeMessageApi(data);
        this.setState({ellipseFlag: true})
    }
    getMoreMessages(page){
        const { thread, hasNextMessages, messagesPageNumber, messages } = this.props;
        if(thread.thread_id && messagesPageNumber && messagesPageNumber[thread.thread_id] > 1 && messages && messages.length > 0 && hasNextMessages && hasNextMessages[thread.thread_id]){
            let data = {
                thread_id: thread.thread_id,
                last_msg_id: messages[0].msg_id,
            }
            this.setState({
                hasMoreItems: false,
            })
            getThreadMessages(data)
            .then(()=>{
                this.setState({hasMoreItems: hasNextMessages[thread.thread_id]})
            })
        }
    }
    render(){
        const { selectedOption } = this.state;
        let { messages, removeThread, thread, user_id, friendsState, onlineUsers } = this.props;
        let avatarPic = !!thread.Name && '/social/images/Avatar_Letters/'+thread.Name.charAt(0).toUpperCase()+'.svg'
        let profPic = !!thread.img ? !thread.img.includes('default.png') ? thread.img : avatarPic : avatarPic;
        let online_flag = false;
        let frnd_index = onlineUsers.findIndex(userId => userId == thread.thread_type_id);
        if(frnd_index != -1){
            online_flag = true;
        }
        return(
            <div className="thread">
                <div className="header">
                    {
                        thread.thread_id !== -99 &&
                        <img src={profPic} />
                    }
                    {
                        thread.thread_id !== -99 &&
                        <i className={"fa fa-circle circle "+ (online_flag ? "online_circle" : "offline_circle")}></i>
                    }
                    <Link className="name" to={"/social/profile/"+thread.thread_type_id}> {" "+thread.Name} </Link>
                    {
                        // user_id == "v20qjMVWPe9ANX3eLywr4pRnJ" &&
                        // <div className="add_user_in_convo" onClick={this._addUserInConvo}>+</div>
                    }
                    <div style={{flex: 1}} />
                    <div className="cross_thread" onClick={() => removeThread(thread.thread_id)}>&#10005;</div>
                </div>
                {
                    thread.thread_id == -99 &&
                    <div className="to_new_msg">
                        <div>To:</div>
                        <Select
                            value={selectedOption}
                            onChange={this.handleChange}
                            options={friendsState}
                            className="react_select"
                        />
                    </div>
                }
                <div className="conversation bottom-spacing">
                    {
                        this.state.loader && messages.length == 0 &&
                        <div className="_thread_plexuss_loader">
                            <img src="/social/images/loader/plexuss-loader-test-2.gif" />
                        </div> 
                    }
                    {
                        thread.thread_id !== -99 &&
                        <div className="welcome-message">
                            <div className="msg_">You are now connected with <span className="name_">{thread.Name}</span></div>
                            <div className="date_">{thread.formatted_date}</div>
                        </div>
                    }
                    <InfiniteScroll
                        pageStart={1}
                        loadMore={this.getMoreMessages}
                        hasMore={this.state.hasMoreItems}
                        useWindow={false}
                        isReverse={true}
                        initialLoad={false}
                    >
                        {
                            messages.map((message, index) =>
                                (message.user_id == user_id) ?
                                    <OutBubble key={index} message={message} time={message.time}/>
                                :
                                    <CommingBubble key={index} img={message.img} message={message} time={message.time}/>
                            )
                        }
                    </InfiniteScroll>
                    <div style={{ float:"left", clear: "both" }}
                        ref={(el) => { this.messagesEnd = el; }}>
                    </div>
                    {
                        this.state.msgFlag &&
                        <div className="typing-msg">
                            typing <BubblesForMsg />
                        </div>
                    }
                </div>
                <textarea className="convo_text" placeholder={this.state.placeholder}
                onKeyDown={this.keyPress}
                onChange={this.onChange}
                onKeyUp={this.keyUp}
                value={this.state.text} />
            </div>
        )
    }
}
function mapStateToProps(state){
    return{
        user_id: state.user && state.user.data && state.user.data.user_id,
        typingMsgArr: state.messages && state.messages.typingMsgArr,
        hasNextMessages: state.messages.hasNextMessages,
        scrollThread: state.messages.scrollThread,
        messagesPageNumber: state.messages.messagesPageNumber,
        onlineUsers: state.user && state.user.onlineUsers,
    }
}
function mapDispatchToProps(dispatch){
    return{
    }
}
export default connect(mapStateToProps, mapDispatchToProps)(Thread);
