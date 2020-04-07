import React, { Component } from 'react';
import { connect } from 'react-redux';
import './styles/stats.scss';
import './styles/admissions.scss';
import { getCollegeAdmissions } from '../../../api/search';
import { withRouter } from 'react-router-dom';


class AdmissionsPage extends Component {

  componentDidMount() {
    !Object.entries(this.props.admissions).length && this.props.getCollegeAdmissions(this.props.match.params.slug);
  }

  render() {
    const collegeData = this.props.admissions;

    const numberWithCommas = (n) =>  n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

    const renderStats = (n) => !!n ? numberWithCommas(n) : 'N/A';

    const renderRequiredOrNA = (str) => !!str ? `\'${str}\'` : 'NA';

    const renderAdmissionPercentage = () => {
      if(!!collegeData && !!collegeData.admissions_total && !!collegeData.applicants_total) {
        return `${Math.round(collegeData.admissions_total/collegeData.applicants_total * 100)}%`;
      }
      return 'N/A';
    }

    return (
      <div id='admissions-page'>
      {!!this.props.isFetching && <div className="college-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>}
      {
        !this.props.isFetching && !!Object.entries(collegeData).length &&
          <div>
            <div className="row" style={{border: 'solid 0px #ff0000'}}>
              <div className="column small-12 no-padding">
                <div style={{display: 'block'}}>
                  <div className="row university-stats-content">
                        <div className="large-12 columns no-padding bg-ext-black radial-bdr">
                            <div className="large-5 columns no-padding college-rank-divide pb20 div-gray-background">
                                <div className="large-12 columns adm-first-contentLeftHead">ADMISSIONS <span className="col-admiss-undergrad-sm">(undergraduate)</span></div>
                                <div className="large-12 columns mt15 mb10 coll-admission-undergrad-info-box">
                                  <div className="adm-topleft-ques small-7 columns no-padding">
                                      APPLICATION DEADLINE
                                    </div>
                                    <div className="adm-topleft-ans small-5 columns ">
                                    { collegeData.deadline || '' }
                                    </div>
                                </div>


                                <div className="large-12 columns mt10 mb10 coll-admission-undergrad-info-box">
                                  <div className="adm-topleft-ques small-7 columns no-padding">
                                      # OF APPLICANTS
                                    </div>
                                    <div className="adm-topleft-ans small-5 columns">
                                    {
                                      renderStats(collegeData.applicants_total)
                                    }
                                    </div>
                                </div>

                                <div className="large-12 columns mt10 mb10 coll-admission-undergrad-info-box">
                                  <div className="row">
                            <div className="small-12 column no-padding">
                              <div className="row">
                                <div className="adm-topleft-ques small-7 columns no-padding">
                                  # ADMITTED
                                </div>
                                <div className="adm-topleft-ans small-5 columns">
                                {
                                  renderStats(collegeData.admissions_total)
                                }
                                </div>
                              </div>
                              <div className="row">
                                <div className="adm-topleft-ques small-7 columns no-padding">
                                  % ADMITTED
                                </div>
                                <div className="adm-topleft-ans small-5 columns">
                                {
                                  renderAdmissionPercentage()
                                  // collegeData.percentadmitted != 0 ? renderStats(collegeData.percentadmitted) : 'N/A'
                                }
                                </div>
                              </div>
                            </div>
                                    </div>
                                </div>
                                <div className="large-12 columns adm-blackbdr"></div>

                                <div className="large-12 columns mt10 mb10 coll-admission-undergrad-info-box">
                                  <div className="row">
                            <div className="small-12 column no-padding">
                              <div className="row">
                                <div className="adm-topleft-ques small-7 columns no-padding">
                                  # ADMITTED &amp; ENROLLED
                                </div>
                                <div className="adm-topleft-ans small-5 columns">
                                {
                                  renderStats(collegeData.enrolled_total)
                                }
                                </div>
                              </div>
                              <div className="row col-admiss-percentAdmit">
                                <div className="adm-topleft-ques small-7 columns no-padding">
                                  % ADMITTED &amp; ENROLLED
                                </div>
                                <div className="adm-topleft-ans small-5 columns">
                                {
                                  collegeData.per_adm_enrolled != 0 ? renderStats(collegeData.per_adm_enrolled)+'%' : 'N/A'
                                }
                                </div>
                              </div>
                            </div>
                                    </div>
                                </div>
                            </div>

                            {
                              !!collegeData.youtube_admissions_videos && collegeData.youtube_admissions_videos.length > 0
                                ? <div className="large-7 column yt-vid-admissions">
                                  {
                                    collegeData.youtube_admissions_videos.map((vid, index) => (
                                      <iframe key={index} width="100%" height="280" src={`https://www.youtube.com/embed/${vid['video_id']}`} style={{border: 'none'}} allowFullScreen></iframe>
                                    ))
                                  }
                                  </div>
                                : <div className="large-7 columns no-padding">
                                    <img className="coll-enroll-tempImg" src="/images/colleges/stats-top-content.jpg" alt="" />
                                  </div>
                            }
                        </div>
                    </div>
                </div>
              </div>
            </div>
            <div className="custom-row col-admiss-cust-row">

                <div className="large-4 column no-padding-left">

                    <div className="large-12 columns no-padding">
                        <div className="row" id="application-info-box">
                    <div className="adm-InfoBox" style={{background: '#FFFFFF'}}>
                        <div className="adm-infobox-title">APPLICATION INFO</div>
                        <div className="adm-infobox-ques">OPEN ADMISSIONS:</div>
                        <div className="adm-infobox-ans">{ collegeData.open_admissions || 'NA' }</div>

                        <div className="adm-infobox-ques">COMMON APPLICATION:</div>
                        <div className="adm-infobox-ans">{ collegeData.common_app || 'No' }</div>

                        <div className="adm-infobox-ques">APPLICATION FEE:</div>
                        <div className="adm-infobox-ans">{ collegeData.application_fee_undergrad || 'NA' }</div>

                        <div className="adm-infobox-weblink">
                        {
                          !!collegeData.application_url && !!collegeData.application_url.trim() &&
                            <div className="row">
                              <div className="column small-12">
                                <a href={ collegeData.application_url || '#' } className="admission_app_link" target="_blank">
                                > APPLICATION LINK
                                </a>
                              </div>
                            </div>
                        }
                        </div>
                    </div>
                        </div>
                    </div>
                    <br/>

                    {
                      !!collegeData.admissions_men && !!collegeData.admissions_women &&
                        <div className="large-12 small-12 columns no-padding mt10 mb10">
                          <div id="undergad-comparison-box">
                            <div className="row">
                                <div className="large-6 small-6 column bg-men-side text-center">
                                    <img src="/images/colleges/men-figure-compare.png" alt="" />
                                    <div className="comparison-content"><span className="fs36">{numberWithCommas(collegeData.admissions_men)}</span><br/>Admitted</div>
                                </div>
                                <div className="large-6 small-6 column  bg-women-side text-center">
                                    <img src="/images/colleges/women-figure-compare.png" alt="" />
                                    <div className="comparison-content"><span className="fs36">{numberWithCommas(collegeData.admissions_women)}</span><br/>Admitted</div>
                                </div>
                            </div>
                            <p className="comparison-title">
                            <span className="font-14">UNDERGRAD</span><br/>STUDENT GENDER
                            </p>
                          </div>
                        </div>
                    }
                </div>

              <div className="large-8 columns no-mob-padding mob-top10-margin no-padding-right">
                <div className="large-12 small-12 columns no-padding">
                  <div className="row" id="salary-box-admissions">
                    <div className="avg-salary-pop-degree pos-relative no-padding">
                        <div className="salarybox-headerImage">
                            <img src="/images/colleges/calendar-top-image.png" alt="" />
                        </div>
                        <div className="avg-salary-title p10 fs12 div-gray-background">
                            <div className="large-4 small-4 columns">TEST</div>
                            <div className="large-4 small-4 columns">25TH PERCENTILE</div>
                            <div className="large-4 small-4 columns">75TH PERCENTILE</div>
                        </div>
                        <div className="row" style={{backgroundColor:'#004358'}}>
                            <div className="large-12 columns salary-structure-list" style={{background: '#00394C'}}>
                                <div className="large-4 small-4 columns salary-content-text-value">SAT CRITICAL READING</div>
                                <div className="large-4 small-4 columns salary-content-text-value">{ collegeData.sat_read_25 || 'NA' }</div>
                                <div className="large-4 small-4 columns salary-content-text-value">{ collegeData.sat_read_75 || 'NA' }</div>
                            </div>
                            <div className="large-12 columns salary-structure-list">
                                <div className="large-4 small-4 columns salary-content-text-value">SAT MATH</div>
                                <div className="large-4 small-4 columns salary-content-text-value">{ collegeData.sat_math_25 || 'NA' }</div>
                                <div className="large-4 small-4 columns salary-content-text-value">{ collegeData.sat_math_75 || 'NA' }</div>
                            </div>
                            <div className="large-12 columns salary-structure-list" style={{background: '#00394C'}}>
                                <div className="large-4 small-4 columns salary-content-text-value">SAT WRITING</div>
                                <div className="large-4 small-4 columns salary-content-text-value">{collegeData.sat_write_25 || 'NA'}</div>
                                <div className="large-4 small-4 columns salary-content-text-value">{collegeData.sat_write_75 || 'NA'}</div>
                            </div>
                            <div className="large-12 columns salary-structure-list">
                                <div className="large-4 small-4 columns salary-content-text-value">ACT COMPOSITE</div>
                                <div className="large-4 small-4 columns salary-content-text-value">{collegeData.act_composite_25 || 'NA'}</div>
                                <div className="large-4 small-4 columns salary-content-text-value">{collegeData.act_composite_75 || 'NA'}</div>
                            </div>
                            <div className="large-12 columns salary-structure-list" style={{background: '#00394C'}}>
                                <div className="large-4 small-4 columns salary-content-text-value">ACT ENGLISH</div>
                                <div className="large-4 small-4 columns salary-content-text-value">{collegeData.act_english_25 || 'NA'}</div>
                                <div className="large-4 small-4 columns salary-content-text-value">{collegeData.act_english_75 || 'NA'}</div>
                            </div>
                            <div className="large-12 columns salary-structure-list">
                                <div className="large-4 small-4 columns salary-content-text-value">ACT MATH</div>
                                <div className="large-4 small-4 columns salary-content-text-value">{collegeData.act_math_25 || 'NA'}</div>
                                <div className="large-4 small-4 columns salary-content-text-value">{collegeData.act_math_75 || 'NA'}</div>
                            </div>
                            <div className="large-12 columns salary-structure-list" style={{background: '#00394C'}}>
                                <div className="large-4 small-4 columns salary-content-text-value">ACT WRITING</div>
                                <div className="large-4 small-4 columns salary-content-text-value">{collegeData.act_write_25 || 'NA'}</div>
                                <div className="large-4 small-4 columns salary-content-text-value">{collegeData.act_write_75 || 'NA'}</div>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
                    <div className="large-12 small-12 columns no-padding mt10">
                  <div className="row" id="admission-consider-box">
                    <div className="avg-salary-pop-degree pos-relative no-padding">
                        <div className="avg-salary-title p10 div-gray-background">WHAT IS CONSIDERED FOR ADMISSION?</div>
                        <div className="row" style={{backgroundColor: '#049AA2'}}>
                            <div className="large-12 columns salary-structure-list" style={{background: '#04A6AE'}}>
                              <div className="large-9 small-6 columns salary-content-text-value">SECONDARY SCHOOL GPA</div>
                              <div className="large-3 small-6 columns salary-content-text-value">{ renderRequiredOrNA(collegeData.secondary_school_gpa) }</div>
                            </div>
                            <div className="large-12 columns salary-structure-list">
                              <div className="large-9 small-6 columns salary-content-text-value">RECORD - COMPLETION OF A COLLEGE-PREP PROGRAM</div>
                              <div className="large-3 small-6 columns salary-content-text-value">{ renderRequiredOrNA(collegeData.secondary_school_record) }</div>
                            </div>
                            <div className="large-12 columns salary-structure-list" style={{background: '#04A6AE'}}>
                              <div className="large-9 small-6 columns salary-content-text-value">PORTFOLIO</div>
                              <div className="large-3 small-6 columns salary-content-text-value">--</div>
                            </div>
                            <div className="large-12 columns salary-structure-list">
                              <div className="large-9 small-6 columns salary-content-text-value">ADMISSION TEST SCORES (SAT/ACT)</div>
                              <div className="large-3 small-6 columns salary-content-text-value">{ renderRequiredOrNA(collegeData.admission_test_scores) }</div>
                              </div>
                            <div className="large-12 columns salary-structure-list" style={{background: '#04A6AE'}}>
                              <div className="large-9 small-6 columns salary-content-text-value">TOEFL (Test of English as a Foreign language)</div>
                              <div className="large-3 small-6 columns salary-content-text-value">{ renderRequiredOrNA(collegeData.admission_test_scores)}</div>
                            </div>
                            <div className="large-12 columns salary-structure-list">
                              <div className="large-9 small-6 columns salary-content-text-value">RECOMMENDATIONS</div>
                              <div className="large-3 small-6 columns salary-content-text-value">{ renderRequiredOrNA(collegeData.recommendations) }</div>
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
    admissions: state.search.admissions,
    isFetching: state.search.isFetchingCollegeSubPage,
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    getCollegeAdmissions: (slug) => { dispatch(getCollegeAdmissions(slug)) },
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(withRouter(AdmissionsPage));
