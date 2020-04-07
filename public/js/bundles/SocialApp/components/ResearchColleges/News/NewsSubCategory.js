import React, { Component } from 'react';
import { connect } from 'react-redux';
import InfiniteScroll from 'react-infinite-scroll-component';
import FeaturedNews from './featuredNews'
import NewsListItem from './NewsListItem'
import { getNews } from './../../../api/news'
import News from './index';
import { withRouter } from 'react-router-dom';
import { Helmet } from 'react-helmet';


class NewsSubcategory extends Component {
  headInfo  = {};

  componentDidMount(){
    const { match } = this.props;
    getNews(match && match.params && match.params.name, 1);
  }

  componentWillReceiveProps(nextProps) {
    if(this.props.location.pathname !== nextProps.location.pathname) {
      const { match } = nextProps;
      getNews(match && match.params && match.params.name, 1);
    }
  }

  render() {
    const {newsList, news, match} = this.props
    const mainUrl = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/"
    const length = !!newsList && newsList.length
    const firstHalf = !!length && newsList.slice(0, (length/3)),
        secondHalf = !!length && newsList.slice(length/3, (2*length/3)),
        thirdHalf = !!length && newsList.slice(2*length/3, (3*length/3))
    const subCategoryName = match && match.params && match.params.name;
    if(subCategoryName === 'survival-guides') {
      this.headInfo = {
        title: "State College News | College Recruiting Academic Network | Plexuss.com",
        metaDescription: "Get out-of-state and in state college news straight from colleges and universities including ranking, financial aid, admissions, college life and much more on Plexuss.com.",
        metaKeywords: "State college news",
      }
    } else if(subCategoryName === 'ranking') {
      this.headInfo = {
        title: "College Ranking News | College Recruiting Academic Network | Plexuss.com",
        metaDescription: "Find college ranking news on Plexuss.com. Discover blogs, news, and community conversations about different college rankings from around the US.",
        metaKeywords: "college ranking",
      }
    } else if(subCategoryName === 'getting-into-college') {
      this.headInfo = {
        title: "How to Get into College | College Recruiting Network | Plexuss.com",
        metaDescription: "Learn how to get into college on Plexuss with actionable lists that help you -- the student -- focus on how to get into college and enjoy your college education.  Get recruited on Plexuss.com today!",
        metaKeywords: "how to get into college",
      }
    } else if(subCategoryName === 'college-sports') {
      this.headInfo = {
        title: "College Basketball News | College Recruiting Academic Network | Plexuss.com",
        metaDescription: "Find the latest college sporting news, including college baseball, college football, college basketball and other college sports at Plexuss.com",
        metaKeywords: "college basketball news",
      }
    } else if(subCategoryName === 'celebrity-alma-mater') {
      this.headInfo = {
        title: "Celebrities College Degrees | College Recruiting Academic Network | Plexuss.com",
        metaDescription: "Learn more about your favorite celebrities with college degrees and their Alma Mater. See the universities these famous people attended at Plexuss.com.",
        metaKeywords: "celebrities with college degrees",
      }
    } else if(subCategoryName === 'campus-life') {
      this.headInfo = {
        title: "Campus Life | College Recruiting Academic Network | Plexuss.com",
        metaDescription: "Guide to Campus Life! Find campus life news on Plexuss.com: getting involved in college, housing, and health.",
        metaKeywords: "campus life",
      }
    } else if(subCategoryName === 'financial-aid') {
      this.headInfo = {
        title: "College Financial Aid News | College Recruiting Network | Plexuss.com",
        metaDescription: "Get information on college financial aid and learn the ways to pay for college on the Plexuss paying for college page.",
        metaKeywords: "college financial aid",
      }
    } else if(subCategoryName === 'careers') {
      this.headInfo = {
        title: "Career Search News | College Recruiting Academic Network | Plexuss.com",
        metaDescription: "Career search news and practical, actionable information and updates that help you -- the student -- focus on college education and which university to choose.",
        metaKeywords: "career search",
      }
    }

    return (
      <News>
        <Helmet>
          <title>{this.headInfo.title}</title>
          <meta name="description" content={this.headInfo.metaDescription} />
          <meta name="keywords" content={this.headInfo.metaKeywords} />
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
                            getNews(match && match.params && match.params.name, news.newsMeta.current_page+1 )}}
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

export default connect(mapStateToProps, null)(withRouter(NewsSubcategory));
