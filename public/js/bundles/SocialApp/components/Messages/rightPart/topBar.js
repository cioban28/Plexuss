import React, { Component } from 'react'
import { connect } from 'react-redux'
import { setThreadInfo, setThread } from './../../../actions/messages'
import { makeThreadApi } from './../../../api/messages.js'
import Select from 'react-select';
import ReactTooltip from 'react-tooltip'
import { Link } from 'react-router-dom'
import './styles.scss'
class Topbar extends Component{
    constructor(props){
        super(props);
        this.state={
            friendsState: [],
        }
        this.handleChange = this.handleChange.bind(this);
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
    handleChange(selectedOption){
        let { handleNewMessagesThread, messageThreads, user } = this.props;
        this.setState({ selectedOption });
        let index = messageThreads.findIndex(thread => thread.thread_type_id == selectedOption.value);
        if(index == -1){
            let data = {
                user_id: selectedOption.value,
                thread_room: 'post:room:'+selectedOption.value,
                user_thread_room: 'post:room:'+user.user_id,
            }
            makeThreadApi(data)
            .then(res => {
                index = messageThreads.findIndex(thread => thread.thread_type_id == selectedOption.value);
                let threadData = {
                    currentThreadId: res.data.thread_id,
                    friendId:  selectedOption.value,
                }
                this.props.setThread(threadData);
                this.props.setThreadInfo(messageThreads[index]);
                handleNewMessagesThread();
            })
        }else{
            let threadData = {
                currentThreadId: messageThreads[index].thread_id,
                friendId:  selectedOption.value,
            }
            this.props.setThread(threadData);
            this.props.setThreadInfo(messageThreads[index]);
            handleNewMessagesThread();
        }
    }
    render(){
        const { selectedOption, friendsState } = this.state;
        let { userInfo, mobileHandle, newMessageThread } = this.props;
        let avatarPic = !!userInfo.Name && '/social/images/Avatar_Letters/'+userInfo.Name.charAt(0).toUpperCase()+'.svg'
        let profPic = !!userInfo.img ? !userInfo.img.includes('default.png') ? userInfo.img : avatarPic : avatarPic;

        return(
            <div className="top_bar">
                <div className="back_btn" onClick={() => mobileHandle()}> <i className="fa fa-angle-left"></i> </div>
                {
                    newMessageThread &&
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
                {
                    !newMessageThread &&
                    <div className="_grand_parent">
                        <div className="image_parent">
                            <img src={profPic} alt=""/>
                        </div>
                        {
                            <ReactTooltip place="bottom" type="light" id='view-profile' aria-haspopup='true' role='example'>
                              <span className="_tooltip_for_profile">View Profile</span>
                            </ReactTooltip>
                        }
                        <Link to={'/social/profile/'+userInfo.thread_type_id} data-tip data-for='view-profile' className="user_info_parent">
                            <div className="name">
                                {userInfo && userInfo.Name && userInfo.Name+" "}
                                <div className={"flag flag-"+ (!!userInfo && !!userInfo.country_code && userInfo.country_code.toLowerCase())}></div>
                            </div>
                            <div className="school">
                                {userInfo && userInfo.current_school}
                            </div>
                            <div className="status">
                                {userInfo && userInfo.current_school ? 'School' : ''}
                            </div>
                        </Link>
                    </div>
                }
            </div>
        )
    }
}
function mapStateToProps(state){
    return{
        userInfo: state.messages && state.messages.threadInfo && state.messages.threadInfo,
        messageThreads: state.messages.messageThreads && state.messages.messageThreads.topicUsr,
        friends: state.user && state.user.networkingDate && state.user.networkingDate.friends,
        user: state.user && state.user.data && state.user.data,
    }
}
function mapDispatchToProps(dispatch) {
    return({
        setThreadInfo: (data) => {dispatch(setThreadInfo(data))},
        setThread: (threadData) => {dispatch(setThread(threadData))},
    })
}
export default connect(mapStateToProps, mapDispatchToProps)(Topbar);
