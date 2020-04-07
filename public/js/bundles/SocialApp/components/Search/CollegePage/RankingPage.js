import React, { Component } from 'react';
import { connect } from 'react-redux';
import { withRouter, Link } from 'react-router-dom';
import { getCollegeRanking } from '../../../api/search';
import Masonry from 'react-masonry-component';
import './styles/stats.scss';
import './styles/admissions.scss';
import Modal from 'react-modal';


class RankingPage extends Component {
  constructor(props) {
    super(props);

    this.state = {
      rankingPin1: {},
      rankingPin2: {},
      rankingPin3: {},
      shouldOpenModal: false,
    }

    this.handleOpenModal = this.handleOpenModal.bind(this);
    this.handleCloseModal = this.handleCloseModal.bind(this);
  }

  componentDidMount() {
    !Object.entries(this.props.ranking).length && this.props.getCollegeRanking(this.props.match.params.slug);
  }

  handleDescriptionClick(pinNumber, index) {
    if(pinNumber === 1) {
      const rankingPin1 = { ...this.state.rankingPin1 };
      this.setState({ rankingPin1: { ...rankingPin1, [index]: !rankingPin1[index] } });
    }
    if(pinNumber === 2) {
      const rankingPin2 = { ...this.state.rankingPin2 };
      this.setState({ rankingPin2: { ...rankingPin2, [index]: !rankingPin2[index] } });
    }
    if(pinNumber === 3) {
      const rankingPin3 = { ...this.state.rankingPin3 };
      this.setState({ rankingPin3: { ...rankingPin3, [index]: !rankingPin3[index] } });
    }
  }

  handleOpenModal() {
    this.setState({ shouldOpenModal: true });
  }

  handleCloseModal() {
    this.setState({ shouldOpenModal: false });
  }

