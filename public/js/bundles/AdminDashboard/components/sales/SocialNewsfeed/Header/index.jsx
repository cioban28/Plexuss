import React, { Component } from 'react';
import './styles.scss';
import { connect } from 'react-redux';


class Header extends Component {

  render() {
    const { user } = this.props;

    return (
      <div className='row header'>
      {
        // <div className='columns large-1'>
        //   <div className='logo-cont'>
        //     <img className='logo-icon' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/sales/Plexuss+P.svg' />
        //     <img className='menu-icon' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/sales/menu.svg' />
        //   </div>
        // </div>
      }
        <div className='columns large-6'>
        {
          <div className='row-nav-cont'>
            <ul className='nav-list'>
              <li>
                <a href='/sales/all-newsfeed'>
                  All
                </a>
              </li>
              <li>
                <a href='/sales/published-newsfeed'>
                  Published
                </a>
              </li>
              <li>
                <a href='/sales/scheduled-newsfeed'>
                  Scheduled
                </a>
              </li>
              <li>
                <a href='/sales/drafts-newsfeed'>
                  Drafts
                </a>
              </li>
              <li>
                <a href='/sales/expired-newsfeed'>
                  Expired
                </a>
              </li>
            </ul>
          </div>
        }
        </div>
        <div className='columns large-6'>
          <div className='header-right-controls'>
            <div className='messages-icon'>
              <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/sales/messages.svg' />
            </div>
            <div className='bell-sic-icon'>
              <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/sales/bell-SIC.svg' />
            </div>
            <div className='avatar-cont'>
              <img src={user.profile_img_loc} />
              <span className='username'>{ user.fname } { user.lname }</span>
              <i className='fa fa-chevron-down'></i>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

const mapStateToProps = state => {
  return {
    user: state.userData.userProfileData,
  }
}

export default connect(mapStateToProps, null)(Header);
