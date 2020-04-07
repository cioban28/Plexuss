import React, { Component } from 'react';
import { connect } from 'react-redux';
import './styles/stats.scss';
import './styles/admissions.scss';
import { getCollegeAlumni, addToConnection, cancelConnectionRequest, declineConnectionRequest } from '../../../api/search';
import { addInConversationArray, addNewThread } from './../../../actions/messages'
import StudentCard from './StudentCard';
import SignInModal from './../../Modal/SignInModal';
import Modal from 'react-modal';
import { openModalAlumni, closeModalAlumni } from '../../../actions/modal';

class AlumniPage extends Component {
  constructor(props) {
    super(props);

    this.handleConnectClick = this.handleConnectClick.bind(this);
    this.handleCancelRequestClick = this.handleCancelRequestClick.bind(this);
    this.handleDeclineRequestClick = this.handleDeclineRequestClick.bind(this);
  }
  componentDidMount() {
    !this.props.alumni.length && this.props.getCollegeAlumni(this.props.college.CollegeId, this.props.userId);
  }

  handleConnectClick(requestedUserId) {
    const { userId, addToConnection, setFriendRequestUserId, user } = this.props;

    const values = {
      user_one_id: userId,
      user_two_id: requestedUserId,
      relation_status: 'Pending',
      action_user: userId,
      user_name: user.fname+' '+user.lname,
    };

    addToConnection(values, 'alumni');
  }

  handleCancelRequestClick(requestedUserId) {
    const { userId, cancelConnectionRequest, user } = this.props;

    const values = {
      user_one_id: userId,
      user_two_id: requestedUserId,
      relation_status: 'Pending',
      action_user: userId,
      user_name: user.fname+' '+user.lname,
    };

    cancelConnectionRequest(values, 'alumni');
  }

  handleDeclineRequestClick(requestedUserId) {
    const { userId, declineConnectionRequest, user } = this.props;

    const values = {
      user_one_id: userId,
      user_two_id: requestedUserId,
      relation_status: 'Declined',
      action_user: userId,
      user_name: user.fname+' '+user.lname,
    };

    declineConnectionRequest(values, 'alumni');
  }

  render() {
    const { isOpen, alumni, isFetching, user, openModalAlumni, routeProps, userId, addInConversationArray, messageThreads } = this.props;

    const titlize = str => str.charAt(0).toUpperCase() + str.slice(1);

    return (
      <div id='alumni-page'>
      {!!isFetching && <div className="college-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>}
      {
        !isFetching && !!alumni.length &&
          <div className="large-12 columns no-padding">
              <div id="container-box">
              {
                !!alumni && !!alumni.length &&
                  alumni.map((student, index) => (
                    <StudentCard
                      student={student}
                      index={index}
                      key={index}
                      signed={user.signed_in}
                      handleConnectClick={this.handleConnectClick}
                      handleCancelRequestClick={this.handleCancelRequestClick}
                      handleDeclineRequestClick={this.handleDeclineRequestClick}
                      openModalAlumni={openModalAlumni}
                      userId={userId}
                      addInConversationArray={addInConversationArray}
                      messageThreads={messageThreads}
                      addNewThread={addNewThread}
                    />
                  ))
              }
              {
                isOpen &&
                  <Modal isOpen={isOpen} onRequestClose={this.props.handleClose} className='sign-modal'>
                    <SignInModal url={routeProps.location.pathname}/>
                  </Modal>
              }
              </div>
          </div>
      }
      </div>
    )
  }
}

const mapStateToProps = (state) => {
  return {
    college: state.search.college,
    alumni: state.search.alumni,
    isFetching: state.search.isFetchingCollegeSubPage,
    userId: state.user.data.user_id,
    user: state.user.data,
    isOpen: state.modal.isOpenAlumni,
    messageThreads: state.messages.messageThreads && state.messages.messageThreads.topicUsr,
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    openModalAlumni: () => { dispatch(openModalAlumni()) },
    closeModalAlumni: () => { dispatch(closeModalAlumni()) },
    getCollegeAlumni: (collegeId, userId) => { dispatch(getCollegeAlumni(collegeId, userId)) },
    addToConnection: (values, pageName) => { dispatch(addToConnection(values, pageName)) },
    cancelConnectionRequest: (values, pageName) => { dispatch(cancelConnectionRequest(values, pageName)) },
    declineConnectionRequest: (values, pageName) => { dispatch(declineConnectionRequest(values, pageName)) },
    addInConversationArray: (thread) => { dispatch(addInConversationArray(thread))},
    addNewThread: (id) => { dispatch(addNewThread(id))},
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(AlumniPage);
