import React, { Component } from 'react';
import { connect } from 'react-redux';
import './styles/stats.scss';
import './styles/admissions.scss';
import { getCollegeCurrentStudents, addToConnection, cancelConnectionRequest, declineConnectionRequest } from '../../../api/search';
import { StudentCard } from './StudentCard';
import SignInModal from './../../Modal/SignInModal';
import Modal from 'react-modal';
import { openModalAlumni, closeModalAlumni } from '../../../actions/modal';


class CurrentStudentsPage extends Component {
  constructor(props) {
    super(props);

    this.handleConnectClick = this.handleConnectClick.bind(this);
    this.handleCancelRequestClick = this.handleCancelRequestClick.bind(this);
    this.handleDeclineRequestClick = this.handleDeclineRequestClick.bind(this);
  }

  componentDidMount() {
    !this.props.currentStudents.length && this.props.getCollegeCurrentStudents(this.props.college.CollegeId, this.props.userId);
  }

  handleConnectClick(requestedUserId) {
    const { userId, addToConnection, user } = this.props;

    const values = {
      user_one_id: userId,
      user_two_id: requestedUserId,
      relation_status: 'Pending',
      action_user: userId,
      user_name: user.fname+' '+user.lname,
    };

    addToConnection(values, 'currentStudents');
  }

  handleCancelRequestClick(requestedUserId) {
    const { userId, cancelConnectionRequest } = this.props;

    const values = {
      user_one_id: userId,
      user_two_id: requestedUserId,
      relation_status: 'Pending',
      action_user: userId,
    };

    cancelConnectionRequest(values, 'currentStudents');
  }

  handleDeclineRequestClick(requestedUserId) {
    const { userId, declineConnectionRequest } = this.props;

    const values = {
      user_one_id: userId,
      user_two_id: requestedUserId,
      relation_status: 'Declined',
      action_user: userId,
    };

    declineConnectionRequest(values, 'currentStudents');
  }

  render() {
    const { isOpen, currentStudents, isFetching, openModalAlumni, routeProps, user } = this.props;

    const titlize = str => str.charAt(0).toUpperCase() + str.slice(1);

    return (
      <div id='current-students-page'>
      {!!isFetching && <div className="college-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>}
      {
        !isFetching && !!currentStudents.length &&
          <div className="large-12 columns no-padding">
            <div id="container-box">
            {
              !!currentStudents && !!currentStudents.length &&
                currentStudents.map((student, index) => (
                  <StudentCard
                    student={student}
                    index={index}
                    key={index}
                    signed={user.signed_in}
                    handleConnectClick={this.handleConnectClick}
                    handleCancelRequestClick={this.handleCancelRequestClick}
                    handleDeclineRequestClick={this.handleDeclineRequestClick}
                    openModalAlumni={openModalAlumni}
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
    currentStudents: state.search.currentStudents,
    isFetching: state.search.isFetchingCollegeSubPage,
    userId: state.user.data.user_id,
    user: state.user.data,
    isOpen: state.modal.isOpenAlumni,
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    openModalAlumni: () => { dispatch(openModalAlumni()) },
    closeModalAlumni: () => { dispatch(closeModalAlumni()) },
    getCollegeCurrentStudents: (collegeId, userId) => { dispatch(getCollegeCurrentStudents(collegeId, userId)) },
    addToConnection: (values, pageName) => { dispatch(addToConnection(values, pageName)) },
    cancelConnectionRequest: (values, pageName) => { dispatch(cancelConnectionRequest(values, pageName)) },
    declineConnectionRequest: (values, pageName) => { dispatch(declineConnectionRequest(values, pageName)) },
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(CurrentStudentsPage);
