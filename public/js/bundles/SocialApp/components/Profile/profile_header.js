import React, { Component } from 'react';
import Profile_edit_share_profile_button from './Profile_edit_share_profile_button'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import BModal from './modal';
import ReportingReasonModal from './../common/post/ReportingReasonModal'
import { friendRequest, declineRequest, acceptRequest, getNetworkingData } from './../../api/post'
import { makeThreadApi } from './../../api/messages'
class ProfileHeader extends Component{
    constructor(props){
        super(props);
        this.state={
            shareButton: false,
            flag: false,
            actionUser: '',
            showReportModal: false,
        }
        this.handleClick = this.handleClick.bind(this)
        this.sendFriendRequest = this.sendFriendRequest.bind(this)
        this.friendRequestAccept = this.friendRequestAccept.bind(this);
        this.friendRequestDecline = this.friendRequestDecline.bind(this);
        this.handleOutsideShare = this.handleOutsideShare.bind(this);
        this.shareContainer;
        this.shareBtn;
    }
    componentDidMount() {
        document.addEventListener('click', this.handleOutsideShare, false);
    }
    componentWillUnmount() {
        document.removeEventListener('click', this.handleOutsideShare, false);
    }
    handleOutsideShare(e) {
        if (this.shareContainer && this.shareBtn) {
            if (this.shareContainer.contains(e.target) || this.shareBtn.contains(e.target)) {return;}
        }
        if(this.state.shareButton === true){
          this.setState({shareButton: false})
        }
    }
    handleClick() {
        this.setState({shareButton: !this.state.shareButton})
    }
    sendFriendRequest(){
        let { id, loggedInUser, logInUserId, user } = this.props;
        let obj = {};
        obj.user_one_id = logInUserId;
        obj.user_name = loggedInUser.fname+' '+loggedInUser.lname;
        obj.user_two_id = id;
        obj.relation_status = 'Pending';
        obj.action_user = logInUserId;
        this.setState({
            flag: true,
        })
        friendRequest(obj)
        .then(()=>{
            this.setState({
                flag: false,
                actionUser: logInUserId,
            })
        })
    }

