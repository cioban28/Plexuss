import React from 'react';
import './styles.scss';
import { PostCard } from '../../../common/PostCard/index.jsx';
import { Link } from 'react-router';


export function PostInsights({ post, linkPreview, handleCloseModal, handleDuplicatePostClick }) {

  return (
    <div id='post-insights-cont'>
      <header className='post-insights-header'>
        <h2>Post Insights</h2>
        <img onClick={handleCloseModal} className='close-modal' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/sales/X.svg' />
      </header>
      <main className='main-cont'>
        <div className='row main-left'>
          <div className='columns large-7 medium-12'>
            <PostCard postInsights={true} mediaAttachments={post.images} linkPreview={linkPreview} post={post} cardBorder='post-card-border' impressionsBorder='impressions-border' />
          </div>
          <div className='columns large-5 medium-12 main-right'>
            <div className='performance-cont'>
              <h3>Performance for your post</h3>
              <p><span>1,324</span>  People reached</p>
              <p><span>{post.likes && post.likes.length}</span>  People liked</p>
              <p><span>{post.comments && post.comments.length}</span>  People commented</p>
              <p><span>{post.share_count}</span>  People shared</p>
            </div>
            <div className='negative-feedback-cont'>
              <h3>NEGATIVE FEEDBACK</h3>
              <p><span>0</span> Report Abuse</p>
              <p><span>0</span> Hide this post</p>
            </div>
          </div>
        </div>
        <div className='footer'>
          <div className='impressions-cont'>
            <h2><span>1,324</span> People Reached</h2>
            <h2><span>500</span> Engagements</h2>
          </div>
          <div>
            <Link className='link-btn' to={{pathname: '/sales/social-newsfeed/edit', state: post}}>
              <button className='btn-edit-post' onClick={handleDuplicatePostClick}>Edit Post</button>
            </Link>
            <Link className='link-btn' to={{pathname: '/sales/social-newsfeed/new', state: post}}>
              <button className='btn-duplicate-post' onClick={handleDuplicatePostClick}>
                <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/sales/Publish.svg' /> Duplicate post
              </button>
            </Link>
          </div>
        </div>
      </main>
    </div>
  )
}
