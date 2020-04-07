import React, { Component } from 'react';
import { Route, Switch, Redirect, withRouter } from 'react-router-dom';

import Social_Dashboard from './components/Social_Dashboard'
import Networking from './components/Networking'
import Article_Editor from './components/Article_Editor'
import Article_Dashboard from './components/Article_Dashboard'
import Article from './components/Article/article'
import Profile from './components/Profile'
import Profile_Edit from './components/Profile/Edit'
import Profile_Documents from './components/Profile/Documents'
import Me_Tab from './components/Me'
import Notifications from './components/Notifications/index'
import Setting from './components/Settings/index'
import Messages from './components/Messages/index'
import Manage_Colleges from './components/Manage_Colleges'
import Colleges from './components/Manage_Colleges/colleges'
import RankingCategories from './components/ResearchColleges/RankingCategories'
import Ranking from './components/ResearchColleges/Ranking'
import Scholarships from './components/Scholarships'
import CollegeComparison from './components/CollegeComparison'
import SearchedCollegePages from './components/Search/SearchedCollegePages';
import CollegePage from './components/Search/CollegePage';
import CollegeEssay from './components/ResearchColleges/CollegeEssay/index';
import MajorsInner from './components/ResearchColleges/Majors/MajorsInner';
import Majors from './components/ResearchColleges/Majors';
import CollegeFairEvents from './components/ResearchColleges/CollegeFairEvents';
import SingleCollegeEssay from './components/ResearchColleges/CollegeEssay/SingleEssay'
import NewsHome from './components/ResearchColleges/News/NewsHome'
import NewsSubCategory from './components/ResearchColleges/News/NewsSubCategory'
import SingleNews from './components/ResearchColleges/News/singleNews'
import FindColleges from './components/ResearchColleges/FindColleges/index'
import DownloadApp from './components/ResearchColleges/DownloadApp';
import CollegeSearchComponent from './components/CollegeSearch/index'
import IntlResources from './components/IntlStudentResources/index'
import NCSA from './components/Scholarships/Ncsa'

import MobileMessages from './components/Footer/MblMessages'
import MobileNetworking from './components/Footer/MblNetworking'
import MobileManageColleges from './components/Footer/ManageColleges'

import OneApp from './components/OneApp'
import { OneAppRoutes } from './components/OneApp/constants'
import Header from './components/Header';
import MblFooter from './components/Footer/MblFooter';
import SinglePost from './components/SinglePost/index'
import Front_Dashboard from './components/Front_Dashboard'
import TermsOfService from './components/Policy/termsOfService'
import PrivacyPolicy from './components/Policy/privacyPolicy'
import CaliforniaPolicy from './components/Policy/californiaPolicy'
import LegalNotice from './components/Policy/legalNotice'

import ViewStudentApplication from './components/OneApp/viewStudentApplication'

class App extends Component {
  componentDidUpdate(prevProps) {
    if(this.props.location.pathname !== prevProps.location.pathname) {
      window.scrollTo(0, 0);
    }
  }

