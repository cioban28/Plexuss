﻿import React, { Component } from 'react';
import { connect } from 'react-redux';
import { withRouter, Link, Redirect } from 'react-router-dom';
import './styles/styles.scss';
import { getCollegeOverview, getCollegeRecruited, getCollegeStats } from '../../../api/search';
import { requestCancellationFn, resetRecruitMeSucess, resetShouldGetSearchedCollege, resetCollegeData } from '../../../actions/search';
import { openModal, closeModal } from '../../../actions/modal';
import OverviewPage from './OverviewPage';
import StatsPage from './StatsPage';
import AdmissionsPage from './AdmissionsPage';
import EnrollmentPage from './EnrollmentPage';
import RankingPage from './RankingPage';
import TuitionPage from './TuitionPage';
import FinancialAidPage from './FinancialAidPage';
import NewsPage from './NewsPage';
import CurrentStudentsPage from './CurrentStudentsPage';
import AlumniPage from './AlumniPage';
import AnimateHeight from 'react-animate-height';
import Modal from 'react-modal';
import RecruitmentModal from './RecruitmentModal';
import SignInModal from './../../Modal/SignInModal';
import Slider from "react-slick";
import Fade from 'react-reveal/Fade'
import { Helmet } from 'react-helmet';


class CollegePage extends Component {
  constructor(props) {
    super(props);

    let toLink = '';
    let linkText = ''
    const urlArray = this.props.location.pathname.split('/');
    if(this.props.subPage === 'overview-page') {
      toLink = `/college/${urlArray[urlArray.length-1]}`;
      linkText = 'overview';
    } else {
      toLink = `/college/${urlArray[urlArray.length-2]}/${urlArray[urlArray.length-1]}`;
      linkText = urlArray[urlArray.length-1];
    }

    this.state = {
      engagementBannerExpanded: false,
      isLoading: true,
      mobileNavHeight: 0,
      clickedButton: linkText,
      oldSlide: 0,
      activeSlide: 0,
      slidePos: this.props.subPageIndex,
      currentSlide: 0,
      topPosition: false,
      mobileNavActiveLinkEl: <Link to={toLink} className='active-link mobile-active-link'>
                                <span className={`icon icon-${linkText}`}></span>
                                <br/>
                                <span>{this.toTitleCase(linkText)}</span>
                              </Link>,
    }

    this.handleGetRecruitedClick = this.handleGetRecruitedClick.bind(this);
    this.handleToggleMobileNavClick = this.handleToggleMobileNavClick.bind(this);
    this.handleMobileOverviewClick = this.handleMobileOverviewClick.bind(this);
    this.handleMobileStatsClick = this.handleMobileStatsClick.bind(this);
    this.handleMobileAdmissionsClick = this.handleMobileAdmissionsClick.bind(this);
    this.handleMobileEnrollmentClick = this.handleMobileEnrollmentClick.bind(this);
    this.handleMobileRankingClick = this.handleMobileRankingClick.bind(this);
    this.handleMobileTuitionClick = this.handleMobileTuitionClick.bind(this);
    this.handleMobileCurrentStudentsClick = this.handleMobileCurrentStudentsClick.bind(this);
    this.handleMobileAlumniClick = this.handleMobileAlumniClick.bind(this);
    this.handleMobileNewsClick = this.handleMobileNewsClick.bind(this);
    this.handleMobileFinancialAidClick = this.handleMobileFinancialAidClick.bind(this);
    this.handleEngagemenBarToggle = this.handleEngagemenBarToggle.bind(this);
    this.checkScreenTop = this.checkScreenTop.bind(this);

    this.settings = {
      dots: false,
      infinite: false,
      speed: 300,
      slidesToShow: 4,
      slidesToScroll: 1,
      arrows: false,
      initialSlide: this.props.subPageIndex,
      beforeChange: (current, next) =>
        this.setState({ oldSlide: current, activeSlide: next }),
      afterChange: current => this.setState({ slidePos: current })

    };

    this.handlePrevArrowClick = this.handlePrevArrowClick.bind(this);
    this.handleNextArrowClick = this.handleNextArrowClick.bind(this);
    this.handleBeforeChangeClick = this.handleBeforeChangeClick.bind(this);
  }



  handlePrevArrowClick() {
    this.slider.slickPrev();
  }

  handleNextArrowClick() {
    this.slider.slickNext();
  }

  handleBeforeChangeClick() {
    let first, second;
  }


