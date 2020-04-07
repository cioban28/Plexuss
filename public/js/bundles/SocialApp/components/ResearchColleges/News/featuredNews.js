import React, { Component } from 'react'
import { connect } from 'react-redux'
import './styles.scss'
import InfiniteScroll from 'react-infinite-scroll-component';
import { getNews } from './../../../api/news'
import { Link } from 'react-router-dom';
import { VideoLightBox } from './VideoLightBox';

class FeaturedNews extends Component {
  constructor(props){
    super(props);

    this.state = {
      videoModals: {},
    };

    this.closeVideoModal = this.closeVideoModal.bind(this);
  }

  handleShowVideoClick(index) {
    const videoModals = {...this.state.videoModals};
    videoModals[index] = true;
    this.setState({ videoModals });
  }

  closeVideoModal(index) {
    const videoModals = {...this.state.videoModals}
    videoModals[index] = false;
    this.setState({ videoModals });
  }

  render(){
    const { news } = this.props
    const { videoModals } = this.state;

    const imgUrlPrefix = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images";

    return (
      <div className="row show-for-medium-up destop-only-featured-box">
      {!!news.featured_rand_news &&
      <div className="column small-12">
        {
          videoModals[news.featured_rand_news[0].id] && <VideoLightBox newsId={news.featured_rand_news[0].id} closeModal={this.closeVideoModal} />
        }
        {
          videoModals[news.featured_rand_news[1].id] && <VideoLightBox newsId={news.featured_rand_news[1].id} closeModal={this.closeVideoModal} />
        }
        {
          videoModals[news.featured_rand_news[2].id] && <VideoLightBox newsId={news.featured_rand_news[2].id} closeModal={this.closeVideoModal} />
        }
          <div className="new-slider-block row" data-equalizer="">
            <div className="leftfeaturecol column small-9" data-equalizer-watch="" style={{height: "379px"}}>
              <div className=" slider-image-left">
              {

                news.featured_rand_news[0].has_video == 1
                  ? <div style={{ position: 'relative', }} onClick={this.handleShowVideoClick.bind(this, news.featured_rand_news[0].id)}>
                      <img src={news.featured_rand_news[0].img_sm} className="hide-for-small-only lg" />
                      <div className="layer">
                        <div className="playbtn text-center">
                          <div className="play-arrow"></div>
                        </div>
                      </div>
                    </div>
                  : <Link to={`/news/article/${news.featured_rand_news[0].slug}`}>
                        <img src={`${imgUrlPrefix}/${news.featured_rand_news[0].img_lg}`} className="hide-for-small-only lg" />
                    </Link>
              }

                <div className="image-overlay hide-for-small-only">
                  <div className="overlay-inner">
                    <div className="newss-overlay-heading">
                      {news.featured_rand_news[0].title}<br/>
                      <span>
                        by {news.featured_rand_news[0].external_author}
                      </span>
                      <br/>
                      {
                        news.featured_rand_news[0].has_video == 1
                          ? <span className="newss-overlay-link">
                              <a onClick={this.handleShowVideoClick.bind(this, news.featured_rand_news[0].id)}>Watch video</a>
                            </span>
                          : <span className="newss-overlay-link">
                              <Link to={`/news/article/${news.featured_rand_news[0].slug}`}>See full article</Link>
                            </span>
                      }

                      <div className="share-buttons-white">
                        <a className="social_share share_facebook
                        " data-params="{&quot;platform&quot;:&quot;facebook&quot;,&quot;name&quot;:&quot;Explore United States Coast Guard Academy&quot;,&quot;picture&quot;:&quot;https:\/\/s3-us-west-2.amazonaws.com\/asset.plexuss.com\/college\/overview_images\/carousel_images\/738_united_states_coast_guard_academy_1.jpg&quot;,&quot;href&quot;:&quot;http:\/\/www.plexuss.com\/news\/article\/youniversitytv-united-states-coast-guard-academy-video&quot;}"></a>
                        <a className="social_share share_twitter
                        " data-params="{&quot;platform&quot;:&quot;twitter&quot;,&quot;text&quot;:&quot;Explore United States Coast Guard Academy&quot;,&quot;href&quot;:&quot;http:\/\/www.plexuss.com\/news\/article\/youniversitytv-united-states-coast-guard-academy-video&quot;}"></a>
                        <a className="social_share share_pinterest
                        " data-params="{&quot;platform&quot;:&quot;pinterest&quot;,&quot;description&quot;:&quot;Explore United States Coast Guard Academy&quot;,&quot;picture&quot;:&quot;https:\/\/s3-us-west-2.amazonaws.com\/asset.plexuss.com\/college\/overview_images\/carousel_images\/738_united_states_coast_guard_academy_1.jpg&quot;,&quot;href&quot;:&quot;http:\/\/www.plexuss.com\/news\/article\/youniversitytv-united-states-coast-guard-academy-video&quot;}" data-pin-do="buttonPin" data-pin-config="above"></a>
                        <a className="social_share share_linkedin
                        " data-params="{&quot;platform&quot;:&quot;linkedin&quot;,&quot;title&quot;:&quot;Explore United States Coast Guard Academy&quot;,&quot;picture&quot;:&quot;https:\/\/s3-us-west-2.amazonaws.com\/asset.plexuss.com\/college\/overview_images\/carousel_images\/738_united_states_coast_guard_academy_1.jpg&quot;,&quot;href&quot;:&quot;http:\/\/www.plexuss.com\/news\/article\/youniversitytv-united-states-coast-guard-academy-video&quot;}"></a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div className="rightfeaturecol column small-3" data-equalizer-watch="" style={{height: "379px"}}>
              <div className="slider-right slider-right-1">
              {
                news.featured_rand_news[1].has_video == 1
                  ? <div onClick={this.handleShowVideoClick.bind(this, news.featured_rand_news[1].id)}>
                      <div className="layer-container">
                          <img src={news.featured_rand_news[1].img_sm} alt={news.featured_rand_news[1].title} className="hide-for-small-only sm" />
                          <div className="layer">
                            <div className="playbtn text-center">
                              <div className="play-arrow">
                              </div>
                            </div>
                          </div>
                        </div>
                      <div className="image-overlay">
                        <p>{news.featured_rand_news[1].meta_description} <br/>
                          <span>
                            <a onClick={this.handleShowVideoClick.bind(this, news.featured_rand_news[1].id)}>Watch video</a>
                          </span>
                        </p>
                      </div>
                    </div>
                  : <div>
                      <Link to={`/news/article/${news.featured_rand_news[1].slug}`}>
                        <img src={`${imgUrlPrefix}/${news.featured_rand_news[1].img_lg}`} className="hide-for-small-only" alt={news.featured_rand_news[1].title}/>
                      </Link>
                      <div className="image-overlay">
                        <p>{news.featured_rand_news[1].title} <br/>
                          <span>
                            <Link to={`/socl/news/article/${news.featured_rand_news[1].slug}`}>See full article</Link>
                          </span>
                        </p>
                      </div>
                    </div>
              }
              </div>

              <div className="slider-right slider-right-2">
                {
                  news.featured_rand_news[2].has_video == 1
                    ? <div onClick={this.handleShowVideoClick.bind(this, news.featured_rand_news[2].id)}>
                        <div className="layer-container" data-id="793">
                          <img src={news.featured_rand_news[2].img_sm} alt={news.featured_rand_news[2].title} className="hide-for-small-only sm"/>
                          <div className="layer">
                            <div className="playbtn text-center">
                              <div className="play-arrow">
                              </div>
                            </div>
                          </div>
                        </div>
                        <div className="image-overlay">
                          <p>{news.featured_rand_news[2].meta_description} <br/>
                            <span>
                              <a onClick={this.handleShowVideoClick.bind(this, news.featured_rand_news[2].id)}>Watch video</a>
                            </span>
                          </p>
                        </div>
                      </div>
                    : <div>
                        <Link to={`/news/article/${news.featured_rand_news[2].slug}`}>
                          <img src={`${imgUrlPrefix}/${news.featured_rand_news[2].img_lg}`} className="hide-for-small-only" alt={news.featured_rand_news[2].title}/>
                        </Link>
                        <div className="image-overlay">
                          <p>{news.featured_rand_news[2].title} <br/>
                            <span>
                              <Link to={`/news/article/${news.featured_rand_news[2].slug}`}>See full article</Link>
                            </span>
                          </p>
                        </div>
                      </div>
                }

                </div>
              </div>
            </div>
          </div>
      }
      </div>
    )
  }
}

export default (FeaturedNews);