  render() {
    return (
      <div>
        <Route component={ Header }/>
					<Switch>
						<Route path="/" exact component={ Front_Dashboard } />
						<Route path="/general" component={ Front_Dashboard } />
						<Route path="/india" component={ Front_Dashboard } />
						<Route path="/home" exact component={ Social_Dashboard } />
						<Route path="/post/:id" exact component={ SinglePost } />
						<Route path="/social/networking" component={ Networking }  />
						<Route path="/social/networking/:slug" component={ Networking } />
						<Route path="/social/networking/:slug/:totalContacts" component={ Networking } />
						<Route path="/social/article-editor" component={ Article_Editor } />
						<Route path="/social/article-editor/:id" component={ Article_Editor } />
						<Route path="/social/article-dashboard" component={ Article_Dashboard } />
						<Route path="/social/article/:id" component={ Article } />
						<Route path="/social/profile/:id" component={ Profile } />
						<Route path="/social/edit-profile" component={ Profile_Edit } />
						<Route path="/social/document-profile" component={ Profile_Documents } />
						<Route path="/social/manage-colleges" component={ Manage_Colleges } />
						<Route path="/social/me" component={ Me_Tab } />
						<Route path="/social/notifications" component={ Notifications } />
						<Route path="/social/settings" component={ Setting } />
						<Route path="/social/messages" component={ Messages } />
						<Route path="/social/messages/:id" component={ Messages } />
						<Route path="/ranking/categories" component={RankingCategories} />
						<Route path="/ranking" component={Ranking} />
						<Route path='/scholarships' component={Scholarships} />
						<Route path='/comparison' component={CollegeComparison} />
						<Route path='/college-majors/:slug/:major' component={MajorsInner} />
						<Route path='/college-majors/:slug' component={MajorsInner} />
						<Route path='/college-majors' component={Majors} />
						<Route path='/college-fair-events' component={CollegeFairEvents} />
						<Route path='/college/:slug' exact render={(routeProps) => <CollegePage subPageIndex={0} subPage='overview-page' />} />
						<Route path='/college/:slug/stats' render={(routeProps) => <CollegePage subPageIndex={1} subPage='stats-page' />} />
						<Route path='/college/:slug/admissions' render={(routeProps) => <CollegePage subPageIndex={2} subPage='admissions-page' />} />
						<Route path='/college/:slug/enrollment' render={(routeProps) => <CollegePage subPageIndex={3} subPage='enrollment-page' />} />
						<Route path='/college/:slug/ranking' render={(routeProps) => <CollegePage subPageIndex={4} subPage='ranking-page' />} />
						<Route path='/college/:slug/tuition' render={(routeProps) => <CollegePage subPageIndex={5} subPage='tuition-page' />} />
						<Route path='/college/:slug/financial-aid' render={(routeProps) => <CollegePage subPageIndex={6} subPage='financial-aid-page' />} />
						<Route path='/college/:slug/news' render={(routeProps) => <CollegePage subPageIndex={7} subPage='news-page' />} />
						<Route path='/college/:slug/current-students' render={(routeProps) => <CollegePage routeProps={routeProps} subPageIndex={8} subPage='current-students-page' />} />
						<Route path='/college/:slug/alumni' render={(routeProps) => <CollegePage routeProps={routeProps} subPageIndex={9} subPage='alumni-page' />} />
						<Route path='/home/search?type=college' component={ SearchedCollegePages } />
						<Route path="/college-essays" exact component={ CollegeEssay } />
						<Route path="/college-essays/:slug" component={ SingleCollegeEssay } />
						<Route path="/news/subcategory/:name" exact component={ NewsSubCategory } />
						<Route path="/news" exact component={ NewsHome } />
						<Route path="/news/article/:slug" exact component={ SingleNews } />
						<Route path="/download-app" exact component={ DownloadApp } />
						<Route path="/social/one-app/" exact render={() => <Redirect to="/social/one-app/basic"/>} />
						{ OneAppRoutes }
						<Route path="/college" exact component={ FindColleges } />
						<Route path="/college-search" component={ CollegeSearchComponent } />
						<Route path="/international-resources" exact component={ IntlResources } />
						<Route path="/ncsa" exact component={ NCSA } />
						<Route path="/social/mbl-messages" exact component={ MobileMessages } />
						<Route path="/social/mbl-networking" exact  render={() => <Redirect to="/social/networking/connection"/>}  />
						<Route path="/social/mbl-manage-colleges" exact component={ MobileManageColleges } />
						<Route path="/terms-of-service" component={ TermsOfService } />
						<Route path="/privacy-policy" component={ PrivacyPolicy } />
						<Route path="/california-policy" component={ CaliforniaPolicy } />
						<Route path="/legal-notice" component={ LegalNotice } />

						<Route path="/view-student-application/:id" component={ ViewStudentApplication } />
					</Switch>
        <Route component={ MblFooter}/>
      </div>
    )
  }
}

export default withRouter(App);