  componentDidMount() {
    getCollegeStats(this.props.match.params.slug, 'stats')
    .then(() => {
      this.setState({isLoading:false})
    });
    this.props.getCollegeOverview(this.props.match.params.slug);
    window.addEventListener('scroll', this.checkScreenTop, { passive: true });
  }

  componentWillMount() {
    // window.removeEventListener('scroll', this.checkScreenTop);
    this.props.resetCollegeData();
  }

  componentWillReceiveProps(nextProps) {
    if(nextProps.shouldGetSearchedCollege || this.props.match.params.slug !== nextProps.match.params.slug) {
      this.props.resetShouldGetSearchedCollege();
      this.setState({isLoading:true})
      this.props.getCollegeOverview(nextProps.match.params.slug);
      getCollegeStats(nextProps.match.params.slug, 'stats')
      .then(() => {
        this.setState({isLoading:false})
      });
    }
    if(nextProps.recruitMeSuccess && this.props.shouldTakeToPortal) {
      this.props.resetRecruitMeSucess();
      this.props.history.push('/social/manage-colleges/favorites');
    }
  }

  componentDidUpdate(prevProps) {
    window.scrollTo(0, 0)
    window.onpopstate = (e) => {
      getCollegeStats(this.props.match.params.slug, 'stats');
      this.props.getCollegeOverview(this.props.match.params.slug);
    }
  }

  toTitleCase(str) {
    return str.split('-').map((s) => s.charAt(0).toUpperCase() + s.substring(1)).join(' ');
  }

  handleGetRecruitedClick() {
    this.props.getCollegeRecruited(this.props.college.CollegeId);
    this.handleEngagemenBarToggle();
  }

  handleToggleMobileNavClick() {
    this.setState((prevState) => ({ mobileNavHeight: prevState.mobileNavHeight === 0 ? 'auto' : 0 }));
  }

  handleMobileOverviewClick() {
    this.setState({
      mobileNavActiveLinkEl: <Link to={`/college/${this.props.college.college_data.slug}`} className='active-link mobile-active-link'>
                                <span className="icon icon-overview"></span>
                                <br/>
                                <span>Overview</span>
                            </Link>,
      mobileNavHeight: 0,
      clickedButton: 'overview'
    })

    // if(this.state.slidePos === 0){
    // this.slider.slickNext();

      // this.handleNextArrowClick();
    // }
  }

  handleMobileStatsClick() {
    this.setState({
      mobileNavActiveLinkEl: <Link to={`/college/${this.props.college.college_data.slug}/stats`} className='active-link mobile-active-link'>
                                <span className="icon icon-stats"></span>
                                <br/>
                                <span>Stats</span>
                              </Link>,
      mobileNavHeight: 0,
      clickedButton: 'state'
    })

    // if(this.state.slidePos === 0){
    // this.slider.slickNext();

      // this.handleNextArrowClick();
    // }
    if(this.state.slidePos === 1){
      this.slider.slickPrev();
    }
  }

  handleMobileRankingClick() {
    this.setState({
      mobileNavActiveLinkEl: <Link to={`/college/${this.props.college.college_data.slug}/ranking`} className='active-link mobile-active-link'>
                                <span className="icon icon-ranking"></span>
                                <br/>
                                <span>Ranking</span>
                              </Link>,
      mobileNavHeight: 0,
      clickedButton: 'ranking'
    })

    if(this.state.slidePos === 1){
      this.slider.slickNext();
    }
    if(this.state.slidePos === 4){
      this.slider.slickPrev();
    }
  }

  handleMobileAdmissionsClick() {
    this.setState({
      mobileNavActiveLinkEl: <Link to={`/college/${this.props.college.college_data.slug}/admissions`} className='active-link mobile-active-link'>
                                <span className="icon icon-admissions"></span>
                                <br/>
                                <span>Admissions</span>
                              </Link>,
      mobileNavHeight: 0,
      clickedButton: 'admissions'
    })

    // if(this.state.slidePos === 0){
    // this.slider.slickNext();
      // this.handleNextArrowClick();
    // }
    if(this.state.slidePos === 2){
      this.slider.slickPrev();
    }
  }

