// /Edit_Profile/index.js

import React from 'react'
import './styles.scss'
import Profile from './../../../../StudentApp/components/profile/profile_edit'
class Edit extends React.Component {
  render(){
    return (
      <div className="social_edit_profile">
        <Profile />
      </div>
    );
  }
}
export default Edit;
