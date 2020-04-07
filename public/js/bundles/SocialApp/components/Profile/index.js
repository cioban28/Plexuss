// /Profile/index.js

import React from 'react'
import Tabs from './../common/Tabs'
import Posts from './../common/post/index'
import ProfileHeader from './profile_header'
import { connect } from 'react-redux'
import { resetProfilePosts } from './../../actions/profile'
import { getProfileData, getProfilePosts } from './../../api/profile'
import './styles.scss'
import UserInfo from './userInfo'
import InfiniteScroll from 'react-infinite-scroller';
import { SpinningBubbles } from './../common/loader/loader'
import { Helmet } from 'react-helmet';

class Profile extends React.Component {
  constructor(props){
    super(props);
    this.state={
      isNextPost: true,
      isMobileNextPost: true,
      isChange: false,
    }
    this.getPosts = this.getPosts.bind(this);
    this.loadData = this.loadData.bind(this);
  }

  componentDidMount() {
    window.scrollTo(0, 0);
  }

  componentWillMount() {
    let id = this.props.match.params.id;
    let obj={
      user_id: id,
    }
    this.loadData(obj)
  }

  componentDidUpdate(prevProps) {
		if (this.props.location !== prevProps.location) {
      let id = this.props.match.params.id;
      let obj={
        user_id: id,
      }
      this.loadData(obj)
		}
	}
	loadData(obj) {
    this.setState({isChange: true, isNextPost: true, isMobileNextPost: true})
    this.props.resetProfilePosts()
    getProfileData(obj)
    .then(() => {
      this.setState({isChange: false})
    })
	}

  getPosts(page){
    let id = this.props.match.params.id;
    this.setState({isNextPost: false});
    let obj ={
      offset: page-1,
      user_id: id,
    }
    getProfilePosts(obj)
    .then(()=>{
      if(this.props.isNextPost){
        this.setState({
          isNextPost: true,
        })
      }
    })
  }

  render(){
    let id = this.props.match.params.id;
    return (
      <div>
      <Helmet>
        <title>College Planning Profile | College Recruiting Network | Plexuss.com</title>
        <meta name="description" content="Welcome to your college planning profile - Complete 30 percent of your profile and get discovered and recruited by colleges. Include your accomplishments, awards and extracurricular activities. Only on Plexuss.com" />
        <meta name="keywords" content="College planning profile" />
      </Helmet>
      {this.state.isChange ? (<div className="profile-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>) : (
        <div>
          <div className="profile_content">
            <ProfileHeader logInUserId={this.props.user ? this.props.user.user_id : ''} id={id}/>
            <div className="row row_container">
              {
                this.props.posts.length <= 0 &&
                <div className="gif-loader-profile"><img src="/social/images/plexuss-loader-test-2.gif" /></div>
              }
              <div className="mbl_none">
                <UserInfo />
                <div className="small-12 medium-8 large-8 columns ">
                  <div id="feeds_section">
                  {this.props.user.user_id !== Number(id) && !!this.props.profile.user && !!this.props.profile.user.userAccountSettings && !!this.props.profile.user.userAccountSettings.is_incognito ?
                      <div className="incognito-message">This account is private</div>
                    :
                      <InfiniteScroll
                        pageStart={0}
                        loadMore={this.getPosts}
                        hasMore={this.state.isNextPost}
                      >
                        <Posts posts={this.props.posts.length > 0 ? this.props.posts : []} logInUser={this.props.user} commentsFlag={false}/>
                      </InfiniteScroll>
                  }
                  </div>
                </div>
              </div>

              <div className="mbl_tabs">
                <Tabs>
                  <div label="About Me">
                    <UserInfo />
                  </div>
                  <div label="Posts">
                    <div className="small-12 medium-8 large-8 columns ">
                      <div id="feeds_section">
                      {!!this.props.profile.user && !!this.props.profile.user.userAccountSettings && !!this.props.profile.user.userAccountSettings.is_incognito ?
                          <div className="incognito-message">This account is private</div>
                        :
                          <InfiniteScroll
                            pageStart={0}
                            loadMore={this.getPosts}
                            hasMore={this.state.isNextPost}
                          >
                            <Posts posts={this.props.posts.length > 0 ? this.props.posts : []} logInUser={this.props.user} commentsFlag={false}/>
                          </InfiniteScroll>
                      }
                      </div>
                    </div>
                  </div>
                </Tabs>
              </div>
            </div>
          </div>
        </div>
      )}
      </div>
    );
  }
}
const mapStateToProps = (state) =>{
  return{
      user: state.user.data,
      profile: state.profile,
      posts: state.posts.profilePosts,
      isNextPost: state.posts.isNextPost,
  }
}
const mapDispatchtoProps = (dispatch) => {
  return {
    resetProfilePosts: () => {dispatch(resetProfilePosts())},
  }
}
export default connect(mapStateToProps, mapDispatchtoProps)(Profile);
