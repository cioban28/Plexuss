import React, { Component } from 'react';
import { connect } from 'react-redux';
import './styles/stats.scss';
import './styles/admissions.scss';
import { getCollegeTuitions } from '../../../api/search';
import { withRouter } from 'react-router-dom';


class TuitionPage extends Component {

  componentDidMount() {
    !Object.entries(this.props.tuitions).length && this.props.getCollegeTuitions(this.props.match.params.slug);
  }

  render() {
    const collegeData = this.props.tuitions;
    // console.log(collegeData);
    const numberWithCommas = (n) => !!n ? '$' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : 'N/A';

    const renderStats = (n) => !!n ? numberWithCommas(n) : 'N/A';

    return (
      <div>
      {!!this.props.isFetching && <div className="college-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>}
      {
        !!Object.entries(collegeData).length &&
          <div id='tuition-page' className="row " style={{border: 'solid 0px #ff0000'}}>
            <div className="column small-12 no-padding">
              <div style={{display: 'block'}}>
                <div className="tuition-first-content">
                    <div className="large-12 columns">
                    <div className="large-5 columns college-rank-divide coll-tuition-cost-side">
                      <div className="row value-first-contentLeftHead">
                        <div className="small-12 column">
                          WHAT WILL IT COST?
                        </div>
                      </div>
                      {
                        !!collegeData.custom_tuition && !!collegeData.custom_tuition.length
                          ? collegeData.custom_tuition.map((customTuition, index)=> (
                              <div key={index}>
                                <div className="row">
                                  <div className="small-12 column cross-color-platform">
                                    { customTuition.cct_title || '' }
                                  </div>
                                </div>
                                <div className="row">
                                  <div className="small-12 column value-money-first">
                                    { customTuition.cct_currency || '' }{ numberWithCommas(customTuition.cct_amount) }
                                  </div>
                                </div>
                                {
                                  !!customTuition.cct_sub_title &&
                                    <div className='row'>
                                      <div className='small-12 column'style={{fontSize: '0.4em', color: '#fff', fontStyle: 'italic', marginBottom: '3.2em' }}>
                                        { customTuition.cct_sub_title || '' }
                                      </div>
                                    </div>
                                }
                                </div>
                            ))
                          : <div>
                              <div className='row'>
                                <div className='small-12 column cross-color-platform'>
                                  In State Tuition
                                </div>
                              </div>
                              <div className='row'>
                                <div className="small-12 column value-money-first">
                                { renderStats(collegeData.tuition_avg_in_state_ftug) }
                                </div>
                              </div>
                              <div className='row'>
                                <div className="small-12 column cross-color-platform">
                                  In State Full Expense
                                </div>
                              </div>
                              <div className='row'>
                                <div className="small-12 column value-money-first">
                                { renderStats(collegeData.total_inexpenses) }
                                </div>
                              </div>
                              <div className='row'>
                                <div className="small-12 column cross-color-platform">
                                  Out of State Tuition
                                </div>
                              </div>
                              <div className='row'>
                                <div className="small-12 column value-money-first">
                                { renderStats(collegeData.tuition_avg_out_state_ftug) }
                                </div>
                              </div>
                              <div className='row'>
                                <div className="small-12 column cross-color-platform">
                                  Out of State Full Expense
                                </div>
                              </div>
                              <div className='row'>
                                <div className="small-12 column value-money-first">
                                { renderStats(collegeData.total_outexpenses) }
                                </div>
                              </div>
                            </div>
                      }
                    </div>
                    {
                      !!collegeData.youtube_tuition_videos && !!collegeData.youtube_tuition_videos.length
                        ? <div className="large-7 column yt-vid-tuition">
                          {
                            collegeData.youtube_tuition_videos.map((vid, index) => (
                              <iframe key={index} width="100%" height="280" src={`https://www.youtube.com/embed/${vid['video_id']}`} style={{border: 'none'}} allowFullScreen></iframe>
                            ))
                          }
                          </div>
                        : <div className="large-7 columns col-tempImg">
                            <img src="/images/colleges/stats-top-content.jpg" alt="" />
                          </div>
                    }
                  </div>
                  <div className="mt10 row">
                  <div className="custom-one-col-box column medium-4 col-tuition-campus-box-left" id="tution-on-campus">
                    <div className="tuition-boxes">
                        <div className="tuition-head-img" style={{ backgroundImage: 'url(/images/colleges/on-campus-box-img.png)', backgroundSize: '100%', backgroundRepeat: 'no-repeat' }}>
                            <div className="impact-title"></div>
                            <div className="tuition-campus-title">ON CAMPUS</div>
                            <div className="title-head-icon"><img src="/images/colleges/on-campus-box-icon.png" alt="" /> </div>
                        </div>
                        <div className="tuition-content">
                            <div className="expenses-header" style={{color: '#168F3A'}}>IN STATE</div>
                            <div className="large-12 columns tution-inner-content">
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Tuition</div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.tuition_avg_in_state_ftug) }</div>
                                </div>
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Books &amp; Supplies:</div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.books_supplies_1213) }</div>
                                </div>
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Room &amp; Board: </div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.room_board_on_campus_1213) }</div>
                                </div>
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Other: </div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.other_expenses_on_campus_1213) }</div>
                                </div>
                            </div>
                        </div>
                        <div className="tuition-total-expense row" style={{color: '#168F3A'}}>
                          <div className="small-7 medium-6 large-7 column text-left exp-outofpocket-total-label">Total Expenses:</div>
                          <div className="small-5 medium-6 large-5 column exp-outofpocket-total-fontsize">{ numberWithCommas(collegeData.total_inexpenses) }</div>
                        </div>
                        <div className="tuition-content">
                            <div className="expenses-header" style={{color: '#005977'}}>OUT OF STATE</div>
                            <div className="large-12 columns tution-inner-content">
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Tuition</div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.tuition_avg_out_state_ftug) }</div>
                                </div>
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Books &amp; Supplies:</div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.books_supplies_1213) }</div>
                                </div>
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Room &amp; Board: </div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.room_board_on_campus_1213) }</div>
                                </div>
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Other: </div>
                                    <div className="large-6 small-6 columns no-padding text-center">{renderStats(collegeData.other_expenses_on_campus_1213)}</div>
                                </div>
                            </div>
                        </div>
                        <div className="tuition-total-expense row" style={{color: '#005977'}}>
                          <div className="small-7 medium-6 large-7 column text-left exp-outofpocket-total-label">Total Expenses:</div>
                          <div className="small-5 medium-6 large-5 column exp-outofpocket-total-fontsize">{ numberWithCommas(collegeData.total_outexpenses) }</div>
                        </div>
                    </div>
                  </div>
                      <div className="custom-one-col-box column medium-4 no-padding-middle" id="tution-off-campus">
                    <div className="tuition-boxes">
                        <div className="tuition-head-img" style={{backgroundImage: 'url(/images/colleges/off-campus-box-img.png)', backgroundSize: '100%', backgroundRepeat: 'no-repeat'}}>
                            <div className="impact-title"></div>
                            <div className="tuition-campus-title">OFF CAMPUS</div>
                            <div className="title-head-icon"><img src="/images/colleges/off-campus-box-icon.png" alt="" /> </div>
                        </div>
                        <div className="tuition-content">
                            <div className="expenses-header" style={{color: '#1DB151'}}>IN STATE</div>
                            <div className="large-12 columns tution-inner-content">
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Tuition</div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.tuition_avg_in_state_ftug) }</div>
                                </div>
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Books &amp; Supplies:</div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.books_supplies_1213) }</div>
                                </div>
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Room &amp; Board: </div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.room_board_off_campus_nofam_1213) }</div>
                                </div>
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Other: </div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.other_expenses_off_campus_nofam_1213) }</div>
                                </div>
                            </div>
                        </div>
                        <div className="tuition-total-expense row" style={{color: '#1DB151'}}>
                          <div className="small-7 medium-6 large-7 column text-left exp-outofpocket-total-label">Total Expenses:</div>
                          <div className="small-5 medium-6 large-5 column exp-outofpocket-total-fontsize">{ numberWithCommas(collegeData.total_off_inexpenses) }</div>
                        </div>
                        <div className="tuition-content">
                            <div className="expenses-header" style={{color: '#04A5AD'}}>OUT OF STATE</div>
                            <div className="large-12 columns tution-inner-content">
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Tuition</div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.tuition_avg_out_state_ftug) }</div>
                                </div>
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Books &amp; Supplies:</div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.books_supplies_1213) }</div>
                                </div>
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Room &amp; Board: </div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.room_board_off_campus_nofam_1213) }</div>
                                </div>
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Other: </div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.other_expenses_off_campus_nofam_1213) }</div>
                                </div>
                            </div>
                        </div>
                        <div className="tuition-total-expense row" style={{color: '#04A5AD'}}>
                          <div className="small-7 medium-6 large-7 column text-left exp-outofpocket-total-label">Total Expenses:</div>
                          <div className="small-5 medium-6 large-5 column exp-outofpocket-total-fontsize">{ numberWithCommas(collegeData.total_off_outexpenses) }</div>
                        </div>
                    </div>
                      </div>
                      <div className="custom-one-col-box column medium-4 col-tuition-campus-box-right no-padding-right" id="tution-home-campus">
                    <div className="tuition-boxes">
                        <div className="tuition-head-img" style={{backgroundImage: 'url(/images/colleges/stay-home-box-img.png)', backgroundSize: '100%', backgroundRepeat: 'no-repeat'}}>
                            <div className="impact-title"></div>
                            <div className="tuition-campus-title">STAY HOME</div>
                            <div className="title-head-icon"><img src="/images/colleges/stay-home-box-icon.png" alt="" /> </div>
                        </div>
                        <div className="tuition-content">
                            <div className="expenses-header" style={{color: '#A0DB39'}}>IN STATE</div>
                            <div className="large-12 columns tution-inner-content">
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Tuition</div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.tuition_avg_in_state_ftug) }</div>
                                </div>
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Books &amp; Supplies:</div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.books_supplies_1213) }</div>
                                </div>
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Room &amp; Board: </div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.room_board_off_campus_nofam_1213) }</div>
                                </div>
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Other: </div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.other_expenses_off_campus_yesfam_1213) }</div>
                                </div>
                            </div>
                        </div>
                        <div className="tuition-total-expense row" style={{color: '#A0DB39'}}>
                          <div className="small-7 medium-6 large-7 column text-left exp-outofpocket-total-label">Total Expenses:</div>
                          <div className="small-5 medium-6 large-5 column exp-outofpocket-total-fontsize">{ numberWithCommas(collegeData.total_home_inexpenses) }</div>
                        </div>
                        <div className="tuition-content">
                            <div className="expenses-header" style={{color: '#05CED3'}}>OUT OF STATE</div>
                            <div className="large-12 columns tution-inner-content">
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Tuition</div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.tuition_avg_out_state_ftug) }</div>
                                </div>
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Books &amp; Supplies:</div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.books_supplies_1213) }</div>
                                </div>
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Room &amp; Board: </div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.room_board_off_campus_nofam_1213) }</div>
                                </div>
                                <div className="row">
                                    <div className="large-6 small-6 columns no-padding">Other: </div>
                                    <div className="large-6 small-6 columns no-padding text-center">{ numberWithCommas(collegeData.other_expenses_off_campus_nofam_1213) }</div>
                                </div>
                            </div>
                        </div>
                        <div className="tuition-total-expense row" style={{color: '#05CED3'}}>
                          <div className="small-7 medium-6 large-7 column text-left exp-outofpocket-total-label">Total Expenses:</div>
                          <div className="small-5 medium-6 large-5 column exp-outofpocket-total-fontsize">{ numberWithCommas(collegeData.total_home_outexpenses) }</div>
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
    tuitions: state.search.tuitions,
    isFetching: state.search.isFetchingCollegeSubPage,
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    getCollegeTuitions: (slug) => { dispatch(getCollegeTuitions(slug)) }
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(withRouter(TuitionPage));
