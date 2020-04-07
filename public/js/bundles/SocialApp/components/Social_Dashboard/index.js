import React, {Component} from 'react'
import { Link } from 'react-router-dom'
import Posts from './../common/post/index'
import Feed from './../common/feedBlock'
import Profile from './../common/profile'
import { connect } from 'react-redux'
import './styles.scss'
import { getHomePosts } from './../../api/post'
import { SpinningBubbles, Bubbles } from '../common/loader/loader'
import InfiniteScroll from 'react-infinite-scroller';
import { homePage } from './../../actions/headerTab';
import { Helmet } from 'react-helmet';


class Social_Dashboard extends Component {
  is_mount = false
  constructor(props){
    super(props);
    this.state={
      hasMoreItems: true,
    }
    this.getPosts = this.getPosts.bind(this);
  }
  getPosts(page){
    this.setState({hasMoreItems: false})
    let obj = {
      offset: page-1,
    };
   getHomePosts(obj).then(() => {
      if(this.props.isNextPost){
        if(this.is_mount)
          this.setState({
            hasMoreItems: true,
          });
      }
    });
  }
  componentDidMount() {
    this.is_mount = true;
  }
  componentWillMount() {
    this.props.homePage();
  }
  componentWillUnmount() {
    this.is_mount = false;
  }
  render(){
    return (
      <div className="social_dashbord_banner">
        <Helmet>
          <title>Home | News Feed | Plexuss.com</title>
          <meta name="description" content="Plexuss Home Page and News Feed" />
          <meta name="keywords" content="college search" />
        </Helmet>
        {
          this.props.posts.length <= 0 &&
          <div className="gif-loader-cont"><img src="/social/images/plexuss-loader-test-2.gif" /></div>
        }
        <div className="row custom-row">
          <div className="small-8 medium-3 large-3 columns mbl_none profile_parent">
            <Profile user={this.props.user} profile={this.props.profile}/>
          </div>
          <div className="small-12 medium-8 large-8 columns padding-0">
            <div id="main-content">
              {!!this.props.user && !!this.props.user.userAccountSettings && !!this.props.user.userAccountSettings.is_incognito ?
                <div className="incognito-dashboard">
                  <img src='/social/images/settings/active_options/noun_Ghost_367889_000000.png'/>
                  <Link to={'/social/settings'} className="incognito-link" >Turn off Incognito Mode to post content</Link>
                </div>
                : (this.props.user.userAccountSettings === null || !!this.props.user.userAccountSettings && !this.props.user.userAccountSettings.is_incognito) &&
                <span><Feed user={this.props.user}/></span>
              }
              {
                this.props.makePost && <div className="post-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>
              }
              <div className="input-style mbl_none">
                <div className="news-all">
                  <span className="time">All news</span>
                  {/* <img src="/social/images/arrow.svg" className="new-arrow" /> */}
                </div>
              </div>
              <InfiniteScroll
                    loader={<div className="gif-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>}
                    pageStart={this.props.startPoint}
                    loadMore={this.getPosts}
                    hasMore={this.state.hasMoreItems}
                >
                  <Posts posts={this.props.posts.length > 0 ? this.props.posts : []} logInUser={this.props.user} commentsFlag={false}/>
              </InfiniteScroll>
            </div>
          </div>
        </div>
      </div>
    );
  }
}
const mapStateToProps = (state) => {
  return{
      user: state.user.data,
      posts: state.posts.posts,
      startPoint: state.posts.startPoint,
      profile: state.profile,
      isNextPost: state.posts.isNextPost,
      makePost: state.posts.makePost,
  }
}

const mapDispatchtoProps = (dispatch) => {
  return {
    homePage: () => {dispatch(homePage())},
  }
}
export default  connect(mapStateToProps, mapDispatchtoProps)(Social_Dashboard);
