import React from 'react'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import { friendRequest } from './../../../api/post'
import './hover_card_for_SIC_styles.scss'

class HoverCardForSIC extends React.Component {
  constructor(props){
    super(props);

    this.friendRequestSend = this.friendRequestSend.bind(this);
  }
  friendRequestSend(){
    let { userId, user, handleVisibleState } = this.props;
    let obj = {};
    obj.user_one_id = userId;
    obj.user_name = user.fname+' '+user.lname;
    obj.user_two_id = user.user_id;
    obj.relation_status = 'Pending';
    obj.action_user = userId;
    friendRequest(obj)
    .then(()=>{
        handleVisibleState();
    })
  }

  render(){
    let { user } = this.props;
    let profileImgStyles = {
      backgroundImage: (!!user && user && user.user_img) ? 'url("'+user.user_img+'")' : (user && user.fname) ? "url(/social/images/Avatar_Letters/"+user.fname.charAt(0).toUpperCase()+".svg)" : 'url(/social/images/Avatar_Letters/P.svg)'
    }
    return (
        <div className="hover_card_for_SIC_container">
          <Link to={'/social/profile/'+ (user && user.user_id)}>
            <div className="user_image_container">
              <div className="user_image" style={profileImgStyles}/>
              <div className="flag_image_container">
                <div className={"flag_image flag flag-"+user.country_code}>
                </div>
              </div>
            </div>
            <div className="user_name">{user && user.fname} {user && user.lname}</div>
            <div className="institute_name"> {user && user.school_name} </div>
            <div className="user_role">
              {user.is_student && 'Student' || user.is_alumni && 'Alumni' || user.is_parent && 'Parent' || user.is_counselor && 'Counselor' || user.is_university_rep && 'University Rep.' || user.is_organization && 'Organization'}
            </div>
          </Link>

          <div className="connect" onClick={this.friendRequestSend}> CONNECT </div>
        </div>
    )
  }
}
function mapStateToProps(state){
  return{
    userId: state.user && state.user.data && state.user.data.user_id,
  }
}
export default connect(mapStateToProps, null)(HoverCardForSIC);
