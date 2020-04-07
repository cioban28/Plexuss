import React, { Component } from 'react';
import { connect } from 'react-redux';
import './styles/stats.scss';
import './styles/admissions.scss';
import { getCollegeEnrollments } from '../../../api/search';
import { withRouter } from 'react-router-dom';


class EnrollmentPage extends Component {

  componentDidMount() {
    !Object.entries(this.props.enrollments).length && this.props.getCollegeEnrollments(this.props.match.params.slug);
  }

  render() {
    const collegeData = this.props.enrollments;

    const numberWithCommas = (n) =>  n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

    const renderStats = (n) => !!n ? numberWithCommas(n) : 'N/A';

    const renderRequiredOrNA = (str) => !!str ? `\'${str}\'` : 'NA';

    return (
      <div id='enrollment-page'>
      {!!this.props.isFetching && <div className="college-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>}
      {
        !this.props.isFetching && !!Object.entries(collegeData).length &&
          <div>
            <div className="row" style={{ border: 'solid 0px #ff0000' }}>
              <div className="column small-12 no-padding">
                  <div style={{display: 'block'}}>
                    <div className="row university-stats-content">
                          <div className="large-12 columns no-padding bg-ext-black radial-bdr">
                              <div className="div-gray-background large-6 columns no-padding college-rank-divide coll-enrollment-box-height">
                                  <div className="university_content_admis_deadline pl10 pb10 pt10 coll-enrollment-box-header">UNDERGRADUATE ENROLLMENT</div>
                                  <div className="detail-university-grey">TOTAL ENROLLMENT</div>
                                  <div className="detail-university-green-content">
                                    { renderStats(collegeData.undergrad_total) }
                                  </div>
                                  <div className="detail-university-grey">TRANSFER ENROLLMENT</div>
                                  <div className="detail-university-green-content">
                                    { renderStats(collegeData.undergrad_transfers_total) }
                                  </div>
                                  <div className="detail-university-grey">ATTENDANCE STATUS</div>
                                  <div className="large-12 columns detail-university-green-content">
                                      <div className="large-6 small-6 columns bdr-dot-right no-padding text-left coll-enroll-attendance">
                                      { renderStats(collegeData.undergrad_full_time_total) }
                                      <br/><span className="font-12">FULL-TIME</span></div>
                                      <div className="large-6 small-6 columns text-center coll-enroll-attendance">
                                      { renderStats(collegeData.undergrad_part_time_total) }
                                      <br/><span className="font-12">PART-TIME</span></div>
                                  </div>
                              </div>
                              {
                                !!collegeData.youtube_enrollment_videos && collegeData.youtube_enrollment_videos.length > 0
                                  ? <div className="large-6 column yt-vid-enrollment">
                                    {
                                      collegeData.youtube_enrollment_videos.map((vid, index) => (
                                        <iframe width="100%" height="280" key={index} src={`https://www.youtube.com/embed/${vid['video_id']}`} style={{border: 'none'}} allowFullScreen></iframe>
                                      ))

                                    }
                                    </div>
                                  : <div className="large-6 columns no-padding">
                                      <img className="coll-enroll-tempImg" src="/images/colleges/stats-top-content.jpg" alt="" />
                                    </div>
                              }
                          </div>
                      </div>
                  </div>

                  <div className="row pos-relative enrollment-custom-row">
                  {
                    !!collegeData.undergrad_men_total && !!collegeData.undergrad_women_total
                      ? <div>
                          <div className="custom-two-col-box bg-pure-white small-12 medium-7">
                            <div id="grad-ethnicBox">
                                  <div className="custom-two-col-box-head" style={{ backgroundImage: 'url(/images/colleges/ug-ethnic-image.png)', backgroundRepeat: 'no-repeat', height: '98px', backgroundSize: 'cover' }}>
                                      <span className="font-18">Undergrad</span><br/>Race/Ethnicity
                                  </div>
                                  <div className="enrollment-twobox-content">
                                      <div className="row mb20">
                                          <div className="small-12 columns">
                                              <ul className="ethnic-bar-graph-ul">
                                                  <div className="text-left enrollment-raceEthnicity-labels">AMERICAN INDIAN OR ALASK NATIVE</div>
                                                  <li className="horizontal-graph-cover">
                                                      <div className="horizontal-graph" style={{width: `${collegeData.aianfinalPercent}%`, background: '#000'}}></div>{ collegeData.aianfinalPercent }%
                                                  </li>
                                                  <div className="text-left enrollment-raceEthnicity-labels race-asian">ASIAN</div>
                                                  <li className="horizontal-graph-cover">
                                                      <div className="horizontal-graph" style={{width: `${ collegeData.asianfinalPercent }%`, background: '#05CCD2'}}></div>{ collegeData.asianfinalPercent }%
                                                  </li>
                                                  <div className="text-left enrollment-raceEthnicity-labels race-afriAmerican">BLACK OR AFRICAN AMERICAN</div>
                                                  <li className="horizontal-graph-cover">
                                                      <div className="horizontal-graph" style={{width: `${collegeData.bkaafinalPercent}%`, background: '#04A5AC'}}></div>{ collegeData.bkaafinalPercent }%
                                                  </li>
                                                  <div className="text-left enrollment-raceEthnicity-labels race-hispanic">HISPANIC / LATINO</div>
                                                  <li className="horizontal-graph-cover">
                                                      <div className="horizontal-graph" style={{width:`${collegeData.hispfinalPercent}%`, background: '#004358'}}></div>{ collegeData.hispfinalPercent }%
                                                  </li>
                                                  <div className="text-left enrollment-raceEthnicity-labels">NATIVE HAWAIIN / OTHER PACIFIC ISLANDER</div>
                                                  <li className="horizontal-graph-cover">
                                                      <div className="horizontal-graph" style={{width: `${collegeData.nhpifinalPercent}%`, background: '#000000'}}></div>{ collegeData.nhpifinalPercent }%
                                                  </li>
                                                  <div className="text-left enrollment-raceEthnicity-labels race-white">WHITE</div>
                                                  <li className="horizontal-graph-cover">
                                                      <div className="horizontal-graph" style={{width: `${collegeData.whitefinalPercent}%`, background: '#9FD939'}}></div>{ collegeData.whitefinalPercent }%
                                                  </li>
                                                  <div className="text-left enrollment-raceEthnicity-labels race-twoOrMore">2 OR MORE RACES</div>
                                                  <li className="horizontal-graph-cover">
                                                      <div className="horizontal-graph" style={{width: `${collegeData.twomorefinalPercent}%`, background: '#1DB151'}}></div>{ collegeData.twomorefinalPercent }%
                                                  </li>
                                                  <div className="text-left enrollment-raceEthnicity-labels">RACE / ETHNICITY UNKNOWN</div>
                                                  <li className="horizontal-graph-cover">
                                                      <div className="horizontal-graph" style={{width: `${collegeData.unknownfinalPercent}%`, background: '#000000'}}></div>{ collegeData.unknownfinalPercent }%
                                                  </li>
                                                  <div className="text-left enrollment-raceEthnicity-labels race-alien">NON-RESIDENT-ALIEN</div>
                                                  <li className="horizontal-graph-cover">
                                                      <div className="horizontal-graph" style={{width: `${collegeData.alienfinalPercent}%`, background: '#148D39'}}></div>{ collegeData.alienfinalPercent }%
                                                  </li>
                                              </ul>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                            </div>
                            <div className="small-12 medium-5 column gender-square">
                                <div className="large-12 small-12 columns no-padding mt10">
                                    <div id="undergad-comparison-box">
                                        <div className="row">
                                            <div className="large-6 small-6 column bg-men-side text-center">
                                                <img src="/images/colleges/men-figure-compare.png" alt="" />
                                                <div className="comparison-content"><span className="fs36">{ collegeData.undergrad_men_total }</span><br/>Admitted</div>
                                            </div>
                                            <div className="large-6 small-6 column  bg-women-side text-center">
                                                <img src="/images/colleges/women-figure-compare.png" alt="" />
                                                <div className="comparison-content"><span className="fs36">{ collegeData.undergrad_women_total }</span><br/>Admitted</div>
                                            </div>
                                        </div>
                                        <p className="comparison-title">
                                            <span className="font-14">UNDERGRAD</span><br/>STUDENT GENDER
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                          : <div className="custom-two-col-box bg-pure-white small-12">
                              <div id="grad-ethnicBox">
                                  <div className="custom-two-col-box-head" style={{ backgroundImage: 'url(/images/colleges/ug-ethnic-image.png)', backgroundRepeat: 'no-repeat', height: '98px', backgroundSize: 'cover' }}>
                                      <span className="font-18">Undergrad</span><br/>Race/Ethnicity
                                  </div>
                                  <div className="enrollment-twobox-content">
                                      <div className="row mb20">
                                          <div className="small-12 columns">
                                              <ul className="ethnic-bar-graph-ul">
                                                  <div className="text-left enrollment-raceEthnicity-labels">AMERICAN INDIAN OR ALASK NATIVE</div>
                                                  <li className="horizontal-graph-cover">
                                                      <div className="horizontal-graph" style={{width: `${collegeData.aianfinalPercent}%`, background: '#000'}}></div>{ collegeData.aianfinalPercent }%
                                                  </li>
                                                  <div className="text-left enrollment-raceEthnicity-labels race-asian">ASIAN</div>
                                                  <li className="horizontal-graph-cover">
                                                      <div className="horizontal-graph" style={{width: `${ collegeData.asianfinalPercent }%`, background: '#05CCD2'}}></div>{ collegeData.asianfinalPercent }%
                                                  </li>
                                                  <div className="text-left enrollment-raceEthnicity-labels race-afriAmerican">BLACK OR AFRICAN AMERICAN</div>
                                                  <li className="horizontal-graph-cover">
                                                      <div className="horizontal-graph" style={{width: `${collegeData.bkaafinalPercent}%`, background: '#04A5AC'}}></div>{ collegeData.bkaafinalPercent }%
                                                  </li>
                                                  <div className="text-left enrollment-raceEthnicity-labels race-hispanic">HISPANIC / LATINO</div>
                                                  <li className="horizontal-graph-cover">
                                                      <div className="horizontal-graph" style={{width:`${collegeData.hispfinalPercent}%`, background: '#004358'}}></div>{ collegeData.hispfinalPercent }%
                                                  </li>
                                                  <div className="text-left enrollment-raceEthnicity-labels">NATIVE HAWAIIN / OTHER PACIFIC ISLANDER</div>
                                                  <li className="horizontal-graph-cover">
                                                      <div className="horizontal-graph" style={{width: `${collegeData.nhpifinalPercent}%`, background: '#000000'}}></div>{ collegeData.nhpifinalPercent }%
                                                  </li>
                                                  <div className="text-left enrollment-raceEthnicity-labels race-white">WHITE</div>
                                                  <li className="horizontal-graph-cover">
                                                      <div className="horizontal-graph" style={{width: `${collegeData.whitefinalPercent}%`, background: '#9FD939'}}></div>{ collegeData.whitefinalPercent }%
                                                  </li>
                                                  <div className="text-left enrollment-raceEthnicity-labels race-twoOrMore">2 OR MORE RACES</div>
                                                  <li className="horizontal-graph-cover">
                                                      <div className="horizontal-graph" style={{width: `${collegeData.twomorefinalPercent}%`, background: '#1DB151'}}></div>{ collegeData.twomorefinalPercent }%
                                                  </li>
                                                  <div className="text-left enrollment-raceEthnicity-labels">RACE / ETHNICITY UNKNOWN</div>
                                                  <li className="horizontal-graph-cover">
                                                      <div className="horizontal-graph" style={{width: `${collegeData.unknownfinalPercent}%`, background: '#000000'}}></div>{ collegeData.unknownfinalPercent }%
                                                  </li>
                                                  <div className="text-left enrollment-raceEthnicity-labels race-alien">NON-RESIDENT-ALIEN</div>
                                                  <li className="horizontal-graph-cover">
                                                      <div className="horizontal-graph" style={{width: `${collegeData.alienfinalPercent}%`, background: '#148D39'}}></div>{ collegeData.alienfinalPercent }%
                                                  </li>
                                              </ul>
                                          </div>
                                      </div>
                                  </div>
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

const mapStateToProps = (state) => {
  return {
    enrollments: state.search.enrollments,
    isFetching: state.search.isFetchingCollegeSubPage,
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    getCollegeEnrollments: (slug) => { dispatch(getCollegeEnrollments(slug)) },
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(withRouter(EnrollmentPage));
