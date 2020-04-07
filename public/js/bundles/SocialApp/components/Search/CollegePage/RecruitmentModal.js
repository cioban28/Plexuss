import React, { Component } from 'react';
import { connect } from 'react-redux';
import './styles/recruitment-modal.scss';
import { saveRecruitMeInfo } from '../../../api/search';
import { closeModal } from '../../../actions/modal';
import { setDontTakeToPortal } from '../../../actions/search';
import AnimateHeight from 'react-animate-height';


class RecruitmentModal extends Component {
  constructor(props) {
    super(props);

    this.state = {
      reputation: '',
      location: '',
      tuition: '',
      program_offered: '',
      athletic: '',
      onlineCourse: '',
      campus_life: '',
      other: '',
      servicesContHeight: window.innerWidth > 767 ? 'auto' : 0,
      scoresHeight: window.innerWidth > 767 ? 'auto' : 0,
    }

    this.handleFormSubmit = this.handleFormSubmit.bind(this);
    this.handleClose = this.handleClose.bind(this);
    this.handleOtherReasonChange = this.handleOtherReasonChange.bind(this);
    this.handleDontTakeToPortalClick = this.handleDontTakeToPortalClick.bind(this);
    this.handleServicesClick = this.handleServicesClick.bind(this);
    this.handleScoresClick = this.handleScoresClick.bind(this);
  }

  handleFormSubmit(e) {
    e.preventDefault();
    this.props.saveRecruitMeInfo(this.props.college.CollegeId, {...this.state});
  }

  handleClose() {
    this.props.closeModal();
  }

  handleOtherReasonChange(e) {
    this.setState({ other: e.target.value });
  }

  handleDontTakeToPortalClick(e) {
    this.props.setDontTakeToPortal();
    this.handleFormSubmit(e);
  }

  handleServicesClick() {
    if (window.innerWidth > 767) return;

    this.setState((prevState) => ({ servicesContHeight: prevState.servicesContHeight === 0 ? 'auto' : 0 }));
  }

  handleScoresClick() {
    if (window.innerWidth > 767) return;

    this.setState((prevState) => ({ scoresHeight: prevState.scoresHeight === 0 ? 'auto' : 0 }));
  }

