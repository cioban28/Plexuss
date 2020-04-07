// IntlStudentResources/index.js
// /Edit_Profile/index.js

import React, { Component } from 'react'
import './styles.scss'
import ISR from './Main'
import Application_checklist from './../../../StudentApp/components/Intl_Resources/Application_Checklist'
import FindSchool from './../../../StudentApp/components/Intl_Resources/Find_School'
import Aid from './../../../StudentApp/components/Intl_Resources/Aid'
import Prep from './../../../StudentApp/components/Intl_Resources/Prep'
import WorkUS from './../../../StudentApp/components/Intl_Resources/Work_In_US'
import StudentVisa from './../../../StudentApp/components/Intl_Resources/Student_Visa'
import Banner from './../../../StudentApp/components/common/Banner'
import { Helmet } from 'react-helmet';

class IntlResources extends Component {
	constructor(props){
		super(props)

		this.state ={
			currentPage: 'main',
      menuLabel: 'Main',
		}

    this.handleSection = this.handleSection.bind(this);
		this.handleMenuLabel = this.handleMenuLabel.bind(this);
	}

	componentDidMount() {
		let params = (new URL(window.location)).searchParams;
    let step = params.get('section');
		this.handleSection(step)
	}

  componentDidUpdate() {
    window.scrollTo(0, 0)
  }

	handleSection(step) {
		switch(step) {
			case 'main': this.setState({currentPage: 'main'}); break;
			case 'application-checklist': this.setState({currentPage: 'application-checklist'}); break;
			case 'find-schools': this.setState({currentPage: 'find-schools'}); break;
			case 'aid': this.setState({currentPage: 'aid'}); break;
			case 'prep': this.setState({currentPage: 'prep'}); break;
			case 'working-in-us': this.setState({currentPage: 'working-in-us'}); break;
			case 'student-visa': this.setState({currentPage: 'student-visa'}); break;
		}
    this.handleMenuLabel(step);
	}

  handleMenuLabel(step){
    switch (step){
      case 'main': this.setState({menuLabel: 'Main'}); break;
      case 'application-checklist': this.setState({menuLabel: 'Application Checklist'}); break;
      case 'find-schools': this.setState({menuLabel: 'Finding the Right School'}); break;
      case 'aid': this.setState({menuLabel: 'Scholarships & Financial Aid'}); break;
      case 'prep': this.setState({menuLabel: 'English Proficiency Tests and Preparations'}); break;
      case 'working-in-us': this.setState({menuLabel: 'Working in the US as an International Student'}); break;
      case 'student-visa': this.setState({menuLabel: 'Student Visa and Immigration Center'}); break;
    }
  }

  _openRoutes(e){
    let drop = document.getElementById("routes-dropdown-list")

    drop.classList.toggle('opened')
  }

  render(){
    return (
      <div className="social-intl-resources">
        <Helmet>
          <title>Wishing to apply for (American) Universities as an international student? Use our international student resource guide to begin!</title>
          <meta name="description" content="Wishing to apply for (American) Universities as an international student? Use our international student resource guide!" />
        </Helmet>
        <div className="social-intl-resources-container">
          <Banner
            customClass="resources"
            bg="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/intl_students_bg.png">
              <div className="resources-banner">
                <h3 className="main-title">Here are some topics you might find helpful!</h3>
                <div className="topics">

                </div>
              </div>
          </Banner>

          <div className="intl-content-container">
            <div className="large-4 medium-4 small-12 columns" style={{padding: '0'}}>
              <div className="social-isr-sidebar">
                <ul>
                  <li className={'isr-section-link ' + (this.state.currentPage === 'main' && 'active')} onClick={() => this.handleSection('main')}> Main </li>
                  <li className={'isr-section-link ' + (this.state.currentPage === 'application-checklist' && 'active')} onClick={() => this.handleSection('application-checklist')}> Application Checklist </li>
                  <li className={'isr-section-link ' + (this.state.currentPage === 'find-schools' && 'active')} onClick={() => this.handleSection('find-schools')}> Finding the Right School </li>
                  <li className={'isr-section-link ' + (this.state.currentPage === 'aid' && 'active')} onClick={() => this.handleSection('aid')}> Scholarships & Financial Aid </li>
                  <li className={'isr-section-link ' + (this.state.currentPage === 'prep' && 'active')} onClick={() => this.handleSection('prep')}> English Proficiency Tests and Preparations </li>
                  <li className={'isr-section-link ' + (this.state.currentPage === 'working-in-us' && 'active')} onClick={() => this.handleSection('working-in-us')}> Working in the US as an International Student </li>
                  <li className={'isr-section-link ' + (this.state.currentPage === 'student-visa' && 'active')} onClick={() => this.handleSection('student-visa')}> Student Visa and Immigration Center </li>
                </ul>
              </div>
              <div className="med-routes-drop-btn">International Resources</div>
              <div className="route-prem-cont">
                <div className="routes-dropdown-cont">
                  <div className="topics-txt">Resource Topics</div>
                  <ul className="routes-dropdown" onClick={ (e) => { e.stopPropagation();  this._openRoutes() } }>
                    <li><span  className="chosen-topic"> { this.state.menuLabel }</span>
                      <span className="topic-arrow"></span>
                      <ul id="routes-dropdown-list" className="routes-dropdown-list">
                        <li onClick={() => this.handleSection('main')}> Main </li>
                        <li onClick={() => this.handleSection('application-checklist')}> Application Checklist </li>
                        <li onClick={() => this.handleSection('find-schools')}> Finding the Right School </li>
                        <li onClick={() => this.handleSection('aid')}> Scholarships & Financial Aid </li>
                        <li onClick={() => this.handleSection('prep')}> English Proficiency Tests and Preparations </li>
                        <li onClick={() => this.handleSection('working-in-us')}> Working in the US as an International Student </li>
                        <li onClick={() => this.handleSection('student-visa')}> Student Visa and Immigration Center </li>
                      </ul>
                    </li>
                  </ul>
                </div>

              </div>
            </div>
            <div className="large-8 medium-8 small-12 columns" style={{padding: '0'}}>
              <div className="intl-resources-section-container">
                <article className='resource-article'>
                  {this.state.currentPage === 'main' && <ISR handleSection={this.handleSection} /> }
                  {this.state.currentPage === 'application-checklist' && <Application_checklist /> }
                  {this.state.currentPage === 'find-schools' && <FindSchool /> }
                  {this.state.currentPage === 'aid' && <Aid /> }
                  {this.state.currentPage === 'prep' && <Prep /> }
                  {this.state.currentPage === 'working-in-us' && <WorkUS /> }
                  {this.state.currentPage === 'student-visa' && <StudentVisa /> }
                </article>
              </div>
            </div>
          </div>
        </div>
      </div>
    );
  }
}

export default IntlResources;
