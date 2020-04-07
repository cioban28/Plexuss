import React, { Component } from 'react';
import './styles.scss';

class UserCard extends Component{
  render() {
    return (
      <div className="user-card">
        <div className="image-container">
          <img className="user-image" src="/social/images/AlveProfilePic.png" />
          <img className="flag" src="/social/images/1280px-Flag_of_the_United_States.svg.png" />
        </div>
        <div className="name">Sam Jones</div>
        <div className="school">Kennedy High School '18</div>
        <div className="status">Student</div>
        <div className="profile-views">
          <div className="views-box">
            <span className="text">Viewed your profile</span>
            <span className="figure">50</span>
          </div>
          <div className="views-box">
            <span className="text">Views of your posts</span>
            <span className="figure">90</span>
          </div>
        </div>
      </div>
    );
  }
}
export default UserCard;
