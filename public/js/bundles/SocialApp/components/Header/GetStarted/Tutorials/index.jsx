import React, { Component } from 'react';
import './styles.scss';
import { connect } from 'react-redux';
import { hideTutorials } from '../../../../actions/tutorials';
import {
  createAProfileSubHeadings,
  researchUniversitiesSubHeadings,
  selectYourCollegesSubHeadings,
  connectAndChatSubHeadings,
  promoteYourselfSubHeadings,
  myCounselorSubHeadings
} from './constants';
import CollegeApplicationAssessment from './CollegeApplicationAssessment.jsx';
import YourFavorites from './YourFavorites.jsx';
import CreateAProfile from './CreateAProfile.jsx';
import MyColleges from './MyColleges.jsx'
import ChatAndConnect from './ChatAndConnect.jsx'
import MyCounsler from './MyCounsler.jsx'
import ResearchUniversities from './ResearchUniversities'
import PromoteYourself from './PromoteYourself'
import { disableBodyScroll, enableBodyScroll, clearAllBodyScrollLocks } from 'body-scroll-lock';
import Swipe from 'react-easy-swipe';

const animatedDelay = 500;


class SICTutorials extends Component {
  constructor(props) {
    super(props);

    this.state = {
      animateModal: false,
      scrollLocked: false,
    }

    this.scrollLockEl = null;
    this.handleWindowResize = this.handleWindowResize.bind(this);
  }

  componentDidMount() {
    this.setState({ animateModal: true });
    this.scrollLockEl = document.querySelector('.sic-tutorials-main');
    if(window.innerWidth <= 767) {
      disableBodyScroll(this.scrollLockEl);
      this.setState({ scrollLocked: true });
    }
    window.addEventListener('resize', this.handleWindowResize);
  }

  componentWillUnmount() {
    enableBodyScroll(this.scrollLockEl);
    window.removeEventListener('resize', this.handleWindowResize);
  }

  handleWindowResize() {
    if(window.innerWidth <= 767) {
      if(!this.state.scrollLocked) {
        disableBodyScroll(this.scrollLockEl);
        this.setState({ scrollLocked: true });
      }
    } else {
      enableBodyScroll(this.scrollLockEl);
      this.setState({ scrollLocked: false });
    }
  }

  hideTutorials = (e) => {
    e && e.stopPropagation();

    this.setState({ animateModal: false }, () => {
      setTimeout(() => {this.props.hideTutorials()}, animatedDelay);
    });
  }

  ifActiveHeadingInSubHeadings(subHeadings) {
    const { activeHeading } = this.props;
    return Object.keys(subHeadings).some(subHeading => activeHeading === subHeadings[subHeading])
  }

  renderSubComponent() {
    const { activeHeading } = this.props;

    if(this.ifActiveHeadingInSubHeadings(createAProfileSubHeadings)) {
      return <CreateAProfile />
    } else if(this.ifActiveHeadingInSubHeadings(selectYourCollegesSubHeadings)) {
      return <MyColleges />
    } else if(this.ifActiveHeadingInSubHeadings(connectAndChatSubHeadings)) {
      return <ChatAndConnect />
    } else if(this.ifActiveHeadingInSubHeadings(myCounselorSubHeadings)) {
      return <MyCounsler />
    } else if(this.ifActiveHeadingInSubHeadings(researchUniversitiesSubHeadings)){
      return <ResearchUniversities />
    } else if(this.ifActiveHeadingInSubHeadings(promoteYourselfSubHeadings)){
      return <PromoteYourself />
    }
  }

  handleTutorialsClick = (e) => {
    e && e.stopPropagation();
  }

  onSwipeMove = (position, event) => {
    if(position.x > 100) {
      this.hideTutorials();
    }
  }

  render() {
    const { showTutorials } = this.props;
    const { animateModal } = this.state;

    return (
      <Swipe onSwipeMove={this.onSwipeMove}>
        <div id='sic-tutorials-main-cont' onClick={this.hideTutorials}>
          {
            animateModal && <div className='close-tutorials-mbl show-for-small-only' onClick={this.hideTutorials}>
              <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/close-tutorials.png' />
            </div>
          }
          <div className={`sic-tutorials-main ${animateModal ? 'show-tutorials' : ''}`} onClick={this.handleTutorialsClick}>
            <div className='close-tutorials-modal hide-for-small-only' onClick={this.hideTutorials}>
              <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/close-tutorials.png' />
            </div>
            {
              this.renderSubComponent()
            }
          </div>
        </div>
      </Swipe>
    );
  }
}

const mapStateToProps = state => ({
  activeHeading: state.tutorials.activeHeading,
});

const mapDispatchToProps = dispatch => ({
  hideTutorials: () => {
    dispatch(hideTutorials());
  }
});

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(SICTutorials);