  render() {
    const collegeData = this.props.ranking;

    return (
      <div>
      {!!this.props.isFetching && <div className="college-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>}
      {
        !this.props.isFetching && !!Object.entries(collegeData).length &&
          <div>
            <div className="row" style={{border: 'solid 0px #ff0000'}}>
              <div className="column small-12 no-padding">
                  <div style={{display: 'block'}}>
                    <div className="ranking-first-content column small-12">
                        <div className="row">
                            <div className="large-6 medium-12 small-12 columns college-rank-divide no-padding div-gray-background">
                                <div className="row">
                                  <div className="small-12 column">
                                    <div className=" avg-rank-button">
                                      <div className="avg-rank-sub">PLEXUSS AVG. RANKING</div>
                                      <div className="avg-rank-number">{ collegeData.plexuss ? `#${collegeData.plexuss}` : 'N/A' }</div>
                                    </div>
                                  </div>
                                </div>
                          <div className="row">
                            <div className="small-12 column text-center">
                              <a href="/ranking" className="ranking-tooltip-text">What is Plexuss Ranking?</a>
                            </div>
                          </div>
                                  <div className="national-rank-panel">UNDERGRAD NATIONAL RANKING</div>
                                  <br />

                                  <div className="large-12 small-12 columns no-padding marg-10bot">
                                      <div className="large-2 small-2 columns">
                                        <a href="https://www.usnews.com/best-colleges/rankings/national-universities" target="_blank">
                                          <img className="rank-logo-resize" src="/images/ranking/new_ranking_logos/usnews.png" alt="ranking-logo" />
                                          </a>
                                      </div>
                                      <div className="large-4 small-4 columns review-icons-text">US NEWS</div>
                                      <div className="large-5 small-5 columns review-icons-rank">
                                      { collegeData.us_news ? `#${collegeData.us_news}` : "N/A" }
                                      </div>
                                  </div>

                                  <div className="large-12 columns no-padding marg-10bot">
                                      <div className="large-2 small-2 columns">
                                        <a href="https://www.timeshighereducation.com/world-university-rankings/2017/world-ranking#!/page/0/length/25/sort_by/rank/sort_order/asc/cols/stats" target="_blank">
                                          <img className="rank-logo-resize" src="/images/ranking/new_ranking_logos/reuters.png" alt="ranking-logo" />
                                        </a>
                                      </div>
                                      <div className="large-4 small-4 columns review-icons-text">REUTERS</div>
                                      <div className="large-5 small-5 columns review-icons-rank">
                                      { collegeData.reuters ? `#${collegeData.reuters}` : "N/A" }
                                      </div>
                                  </div>

                                  <div className="large-12 columns no-padding marg-10bot">
                                      <div className="large-2 small-2 columns">
                                        <a href="https://www.forbes.com/top-colleges/list/#tab:rank" target="_blank">
                                          <img className="rank-logo-resize" src="/images/ranking/new_ranking_logos/forbes.png" alt="ranking-logo" />
                                        </a>
                                      </div>
                                      <div className="large-4 small-4 columns review-icons-text">FORBES</div>
                                      <div className="large-5 small-5 columns review-icons-rank">
                                      { collegeData.forbes ? `#${collegeData.forbes}` : "N/A" }
                                      </div>
                                  </div>

                                  <div className="large-12 columns no-padding marg-10bot">
                                      <div className="large-2 small-2 columns">
                                        <a href="https://www.topuniversities.com/university-rankings/world-university-rankings/2016" target="_blank">
                                          <img className="rank-logo-resize" src="/images/ranking/new_ranking_logos/qs-world.png" alt="ranking-logo" />
                                        </a>
                                      </div>
                                      <div className="large-4 small-4 columns review-icons-text">QS</div>
                                      <div className="large-5 small-5 columns review-icons-rank">
                                        { collegeData.qs ? `#${collegeData.qs}` : "N/A" }
                                      </div>
                                  </div>

                                  <div className="large-12 columns no-padding marg-10bot">
                                      <div className="large-2 small-2 columns">
                                        <a href="http://www.shanghairanking.com/ARWU2016.html" target="_blank">
                                          <img className="rank-logo-resize" src="/images/ranking/new_ranking_logos/shanghai.png" alt="ranking-logo" />
                                        </a>
                                      </div>
                                      <div className="large-4 small-4 columns review-icons-text">SHANGHAI</div>
                                      <div className="large-5 small-5 columns review-icons-rank">
                                          { collegeData.shanghai_academic ? `#${collegeData.shanghai_academic}` : "N/A" }
                                      </div>
                                  </div>
                              </div>

                              <div className="large-6 medium-12 small-12 columns">
                                <div className="rank-title-oneplace mt10">COLLEGE RANKING IN ONE PLACE</div>
                                  <div className="rank-video-img">
                                      <div className="row">
                                          <div className="column text-center">
                                              <img style={{cursor: 'pointer'}} src="/images/video-images/rankings-video.png" alt="Plexuss Ranking Video" onClick={this.handleOpenModal} />
                                              {
                                                this.state.shouldOpenModal &&
                                                  <Modal isOpen={this.state.shouldOpenModal} className='recruitment-modal ranking-video-modal'>
                                                    <div className="close-modal-cont">
                                                      <span className="close-reveal-modal closer_sec" onClick={this.handleCloseModal}>&#215;</span>
                                                    </div>
                                                    <iframe width="100%" height="100%" src="https://www.youtube.com/embed/O73eOnoTtPE?version=3&amp;showinfo=0&amp;controls=1&amp;rel=0" frameBorder="0" allowFullScreen></iframe>
                                                  </Modal>
                                              }
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                    </div>
                  </div>
                </div>
              <Masonry>
                <div className="row generated-ranking-pins-container">
                  <div className="column small-12 medium-6 large-4 no-padding-left">
                  {
                    !!collegeData.ranking_pins_col_one && !!collegeData.ranking_pins_col_one.length &&
                      collegeData.ranking_pins_col_one.map((pin, index) => (
                        <div className="row" key={index}>
                          <div className="column small-12 no-padding-left">
                              <div className="pin-inner-container">
                                  <div className="row pin-title">
                                      <div className="column small-12 text-center">
                                          { pin.title }
                                      </div>
                                  </div>

                                  <div className="row pin-content div-gray-background">
                                      <div className="column small-6 pin-rank small-text-center large-text-left">
                                          <div>RANKED</div>
                                          <div>{ pin.rank_num ? `#${pin.rank_num}` : 'N/A' }</div>
                                      </div>
                                      <div className="column small-6 small-text-left large-text-center pin-img">
                                      { !!pin.image && <img src={`https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/${pin.image}`} alt="Ranking Pin Image" /> }
                                      </div>
                                      <div className="column small-12 pin-descript">
                                        {
                                          !!pin.rank_descript &&
                                            <div className="descript-container">
                                              { pin.rank_descript.length > 80
                                                ? !!this.state.rankingPin1[index] ? pin.rank_descript : pin.rank_descript.substring(0, 80) + '...'
                                                : pin.rank_descript
                                              }
                                              {
                                                pin.rank_descript.length > 80 &&
                                                  <div className="column small-12 text-center see-more-container">
                                                    <span className="see-more-pin-descript-btn" onClick={ this.handleDescriptionClick.bind(this, 1, index) }>{ !!this.state.rankingPin1[index] ? 'Show less' : 'Show more' }</span>
                                                  </div>
                                              }
                                            </div>
                                        }
                                        </div>
                                      {
                                        !!pin.source && <div className="column small-12 see-full-rank-article">
                                          <a href={pin.source} target="_blank" className="see-full-rank-article-btn">See full article</a>
                                        </div>
                                      }
                                  </div>
                              </div>
                          </div>
                        </div>
                      ))
                  }
                  </div>

                  <div className="column small-12 medium-6 large-4 no-padding-middle">
                  {
                    !!collegeData.ranking_pins_col_two && !!collegeData.ranking_pins_col_two.length &&
                      collegeData.ranking_pins_col_two.map((pin, index) => (
                        <div className="row" key={index}>
                          <div className="column small-12 no-padding-middle">
                              <div className="pin-inner-container">
                                  <div className="row pin-title">
                                      <div className="column small-12 text-center">
                                          { pin.title }
                                      </div>
                                  </div>

                                  <div className="row pin-content">
                                      <div className="column small-6 pin-rank small-text-center large-text-left">
                                          <div>RANKED</div>
                                          <div>{ pin.rank_num ? `#${pin.rank_num}` : 'N/A' }</div>
                                      </div>
                                      <div className="column small-6 small-text-left large-text-center pin-img">
                                      { !!pin.image && <img src={`https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/${pin.image}`} alt="Ranking Pin Image" /> }
                                      </div>
                                      <div className="column small-12 pin-descript see-more-container">
                                        {
                                          !!pin.rank_descript &&
                                            <div className="descript-container">
                                              { pin.rank_descript.length > 80
                                                ? !!this.state.rankingPin2[index] ? pin.rank_descript : pin.rank_descript.substring(0, 80) + '...'
                                                : pin.rank_descript
                                              }
                                              {
                                                pin.rank_descript.length > 80 &&
                                                  <div className="column small-12 text-center">
                                                    <span className="see-more-pin-descript-btn" onClick={ this.handleDescriptionClick.bind(this, 2, index) }>{ !!this.state.rankingPin2[index] ? 'Show less' : 'Show more' }</span>
                                                  </div>
                                              }
                                            </div>
                                        }
                                        </div>
                                      {
                                        !!pin.source && <div className="column small-12 see-full-rank-article">
                                          <a href={pin.source} target="_blank" className="see-full-rank-article-btn">See full article</a>
                                        </div>
                                      }
                                  </div>
                              </div>
                          </div>
                        </div>
                      ))
                  }
                  </div>

                  <div className="column small-12 medium-6 large-4 no-padding-right">
                  {
                    !!collegeData.ranking_pins_col_three && !!collegeData.ranking_pins_col_three.length &&
                      collegeData.ranking_pins_col_three.map((pin, index) => (
                        <div className="row" key={index}>
                          <div className="column small-12 no-padding-right">
                              <div className="pin-inner-container">
                                  <div className="row pin-title">
                                      <div className="column small-12 text-center">
                                          { pin.title }
                                      </div>
                                  </div>

                                  <div className="row pin-content">
                                      <div className="column small-6 pin-rank small-text-center large-text-left">
                                          <div>RANKED</div>
                                          <div>{ pin.rank_num ? `#${pin.rank_num}` : 'N/A' }</div>
                                      </div>
                                      <div className="column small-6 small-text-left large-text-center pin-img">
                                      { !!pin.image && <img src={`https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/${pin.image}`} alt="Ranking Pin Image" /> }
                                      </div>
                                      <div className="column small-12 pin-descript see-more-container">
                                        {
                                          !!pin.rank_descript &&
                                            <div className="descript-container">
                                              { pin.rank_descript.length > 80
                                                ? !!this.state.rankingPin3[index] ? pin.rank_descript : pin.rank_descript.substring(0, 80) + '...'
                                                : pin.rank_descript
                                              }
                                              {
                                                pin.rank_descript.length > 80 &&
                                                  <div className="column small-12 text-center">
                                                    <span className="see-more-pin-descript-btn" onClick={ this.handleDescriptionClick.bind(this, 3, index) }>{ !!this.state.rankingPin3[index] ? 'Show less' : 'Show more' }</span>
                                                  </div>
                                              }
                                            </div>
                                        }
                                        </div>
                                      {
                                        !!pin.source && <div className="column small-12 see-full-rank-article">
                                          <a href={pin.source} target="_blank" className="see-full-rank-article-btn">See full article</a>
                                        </div>
                                      }
                                  </div>
                              </div>
                          </div>
                        </div>
                      ))
                  }
                </div>
              </div>
            </Masonry>
          </div>
      }
      </div>
    )
  }
}

const mapStateToProps = (state) => {
  return {
    ranking: state.search.ranking,
    isFetching: state.search.isFetchingCollegeSubPage,
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    getCollegeRanking: (slug) => { dispatch(getCollegeRanking(slug)) },
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(withRouter(RankingPage));
