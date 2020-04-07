import { combineReducers } from 'redux';
import {reducer as toastrReducer} from 'react-redux-toastr'

import posts from './posts';
import conversations from './conversation';
import setting from './setting';
import user from './user';
import profile from './profile';
import _profile from '../../StudentApp/reducers/Profile';
import articles from './article';
import search from './search';
import { scholarship } from './scholarship';
import { college } from './college';
import messages from './messages';
import collegeEssays from  './CollegeEssays';
import news from './news'
import findColleges from './findColleges'
import { modal } from './modal';
import events from './events'
import { notification } from './notification'
import headerTabs from './headerTab'
import favColleges from './favColleges'
import recColleges from './recColleges'
import recuColleges from './recuColleges'
import viewColleges from './viewColleges'
import application from './application'
import trash from './trash'
import slidingMenu from './slidingMenu'
import carousles from './carousles'
import tutorials from './tutorials'

const rootReducer = combineReducers({
  posts: posts,
  conversations: conversations,
  setting: setting,
  user: user,
  profile: profile,
  articles: articles,
  _profile: _profile,
  search: search,
  scholarships: scholarship,
  colleges: college,
  messages: messages,
  collegeEssays: collegeEssays,
  news: news,
  modal: modal,
  events: events,
  findColleges: findColleges,
  notification: notification,
  toastr: toastrReducer,
  headerTabs: headerTabs,
  favColleges: favColleges,
  recColleges: recColleges,
  recuColleges: recuColleges,
  viewColleges: viewColleges,
  applications: application,
  trash: trash,
  slidingMenu: slidingMenu,
  carousles: carousles,
  tutorials: tutorials,
});

export default rootReducer;
