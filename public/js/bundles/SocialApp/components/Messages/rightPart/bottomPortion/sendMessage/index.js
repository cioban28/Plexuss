import React, { Component } from 'react'
import { connect } from 'react-redux'
import GifPlayer from 'react-gif-player';
import { saveMessage, typeMessageApi, cancelTypeMessageApi, setMsgReadTime } from './../../../../../api/messages'
import { BubblesForMsg } from './../../../../common/loader/loader'
import './styles.scss'
class SendMessage extends Component{
    constructor(props){
        super(props);
        this.state={
            message: '',
            giphyImages: [],
            modal: false,
            imageFlag: false,
            file: false,
            ellipseFlag: true,
            msgFlag: false,
            friend_name: '',
            timer: null,
            viewTimeFlag: true,
        }
        this.onChange = this.onChange.bind(this);
        this.sendMessage = this.sendMessage.bind(this);
        this.setImageSrc = this.setImageSrc.bind(this);
        this.addImage = this.addImage.bind(this);
        this.removeGiph = this.removeGiph.bind(this);
        this.keyPress = this.keyPress.bind(this);
        this.keyUp = this.keyUp.bind(this);
        this.doneTyping = this.doneTyping.bind(this);
    }
    componentDidMount(){
        const { typingMsgArr, currentThreadId, topicUsr } = this.props;
        if(topicUsr){
            let index = topicUsr.findIndex(user => user.thread_id == currentThreadId);
            if(index != -1){
                this.setState({
                    friend_name: topicUsr[index].Name,
                })
            }
        }
        if(typingMsgArr.includes(currentThreadId)){
            this.setState({msgFlag: true})
        }
    }
    componentDidUpdate(prevProps){
        const { typingMsgArr, currentThreadId, topicUsr, allMessages } = this.props;
        if(prevProps.typingMsgArr != this.props.typingMsgArr){
            if(typingMsgArr.includes(currentThreadId)){
                this.setState({msgFlag: true})
            }else{
                this.setState({msgFlag: false})
            }
        }
        if(prevProps.allMessages != allMessages){
            this.setState({viewTimeFlag: true})
        }
        if(prevProps.currentThreadId != this.props.currentThreadId){
            if(topicUsr){
                let index = topicUsr.findIndex(user => user.thread_id == currentThreadId);
                if(index != -1){
                    this.setState({
                        friend_name: topicUsr[index].Name,
                    })
                }
            }
            this.setState({
                message: '',
                ellipseFlag: true,
            })
            if(typingMsgArr.includes(currentThreadId)){
                this.setState({msgFlag: true})
            }else{
                this.setState({msgFlag: false})
            }
            this.setState({viewTimeFlag: true})
        }
    }
    onChange(event){
        this.setState({
            message: event.target.value,
        })
        if(event.target.value == ""){
            this.setState({viewTimeFlag: true})
        }
    }
    sendMessage(){
        let { currentThreadId, friendId, userId, topicUsr } = this.props;
        let { message } = this.state;
        let thread_room_list = ['post:room:'+userId];
        let index = topicUsr.findIndex(user => user.thread_id == currentThreadId);
        if(index != -1){
            topicUsr[index].thread_members.map((member) => {
                thread_room_list.push('post:room:'+member.user_id)
            })
        }
        let msg = {
            message: message,
            thread_id: currentThreadId,
            to_user_id: friendId,
            thread_type: 'users',
            user_id: userId,
            thread_room: currentThreadId,
            thread_room_list: thread_room_list
        };
        saveMessage(msg)
        .then(() => {
            this.setState({viewTimeFlag: true})
        })
        this.setState({
            message: '',
            giphyImages: [],
        })
    }
    setImageSrc(imageUrl, previewImg){
        let obj = {};
        obj.imageSrc = imageUrl;
        obj.previewImg = previewImg;
        let arr = [];
        arr.push(obj);
        this.setState({
            giphyImages: arr,
            modal: !this.state.modal,
        }, function(){
            this.setState({
                message: '<GifPlayer gif={'+this.state.giphyImages[0].imageSrc+'} still={'+this.state.giphyImages[0].previewImg+'} />'
            })
        });
    }
    addImage(){
        this.setState({
            imageFlag: !this.state.imageFlag,
        })
    }
    removeGiph(){
        this.setState({
            giphyImages: [],
            message: '',
        })
    }
    keyPress(event){
        if(event.shiftKey && event.keyCode === 13) {
            this.setState({ message: event.target.value + "\n" });
        }
        else if(!event.shiftKey && event.keyCode == 13){
            if(this.state.message){
                this.sendMessage();
            }
            event.preventDefault()
        }
    }
    keyUp(event){
        const { currentThreadId, friendId, userId, allMessages } = this.props;
        const { ellipseFlag, viewTimeFlag } = this.state;
        let data ={
            thread_room: 'post:room:'+friendId,
            user_id: userId,
            thread_id: currentThreadId,
        }
        if(event.target.value && ellipseFlag){
            typeMessageApi(data);
            this.setState({ellipseFlag: false})
        }else if(!event.target.value && !ellipseFlag){
            this.doneTyping(data);
        }
        clearTimeout(this.state.timer);
        this.state.timer=setTimeout(() => this.doneTyping(data),800);
        if(allMessages && allMessages[allMessages.length-1] && userId != allMessages[allMessages.length-1].user_id && viewTimeFlag && !allMessages[allMessages.length-1].read_time){
            let setReadTimeData = {
                msg_id: allMessages[allMessages.length-1].msg_id,
                thread_id: currentThreadId,
                thread_room: 'post:room:'+allMessages[allMessages.length-1].user_id,
            }
            setMsgReadTime(setReadTimeData)
            this.setState({viewTimeFlag: false})
        }
    }
    doneTyping(data){
        cancelTypeMessageApi(data);
        this.setState({ellipseFlag: true})
    }
    render(){
        return(
            <div className="textarea_parent">
                {
                    this.state.msgFlag &&
                    <div className="typing_msg">
                        <span className="f_name">{this.state.friend_name}</span> is typing <BubblesForMsg />
                    </div>
                }
                <div className="textarea_banner">
                    {   
                        this.state.giphyImages.length == 0 &&
                            <textarea placeholder="Type your message here"
                            rows="1" onChange={this.onChange}
                            onKeyDown={this.keyPress}
                            value={this.state.message}
                            onKeyUp={this.keyUp}
                            ></textarea>||
                        this.state.giphyImages.length > 0 &&
                            <div className="giph_parent">
                                <div className="gigh_close" onClick={this.removeGiph}>x</div>
                                <GifPlayer gif={this.state.giphyImages[0].imageSrc} still={this.state.giphyImages[0].previewImg} />
                            </div>
                    }
                    <div className="sending_option">
                        {
                            this.state.message &&
                                <button className="btn" onClick={this.sendMessage}>SEND</button>
                        }
                    </div>
                </div>
            </div>
        )
    }
}
function mapStateToProps(state){
    return{
        currentThreadId: state.messages.currentThreadId,
        friendId: state.messages && state.messages.friendId,
        userId: state.user && state.user.data && state.user.data.user_id,
        typingMsgArr: state.messages && state.messages.typingMsgArr,
        topicUsr: state.messages && state.messages.messageThreads && state.messages.messageThreads.topicUsr && state.messages.messageThreads.topicUsr,
        allMessages: state.messages && state.messages.allThreadMessages && state.messages.allThreadMessages[state.messages.currentThreadId],
    }
}
export default connect(mapStateToProps, null)(SendMessage);