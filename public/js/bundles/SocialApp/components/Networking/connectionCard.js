import React, { Component } from 'react'
import { Link } from 'react-router-dom'
import { connect } from 'react-redux';
import DeleteConnection from './DeleteConnection'
import { addInConversationArray, addNewThread } from './../../actions/messages'
import { makeThreadApi, getThreaData } from './../../api/messages'
class ConnectionCard extends Component{
    constructor(props){
        super(props);
        this.state={
            userRole: '',
            deleteModal: false,
            thread: {}
        }
        this.openMessageBox = this.openMessageBox.bind(this);
        this.setThread = this.setThread.bind(this);
        this.confirmDeleteConnection = this.confirmDeleteConnection.bind(this);
    }
    confirmDeleteConnection(){
        this.setState({deleteModal: !this.state.deleteModal});
    }
    componentDidMount() {
        let {friend, messageThreads} = this.props;
        if (friend.is_student == 1) {
            this.setState({ userRole: 'Student'})
        }
        else if(friend.is_alumni == 1) {
            this.setState({ userRole: 'Alumni'})
        }
        else if(friend.is_parent == 1) {
            this.setState({ userRole: 'Parent'})
        }
        else if(friend.is_counselor == 1) {
            this.setState({ userRole: 'Counselor'})
        }
        else if(friend.is_university_rep == 1) {
            this.setState({ userRole: 'University Rep.'})
        }
        else if(friend.is_organization == 1) {
            this.setState({ userRole: 'Organization'})
        }
        if(messageThreads){
            let index = messageThreads.findIndex(thred=> thred.thread_type_id == friend.user_id)
            if(index !== -1){
                this.setState({
                    thread: messageThreads[index],
                })
            }
        }
    }
    setThread(thread_id){
        let { messageThreads } = this.props;
        let index = messageThreads.findIndex(thred => thred.thread_id == thread_id)
        if(index != -1){
            this.setState({
                thread: messageThreads[index],
            },() =>{
                this.props.addInConversationArray(this.state.thread);
            })
        }
    }
    isEmpty(obj) {
        for(var key in obj) {
            if(obj.hasOwnProperty(key))
                return false;
        }
        return true;
    }
    openMessageBox(){
        let { friend, user } = this.props;
        if(this.isEmpty(this.state.thread)){
            let data = {
                user_id: friend.user_id,
                thread_room: 'post:room:'+friend.user_id,
                user_thread_room: 'post:room:'+user.user_id,
            }
            makeThreadApi(data)
            .then(res=>{
                this.props.addNewThread(res.data.thread_id);
                let data={
                    id: res.data.thread_id,
                }
                getThreaData(data)
                .then(()=>{
                    this.setThread(res.data.thread_id);
                })
            })
        }else{
            this.props.addInConversationArray(this.state.thread);
        }
    }
    render(){
        let { friend } = this.props;
        let profileImgStyles = {
          backgroundImage: !!friend && friend.user_img ? 'url("'+friend.user_img+'")' : "url(/social/images/Avatar_Letters/"+friend.fname.charAt(0).toUpperCase()+".svg)"
        }
        return(
            <li>
                <div className="row networking_card">
                    <div className="large-2 medium-2 small-2 columns">
                        <div className="userProfileImg">
                            <i className="fa fa-trash removeIcon" onClick={this.confirmDeleteConnection} title="Remove" aria-hidden="true"></i>
                            <Link to={"/social/profile/"+friend.user_id}>
                                <div className="profile_image" style={profileImgStyles}/>
                            </Link>
                        </div>
                    </div>
                    <div className="large-8 medium-8 small-7 columns" style={{marginLeft: '5%'}}>
                        <div className="userDiscription">
                        <Link to={"/social/profile/"+friend.user_id} className="userName user_name_hover">{friend && friend.fname} {friend && friend.lname}</Link>
                        <div className="countryFlag">
                            <div className={"flag flag-"+friend.country_code}></div>
                        </div>
                        <div className="collegeName">{friend.school_name}</div>
                        <div className="userRole">{this.state.userRole}</div>
                        </div>
                    </div>
                    <div onClick={() => this.openMessageBox()} className="large-2 medium-2 small-2 columns right_portion">
                        <div className="userContactImg">
                            <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Message.svg" />
                        </div>
                    </div>
                </div>

                {
                    this.state.deleteModal &&

                    <DeleteConnection  id={friend.user_id} logInUserId={this.props.user ? this.props.user.user_id : ''} entityName= "Connection" deleteModal={this.state.deleteModal} confirmDeleteConnection={this.confirmDeleteConnection}/>
                }
            </li>
        )
    }
}
const mapStateToProps = (state) =>{
    return{
      user: state.user.data,
    }
  }
function mapDispatchToProps(dispatch) {
    return({
        addInConversationArray: (thread) => { dispatch(addInConversationArray(thread))},
        addNewThread: (id) => { dispatch(addNewThread(id))},
    })
}
export default connect(mapStateToProps, mapDispatchToProps)(ConnectionCard);
