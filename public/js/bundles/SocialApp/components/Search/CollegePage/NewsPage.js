import React, { Component } from 'react';
import { connect } from 'react-redux';
import './styles/stats.scss';
import './styles/admissions.scss';
import { getCollegeNews } from '../../../api/search';
import { withRouter } from 'react-router-dom';


class NewsPage extends Component {

  componentDidMount() {
    !this.props.news.length && this.props.getCollegeNews(this.props.college.CollegeId);
  }

  render() {

    return (
      <div id='news-page'>
      {!!this.props.isFetching && <div className="college-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>}
      {
        !this.props.isFetching && !!this.props.news.length &&
          <div className="row news-container">
            <div className="column small-12 overview-news-area radial-bdr">
              {
                this.props.news.map((newsItem, index) => (
                  <div className="row news-item-container" key={index}>
                    <div className="column small-2 text-center">
                      <a href={newsItem['url'] || '#'} target="_blank" className="news-related">
                      {
                        newsItem['image'] && newsItem['image']['thumbnail'] && newsItem['image']['thumbnail']['contentUrl']
                        ? <img src={newsItem['image']['thumbnail']['contentUrl']} width='100' height="100" />
                        : <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/icons/default_news_icon.png' width='100' height="100" />
                      }
                      </a>
                    </div>
                    <div className="column small-10 end">
                      <div className="row">
                        <a href={newsItem['url'] || '#'} target="_blank" className="news-related">
                          {newsItem['name'] || ''}
                        </a>
                      </div>
                      <div className="row news-desc">
                        {newsItem['description'] || ''}
                      </div>
                      <div className="row text-right news-date">
                        <span>{newsItem['provider'][0]['name'] || ''}</span>&nbsp;&nbsp;-&nbsp;&nbsp;
                        <span>{newsItem['datePublished'] || ''}</span>
                      </div>
                    </div>
                  </div>
                ))

              }
              {
                !this.props.news.length &&
                <div className="row news-item-container no-news">
                  <div className="column small-12 text-center">
                    Sorry, we couldn't find any news associated with this school.
                  </div>
                </div>
              }
            </div>
          </div>
      }
      </div>
    )
  }
}

const mapStateToProps = (state) => {
  return {
    news: state.search.news,
    college: state.search.college,
    isFetching: state.search.isFetchingCollegeSubPage,
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    getCollegeNews: (collegeId) => { dispatch(getCollegeNews(collegeId)) },
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(withRouter(NewsPage));
