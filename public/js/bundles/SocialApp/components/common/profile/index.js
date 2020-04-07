import React, { Component } from 'react'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import './styles.scss'

import CircularProgressbar from 'react-circular-progressbar'
import './../../Header/GetStarted/progressbar.scss'

import { getProfileCompleteness, getProfileData } from './../../../api/profile'

class Profile extends Component {
  constructor(props) {
    super(props)
    this.state = {}

    this.handleClose = this.handleClose.bind(this)
    this.handleStepName = this.handleStepName.bind(this)
  }
  componentDidMount(){
    getProfileCompleteness()
  }
  handleClose() {
    this.setState({close: false})
  }
  handleStepName(step) {
    switch (step){
      case 'profile-picture': return 'Profile Picture'
      case 'basic-info': return 'Basic Info';
      case 'education': return 'Education';
      case 'claim-to-fame': return 'Claim to Fame';
      case 'objective': return 'Objective';
      case 'occupation': return 'Current Occupation';
      case 'skills-endorsements': return 'Skills and Endorsements';
      case 'projects-publications': return 'Projects and Publications';
      case 'liked-colleges': return; //'Liked Colleges';
    }
  }
  render(){
    let { user, profile, networkingDate } = this.props;
    let userImg = user && user.profile_img_loc ?
      'url("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'+user.profile_img_loc+'")'
      :
      user && user.fname ? 'url(/social/images/Avatar_Letters/'+user.fname.charAt(0).toUpperCase()+'.svg)'
      :
      'url(/social/images/Avatar_Letters/P.svg)'

    let profileImg = {
      backgroundImage: userImg,
    }
    let school = !!user.user_school_names ? (!!user.user_school_names.profile && user.user_school_names.profile) : false;

    return (
      <div>
        <div className="home_widget">
          <div className="profile_box">
            <Link to={"/social/profile/"+user.user_id} className="profile_image" >
              <div className="prof-img" style={profileImg}/>
              <div className="flag_image_container">
                <div className={"flag flag-"+ (!!user && !!user.country_based_on_ip && user.country_based_on_ip.toLowerCase())}></div>
              </div>
            </Link>


            <Link to={"/social/profile/"+user.user_id} className="user_name">{user.fname} {user.lname}</Link>
            <div className="institute_name">{!!school ? school : user.in_college !== null && ((user.in_college === 1 && user.user_school_names && user.user_school_names.college) || (user.in_college === 0 && user.user_school_names && user.user_school_names.highschool)) }</div>
            <div className="user_role">{user.is_student === 1 ? 'Student' : ''}</div>

            <Link to="social/edit-profile" className="edit_profile">Edit Profile</Link>
          </div>
        </div>
        <div className="home_widget">
          <ProfileProgress profile={this.props.profile} id={this.props.user.user_id} />
          {profile.user.step !== 'complete' &&
            <div>
              <div className="next-step-text">Next Step:</div>
              <NextStep step={profile.user.step} stepName={this.handleStepName}/> 
            </div>
          }
        </div>
        <div className="home_widget">
          <Link to={"/social/networking/connections"} className="connect_count">{networkingDate && networkingDate.friends && networkingDate.friends.length+' Connections'}</Link> 
        </div>
        {/* Hidden till proper logic created
        <div className="home_widget">
          <ul className="views_list">
            
            <li>
              <span className="view_heading">Viewed your profile</span>
              <div className="views_count">50</div>
            </li>
            
            <li>
              <span className="view_heading">Views of your posts</span>
              <div className="views_count">50</div>
            </li> 
          </ul>
        </div>
        */}
      </div>
    )
  }
}

class ProfileProgress extends Component{
    render(){
        let { profile, id } = this.props;
        let { user } = profile;
        return(
          <div className={"prof-progress row "+(user.step === 'complete' && 'complete')} >
            <div className="small-3 columns">
              <div className="progress_bar">
                <CircularProgressbar percentage={!!user.profile_percent ? (user.profile_percent > 100) ? 100 : user.profile_percent : 0} text={`${!!user.profile_percent ? (user.profile_percent > 100) ? 100 : user.profile_percent : 0}%`} />
              </div>
            </div>
            <div className="small-9 columns text">
                <Link to={"/social/profile/"+id} className="title">Public Profile</Link>
                <div className="sub_title ">{(!!user.profile_percent ? (user.profile_percent > 100) ? 100 : user.profile_percent : 0)  + '% Complete'}</div>
            </div>
          </div>
        )
    }
}

class NextStep extends Component{
    render(){
        let { step, stepName } = this.props;
        return(
            <div className="next-step row">
                <div className="small-3 columns checkMark_parent">
                    <i className="fa fa-check check-mark"></i>
                </div>
                <Link className="small-9 columns text" to={'/social/edit-profile?step='+step}>{stepName(step)}</Link>
            </div>
        )
    }
}

const mapStateToProps = (state) => {
  return{
      user: state.user.data,
      profile: state.profile,
      networkingDate: state.user.networkingDate,
  }
}

export default connect(mapStateToProps, null)(Profile);