  handleMobileEnrollmentClick() {
    this.setState({
      mobileNavActiveLinkEl: <Link to={`/college/${this.props.college.college_data.slug}/enrollment`} className='active-link mobile-active-link'>
                                <span className="icon icon-enrollment"></span>
                                <br/>
                                <span>Enrollment</span>
                              </Link>,
      mobileNavHeight: 0,
      clickedButton: 'enrollment'
    })

    if(this.state.slidePos === 0){
      this.slider.slickNext();
      // this.handleNextArrowClick();
    }
    if(this.state.slidePos === 3){
      this.slider.slickPrev();
    }
  }

  handleMobileTuitionClick() {
    this.setState({
      mobileNavActiveLinkEl: <Link to={`/college/${this.props.college.college_data.slug}/tuition`} className='active-link mobile-active-link'>
                                <span className="icon icon-tuition"></span>
                                <br/>
                                <span>Tuition</span>
                              </Link>,
      mobileNavHeight: 0,
      clickedButton: 'tuition'
    })

    if(this.state.slidePos === 2){
      this.slider.slickNext();
    }
    if(this.state.slidePos === 5){
      this.slider.slickPrev();
    }
  }

  handleMobileNewsClick() {
    this.setState({
      mobileNavActiveLinkEl: <Link to={`/college/${this.props.college.college_data.slug}/news`} className='active-link mobile-active-link'>
                                <span className="icon icon-news"></span>
                                <br/>
                                <span>News</span>
                              </Link>,
      mobileNavHeight: 0,
      clickedButton: 'news'
    })


    if(this.state.slidePos === 4){
      this.slider.slickNext();
    }
  }

  handleMobileCurrentStudentsClick() {
    this.setState({
      mobileNavActiveLinkEl: <Link to={`/college/${this.props.college.college_data.slug}/current-students`} className='active-link mobile-active-link'>
                                Current Students
                              </Link>,
      mobileNavHeight: 0,
      clickedButton: 'current'
    })

    if(this.state.slidePos === 5){
      this.slider.slickNext();
    }
  }

  handleMobileAlumniClick() {
    this.setState({
      mobileNavActiveLinkEl: <Link to={`/college/${this.props.college.college_data.slug}/alumni`} className='active-link mobile-active-link'>
                                Alumni
                              </Link>,
      mobileNavHeight: 0,
      clickedButton: 'alumni'
    })

    // if(this.state.slidePos === 0){
    //   this.handleNextArrowClick();
    // }
  }

  handleMobileFinancialAidClick() {
    this.setState({
      mobileNavActiveLinkEl: <Link to={`/college/${this.props.college.college_data.slug}/financial-aid`} className='active-link mobile-active-link'>
                                <span className="icon icon-financial-aid"></span>
                                <br/>
                                <span style={{color: 'white'}}>Financial Aid</span>
                              </Link>,
      mobileNavHeight: 0,
      clickedButton: 'financial'
    })

    if(this.state.slidePos === 3){
      this.slider.slickNext();
    }
    if(this.state.slidePos === 6){
      this.slider.slickPrev();
    }
  }

  handleEngagemenBarToggle(){
    this.setState((prevState) => ({ engagementBannerExpanded: !prevState.engagementBannerExpanded }));
  }

  renderSubPage() {
    switch(this.props.subPage) {
      case 'overview-page':
        return <OverviewPage />
      case 'stats-page':
        return <StatsPage />
      case 'admissions-page':
        return <AdmissionsPage />
      case 'enrollment-page':
        return <EnrollmentPage />
      case 'ranking-page':
        return <RankingPage />
      case 'tuition-page':
        return <TuitionPage />
      case 'financial-aid-page':
        return <FinancialAidPage />
      case 'news-page':
        return <NewsPage />
      case 'current-students-page':
        return <CurrentStudentsPage routeProps={this.props.routeProps} />
      case 'alumni-page':
        return <AlumniPage routeProps={this.props.routeProps}/>
      default:
        return <OverviewPage />
    }
  }

  checkScreenTop(){
    var elem = document.getElementById("my-sticky-header")

    if (window.scrollY >= elem.clientHeight) {
      elem.classList.add("my-sticky");
    }
    else {
      elem.classList.remove("my-sticky");
    }
  }

