import React, { Component } from 'react';
import { connect } from 'react-redux';
import './styles.scss';
import { getUserData } from '../../../actions/user';
import Modal from 'react-modal';
import { DotsLoader } from '../../common/DotsLoader/index.jsx';


class SocialNewsfeed extends Component {
  componentDidMount() {
    this.props.getUserData();
    Modal.setAppElement('body');
  }

  render() {
    const { children, userData, isLoading } = this.props;
    const isSidebarActive = document.body.classList.contains('sidebar-active');

    return (
      <div>
      {
        isLoading && <DotsLoader />
      }
      {
        !!Object.entries(userData).length &&
          <div id='social-newsfeed' className={isSidebarActive && 'content-padding-left'}>
            <div className='black-bg-under-header'>
            </div>
            <div id='newsfeed-content'>
            { children }
            </div>
          </div>
      }
      </div>
    )
  }
}

const mapStateToProps = state => {
  return {
    isLoading: state.newsfeed.loader.isLoading,
    userData: state.userData.userProfileData,
  }
}

const mapDispatchToProps = dispatch => {
  return {
    getUserData: () => { dispatch(getUserData()) },
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(SocialNewsfeed);
