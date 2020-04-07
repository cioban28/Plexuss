import React, { Component } from 'react';
import { connect } from 'react-redux';
import PostsCommonLayout from '../PostsCommonLayout/index.jsx';
import { getNewsfeedPosts } from '../../../../actions/newsfeedActions';


class PlexussOnlyPosts extends Component {
  getPosts = (pageNumber) => {
    this.props.getPlexussOnlyPosts(pageNumber, true)
  }

  render() {
    const { plexussOnlyPosts } = this.props;

    return (
      <PostsCommonLayout
        heading='Plexuss Only Posts'
        showPostCreateButton={true}
        getPosts={this.getPosts}
        posts={plexussOnlyPosts}
      />
    )
  }
}


const mapStateToProps = state => ({
  plexussOnlyPosts: state.newsfeed.plexussOnlyPosts,
});

const mapDispatchToProps = dispatch => ({
  getPlexussOnlyPosts: (pageNumber, plexussOnly) => { dispatch(getNewsfeedPosts(pageNumber, plexussOnly)) },
});

export default connect(mapStateToProps, mapDispatchToProps)(PlexussOnlyPosts);
