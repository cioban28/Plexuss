import React from 'react';
import './styles.scss';
import Slider from 'react-slick';
import $ from 'jquery';

export function PostCard({ post, description, mediaAttachments, postInsights, cardBorder, impressionsBorder, linkPreview, handleFileLabelClick, handleRemoveLinkPreview }) {
  const settings = {
    dots: false,
    infinite: true,
    speed: 500,
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: false,
  };

  let sliderRef = null;

  const handlePrevArrowClick = () => {
    sliderRef.slickPrev();
  }

  const handleNextArrowClick = () => {
    sliderRef.slickNext();
  }

  const renderDescription = () => !!$(description).text() ? description : 'Write something here';

  const renderPreviewForPostInsights = () => (
    <div className='media-preview row'>
      {
        mediaAttachments.length === 1 &&
            <img className='full-width-height' src={mediaAttachments[0].image_link} />
      }
      {
        mediaAttachments.length > 1 &&
          <div>
            <div className='slider-arrow arrow-prev' onClick={handlePrevArrowClick.bind(this)}>
              <img src='/social/images/post/left/noun_Arrow_1830603_000000.png' />
            </div>
            <div className='slider-arrow arrow-next' onClick={handleNextArrowClick.bind(this)}>
              <img src='/social/images/post/right/noun_Arrow_1830603_000000.png' />
            </div>
            <Slider ref={r => (sliderRef = r)} {...settings}>
              {
                mediaAttachments.map((media, index) => (
                  <div key={index}>
                      <img className='full-width-height' src={media.image_link} />
                  </div>
                ))
              }
            </Slider>
          </div>
      }
    </div>
  )

  const renderPreviewForNewPost = () => (
    <div className='media-preview row'>
      {
        !!mediaAttachments && mediaAttachments.length === 0 &&
          <div className='full-width-height'>
            <div className='columns large-6 medium-6 no-media'>
              <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/sales/noun_Photo_2067987_000000.svg' />
            </div>
            <div className='columns large-6 medium-6 no-media'>
              <label onClick={handleFileLabelClick.bind(this)} className='custom-file-upload'>
                  Upload photo(s)
              </label>
              <input type='file' multiple hidden />
            </div>
          </div>
      }
      {
        !!mediaAttachments && mediaAttachments.length === 1 &&
            <img className='full-width-height' src={mediaAttachments[0].image_link} />
      }
      {
        !!mediaAttachments && mediaAttachments.length > 1 &&
          <div>
            <div className='slider-arrow arrow-prev' onClick={handlePrevArrowClick.bind(this)}>
              <img src='/social/images/post/left/noun_Arrow_1830603_000000.png' />
            </div>
            <div className='slider-arrow arrow-next' onClick={handleNextArrowClick.bind(this)}>
              <img src='/social/images/post/right/noun_Arrow_1830603_000000.png' />
            </div>
            <Slider ref={r => (sliderRef = r)} {...settings}>
              {
                mediaAttachments.map((media, index) => (
                  <div className='full-width-height' key={index}>
                      <img className='full-width-height' src={media.image_link} />
                  </div>
                ))
              }
            </Slider>
          </div>
      }
    </div>
  )

  return (
    <div id='post-preview-card' className={cardBorder}>
      <div className='preview-inner'>
        <header className='preview-header'>
          <div className='user-info-cont'>
            <img src='https://images.unsplash.com/photo-1527980965255-d3b416303d12?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=800&q=80' />
            <div className='user-info'>
              <p className='user-name'>Plex Admin</p>
              <p className='user-status'>Student</p>
            </div>
          </div>
          <div className='post-action-preview'>
            <span className='time-ago'>1 min ago</span>
            <img src='/social/images/arrow.svg' />
          </div>
        </header>
        {
          !!postInsights && <div className='description' >
          { post.post_text }
          </div>
        }
        {
          !postInsights && <div className='description' dangerouslySetInnerHTML={{ __html: renderDescription() }} />
        }
        {
          !linkPreview && !!postInsights && !!mediaAttachments.length && renderPreviewForPostInsights()
        }
        {
          !linkPreview && !postInsights && renderPreviewForNewPost()
        }
        {
          !!linkPreview && <div className='shared-link-content'>
            { !postInsights && <div className='link-remove' onClick={handleRemoveLinkPreview}>&#10005;</div>}
            <img src={linkPreview.image} className='link-image' />
            <div className='link-title'>{linkPreview.title}</div>
            <div className='link-description'>{linkPreview.description}</div>
            <a href={linkPreview.url} className='shared-url' target='_blank'>{linkPreview.url}</a>
          </div>
        }
      </div>
      <div className={`reactions-count ` + impressionsBorder}>
        <p>{!!post && !!post.likes && !!post.likes.length || 0} Likes</p>
        <p>{!!post && !!post.comments && !!post.comments.length || 0} Comments</p>
        <p>{!!post && post.shared_count || 0} Shares</p>
      </div>
      <footer className='reactions-cont'>
        <div className='reaction-cont'>
          <img src='/social/images/heart-icon.png' /> Like
        </div>
        <div className='reaction-cont'>
          <img src='/social/images/noun_comment.png' /> Comment
        </div>
        <div className='reaction-cont'>
          <img src='/social/images/noun_share.png' /> Share
        </div>
      </footer>
    </div>
  )
}
