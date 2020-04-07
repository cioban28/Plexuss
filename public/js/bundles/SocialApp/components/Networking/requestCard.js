import React, { Component } from 'react'
import { Link } from 'react-router-dom'
import { getNetworkingData, declineRequest, acceptRequest } from './../../api/post'
import { makeThreadApi } from './../../api/messages'
class RequestCard extends Component{
    constructor(props){
        super(props);
        this.state={
            userRole: '',
            visibleFlag: true,
        }
        this.friendRequestAccept = this.friendRequestAccept.bind(this);
        this.friendRequestDecline = this.friendRequestDecline.bind(this);
        this.handleVisibleState = this.handleVisibleState.bind(this);
    }
    componentDidMount() {
        let {requestedUser} = this.props;
        if (requestedUser.is_student == 1) {
            this.setState({ userRole: 'Student'})
        }
        else if(requestedUser.is_alumni == 1) {
            this.setState({ userRole: 'Alumni'})
        }
        else if(requestedUser.is_parent == 1) {
            this.setState({ userRole: 'Parent'})
        }
        else if(requestedUser.is_counselor == 1) {
            this.setState({ userRole: 'Counselor'})
        }
        else if(requestedUser.is_university_rep == 1) {
            this.setState({ userRole: 'University Rep.'})
        }
        else if(requestedUser.is_organization == 1) {
            this.setState({ userRole: 'Organization'})
        }
    }
    friendRequestAccept(){
        let { logInUser, requestedUser } = this.props;
        let obj = {};
        obj.user_one_id = logInUser.user_id;
        obj.user_name = logInUser.fname+' '+logInUser.lname;
        obj.user_two_id = requestedUser.user_id;
        obj.relation_status = 'Accepted';
        obj.action_user = logInUser.user_id;
        acceptRequest(obj)
        .then(()=>{
            getNetworkingData()
            let data = {
                user_id: requestedUser.user_id,
                thread_room: 'post:room:'+requestedUser.user_id,
                user_thread_room: 'post:room:'+logInUser.user_id,
            }
            makeThreadApi(data);
            this.handleVisibleState();
        })
    }
    friendRequestDecline(){
        let { logInUser, requestedUser } = this.props;
        let obj = {};
        obj.user_one_id = logInUser.user_id;
        obj.user_two_id = requestedUser.user_id;
        obj.relation_status = 'Declined';
        obj.action_user = logInUser.user_id;
        obj.user_name = logInUser.fname+' '+logInUser.lname;
        declineRequest(obj);
        this.handleVisibleState();
    }
    handleVisibleState(){
        this.setState({
            visibleFlag: false,
        })
    }
    render(){
        let { requestedUser } = this.props;
        let profileImgStyles = {
          backgroundImage: !!requestedUser && requestedUser.user_img ? 'url("'+requestedUser.user_img+'")' : "url(/social/images/Avatar_Letters/"+requestedUser.fname.charAt(0).toUpperCase()+".svg)"
        }
        return(
            this.state.visibleFlag &&
            <li>
                <div className="row networking_card">
                    <div className="large-2 medium-2 small-2 columns">
                        <Link className="userProfileImg" to={"/social/profile/"+requestedUser.user_id}>
                          <div className="profile_image no-trash" style={profileImgStyles}/>
                        </Link>
                    </div>
                    <div className="large-8 medium-8 small-7 columns" style={{marginLeft: '5%'}}>
                        <div className="userDiscription">
                            <Link to={"/social/profile/"+requestedUser.user_id} className="userName user_name_hover">{requestedUser && requestedUser.fname} {requestedUser && requestedUser.lname}</Link>
                            <div className="countryFlag">
                                <div className={"flag flag-"+requestedUser.country_code}>
                                </div>
                            </div>
                            <div className="collegeName">{requestedUser && requestedUser.school_name}</div>
                            <div className="userRole">{this.state.userRole}</div>
                        </div>
                    </div>
                    <div className="large-2 medium-2 small-3 columns tick_cross_parent">
                        <div className="requestAction">
                            <div className="circle cross_circle" onClick={this.friendRequestDecline}>
                                <div className="close-button">&#10005;</div>
                            </div>
                            <div className="circle" onClick={this.friendRequestAccept}>
                                <img src="/social/images/Icons/accept.png" alt="contact" className="contact" />
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        )
    }
}

export default RequestCard;
