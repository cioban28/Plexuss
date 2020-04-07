import React, { Component } from 'react';
import { connect } from 'react-redux';
import InfiniteScroll from 'react-infinite-scroll-component';
import FeaturedNews from './featuredNews'
import NewsListItem from './NewsListItem'
import { getNews } from './../../../api/news'
import News from './index';
import { withRouter } from 'react-router-dom';
import { Helmet } from 'react-helmet';


class NewsHome extends Component {
  componentDidMount(){
    getNews(undefined, 1)
  }

  render() {
    const {newsList, news} = this.props;
    const mainUrl = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/"
    const length = !!newsList && newsList.length
    const firstHalf = !!length && newsList.slice(0, (length/3)),
        secondHalf = !!length && newsList.slice(length/3, (2*length/3)),
        thirdHalf = !!length && newsList.slice(2*length/3, (3*length/3))

    return (
      <News>
        <Helmet>
          <title>State College News | College Recruiting Academic Network | Plexuss.com</title>
          <meta name="description" content="Get out-of-state and in state college news straight from colleges and universities including ranking, financial aid, admissions, college life and much more on Plexuss.com." />
          <meta name="keywords" content="State college news" />
        </Helmet>
        {
          length<=0 && <div className="news-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>
        }
        <div className="row width-adjustment">
          <div id="newsshomecontent">
            <div className="newss-cont-left-container">
              <div className="newss-content-container">
                {!!news.featured_rand_news &&  <FeaturedNews news={news} /> }

                  <div id="container-box" className="row">
                    { !!newsList && <InfiniteScroll
                        className="large-12"
                        style={{overflowY: 'hidden'}}
                        dataLength={newsList.length}
                        next={ () => {
                            getNews(undefined, news.newsMeta.current_page+1 )}}
                        hasMore={true}
                        endMessage={
                          <p style={{ textAlign: "center" }}>
                            <b>Yay! You have seen it all</b>
                          </p>
                        }
                      >
                      <NewsListItem newsList={firstHalf}/>
                      <NewsListItem newsList={secondHalf}/>
                      <NewsListItem newsList={thirdHalf}/>
                  </InfiniteScroll>}
                </div>
              </div>
            </div>
          </div>
        </div>
      </News>
    )
  }
}

const mapStateToProps = (state) => {
  return {
    news: state.news,
    newsList: state.news.newsList,
  }
}

export default connect(mapStateToProps, null)(withRouter(NewsHome));
