import React, { Component } from 'react'
import { friendRequest } from './../../api/post'
import { Link } from 'react-router-dom'
class SuggestionCard extends Component{
    constructor(props){
        super(props);
        this.state={
            visibleFlag: true,
            userRole: '',
        }
        this.friendRequestSend = this.friendRequestSend.bind(this);
        this.handleVisibleState = this.handleVisibleState.bind(this);
    }
    componentDidMount() {
        let {suggestedUser} = this.props;
        if (suggestedUser.is_student == 1) {
            this.setState({ userRole: 'Student'})
        }
        else if(suggestedUser.is_alumni == 1) {
            this.setState({ userRole: 'Alumni'})
        }
        else if(suggestedUser.is_parent == 1) {
            this.setState({ userRole: 'Parent'})
        }
        else if(suggestedUser.is_counselor == 1) {
            this.setState({ userRole: 'Counselor'})
        }
        else if(suggestedUser.is_university_rep == 1) {
            this.setState({ userRole: 'University Rep.'})
        }
        else if(suggestedUser.is_organization == 1) {
            this.setState({ userRole: 'Organization'})
        }
    }
    friendRequestSend(){
        let { logInUser, suggestedUser } = this.props;
        let obj = {};
        obj.user_one_id = logInUser.user_id;
        obj.user_name = logInUser.fname+' '+logInUser.lname;
        obj.user_two_id = suggestedUser.user_id;
        obj.relation_status = 'Pending';
        obj.action_user = logInUser.user_id;
        friendRequest(obj)
        .then(()=>{
            this.handleVisibleState();
        })
    }
    handleVisibleState(){
        this.setState({
            visibleFlag: false,
        })
    }
    render(){
        let { suggestedUser } = this.props;
        let profileImgStyles = {
          backgroundImage: !!suggestedUser && suggestedUser.user_img ? 'url("'+suggestedUser.user_img+'")' : "url(/social/images/Avatar_Letters/"+suggestedUser.fname.charAt(0).toUpperCase()+".svg)"
        }
        return(
            this.state.visibleFlag &&
                <li>
                    <div className="row networking_card">
                        <div className="large-2 medium-2 small-2 columns">
                            <Link className="userProfileImg" to={"/social/profile/"+suggestedUser.user_id}>
                                <div className="profile_image no-trash" style={profileImgStyles}/>
                            </Link>
                        </div>
                        <div className="large-8 medium-8 small-7 columns" style={{marginLeft: '5%'}}>
                            <div className="userDiscription">
                                <Link className="userName user_name_hover" to={"/social/profile/"+suggestedUser.user_id}>{suggestedUser && suggestedUser.fname} {suggestedUser && suggestedUser.lname}</Link>
                                <div className="countryFlag">
                                    <div className={"flag flag-"+suggestedUser.country_code}>
                                    </div>
                                </div>
                                <div className="collegeName">{suggestedUser.school_name}</div>
                                <div className="userRole">{this.state.userRole}</div>
                            </div>
                        </div>
                        <div className="large-2 medium-2 small-3 columns tick_cross_parent">
                            <div className="requestAction">
                                <div className="circle cross_circle" onClick={this.handleVisibleState}>
                                    <div className="close-button">&#10005;</div>
                                </div>
                                <div className="circle" onClick={this.friendRequestSend}>
                                    <img src="/social/images/Icons/accept.png" alt="contact" className="contact" />
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
        )
    }
}

export default SuggestionCard;
