import React, { Component } from 'react';
import { connect } from 'react-redux';
import './styles/stats.scss';
import './styles/admissions.scss';
import { getCollegeFianancialAid } from '../../../api/search';
import { withRouter } from 'react-router-dom';


class FinancialAidPage extends Component {
  componentDidMount() {
    !Object.entries(this.props.financialAid).length && this.props.getCollegeFianancialAid(this.props.match.params.slug);
  }

  render() {
    const collegeData = this.props.financialAid;

    const numberWithCommas = (n) => n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

    const renderStats = (n) => !!n ? numberWithCommas(n) : 'N/A';

    const renderValueOrNA = (n) => !!n ? n + '%' : 'N/A'

    return (
      <div>
      {!!this.props.isFetching && <div className="college-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>}
      {
        !this.props.isFetching && !!Object.entries(collegeData).length &&
          <div id='financial-aid-page'>
            <div className="row" style={{border: 'solid 0px #ff0000'}}>
                <div className="column small-12 no-padding">
                    <div style={{display: 'block'}}>
                      <div className="financial-panel-content">
                          <div className="large-12 columns no-padding">
                              <div className="large-5 columns college-rank-divide">
                                  <div className="row value-first-contentLeftHead">
                              <div className="column">
                                FINANCIAL AID
                              </div>
                                    </div>
                                    <div className="row cross-color-platform bold-font">
                              <div className="small-12 column">
                                GRANT OR SCHOLARSHIP AID
                              </div>
                                    </div>
                                    <div className="row">
                                      <div className="large-8 small-7 columns financial-label-tpanel">
                                            Students who received aid:
                                        </div>
                                        <div className="large-4 small-5 columns value-money-financial no-padding">
                                        { renderValueOrNA(collegeData.undergrad_grant_pct) }
                                        </div>
                                    </div>
                                    <div className="row">
                                      <div className="large-8 small-7 columns financial-label-tpanel">
                                            Avg. Financial aid given:
                                        </div>
                                        <div className="large-4 small-5 columns value-money-financial no-padding">
                                        { '$'+renderStats(collegeData.undergrad_grant_avg_amt) }
                                        </div>
                                    </div>
                                    <div className="row cross-color-platform bold-font">
                              <div className="small-12 column">
                                FEDERAL STUDENT LOANS
                              </div>
                                    </div>

                                    <div className="row">
                                      <div className="large-8 small-7 columns financial-label-tpanel">
                                            Students who received aid:
                                        </div>
                                        <div className="large-4 small-5 columns value-money-financial no-padding">
                                        { renderValueOrNA(collegeData.undergrad_loan_pct) }
                                        </div>
                                    </div>
                                    <div className="row">
                                      <div className="large-8 small-7 columns financial-label-tpanel">
                                            Avg. Financial aid given:
                                        </div>
                                        <div className="large-4 small-5 columns value-money-financial no-padding">
                                        { '$' + renderStats(collegeData.undergrad_loan_avg_amt) }
                                        </div>
                                    </div>

                                    <div className="row cross-color-platform bold-font">
                              <div className="small-12 column">
                                Out of State Tuition
                              </div>
                                    </div>
                                    <div className="row">
                                      <div className="large-8 small-7 columns financial-label-tpanel">
                                            Avg. Financial aid given:
                                        </div>
                                        <div className="large-4 small-5 columns value-money-financial no-padding">
                                        { !!collegeData.undergrad_aid_avg_amt ? '$' + numberWithCommas(collegeData.undergrad_aid_avg_amt + collegeData.undergrad_loan_avg_amt) : 'N/A' }
                                        </div>
                                    </div>
                                </div>
                                {
                                  !!collegeData.youtube_financial_videos && !!collegeData.youtube_financial_videos.length
                                    ? <div className="large-7 column yt-vid-financial">
                                      {
                                        collegeData.youtube_financial_videos.map((vid, index) => (
                                          <iframe width="100%" height="280" key={index} src={`https://www.youtube.com/embed/${vid['video_id']}`} style={{border: 'none'}} allowFullScreen></iframe>
                                        ))
                                      }
                                      </div>
                                    : <div className="large-7 columns no-padding">
                                        <img className="coll-enroll-tempImg" src="/images/colleges/stats-top-content.jpg" alt="" />
                                      </div>
                                }
                        </div>
                      </div>

                        <div className="mt10">
                          <div className="row">
                                <div className="custom-4 mb5">
                                    <div id="avg-cost-on-campus">
                                        <div className="tuition-boxes">
                                            <div className="tuition-head-img" style={{ backgroundImage: 'url(/images/colleges/on-campus-box-img.png)', backgroundSize: '100%', backgroundRepeat: 'no-repeat'}}>
                                                <div className="impact-title"></div>
                                                <div className="financial-top-title">AVG COST AFTER AID</div>
                                                <div className="financial-campus-title">ON CAMPUS</div>
                                                <div className="title-head-icon"><img src="/images/colleges/on-campus-box-icon.png" alt="" /> </div>
                                            </div>
                                            <div className="tuition-content">
                                                <div className="expenses-header" style={{color: '#158E39'}}>IN STATE</div>
                                                <div className="large-12 columns tution-inner-content fs11">
                                                    <div className="row">
                                                        <div className="large-8 small-6 columns no-padding">Total Expense:</div>
                                                        <div className="large-4 small-6 columns no-padding text-center">${ renderStats(collegeData.total_inexpenses) }</div>
                                                    </div>
                                                    <div className="row">
                                                        <div className="large-8 small-6 columns no-padding">Avg. Grant or Scholarship Aid:</div>
                                                        <div className="large-4 small-6 columns no-padding text-center">${ renderStats(collegeData.undergrad_grant_avg_amt) }</div>
                                                    </div>
                                                    <div className="row">
                                                        <div className="large-8 small-6 columns no-padding">Avg. Federal Student Loans:  </div>
                                                        <div className="large-4 small-6 columns no-padding text-center">${ renderStats(collegeData.undergrad_loan_avg_amt) }</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="tuition-total-expense row" style={{color: '#168F3A'}}>
                                                <div className="column small-6 medium-6 large-7 text-left exp-outofpocket-total-label">
                                                    Out of pocket:
                                                </div>
                                                <div className="column small-6 medium-6 large-5 exp-outofpocket-total-fontsize">
                                                {
                                                  parseInt(collegeData.total_incamp_financial) < 0
                                                    ? '-$' + renderStats(parseInt(collegeData.total_incamp_financial)*-1)
                                                    : '$' + renderStats(collegeData.total_incamp_financial)
                                                }
                                                </div>
                                            </div>
                                            <div className="tuition-content">
                                                <div className="expenses-header" style={{color: '#005977'}}>OUT OF STATE</div>
                                                <div className="large-12 columns tution-inner-content fs11">
                                                    <div className="row">
                                                        <div className="large-8 small-6 columns no-padding">Total Expense:</div>
                                                        <div className="large-4 small-6 columns no-padding text-center">${ renderStats(collegeData.total_outexpenses) }</div>
                                                    </div>
                                                    <div className="row">
                                                        <div className="large-8 small-6 columns no-padding">Avg. Grant or Scholarship Aid:</div>
                                                        <div className="large-4 small-6 columns no-padding text-center">${ renderStats(collegeData.undergrad_grant_avg_amt) }</div>
                                                    </div>
                                                    <div className="row">
                                                        <div className="large-8 small-6 columns no-padding">Avg. Federal Student Loans:  </div>
                                                        <div className="large-4 small-6 columns no-padding text-center">${ renderStats(collegeData.undergrad_loan_avg_amt) }</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="tuition-total-expense row" style={{color: '#004358'}}>
                                                <div className="column small-6 medium-6 large-7 text-left exp-outofpocket-total-label">Out of pocket:</div>
                                                <div className="column small-6 medium-6 large-5 exp-outofpocket-total-fontsize">
                                                {
                                                  parseInt(collegeData.total_outcamp_financial) < 0
                                                    ? '-$' + renderStats(parseInt(collegeData.total_outcamp_financial)*-1)
                                                    : '$' + renderStats(collegeData.total_outcamp_financial)
                                                }
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="custom-4 mb5">
                                    <div id="avg-cost-off-campus">
                                        <div className="tuition-boxes">
                                            <div className="tuition-head-img" style={{backgroundImage: 'url(/images/colleges/off-campus-box-img.png)', backgroundSize: '100%', backgroundRepeat: 'no-repeat'}}>
                                                <div className="impact-title"></div>
                                                <div className="financial-top-title">AVG COST AFTER AID</div>
                                                <div className="financial-campus-title">OFF CAMPUS</div>
                                                <div className="title-head-icon"><img src="/images/colleges/off-campus-box-icon.png" alt="" /> </div>
                                            </div>
                                            <div className="tuition-content">
                                                <div className="expenses-header" style={{color: '#1DB151'}}>IN STATE</div>
                                                <div className="large-12 columns tution-inner-content fs11">
                                                    <div className="row">
                                                        <div className="large-8 small-6 columns no-padding">Total Expense:</div>
                                                        <div className="large-4 small-6 columns no-padding text-center">${ renderStats(collegeData.total_off_inexpenses) }</div>
                                                    </div>
                                                    <div className="row">
                                                        <div className="large-8 small-6 columns no-padding">Avg. Grant or Scholarship Aid:</div>
                                                        <div className="large-4 small-6 columns no-padding text-center">${ renderStats(collegeData.undergrad_grant_avg_amt) }</div>
                                                    </div>
                                                    <div className="row">
                                                        <div className="large-8 small-6 columns no-padding">Avg. Federal Student Loans:  </div>
                                                        <div className="large-4 small-6 columns no-padding text-center">${ renderStats(collegeData.undergrad_loan_avg_amt) }</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="tuition-total-expense row" style={{color: '#168F3A'}}>
                                                <div className="column small-6 medium-6 large-7 text-left exp-outofpocket-total-label">
                                                    Out of pocket:
                                                </div>
                                                <div className="column small-6 medium-6 large-5 exp-outofpocket-total-fontsize">
                                                {
                                                  parseInt(collegeData.total_off_outcamp_infinancial) < 0
                                                    ? '-$' + renderStats(parseInt(collegeData.total_off_outcamp_infinancial)*-1)
                                                    : '$' + renderStats(collegeData.total_off_outcamp_infinancial)
                                                }
                                                </div>
                                            </div>
                                            <div className="tuition-content">
                                                <div className="expenses-header" style={{color: '#04A5AD'}}>OUT OF STATE</div>
                                                <div className="large-12 columns tution-inner-content fs11">
                                                    <div className="row">
                                                        <div className="large-8 small-6 columns no-padding">Total Expense:</div>
                                                        <div className="large-4 small-6 columns no-padding text-center">${ renderStats(collegeData.total_off_outexpenses) }</div>
                                                    </div>
                                                    <div className="row">
                                                        <div className="large-8 small-6 columns no-padding">Avg. Grant or Scholarship Aid:</div>
                                                        <div className="large-4 small-6 columns no-padding text-center">${ renderStats(collegeData.undergrad_grant_avg_amt) }</div>
                                                    </div>
                                                    <div className="row">
                                                        <div className="large-8 small-6 columns no-padding">Avg. Federal Student Loans:  </div>
                                                        <div className="large-4 small-6 columns no-padding text-center">${ renderStats(collegeData.undergrad_loan_avg_amt) }</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="tuition-total-expense row" style={{color: '#004358'}}>
                                                <div className="column small-6 medium-6 large-7 text-left exp-outofpocket-total-label">Out of pocket:</div>
                                                <div className="column small-6 medium-6 large-5 exp-outofpocket-total-fontsize">
                                                {
                                                  parseInt(collegeData.total_off_outcamp_outfinancial) < 0
                                                    ? '-$' + renderStats(parseInt(collegeData.total_off_outcamp_outfinancial)*-1)
                                                    : '$' + renderStats(collegeData.total_off_outcamp_outfinancial)
                                                }
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="custom-4 mb5 no-margin">
                                    <div id="avg-cost-stay-home">
                                        <div className="tuition-boxes">
                                            <div className="tuition-head-img" style={{backgroundImage: 'url(/images/colleges/stay-home-box-img.png)', backgroundSize: '100%', backgroundRepeat: 'no-repeat'}}>
                                                <div className="impact-title"></div>
                                                <div className="financial-top-title">AVG COST AFTER AID</div>
                                                <div className="financial-campus-title">STAY HOME</div>
                                                <div className="title-head-icon"><img src="/images/colleges/stay-home-box-icon.png" alt="" /> </div>
                                            </div>
                                            <div className="tuition-content">
                                                <div className="expenses-header" style={{color: '#A0DB39'}}>IN STATE</div>
                                                <div className="large-12 columns tution-inner-content fs11">
                                                    <div className="row">
                                                        <div className="large-8 small-6 columns no-padding">Total Expense:</div>
                                                        <div className="large-4 small-6 columns no-padding text-center">${ renderStats(collegeData.total_home_inexpenses) }</div>
                                                    </div>
                                                    <div className="row">
                                                        <div className="large-8 small-6 columns no-padding">Avg. Grant or Scholarship Aid:</div>
                                                        <div className="large-4 small-6 columns no-padding text-center">${ renderStats(collegeData.undergrad_grant_avg_amt) }</div>
                                                    </div>
                                                    <div className="row">
                                                        <div className="large-8 small-6 columns no-padding">Avg. Federal Student Loans:  </div>
                                                        <div className="large-4 small-6 columns no-padding text-center">${ renderStats(collegeData.undergrad_loan_avg_amt) }</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="tuition-total-expense row" style={{color: '#168F3A'}}>
                                                <div className="column small-6 medium-6 large-7 text-left exp-outofpocket-total-label">
                                                    Out of pocket:
                                                </div>
                                                <div className="column small-6 medium-6 large-5 exp-outofpocket-total-fontsize">
                                                {
                                                  parseInt(collegeData.total_home_infinancial) < 0
                                                    ? '-$' + renderStats(parseInt(collegeData.total_home_infinancial)*-1)
                                                    : '$' + renderStats(collegeData.total_home_infinancial)
                                                }
                                                </div>
                                            </div>
                                            <div className="tuition-content">
                                                <div className="expenses-header" style={{color: '#05CED3'}}>OUT OF STATE</div>
                                                <div className="large-12 columns tution-inner-content fs11">
                                                    <div className="row">
                                                        <div className="large-8 small-6 columns no-padding">Total Expense:</div>
                                                        <div className="large-4 small-6 columns no-padding text-center">${ renderStats(collegeData.total_home_outexpenses) }</div>
                                                    </div>
                                                    <div className="row">
                                                        <div className="large-8 small-6 columns no-padding">Avg. Grant or Scholarship Aid:</div>
                                                        <div className="large-4 small-6 columns no-padding text-center">${ renderStats(collegeData.undergrad_grant_avg_amt) }</div>
                                                    </div>
                                                    <div className="row">
                                                        <div className="large-8 small-6 columns no-padding">Avg. Federal Student Loans:  </div>
                                                        <div className="large-4 small-6 columns no-padding text-center">${ renderStats(collegeData.undergrad_loan_avg_amt) }</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="tuition-total-expense row" style={{color: '#004358'}}>
                                                <div className="small-6 medium-6 large-7 column text-left exp-outofpocket-total-label">Out of pocket:</div>
                                                <div className="small-6 medium-6 large-5 column total exp-outofpocket-total-fontsize">
                                                {
                                                  parseInt(collegeData.total_home_outfinancial) < 0
                                                    ? '-$' + renderStats(parseInt(collegeData.total_home_outfinancial)*-1)
                                                    : '$' + renderStats(collegeData.total_home_outfinancial)
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
            </div>
          </div>
      }
      </div>
    )
  }
}

const mapStateToProps = (state) => {
  return {
    financialAid: state.search.financialAid,
    isFetching: state.search.isFetchingCollegeSubPage,
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    getCollegeFianancialAid: (slug) => { dispatch(getCollegeFianancialAid(slug)) },
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(withRouter(FinancialAidPage));
