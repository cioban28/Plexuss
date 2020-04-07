import React, { Component } from 'react';
import { connect } from 'react-redux';
import { Link } from 'react-router-dom';
import './styles.scss';
import { resetCollegeData, resetSearchResults, setShouldGetSearchedCollege } from '../../../actions/search';


class SearchResults extends Component {
  constructor(props) {
    super(props);

    this.state = {
      colleges: this.props.searchResults.filter(result => result.category === 'college').splice(0, 3),
      news: this.props.searchResults.filter(result => result.category === 'news').splice(0, 3),
    }

    this.handleCollegeClick = this.handleCollegeClick.bind(this);
  }

  handleCollegeClick() {
    this.props.resetCollegeData();
    !!this.props.unmountSearchResults && this.props.unmountSearchResults();
    this.props.setShouldGetSearchedCollege();
  }

  render() {
    const { colleges, news } = this.state;
    const { searchResults } = this.props;

    const limitDescription = (description) => description.length > 50 ? description.substring(0, 50) : description;

    return (
      <div>
        <div className='search-results-container'>
        {
          colleges.length > 0 &&
            <div>
              <header className='header'>
                <p className='category'>Colleges</p>
                {/* <p className='more'>More...</p> */}
              </header>
              {
                colleges.map((college, index) => {
                  let imgStyle = {backgroundImage: 'url("'+college.image+'")'}
                  return <Link to={`/college/${college.slug}`} key={index} onClick={this.handleCollegeClick}>
                    <div className='results-container'>
                      <div className='result-block'>
                        <div className='avatar' style={imgStyle}/>
                        <div className='content'>
                          <h3 className='headline'>{college.value}</h3>
                          <p className='description' style={{textAlign: 'left'}}>{limitDescription(college.desc)}</p>
                        </div>
                      </div>
                    </div>
                  </Link>
                })
              }
            </div>
        }
        {
          news.length > 0 &&
            <div>
              <header className='header'>
                <p className='category'>News</p>
                {/* <p className='more' onClick={this.handleNewsMoreClick}>More...</p> */}
              </header>
              {
                news.map((news, index) => {
                  return <div key={index} className='results-container' onClick={() => window.location.href = '/news'+(!!news.slug && ('/'+news.slug))}>
                    <div className='result-block'>
                      <div className='news-avatar'>
                        <img src={news.image} />
                      </div>
                      <div className='content news-content'>
                        <h3 className='headline'>{news.value || 'Untitled'}</h3>
                        <p className='description'>{limitDescription(news.desc)}</p>
                      </div>
                    </div>
                  </div>
                })
              }
            </div>
        }
        </div>
        {
          searchResults.length > 6 &&
          <div className='show-all-results'>
            <p>Show all { searchResults.length } results</p>
          </div>
        }
      </div>
    );
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    resetCollegeData: () => { dispatch(resetCollegeData()) },
    resetSearchResults: () => { dispatch(resetSearchResults()) },
    setShouldGetSearchedCollege: () => { dispatch(setShouldGetSearchedCollege()) },
  }
}

export default connect(null, mapDispatchToProps)(SearchResults);