  render() {
    const { getRecruited } = this.props;

    return (
      <form id='recruitmeModal' onSubmit={this.handleSubmit}>
      {
        !!Object.entries(getRecruited).length &&
          <div className="model-inner-div regularRecruitme">
            <div className="close-modal-cont">
              <span className="close-reveal-modal closer_sec" onClick={this.handleClose}>&#215;</span>
            </div>

            <div>
              <div className='header-container'>
                <div className="recruitTitle column small-12 medium-12 large-12 text-center">
                  This school has been added to your list!
                </div>
                <div className="recruitSubTitle column small-12 text-center">
                  {getRecruited.school_name || ''} wants to know why you’re interested
                </div>
              </div>

              <div className="row">
                <div className="column small-12  large-6 leftRecruitForm">
                    <div className="row">
                        <div className="applyTitle small-12 column" onClick={this.handleServicesClick}>SELECT ALL THAT APPLY <i className={`show-hide-services fa fa-chevron-${ this.state.servicesContHeight === 0 ? 'down' : 'up'}`}></i></div>
                    </div>
                    <AnimateHeight duration={500} height={this.state.servicesContHeight}>
                      <div className="row">
                          <ul className="services-ul small-12 column">
                              <li className='checkbox-cont'>
                                <input id='rmm_reputation' name='reputation' type='checkbox' value='1' onClick={() => { this.setState({ reputation: 1 }) }} />
                                <label htmlFor='rmm_reputation'>Academic Reputation</label>
                              </li>
                              <li className='checkbox-cont'>
                                <input id='rmm_location' name='location' type='checkbox' value='1' onClick={() => { this.setState({ location: 1 }) }} />
                                <label htmlFor='rmm_location'>Location</label>
                              </li>
                              <li className='checkbox-cont'>
                                <input id='rmm_tuition' name='tuition' type='checkbox' value='1' onClick={() => { this.setState({ tuition: 1 }) }} />
                                <label htmlFor='rmm_tuition'>Cost of Tuition</label>
                              </li>
                              <li className='checkbox-cont'>
                                <input id='rmm_program_offered' name='program_offered' type='checkbox' value='1' onClick={() => { this.setState({ program_offered: 1 }) }} />
                                <label htmlFor='rmm_program_offered'>Majors or Programs Offered</label>
                              </li>
                              <li className='checkbox-cont'>
                                <input id='rmm_athletic' name='athletic' type='checkbox' value='1' onClick={() => { this.setState({ athletic: 1 }) }} />
                                <label htmlFor='rmm_athletic'>Athletics</label>
                              </li>
                              <li className='checkbox-cont'>
                                <input id='rmm_onlineCourse' name='onlineCourse' type='checkbox' value='1' onClick={() => { this.setState({ onlineCourse: 1 }) }} />
                                <label htmlFor='rmm_onlineCourse'>Online Courses</label>
                              </li>
                              <li className='checkbox-cont'>
                                <input id='rmm_campus_life' name='campus_life' type='checkbox' value='1' onClick={() => { this.setState({ campus_life: 1 }) }} />
                                <label htmlFor='rmm_campus_life'>Campus Life</label>
                              </li>
                               <li>
                                  Other
                               </li>
                              <li>
                                <input className='otherReason' name='other' type='text' onChange={ e => { this.setState({ other: e.target.value }) }} />
                              </li>
                          </ul>
                      </div>
                    </AnimateHeight>
                </div>

                <div className="small-12 medium-12 large-6  column rightRecruitCompare">
                    <div className="row">
                        <div className="compareTitle column small-12" onClick={this.handleScoresClick}>COMPARE YOUR SCORES TO THEIRS</div>
                    </div>
                    <AnimateHeight duration={500} height={this.state.scoresHeight}>
                      <div>
                        <div className="row">
                            <div className="small-12 column compareMessage">
                                Colleges will review your request and are not required to contact you.  It is completely up to their discretion and enrollment requirements.
                            </div>
                        </div>
                        <div className="row">
                            <div className="column small-6 large-6">
                                <div className="row">
                                    <div className="avgScoreTitle column small-12 text-center">AVERAGE SCORES</div>
                                </div>
                                <div className="row">
                                    <div className="column small-6">
                                        <div className="circle avgGPA">GPA</div>
                                    </div>
                                    <div className="column small-6">
                                        <div className="circle gray">{ getRecruited.collegeScores['gpa'] }</div>
                                    </div>
                                </div>
                                <div className="row pb5">
                                    <div className="column small-6">
                                        <div className="circle avgSAT">SAT</div>
                                    </div>
                                    <div className="column small-6">
                                        <div className="circle gray">{ getRecruited.collegeScores['sat'] }</div>
                                    </div>
                                </div>
                                <div className="row pb5">
                                    <div className="column small-6">
                                        <div className="circle avgACT">ACT</div>
                                    </div>
                                    <div className="column small-6">
                                        <div className="circle gray">{ getRecruited.collegeScores['act'] }</div>
                                    </div>
                                </div>
                            </div>
                            <div className="column small-6 large-6">
                                <div className="row">
                                    <div className="scoreTitle column small-12 text-center">YOUR SCORES</div>
                                </div>
                                <div className="row">
                                    <div className="column small-6">
                                        <div className="circle yourGPA">GPA</div>
                                    </div>
                                    <div className="column small-6">
                                        <div className="circle gray">{ getRecruited.usrScores['gpa'] }</div>
                                    </div>
                                </div>
                                <div className="row pb5">
                                    <div className="column small-6">
                                        <div className="circle yourSAT">SAT</div>
                                    </div>
                                    <div className="column small-6">
                                        <div className="circle gray">{ getRecruited.usrScores['sat'] }</div>
                                    </div>
                                </div>
                                <div className="row pb5 ">
                                    <div className="column small-6">
                                        <div className="circle yourACT">ACT</div>
                                    </div>
                                    <div className="column small-6">
                                        <div className="circle gray">{ getRecruited.usrScores['act'] }</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                      </div>
                    </AnimateHeight>
                </div>
              </div>
            </div>

            <div className="row">
                <div className="column large-5 medium-6 small-12 small-centered text-center">
                    <input type='submit' onClick={this.handleDontTakeToPortalClick} className='btn green-btn' style={{cursor: 'pointer', marginTop: '20px', marginBottom: '4px'}} value='Ok' />
                </div>
            </div>

            <div className="row">
                <div className="column large-5 medium-6 small-12 small-centered text-center">
                    <input type='submit' onClick={this.handleFormSubmit} className='btn white-btn' style={{cursor: 'pointer', marginTop: '20px', marginBottom: '4px'}} value='View my list' />
                </div>
            </div>
            <div className="row network-msg-cont">
            {
              getRecruited.in_our_network == 0
              ? <div className="column large-7 medium-10 small-12 notInNetwork">
                    This college is not part of our network, but we will be reaching out to them. We will let them know you are interested in their program.
                </div>
              : !!getRecruited.aorSchool && getRecruited.aorSchool[0] == 1
                ? <div className="column large-7 medium-10 small-12 inNetwork">
                      This college is part of our network for its online programs. It is represented by a partner who is affiliated with the university.
                  </div>
                : <div className="column large-7 medium-10 small-12 inNetwork">
                    This college is part of our network. After you have finished your profile we will automatically let their admission office know you are interested so they can contact you.
                  </div>
            }
            </div>
          </div>
      }
      </form>
    )
  }
}

const mapStateToProps = (state) => {
  return {
    getRecruited: state.search.getRecruited,
    college: state.search.college,
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    saveRecruitMeInfo: (collegeId, values) => { dispatch(saveRecruitMeInfo(collegeId, values)) },
    setDontTakeToPortal: () => { dispatch(setDontTakeToPortal()) },
    closeModal: () => { dispatch(closeModal()) },
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(RecruitmentModal);