  render() {
    const { collegeMedia } = this.props;
    const { college, isFetching, isOpen, subPage, getRecruited, overview } = this.props;
    const collegeData = college.college_data;
    const renderEngagementBar = () => {
      return <div>
      {
        this.state.engagementBannerExpanded
          ? <div className='engagement-bar-cont'>
              <div className='engagement-bar'>
                <img src='/social/images/star.svg' className='star-img' onClick={this.handleEngagemenBarToggle} />
                <div className='action-buttons'>
                  {
                    college.isInUserList === 0 &&
                      <button className='get-recruited-btn' onClick={this.handleGetRecruitedClick}>
                        <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/college/get-recruited.png' />
                        Get Recruited!
                      </button>
                  }
                  {
                    college.isInUserList === 1 &&
                      <button className='already-recruited-btn'>
                        <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/college/already-recruited.png' />
                        Already on my list!
                      </button>
                  }
                  <button className='apply-now-btn'><img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/college/apply.png' />Apply Now!</button>
                </div>
              </div>
            </div>
          : <div className='engagement-bar-collapsed' onClick={this.handleEngagemenBarToggle}>
              <img src='/social/images/star.svg' />
            </div>
      }
      </div>
    }

    return (
      <div id='social-college-main-cont'>
        {
          !!isFetching && <div className="college-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>
        }
        {
          !isFetching && Object.entries(college).length > 0 &&
            <div>
              <Helmet>
                <title>{overview.page_title}</title>
                <meta name='keywords' content={overview.meta_keywords} />
                <meta name='description' content={overview.meta_description} />
              </Helmet>
              <div className='college-cont college-content-small background-for-small'>
                {
                //  <div className='mobile-engagement-bar hide-for-small' >
                //   {
                //     college.isInUserList === 0 &&
                //       <button className='get-recruited-btn' onClick={this.handleGetRecruitedClick}>
                //         <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/college/get-recruited.png' />
                //         Get Recruited!
                //       </button>
                //   }
                //   {
                //     college.isInUserList === 1 &&
                //       <button className='already-recruited-btn'>
                //         <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/college/already-recruited.png' />
                //         Already on my list!
                //       </button>
                //   }
                //   <button className='apply-now-btn'>
                //     <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/college/apply.png' />
                //     Apply Now!
                //   </button>
                // </div>
              }

                <div className='college-header'>
                  <div className='college-ranking-cont hide-for-small'>
                    <span># {collegeData.plexuss_ranking || 'na'}</span>
                  </div>
                  <div className='header-content hide-for-small'>
                    <div className='college-logo'>
                      {
                        !!collegeData.logo_url && collegeData.id !== '1785' &&
                          <Link to={ `/college/${collegeData.slug}` }>
                            <img src={`https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/${collegeData.logo_url}`} alt="" />
                          </Link>
                      }
                    </div>
                    <div className='college-info'>
                      <div className='college-name'>
                        {
                          <span className={`flag flag-${collegeData.country_code || ''}`}></span>
                        }
                        <h2>{
                          collegeData.school_name
                        }</h2>
                      </div>
                      { collegeData.is_online == 1 && <p className='online-school'>Online School</p> }
                      <p className='college-address'>
                        { !!collegeData.address && collegeData.address + ', ' }
                        { !!collegeData.city && collegeData.city + ', ' }
                        { !!collegeData.state && collegeData.state + ' ' }
                        { !!collegeData.zip && collegeData.zip + ', ' }
                        { !!collegeData.country_name && collegeData.country_name + ' | ' }
                        { !!collegeData.general_phone && collegeData.general_phone }
                      </p>
                    </div>
                  </div>
                  <div className='college-nav-cont'>
                    <ul className='college-nav-list'>
                      <li className='college-nav-list-item'>
                        <Link to={`/college/${collegeData.slug}`} className={subPage === 'overview-page' ? 'active-link' : ''}>
                          <span className='icon icon-overview'></span>Overview
                        </Link>
                      </li>
                      <li className='college-nav-list-item'>
                        <div className='dropdown'>
                          <Link to={`/college/${collegeData.slug}/stats`} className={subPage === 'stats-page' ? 'active-link' : ''}>
                            <span className='icon icon-stats'></span>Stats
                            <i className='caret fa fa-caret-down'></i>
                          </Link>
                          <ul className='dropdown-content'>
                            <li className='dropdown-list-item'>
                              <Link to={`/college/${collegeData.slug}/admissions`} className={subPage === 'admissions-page' ? 'active-link' : ''}>
                                <span className='icon icon-admissions'></span>Admissions
                              </Link>
                            </li>
                            <li className='dropdown-list-item'>
                              <Link to={`/college/${collegeData.slug}/enrollment`} className={subPage === 'enrollment-page' ? 'active-link' : ''}>
                                <span className='icon icon-enrollment'></span>Enrollment
                              </Link>
                            </li>
                          </ul>
                        </div>
                      </li>
                      <li className='college-nav-list-item'>
                        <Link to={`/college/${collegeData.slug}/ranking`} className={subPage === 'ranking-page' ? 'active-link' : ''}>
                          <span className='icon icon-ranking'></span>Ranking
                        </Link>
                      </li>
                      <li className='college-nav-list-item'>
                        <div className='dropdown'>
                          <Link to={`/college/${collegeData.slug}/tuition`} className={subPage === 'tuition-page' ? 'active-link' : ''}>
                            <span className='icon icon-tuition'></span>Tuition
                            <i className='caret fa fa-caret-down'></i>
                          </Link>
                          <ul className='dropdown-content'>
                            <li className='dropdown-list-item'>
                              <Link to={`/college/${collegeData.slug}/financial-aid`} className={subPage === 'financial-aid-page' ? 'active-link' : ''}>
                                <span className='icon icon-financial-aid'></span>Financial Aid
                              </Link>
                            </li>
                          </ul>
                        </div>
                      </li>
                      <li className='college-nav-list-item'>
                        <Link to={`/college/${collegeData.slug}/news`} className={subPage === 'news-page' ? 'active-link' : ''}>
                          <span className='icon icon-news'></span>News
                        </Link>
                      </li>
                      <li className='college-nav-list-item'>
                        <div className='dropdown'>
                          <Link to={`/college/${collegeData.slug}/current-students`} className={subPage === 'current-students-page' ? 'active-link' : ''}>
                            <img src="/social/images/current-student.svg" className="icon" style={{height: '24px', width: '24px'}} />
                            Current Students
                            <i className='caret fa fa-caret-down'></i>
                          </Link>
                          <ul className='dropdown-content'>
                            <li className='dropdown-list-item'>
                              <Link to={`/college/${collegeData.slug}/alumni`} className={subPage === 'alumni-page' ? 'active-link' : ''}>
                                <img src="/social/images/alumni.svg" className="icon" style={{height: '24px', width: '24px'}} />
                                Alumni
                              </Link>
                            </li>
                          </ul>
                        </div>
                      </li>
                      {
                        // <li className='college-nav-list-item'>
                        //   <div className='dropdown'>
                        //     <Link to={`/college/${collegeData.slug}/chat`}>
                        //       More
                        //       <i className='caret fa fa-caret-down'></i>
                        //     </Link>
                        //     <ul className='dropdown-content'>
                        //       <li className='dropdown-list-item'>
                        //         <Link to={`/college/${collegeData.slug}/chat`} className={subPage === 'chat-page' && 'active-link'}>
                        //           <span className='icon icon-chat'></span>Chat
                        //         </Link>
                        //       </li>
                        //     </ul>
                        //   </div>
                        // </li>
                      }
                    </ul>

                    <div id="my-sticky-header" className="college-mobile-nav" onScroll={this.checkScreenTop}>
                    <div className='college-slider-cont slider-icon' style={{position: 'relative'}}>
                      <div className='slider-arrow slider-arrow-style arrow-prev slider-prev' onClick={this.handlePrevArrowClick}>
                        {
                          (this.state.slidePos != 0) &&
                          <img src="/social/images/arrow-left.svg" />
                        }
                      </div>
                      <div style={{marginLeft: '10px', marginRight: '10px'}}>
                        <Slider
                          ref={c => (this.slider = c)}
                          {...this.settings}
                          style={{marginLeft: '67px'}}
                          afterChange={(e) => this.setState({slidePos: e})}
                        >
                          <div>
                            <div style={{textAlign: 'center', marginTop: '25px'}} className={this.state.clickedButton === 'overview' ? 'active-link' : 'mobile-color-white'}>
                              <Link to={`/college/${collegeData.slug}`} onClick={this.handleMobileOverviewClick}>
                                <span className="icon icon-overview"></span>
                                <br/>
                                <span className={this.state.clickedButton === 'overview' ? 'active-link' : 'mobile-color-white'}>Overview</span>
                              </Link>
                            </div>
                          </div>

                          <div>
                            <div style={{textAlign: 'center', marginTop: '25px'}} className={this.state.clickedButton === 'state' ? 'active-link' : 'mobile-color-white'}>
                              <Link to={`/college/${collegeData.slug}/stats`} onClick={this.handleMobileStatsClick}>
                                <span className="icon icon-stats"></span>
                                <br/>
                                <span className={this.state.clickedButton === 'state' ? 'active-link' : 'mobile-color-white'}>Stats</span>
                              </Link>
                            </div>
                          </div>

                          <div>
                            <div style={{textAlign: 'center', marginTop: '25px'}} className={this.state.clickedButton === 'admissions' ? 'active-link' : 'mobile-color-white'}>
                              <Link to={`/college/${collegeData.slug}/admissions`} onClick={this.handleMobileAdmissionsClick}>
                                <span className="icon icon-admissions"></span>
                                <br/>
                                <span className={this.state.clickedButton === 'admissions' ? 'active-link' : 'mobile-color-white'}>Admissions</span>
                              </Link>
                            </div>
                          </div>

                          <div>
                            <div style={{textAlign: 'center', marginTop: '25px'}} className={this.state.clickedButton === 'enrollment' ? 'active-link' : 'mobile-color-white'}>
                              <Link to={`/college/${collegeData.slug}/enrollment`} onClick={this.handleMobileEnrollmentClick}>
                                <span className="icon icon-enrollment"></span>
                                <br/>
                                <span className={this.state.clickedButton === 'enrollment' ? 'active-link' : 'mobile-color-white'}>Enrollments</span>
                              </Link>
                            </div>
                          </div>

                          <div>
                            <div style={{textAlign: 'center', marginTop: '25px'}} className={this.state.clickedButton === 'ranking' ? 'active-link' : 'mobile-color-white'}>
                              <Link to={`/college/${collegeData.slug}/ranking`} onClick={this.handleMobileRankingClick}>
                                <span className="icon icon-ranking"></span>
                                <br/>
                                <span className={this.state.clickedButton === 'ranking' ? 'active-link' : 'mobile-color-white'}>Ranking</span>
                              </Link>
                            </div>
                          </div>

                          <div>
                            <div style={{textAlign: 'center', marginTop: '25px'}} className={this.state.clickedButton === 'tuition' ? 'active-link' : 'mobile-color-white'}>
                              <Link to={`/college/${collegeData.slug}/tuition`} onClick={this.handleMobileTuitionClick}>
                                <span className="icon icon-tuition"></span>
                                <br/>
                                <span className={this.state.clickedButton === 'tuition' ? 'active-link' : 'mobile-color-white'}>Tuition</span>
                              </Link>
                            </div>
                          </div>

                          <div>
                            <div style={{textAlign: 'center', marginTop: '25px'}} className={this.state.clickedButton === 'financial' ? 'active-link' : 'mobile-color-white'}>
                              <Link to={`/college/${collegeData.slug}/financial-aid`} onClick={this.handleMobileFinancialAidClick}>
                                <span className="icon icon-financial-aid"></span>
                                <br/>
                                <span className={this.state.clickedButton === 'financial' ? 'active-link' : 'mobile-color-white'}>Financial Aid</span>
                              </Link>
                            </div>
                          </div>

                          <div>
                            <div style={{textAlign: 'center', marginTop: '25px'}} className={this.state.clickedButton === 'news' ? 'active-link' : 'mobile-color-white'}>
                              <Link to={`/college/${collegeData.slug}/news`} onClick={this.handleMobileNewsClick}>
                                <span className="icon icon-news"></span>
                                <br/>
                                <span className={this.state.clickedButton === 'news' ? 'active-link' : 'mobile-color-white'}>News</span>
                              </Link>
                            </div>
                          </div>

                          <div>
                            <div style={{textAlign: 'center', marginTop: '25px'}} className={this.state.clickedButton === 'current' ? 'active-link' : 'mobile-color-white'}>
                              <Link to={`/college/${collegeData.slug}/current-students`} onClick={this.handleMobileCurrentStudentsClick}>
                                <img src={this.state.clickedButton === 'current' ? "/social/images/current-student-active.svg" : "/social/images/current-student.svg" } className="icon" style={{height: '24px', width: '24px'}} />
                                <br/>
                                <span className={this.state.clickedButton === 'current' ? 'active-link' : 'mobile-color-white'}>Current Students</span>
                              </Link>
                            </div>
                          </div>

                          <div>
                            <div style={{textAlign: 'center', marginTop: '25px'}} className={this.state.clickedButton === 'alumni' ? 'active-link' : 'mobile-color-white'}>
                              <Link to={`/college/${collegeData.slug}/alumni`} onClick={this.handleMobileAlumniClick}>
                                <img src={this.state.clickedButton === 'alumni' ? "/social/images/alumni-active.svg" : "/social/images/alumni.svg" } className="icon" style={{height: '24px', width: '24px'}} />
                                <br/>
                                <span className={this.state.clickedButton === 'alumni' ? 'active-link' : 'mobile-color-white'}>Alumni</span>
                              </Link>
                            </div>
                          </div>
                        </Slider>
                      </div>
                      <div className='slider-arrow slider-arrow-style arrow-next slider-next' onClick={this.handleNextArrowClick}>
                        {
                          (this.state.slidePos != 6) &&
                          <img src="/social/images/arrow-right.svg" />
                        }
                      </div>
                    </div>
                    </div>
                  </div>
                </div>

                <div id="colleges-star-id" className="show-for-small-only" style={{ position: 'relative', marginTop: '20px'}} onClick={this.handleEngagemenBarToggle}>
                  <div className='recruited-star-div'>
                    <span className='fa fa-star fa-star-styling'></span>
                  </div>
                </div>

                {
                  this.state.engagementBannerExpanded &&
                    <div id='recruited-modal' className='recruited-modal-styling show-for-small-only'>
                      <div className='modal-styling'>
                        <div className="college-logo-container">
                          <img src={`https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/${collegeData.logo_url}`} className="college-logo" />
                        </div>
                        <div className='mobile-engagement-bar recruited-modal-buttons-div'>
                          {
                            college.isInUserList === 0 &&
                              <button className='get-recruited-btn get-recruited-btn-new-styling' onClick={this.handleGetRecruitedClick}>
                                <img className='image-new-styling' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/college/get-recruited.png' />
                                Get Recruited!
                              </button>
                          }
                          {
                            college.isInUserList === 1 &&
                              <button className='already-recruited-btn get-recruited-btn-new-styling already-recruited-btn-new-styling'>
                                <img className='image-new-styling' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/college/already-recruited.png' />
                                Already on my list!
                              </button>

                          }
                          <button className='apply-now-btn get-recruited-btn-new-styling'>
                            <img className='image-new-styling' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/college/apply.png' />
                            Apply Now!
                          </button>
                        </div>
                        <div style={{textAlign: 'center'}}>
                          <button className='close-button-style' onClick={this.handleEngagemenBarToggle}>
                              Close
                          </button>
                        </div>
                      </div>
                    </div>
                }

                <div id='colleges-body'>
                  <div className='college-body college-body-margin'>
                    { this.state.isLoading ? (<div className="college-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>) : (this.renderSubPage()) }
                  </div>
                </div>
              </div>
              { renderEngagementBar()  }
              {
                isOpen && !!Object.entries(getRecruited).length &&
                  <Modal isOpen={isOpen} onRequestClose={this.props.handleClose} className={this.props.user.signed_in ? 'recruitment-modal' : 'sign-modal'}>
                    {this.props.user.signed_in == 1 ? <RecruitmentModal /> : <SignInModal url={'/'}/>}
                  </Modal>
              }
            </div>
        }
      </div>
    )
  }
}

const mapStateToProps = state => {
  return {
    user: state.user.data,
    stats: state.search.stats,
    college: state.search.college,
    isFetching: state.search.isFetchingCollegePage,
    getRecruited: state.search.getRecruited,
    isOpen: state.modal.isOpen,
    recruitMeSuccess: state.search.recruitMeSuccess,
    shouldTakeToPortal: state.search.shouldTakeToPortal,
    shouldGetSearchedCollege: state.search.shouldGetSearchedCollege,
    overview: state.search.overview,
  }
}

const mapDispatchToProps = dispatch => {
  return {
    getCollegeOverview: (slug) => { dispatch(getCollegeOverview(slug)) },
    requestCancellationFn: () => { dispatch(requestCancellationFn()) },
    getCollegeRecruited: (collegeId) => { dispatch(getCollegeRecruited(collegeId)) },
    resetRecruitMeSucess: () => { dispatch(resetRecruitMeSucess()) },
    resetShouldGetSearchedCollege: () => { dispatch(resetShouldGetSearchedCollege()) },
    openModal: () => { dispatch(openModal()) },
    closeModal: () => { dispatch(closeModal()) },
    resetCollegeData: () => { dispatch(resetCollegeData()) },
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(withRouter(CollegePage));