    friendRequestAccept(){
        let { logInUserId, loggedInUser, id, user } = this.props;
        let obj = {};
        obj.user_one_id = logInUserId;
        obj.user_two_id = id;
        obj.user_name = loggedInUser.fname+' '+loggedInUser.lname;
        obj.relation_status = 'Accepted';
        obj.action_user = logInUserId;
        this.setState({
            flag: true,
        })
        acceptRequest(obj)
        .then(()=>{
            getNetworkingData()
            this.setState({
                flag: false,
            })
            let data = {
                user_id: id,
                thread_room: 'post:room:'+id,
                user_thread_room: 'post:room:'+logInUserId,
            }
            makeThreadApi(data)
        })
    }
    friendRequestDecline(){
        let { logInUserId, loggedInUser, id } = this.props;
        let obj = {};
        obj.user_one_id = logInUserId;
        obj.user_name = loggedInUser.fname+' '+loggedInUser.lname;
        obj.user_two_id = id;
        obj.relation_status = 'Declined';
        obj.action_user = logInUserId;
        this.setState({
            flag: true,
        })
        declineRequest(obj)
        .then(()=>{
            getNetworkingData()
            this.setState({
                flag: false,
            })
        })
    }
    render(){
        let { user, id, logInUserId, actionUser, status, account_settings } = this.props;
        let pps = this.props.public_profile_settings;
        let showBasic = !!pps === false || (!!pps && logInUserId === pps.user_id) || (!!pps && pps.basic_info === 1) || (!!pps && pps.basic_info === 2 && this.props.status === 'Accepted') || (!!user && !!user.is_organization);
        let showSchool = logInUserId === Number(id) || !!account_settings == false ||(!!account_settings && (account_settings.show_school === 1 || account_settings.show_school === null ) );
        let school = !!user ? (!!user.user_school_names ? (!!user.user_school_names.profile ? user.user_school_names.profile : user.currentSchoolName) : user.currentSchoolName ) : '';
        let imgStyles = {
            backgroundImage: (user && user.profile_img_loc) ? 'url("'+user.profile_img_loc+'")' : (user && user.fname) ? 'url(/social/images/Avatar_Letters/'+user.fname.charAt(0).toUpperCase()+'.svg)' : "url(/social/images/Avatar_Letters/P.svg)"
        }
        return(
            <div className="profile_header">
                <div className="profile_action_area">
                <div className="user_info">
                    <div className="profile_image">
                        <div className="profile_image-img" style={imgStyles}/>
                    </div>
                    {
                        user &&
                        <div className="profile_info">
                            <div className="profile_name">
                                {user.fname} {showBasic && user.lname+' '}
                                <div className={"profile_flag flag flag-"+ (!!user.country_code && user.country_code.toLowerCase())}></div>
                            </div>
                            {showBasic && showSchool &&
                                <div>
                                    <p><span>{!!user.user_type && user.user_type}</span> at {school}</p>
                                    {!!user.grad_year && <p>Class of {user.grad_year}</p>}
                                </div>
                            }
                        </div>
                    }
                </div>
                <div className="action_buttons">
                {
                    logInUserId == id &&
                    <span>
                        <div ref={(ref) => {this.shareBtn = ref;}} className={"button share_profile "+(this.state.shareButton && 'active')} onClick={this.handleClick}>
                            {/* <img scr="/images/plexuss-logo-p.png" /> */}
                            Share Profile
                        </div>
                        <Link className="button edit_profile" to="/social/edit-profile"> Edit Profile </Link>
                    </span> ||

                    (logInUserId != id && (!!user && (status == "" || status == "Declined")) ) && (account_settings === null || !!account_settings && !!account_settings.receive_requests) &&
                        <div>
                            <button className="button profile_button" onClick={this.sendFriendRequest} disabled={this.state.flag}> <span>Connect</span> </button>
                            <button className="button profile_button" onClick={() => {this.setState({showReportModal: true})} } ><span>Report</span></button>
                            {logInUserId === 'v20qjMVWPe9ANX3eLywr4pRnJ' && <button className="button profile_button" onClick={() => window.open('http://'+window.location.hostname+'/'+this.props.viewApplicationUrl, '_blank') } ><span>View Student App</span></button>}
                        </div>
                    || (logInUserId != id && status == "Accepted") &&
                         <div>
                            <BModal user={user} id={id} logInUserId={logInUserId} accountSettings={account_settings}/>
                            <button className="button profile_button" onClick={() => {this.setState({showReportModal: true})} } ><span>Report</span></button>
                            {logInUserId === 'v20qjMVWPe9ANX3eLywr4pRnJ' && <button className="button profile_button" onClick={() => window.open('http://'+window.location.hostname+'/'+this.props.viewApplicationUrl, '_blank') } ><span>View Student App</span></button>}
                        </div>
                    || (logInUserId != id && status == "Pending" && (logInUserId == this.props.actionUser || logInUserId == this.state.actionUser) ) &&
                         <div>
                            <a className="button profile_button remove_cursor"> <span>Request Sent</span> </a>
                            <button className="button profile_button" onClick={() => {this.setState({showReportModal: true})} } ><span>Report</span></button>
                            {logInUserId === 'v20qjMVWPe9ANX3eLywr4pRnJ' && <button className="button profile_button" onClick={() => window.open('http://'+window.location.hostname+'/'+this.props.viewApplicationUrl, '_blank') } ><span>View Student App</span></button>}
                        </div>
                    || (logInUserId != id && status == "Pending" && (logInUserId != this.props.actionUser || logInUserId == this.state.actionUser) ) &&
                         <div>
                            <button className="button profile_button" onClick={this.friendRequestAccept} disabled={this.state.flag}> <span>Accept Request</span> </button>
                            <button className="button profile_button" onClick={() => {this.setState({showReportModal: true})} } ><span>Report</span></button>
                            {logInUserId === 'v20qjMVWPe9ANX3eLywr4pRnJ' && <button className="button profile_button" onClick={() => window.open('http://'+window.location.hostname+'/'+this.props.viewApplicationUrl, '_blank') } ><span>View Student App</span></button>}
                        </div>
                }
                </div>
                {
                    this.state.shareButton &&
                    <span ref={(ref) => {this.shareContainer = ref;}}><Profile_edit_share_profile_button /></span>
                }
                </div>
                <ReportingReasonModal profile={user} modalIsOpen={this.state.showReportModal} closeModal={() => this.setState({showReportModal: false})} />
            </div>
        )
    }
}


const mapStateToProps = (state) =>{
    return{
        loggedInUser: state.user.data,
        user: state.profile.user.user,
        actionUser: state.profile.user.relation_status && state.profile.user.relation_status[0] && state.profile.user.relation_status[0].action_user || null,
        status: state.profile.relationStatus,
        viewApplicationUrl: state.profile.user.student_app_url,
        public_profile_settings: !!state.profile.user && state.profile.user.publicProfileSettings,
        account_settings: !!state.profile.user && state.profile.user.userAccountSettings,
    }
}
export default connect(mapStateToProps, null)(ProfileHeader);
