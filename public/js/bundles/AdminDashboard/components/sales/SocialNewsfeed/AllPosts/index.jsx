import React, { Component } from 'react';
import { connect } from 'react-redux';
import PostsCommonLayout from '../PostsCommonLayout/index.jsx';
import { getNewsfeedPosts } from '../../../../actions/newsfeedActions';


class AllPosts extends Component {
  getPosts = (pageNumber) => {
    this.props.getAllPosts(pageNumber)
  }

  render() {
    const { allPosts } = this.props;

    return (
      <PostsCommonLayout
        heading='All Posts'
        showPostCreateButton={false}
        getPosts={this.getPosts}
        posts={allPosts}
      />
    )
  }
}


const mapStateToProps = state => ({
  allPosts: state.newsfeed.allPosts,
});

const mapDispatchToProps = dispatch => ({
  getAllPosts: (pageNumber) => { dispatch(getNewsfeedPosts(pageNumber)) },
});

export default connect(mapStateToProps, mapDispatchToProps)(AllPosts);
