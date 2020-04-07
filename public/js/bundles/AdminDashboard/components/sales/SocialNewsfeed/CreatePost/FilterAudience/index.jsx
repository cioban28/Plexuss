import React, { Component } from 'react';
import { connect } from 'react-redux';
import './styles.scss';
import Location from './Location.jsx';
import StartDate from './StartDate.jsx';
import Financials from './Financials.jsx';
import TypeOfSchool from './TypeOfSchool.jsx';
import Scores from './Scores.jsx';
import Uploads from './Uploads.jsx';
import EducationLevel from './EducationLevel.jsx';
import MilitaryAffiliation from './MilitaryAffiliation.jsx';
import ProfileCompletion from './ProfileCompletion.jsx';
import Demographics from './Demographics.jsx';
import Majors from './Majors.jsx';
import Modal from 'react-modal';
import _ from 'lodash';
import { setRecommendationFilter } from '../../../../../actions/newsfeedActions';


const locationTab = 'location';
const startDateTab = 'startDateTerm';
const demographicsTab = 'demographic';
const educationLevelTab = 'educationLevel';
const financialsTab = 'financial';
const majorsTab = 'majorDeptDegree';
const militaryAffiliationTab = 'militaryAffiliation';
const profileCompletionTab = 'profileCompletion';
const scoresTab = 'scores';
const typeOfSchoolTab = 'typeofschool';
const uploadsTab = 'uploads';

class FilterAudience extends Component {
  constructor(props) {
    super(props);

    this.state = {
      activeTab: 'location',
      prevTab: '',
      filterChanges: {},
      showFilterChangesModal: false,
    }

    this.handleCloseModal = this.handleCloseModal.bind(this);
    this.setFilterCurrentChanges = this.setFilterCurrentChanges.bind(this); 
    this.handleFilterSave = this.handleFilterSave.bind(this);
    this.closeFilterChangesModal = this.closeFilterChangesModal.bind(this);
    this.handleCloseFilterModal = this.handleCloseFilterModal.bind(this);
  }

  componentDidUpdate(prevProps, prevState) {
    const { showFilterChangesModal, filterChanges, prevTab } = this.state;

    if(!showFilterChangesModal && filterChanges.tabName === prevTab && !_.isEqual(filterChanges, prevState.filterChanges)) {
      this.setState({ showFilterChangesModal: true });
    }
  }

  handleTabClick(activeTab) {
    this.setState(prevState => ({ activeTab: activeTab, prevTab: prevState.activeTab }));
  }

  handleCloseModal() {
    this.props.closeModal();
  }

  setFilterCurrentChanges(filterChanges) {
    this.setState({ filterChanges });
  }

  handleFilterSave() {
    const { filterChanges } = this.state;
    this.props.setRecommendationFilter(filterChanges.tabName, filterChanges.formData);
    this.props.setRecommendationFilterChanged({ 
      type: `SET_RECOMMENDATION_FILTER_${filterChanges.tabName.replace(/([a-z])([A-Z])/g, '$1 $2').split(' ').join('_').toUpperCase()}`,
      payload: filterChanges.state, 
    })
    this.closeFilterChangesModal();
  }

  handleCloseFilterModal() {
    this.setState({ activeTab: this.state.prevTab, showFilterChangesModal: false });
  }

  closeFilterChangesModal() {
    this.setState({ showFilterChangesModal: false });    
  }

