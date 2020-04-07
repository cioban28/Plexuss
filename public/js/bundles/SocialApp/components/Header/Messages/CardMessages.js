import React, { Component } from 'react';
import { sendMessageAction, readThreadMsg } from '../../../actions/messages'
import { connect } from 'react-redux'
import moment from 'moment'
class CardMessages extends Component{
    constructor(props){
        super(props);
        this.readMessage= this.readMessage.bind(this);
    }
    readMessage(){
        const { thread } = this.props;
        let data ={
            thread_id: thread.thread_id
        }
        this.props.readThreadMsg(data);
    }
    render(){
        let { thread, addConversationThread } = this.props;
        let avatarPic = !!thread.Name && '/social/images/Avatar_Letters/'+thread.Name.charAt(0).toUpperCase() +'.svg'
        let profPic = !!thread.img ? !thread.img.includes('default.png') ? thread.img : avatarPic : avatarPic;

        let imgStyles = {backgroundImage: 'url("'+profPic+'")'};
        let localtime = moment.utc(thread.date).local().format('lll');

        return(
            <li onClick={this.readMessage}>
                <a className="row message_banner" onClick={() => addConversationThread(thread)} >
                    <div className="time">{localtime}</div>
                    <div className="large-3 medium-4 small-2 columns">
                        <div className="message-thread-img" style={imgStyles}/>
                    </div>
                    <div className="large-8 medium-8 small-10 columns">
                        <div className="user_name">{thread.Name+" "} <div className={"flag flag-"+ (!!thread && !!thread.country_code && thread.country_code.toLowerCase())}></div></div>
                        <div className="message">
                            {
                                thread.msg &&
                                isHTML(thread.msg) ?
                                    'Shared Post'
                                :
                                    thread.msg
                            }
                        </div>
                        {
                            thread.num_unread_msg > 0 &&
                                <div className="message_number">{thread.num_unread_msg}</div>
                        }
                    </div>
                     <div className="large-1 medium-1 small-0 columns"></div>
                </a>
            </li>
        )
    }

}
const isHTML = (str) => {
    const doc = new DOMParser().parseFromString(str, "text/html");
    return Array.from(doc.body.childNodes).some(node => node.nodeType === 1);
}
function mapDispatchToProps(dispatch) {
    return({
        sendMessage: (data) => {dispatch(sendMessageAction(data))},
        readThreadMsg: (data) => {dispatch(readThreadMsg(data))}
    })
}
export default connect(null, mapDispatchToProps)(CardMessages);
