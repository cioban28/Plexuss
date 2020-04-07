// /Me/index.js

import React from 'react'
import { connect } from 'react-redux'
import ProfileOption from './profileOption';
import { mePage } from './../../actions/headerTab'

import './styles.scss'

class Me extends React.Component {
  constructor(props) {
    super(props);
  }
  componentWillMount(){
    this.props.mePage();
  }
  render(){
    let { user } = this.props;
    return (
      <div>
        <div className="mainContainer">
          <div className="contentContainer">
            <div className="userDetails">
              <img src={user && user.profile_img_loc ? "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/"+user.profile_img_loc : ''} />
              <div className="userName">{user && user.fname} {user && user.lname}</div>
            </div>
            <div className="profileOptions">
              <ProfileOption icon={user && user.profile_img_loc ? "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/"+user.profile_img_loc : ''}
                           iconStyle={{border: '3px 3px 5px rgba(0,0,0,.2)'}}
                           title="Sam Jones"
                           description="Edit Public Profile"
                           link="/social/edit-profile" />

              <ProfileOption icon={"https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/user-avatar-r1.png"}
                       iconStyle={{border: '3px 3px 5px rgba(0,0,0,.2)'}}
                       title="College Application"
                       description= "60% complete"
                       link='/home' />

              <ProfileOption icon="/images/upload-icon.png"
                       iconStyle={{border: '3px 3px 5px rgba(0,0,0,.2)'}}
                       title="Your Documents"
                       description= "4 Documents"
                       link="/home" />
            </div>
          </div>
        </div>
      </div>
    );
  }
}
const mapStateToProps = (state) =>{
  return{
      user: state.user.data,
  }
}
const mapDispatchtoProps = (dispatch) => {
  return {
    mePage: () => {dispatch(mePage())},
  }
}
export default connect(mapStateToProps, mapDispatchtoProps)(Me);