  render() {
    const { activeTab, showFilterChangesModal, filterChanges } = this.state;

    return (
      <div id='filter-audience-cont'>
        <Modal isOpen={showFilterChangesModal} className='save-filter-changes-modal'>
          <div className='filter-save-changes-cont'>
            <div className='row'>
              <div className='columns small-10 small-offset-1'>
                <h3>Save before leaving?</h3>
              </div>
              <div className='columns small-1'>
                <a className='close-modal' onClick={this.handleCloseFilterModal}>×</a>
              </div>
              <div>
              </div>
            </div>
            <div className='row'>
              <div className='columns small-4 text-center'><button className='btn-save' onClick={this.handleFilterSave}>Save</button></div>
              <div className='columns small-4 text-center'><button className='btn-discard' onClick={this.closeFilterChangesModal}>Discard</button></div>
              <div className='columns small-4 text-center'><button className='btn-close' onClick={this.handleCloseFilterModal}>Close</button></div>
            </div>
          </div>
        </Modal>
        <header className='filter-audience-header'>
          <h2>Filter Audience</h2>
          <img className='close-modal' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/sales/X.svg' onClick={this.handleCloseModal} />
        </header>
        <main className='filter-main-cont'>
          <div className='row'>
            <div className='columns large-3 medium-3'>
            </div>
            <div className='columns large-9 medium-9 header-content'>
              <h3>Filter the results you receive in your student recommendations</h3>
              <a className='targeting-tutorial' onClick={this.handleTabClick.bind(this, 'targetingVideo')}>Learn how targeting works by watching this video</a>
            </div>
          </div>
          <div className='row'>
            <section className='left-nav-section columns large-3 medium-3'>
              <div className='nav-cont'>
                <ul className='nav-list'>
                  <li className={`nav-list-item ${activeTab === locationTab && 'active-tab'}`}>
                    <a onClick={this.handleTabClick.bind(this, locationTab)}>
                      Location
                    </a>
                    <span className='change-icon'>✓</span>
                  </li>
                  <li className={`nav-list-item ${activeTab === startDateTab && 'active-tab'}`}>
                    <a onClick={this.handleTabClick.bind(this, startDateTab)}>
                      Start Date
                    </a>
                    <span className='change-icon'>✓</span>
                  </li>
                  <li className={`nav-list-item ${activeTab === financialsTab && 'active-tab'}`}>
                    <a onClick={this.handleTabClick.bind(this, financialsTab)}>
                      Financials
                    </a>
                    <span className='change-icon'>✓</span>
                  </li>
                  <li className={`nav-list-item ${activeTab === typeOfSchoolTab && 'active-tab'}`}>
                    <a onClick={this.handleTabClick.bind(this, typeOfSchoolTab)}>
                      Type of School
                    </a>
                    <span className='change-icon'>✓</span>
                  </li>
                  <li className={`nav-list-item ${activeTab === majorsTab && 'active-tab'}`}>
                    <a onClick={this.handleTabClick.bind(this, majorsTab)}>
                      Major
                    </a>
                    <span className='change-icon'>✓</span>
                  </li>
                  <li className={`nav-list-item ${activeTab === scoresTab && 'active-tab'}`}>
                    <a onClick={this.handleTabClick.bind(this, scoresTab)}>
                      Scores
                    </a>
                    <span className='change-icon'>✓</span>
                  </li>
                  <li className={`nav-list-item ${activeTab === uploadsTab && 'active-tab'}`}>
                    <a onClick={this.handleTabClick.bind(this, uploadsTab)}>
                      Uploads
                    </a>
                    <span className='change-icon'>✓</span>
                  </li>
                  <li className={`nav-list-item ${activeTab === demographicsTab && 'active-tab'}`}>
                    <a onClick={this.handleTabClick.bind(this, demographicsTab)}>
                      Demographics
                    </a>
                    <span className='change-icon'>✓</span>
                  </li>
                  <li className={`nav-list-item ${activeTab === educationLevelTab && 'active-tab'}`}>
                    <a onClick={this.handleTabClick.bind(this, educationLevelTab)}>
                      Education Level
                    </a>
                    <span className='change-icon'>✓</span>
                  </li>
                  <li className={`nav-list-item ${activeTab === militaryAffiliationTab && 'active-tab'}`}>
                    <a onClick={this.handleTabClick.bind(this, militaryAffiliationTab)}>
                      Military Affiliation
                    </a>
                    <span className='change-icon'>✓</span>
                  </li>
                  <li className={`nav-list-item ${activeTab === profileCompletionTab && 'active-tab'}`}>
                    <a onClick={this.handleTabClick.bind(this, profileCompletionTab)}>
                      Profile Completion
                    </a>
                    <span className='change-icon'>✓</span>
                  </li>
                </ul>
              </div>
            </section>
            <section className='right-content-section columns large-9 medium-9'>
            {
              activeTab === locationTab && <Location filterChanges={filterChanges} setFilterCurrentChanges={this.setFilterCurrentChanges} />
            }
            {
              activeTab === startDateTab && <StartDate filterChanges={filterChanges} setFilterCurrentChanges={this.setFilterCurrentChanges} />
            }
            {
              activeTab === financialsTab && <Financials filterChanges={filterChanges} setFilterCurrentChanges={this.setFilterCurrentChanges} />
            }
            {
              activeTab === typeOfSchoolTab && <TypeOfSchool filterChanges={filterChanges} setFilterCurrentChanges={this.setFilterCurrentChanges} />
            }
            {
              activeTab === majorsTab && <Majors filterChanges={filterChanges} setFilterCurrentChanges={this.setFilterCurrentChanges} />
            }
            {
              activeTab === scoresTab && <Scores filterChanges={filterChanges} setFilterCurrentChanges={this.setFilterCurrentChanges} />
            }
            {
              activeTab === uploadsTab && <Uploads filterChanges={filterChanges} setFilterCurrentChanges={this.setFilterCurrentChanges} />
            }
            {
              activeTab === demographicsTab && <Demographics filterChanges={filterChanges} setFilterCurrentChanges={this.setFilterCurrentChanges} />
            }
            {
              activeTab === educationLevelTab && <EducationLevel filterChanges={filterChanges} setFilterCurrentChanges={this.setFilterCurrentChanges} />
            }
            {
              activeTab === militaryAffiliationTab && <MilitaryAffiliation filterChanges={filterChanges} setFilterCurrentChanges={this.setFilterCurrentChanges} />
            }
            {
              activeTab === profileCompletionTab && <ProfileCompletion filterChanges={filterChanges} setFilterCurrentChanges={this.setFilterCurrentChanges} />
            }
            {
              activeTab === 'targetingVideo' && <div className='column small-12 large-9 end'>
                <div className='text-right'>
                  <a onClick={this.handleTabClick.bind(this, 'location')}>×</a>
                </div>
                <iframe src='https://player.vimeo.com/video/184889832?title=0&amp;byline=0&amp;portrait=0' width='100%' height='360' frameBorder='0' allowFullScreen></iframe>
              </div>
            }
            </section>
          </div>
        </main>
      </div>
    )
  }
}

const mapDispatchToProps = dispatch => {
  return {
    closeModal: () => { dispatch({ type: 'CLOSE_MODAL' }) },
    setRecommendationFilter: (tabName, values) => { dispatch(setRecommendationFilter(tabName, values)) },
    setRecommendationFilterChanged: (values) => { dispatch({ type: values.type, payload: values.payload }) },
  }
}

export default connect(null, mapDispatchToProps)(FilterAudience);
