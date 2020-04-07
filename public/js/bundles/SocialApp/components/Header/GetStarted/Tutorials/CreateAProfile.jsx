import React, { Component } from 'react';
import { connect } from 'react-redux';
import PublicProfile from './PublicProfile.jsx';
import CollegeApplicationAssessment from './CollegeApplicationAssessment.jsx';
import { createAProfileSubHeadings } from './constants';
import $ from 'jquery';


class CreateAProfile extends Component {
  componentDidMount() {
    this.scrollToTabSection(this.props.activeHeading);
  }

  componentWillReceiveProps(nextProps) {
    this.scrollToTabSection(nextProps.activeHeading);
  }

  scrollToTabSection(activeHeading) {
    const container = $('.sic-tutorials-main');
    if(Object.values(createAProfileSubHeadings)[0] === activeHeading) {
      container.scrollTop(0);
    } else {
      const targetEl = container && document.querySelector('.sic-tutorials-main').querySelector('#college-application-assessment');
      targetEl && container.scrollTop(targetEl.offsetTop - 50);
    }
  }

  render() {
    return (
      <div id='create-a-profile'>
        <PublicProfile />
        <CollegeApplicationAssessment />
      </div>
    )
  }
}

const mapStateToProps = state => ({
  activeHeading: state.tutorials.activeHeading,
  toggleHeadingChanged: state.tutorials.toggleHeadingChanged,
});

export default connect(mapStateToProps, null)(CreateAProfile);
