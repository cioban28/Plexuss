import React, { Component } from 'react'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import { friendRequest } from './../../api/post'
import { sentRequsetAction } from './../../actions/setting'

class ImportPlexussMemberCard extends Component{
    constructor(props){
        super(props);
        this.state={
            sendRequest: false,
            status: 'Request Sent!',
            userRole: '',
        }
        this.friendRequestSend = this.friendRequestSend.bind(this);
        this.findUserRole = this.findUserRole.bind(this);
    }

    friendRequestSend(){
        let { loggedInUser, member } = this.props;
        let obj = {};
        obj.user_one_id = loggedInUser.user_id;
        obj.user_name = loggedInUser.fname+' '+loggedInUser.lname;
        obj.user_two_id = member.id;
        obj.relation_status = 'Pending';
        obj.action_user = loggedInUser.user_id;
        friendRequest(obj)
        .then(()=>{
            this.setState({
                sendRequest: true,
            })
        })
        let data = {
            id: member.id,
        }
        this.props.sentRequsetAction(data);
    }

    componentDidMount() {
        let { member } = this.props;
        if(member.relation_status !== "N/A"){
            if(member.relation_status && member.relation_status.relation_status === 'Accepted'){
                this.setState({
                    status: 'Friend'
                })
            }
            this.setState({
                sendRequest: true,
            })
        }
        this.findUserRole(member)
    }

    findUserRole(member) {
        if (member.is_student && member.is_student != 0) {
            this.setState({ userRole: 'Student' })
        }
        else if (member.is_alumni && member.is_alumni != 0) {
            this.setState({ userRole: 'Alumni' })
        }
        else if (member.is_parent && member.is_parent != 0) {
            this.setState({ userRole: 'Parent' })
        }
        else if (member.is_counselor && member.is_counselor != 0) {
            this.setState({ userRole: 'Counselor' })
        }
        else if (member.is_university_rep && member.is_university_rep != 0) {
            this.setState({ userRole: 'University Rep.' })
        }
        else if (member.is_organization && member.is_organization != 0) {
            this.setState({ userRole: 'Organization' })
        }
        else if (member.is_agency && member.is_agency != 0) {
            this.setState({ userRole: 'Agency' })
        }
    }

    render(){
        let { member } = this.props;
        let profileImgStyles = {
          backgroundImage: !!member && member.profile_img_loc ? 'url("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'+member.profile_img_loc+'")' : "url(/social/images/Avatar_Letters/"+member.fname.charAt(0).toUpperCase()+".svg)"
        }
        return(
            <li>
                <div className="row networking_card">
                    <div className="large-2 medium-2 small-2 columns">
                        <Link className="userProfileImg" to={"/social/profile/"+member.id}>
                            <div className="profile_image no-trash" style={profileImgStyles}/>
                        </Link>
                    </div>
                    <div className="large-7 medium-7 small-7 columns" style={{marginLeft: '5%'}}>
                        <div className="userDiscription" >
                            <Link className="userName user_name_hover" to={"/social/profile/"+member.id}>{member && member.fname} {member && member.lname}</Link>
                            <div className="countryFlag">
                                <div className={"flag flag-"+member.country_code}>
                                </div>
                            </div>
                            <div className="collegeName">{member.school_name}</div>
                            <div className="userRole">
                                { this.state.userRole }
                            </div>
                        </div>
                    </div>
                    <div className="large-3 medium-3 small-3 columns tick_cross_parent">
                        <div className="requestAction">
                            {
                                !this.state.sendRequest &&
                                    <div className="addUser" onClick={this.friendRequestSend}>
                                        <img src="/social/images/Icons/add-user.png" alt="contact" className="contact" />
                                    </div>||
                                this.state.sendRequest &&
                                <div className="">
                                    <div className="requst_sent">{this.state.status}</div>
                                </div>
                            }
                        </div>
                    </div>
                </div>
            </li>

        )
    }
}
function mapStateToProps(state){
  return{
    loggedInUser: state.user && state.user.data,
  }
}
function mapDispatchtoProps(dispatch){
    return {
        sentRequsetAction: (data) => {dispatch(sentRequsetAction(data))},
    }
}
export default connect(mapStateToProps, mapDispatchtoProps)(ImportPlexussMemberCard);
