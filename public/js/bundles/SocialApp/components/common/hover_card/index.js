import React from 'react'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import { friendRequest } from './../../../api/post'
import './styles.scss'

class HoverCard extends React.Component {
  constructor(props){
    super(props)

    this.state = {
      flag: false,
      status:'',
      requestSent: false,
      friendStatus: this.props.friendStatus
    }

    this.sendFriendRequest = this.sendFriendRequest.bind(this);
  }
  sendFriendRequest(e){
      e.stopPropagation();
      let { user, loggedInUser } = this.props;
      let obj = {};
      obj.user_one_id = loggedInUser.user_id;
      obj.user_name = loggedInUser.fname+' '+loggedInUser.lname;
      obj.user_two_id = user.id;
      obj.relation_status = 'Pending';
      obj.action_user = loggedInUser.user_id;
      this.setState({
          flag: true,
      })
      friendRequest(obj)
      .then(()=>{
          this.setState({
              flag: false,
              status: 'Pending',
              requestSent: true,
          }, ()=>{this.props.changeStatus('Pending')})
      })
  }

  componentWillReceiveProps(nextProps) {
    if (this.props.friendStatus !== nextProps.friendStatus)
      this.setState({friendStatus: nextProps.friendStatus})
  } 

  render(){
    let { user, logInUser, accountSettings } = this.props;
    const { friendStatus } = this.state
    let canRequest = accountSettings === null || (!!accountSettings && !!accountSettings.receive_requests);
    let profileImgStyles = {
      backgroundImage: (user && user.profile_img_loc) ? 'url("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'+user.profile_img_loc+'")' : ( user && user.fname) ?"url(/social/images/Avatar_Letters/"+user.fname.charAt(0).toUpperCase()+".svg)" : 'url(/social/images/Avatar_Letters/P.svg)',
    }
    return (
      <div>
          <div className="hover_card_container">
            <Link to={'/social/profile/'+ (!!user && user.id)}>
              <div className="user_image_container">
                <div className="user_image" style={profileImgStyles}/>
                <div className="flag_image_container">
                  <div className={"flag flag-"+ (!!user && !!user.country && user.country.country_code.toLowerCase())}></div>
                </div>
              </div>
              <div className="user_name">{user && user.fname} {user && user.lname}</div>
              <div className="institute_name"> {user && ( (user.in_college == 1 && (user.college && user.college.school_name)) || (user.in_college == 0 && (user.highschool && user.highschool.school_name)) )}</div>
              <div className="user_role"> {user && user. is_student ? 'Student' : '' } </div>
            </Link>
            {(friendStatus == '' || friendStatus === 'Declined') && canRequest && !this.state.requestSent ?
                <div className="connect" onClick={this.sendFriendRequest}> CONNECT </div>
              : friendStatus === 'Pending' ?
                <div className="connect pending"> REQUEST SENT </div>
              : this.state.requestSent ?
                <div className="connect pending"> REQUEST SENT </div>
              : friendStatus === 'Accepted' ?
                <div className="connect connected"> CONNECTED </div>
              : <div />
            }
          </div>
      </div>
    )
  }
}
function mapStateToProps(state){
  return{
    loggedInUser: state.user && state.user.data,
  }
}
export default connect(mapStateToProps, null)(HoverCard);
