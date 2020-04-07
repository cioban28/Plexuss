import React, { Component } from 'react';
import { connect } from 'react-redux';
import { withRouter, Link } from 'react-router-dom';
import Masonry from 'react-masonry-component';
import './styles/stats.scss';
import CircularProgressbar from 'react-circular-progressbar';
import CreateReactClass from "create-react-class";
import Gauge from "svg-gauge";


class StatsPage extends Component {
  constructor(props) {
    super(props);
  }


  componentDidMount() {
    this.renderGauge(this.props.stats);
  }

  renderGauge(props) {
    const firstGaugeOption = {
      animDuration: 1,
      showValue: true,
      min: 600,
      max: 2400,
      value: props.sat25percentile || 0,
    };

    const secondGaugeOption = {
      animDuration: 1,
      showValue: true,
      min: 600,
      max: 2400,
      value: props.sat75percentile || 0,
    };

    const thirdGaugeOption = {
      animDuration: 1,
      showValue: true,
      min: 1,
      max: 36,
      value: props.act_composite_25 || 0,
    };

    const fourthGaugeOption = {
      animDuration: 1,
      showValue: true,
      min: 1,
      max: 36,
      value: props.act_composite_75 || 0,
    };

    let gaugeOptions = Object.assign({}, firstGaugeOption, props);
    if(!this.firstGauge && !props.hide_sat25percentile) {
      this.firstGauge = Gauge(this.gaugeEl1st, gaugeOptions);
      this.firstGauge.setValueAnimated(props.sat25percentile || 0, gaugeOptions.animDuration);
    }
    

    gaugeOptions = Object.assign({}, secondGaugeOption, props);
    if(!this.secondGuage && !props.hide_sat75percentile) {
      this.secondGauge = Gauge(this.gaugeEl2nd, gaugeOptions);
      this.secondGauge.setValueAnimated(props.sat75percentile || 0, gaugeOptions.animDuration);
    }
    

    gaugeOptions = Object.assign({}, thirdGaugeOption, props);
    if(!this.thirdGauge && !props.hide_act_composite_25) {
      this.thirdGauge = Gauge(this.gaugeEl3rd, gaugeOptions);
      this.thirdGauge.setValueAnimated(props.act_composite_25 || 0, gaugeOptions.animDuration);
    }
    

    gaugeOptions = Object.assign({}, fourthGaugeOption, props);
    if(!this.fourthGauge && !props.hide_act_composite_75) {
      this.fourthGauge = Gauge(this.gaugeEl4th, gaugeOptions);
      this.fourthGauge.setValueAnimated(props.act_composite_75 || 0, gaugeOptions.animDuration);
    }
    
  }

  render() {
    const collegeData = this.props.stats;
    return (
      <div id='stats-page'>
      {!!this.props.isFetching && <div className="college-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>}
      {
        !!Object.entries(this.props.stats).length &&
          <div>
            <div className="row" style={{border: 'solid 0px #ff0000'}}>
              <div className="column small-12 no-padding">
                <div style={{display: 'block'}}>
                  <div className="row university-stats-content">
                    <div className="large-12 columns no-padding bg-ext-black radial-bdr">
                      <div className="large-6 columns no-padding college-rank-divide div-gray-background">
                        <div className="row">
                          <div className="small-12 column university_content_admis_deadline">STATS</div>
                        </div>
                        <div className="detail-university-grey">
                          <span>ADMISSION DEADLINE</span>
                        </div>
                        <div className="detail-university-green-content">{ collegeData.deadline || '' }</div>
                        <div className="detail-university-grey">ACCEPTANCE RATE</div>
                        <div className="detail-university-green-content">
                          {
                            !!collegeData.acceptance_rate ? collegeData.acceptance_rate + '% ACCEPTED' : 'N/A'
                          }
                        </div>
                        <div className="detail-university-grey">STUDENT BODY SIZE</div>
                        <div className="large-12 columns detail-university-green-content stats-student-body-size-height">
                          <div className="large-6 small-6 columns bdr-dot-right text-left padding-issue-fix">
                          {
                            !!collegeData.student_body_total
                              ? <div>
                                  { parseFloat(collegeData.student_body_total).toLocaleString() }
                                  <br /><span className="font-12">TOTAL</span>
                                </div>
                              : <div>
                                  N/A<br /><span className="font-12">TOTAL</span>
                                </div>
                          }
                          </div>
                          <div className="large-6 small-6 columns text-center">
                          {
                            !!collegeData.undergrad_enroll_1112
                              ? <div>
                                  { parseFloat(collegeData.undergrad_enroll_1112).toLocaleString() }
                                  <br /><span className="font-12">UNDERGRAD</span>
                                </div>
                              : <div>
                                  N/A<br /><span className="font-12">UNDERGRAD</span>
                                </div>
                          }
                          </div>
                        </div>
                      </div>
                      {
                        (!!collegeData.youtube_stats_videos && collegeData.youtube_stats_videos.length > 0)
                          ? <div className="large-6 column yt-vid-stats">
                            {
                              collegeData.youtube_stats_videos.map((video, index) => (
                                <iframe key={index} width="100%" height="280" src={`https://www.youtube.com/embed/${video['video_id']}`} style={{border: 'none'}} allowFullScreen></iframe>
                              ))
                            }
                            </div>
                          : <div className="large-6 columns no-padding hide-for-small-only">
                              <img src="/images/colleges/default-college-page-photo_3.jpg" className="default-coll-stats-img" alt="" />
                            </div>
                      }
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div className="custom-row">
              <div className="row mt15">
                  <Masonry>
                  {
                    !collegeData.hide_graduation_rate_4_year &&
                    <div className="box-div column small-12 medium-6 no-padding-left" id="graduation-doughnut" style={{position: 'absolute', left: '0px', top: '0px'}}>
                        <div className="">
                            <div className="bg-pure-white box-graduation-degree radial-bdr row collapse">
                                <div className="box-top-content-image column small-12"><img src="/images/colleges/box-1-top-content-img.png" alt="" /></div>
                                <div className="text-center box-1-title-head">4 - Year<br/><span>Graduation Rate</span></div>
                                <div className="text-center box-1-chart-donut">
                                    <div className='graduation-rate-bar-progress'>
                                      <CircularProgressbar
                                        strokeWidth={16}
                                        initialAnimation={true}
                                        percentage={collegeData.graduation_rate_4_year}
                                        text={`${collegeData.graduation_rate_4_year}%`}
                                      />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                  }


                      <div className="box-div column small-12 medium-6 no-padding-right" style={{position: 'absolute', left: '464px', top: '0px'}}>
                          <div className="">
                              <div className="bg-box-2 radial-bdr">
                                  <div className="box-2-header div-gray-background">GENERAL INFORMATION</div>
                                  <div className="box-2-content">
                                      <div className="fs16"><span>Type :</span><br/>{collegeData.school_sector}</div>
                                      <div className="fs16"><span>Campus setting:</span><br/>{collegeData.institution_size}</div>
                                      <div className="fs16"><span>Campus housing:</span><br/>{collegeData.campus_housing}</div>
                                      <div className="fs16"><span>Religious Affiliation:</span><br/>{collegeData.religious_affiliation}</div>
                                      <div className="fs16"><span>Academic Calendar:</span><br/>{collegeData.calendar_system}</div>
                                  </div>
                              </div>
                          </div>
                      </div>



                      {
                        !!collegeData.average_freshman_gpa &&
                           <div className="box-div column small-12 medium-6" id="incFresh-Ave-GPA" style={{position: 'absolute', left: '464px', top: '415px'}}>
                             <div className="row">
                                 <div className="small-12 columns pin-stats-aveGPA-header text-center pin-stats-aveGPA div-gray-background">Average Incoming Freshmen GPA</div>
                             </div>
                             <div className="row">
                                 <div className="small-12 columns pin-stats-aveGPA-header text-center pin-stats-aveGPA-body-text">{collegeData.average_freshman_gpa || ""}</div>
                             </div>
                            </div>
                      }

                      {
                        !collegeData.hide_sat25percentile &&
                          <div className="box-div column small-12 medium-6" id="avg-sat25-percentile" style={{position: 'absolute', left: '0px', top: '493px'}}>
                            <div className="">
                                <div className="margin10bottom" style={{backgroundColor: '#a0db39', minHeight: '354px' }}>
                                    <div className="titleAvgBox div-gray-background">25th PERCENTILE<br/><br/><span>SAT SCORE</span></div>
                                    <br/>

                                    <div className="text-center box-1-chart-donut row">
                                        <br/>
                                        <div className='gauge-cont'>
                                            <div className="gauge-container first" ref={el => this.gaugeEl1st = el}></div>
                                        </div>
                                        <div className="small-10 small-centered columns pos-arc-values">
                                            <div className="row">
                                                <div className="column small-6 gauge-min-lim">600</div>
                                                <div className="column small-6 gauge-max-lim">2400</div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                              </div>
                            </div>
                      }
                      {
                        !collegeData.hide_sat75percentile &&
                          <div className="box-div column small-12 medium-6" id="avg-sat75-percentile" style={{position: 'absolute', left: '464px', top: '536px'}}>
                              <div className="">
                                  <div className="margin10bottom" style={{backgroundColor: '#a0db39', minHeight: '354px'}}>
                                      <div className="titleAvgBox div-gray-background">75th PERCENTILE<br/><br/><span>SAT SCORE</span></div>
                                      <br/>

                                      <div className="text-center box-1-chart-donut row">
                                          <br/>
                                          <div className='gauge-cont'>
                                              <div className="gauge-container first" ref={el => this.gaugeEl2nd = el}></div>
                                          </div>
                                          <div className="small-10 small-centered columns pos-arc-values">
                                              <div className="row">
                                                  <div className="small-6 columns gauge-min-lim">600</div>
                                                  <div className="small-6 columns gauge-max-lim">2400</div>
                                              </div>
                                          </div>
                                      </div>

                                  </div>
                              </div>
                          </div>
                      }

                      {
                        !collegeData.hide_sat_percent &&
                          <div className="box-div column small-12 medium-6" id="SATScores-graph" style={{position: 'absolute', left: '0px', top: '886px'}}>
                              <div className="">
                                  <div className="min-h-300" style={{backgroundColor: '#a0db39'}}>
                                      <div className="text-right share-btn-height"></div>
                                      <div className="graph-image">
                                          <div className="vertical-graph-cover">
                                              <div className="vertical-graph" style={{height: !!collegeData.sat_percent ? 100-collegeData.sat_percent+'%' : '0%'}}></div>
                                          </div>

                                      </div>
                                      <div className="graph-image">
                                          <strong className="graph-per-fs text-white">{collegeData.sat_percent ? collegeData.sat_percent+'%' : 'N/A' }</strong>
                                          <br/>
                                          <strong className="sub-per-fs text-white">SUBMITING</strong>
                                          <br/>
                                          <strong className="sub-per-fs">SAT SCORES</strong>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      }
                      {
                        !collegeData.hide_act_composite_25 &&
                          <div className="box-div column small-12 medium-6" id="avg-act25-percentile" style={{position: 'absolute', left: '464px', top: '929px'}}>
                              <div className="">
                                  <div className="margin10bottom" style={{backgroundColor: '#168f3a', minHeight: '354px' }}>
                                      <div className="titleAvgBox div-gray-background">25th PERCENTILE<br/><br/><span>ACT SCORE</span></div>
                                      <br/>
                                      <div className="text-center box-1-chart-donut row">
                                          <br/>
                                          <div className='gauge-cont'>
                                              <div className="gauge-container first" ref={el => this.gaugeEl3rd = el}></div>
                                          </div>
                                          <div className="small-10 small-centered columns pos-arc-values">
                                              <div className="row">
                                                  <div className="small-6 columns gauge-min-lim">1</div>
                                                  <div className="small-6 columns gauge-max-lim">36</div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      }
                      {
                        !collegeData.hide_act_composite_75 &&
                          <div className="box-div column small-12 medium-6" id="avg-act75-percentile" style={{position: 'absolute', left: '0px', top: '1211px'}}>
                              <div className="">
                                  <div className="margin10bottom" style={{backgroundColor: '#168f3a', minHeight: '354px'}}>
                                      <div className="titleAvgBox div-gray-background">75th PERCENTILE<br/><br/><span>ACT SCORE</span></div>
                                      <br/>
                                      <div className="text-center box-1-chart-donut row">
                                          <br/>
                                          <div className='gauge-cont'>
                                              <div className="gauge-container first" ref={el => this.gaugeEl4th = el}></div>
                                          </div>
                                          <div className="small-10 small-centered columns pos-arc-values">
                                              <div className="row">
                                                  <div className="small-6 columns gauge-min-lim">1</div>
                                                  <div className="small-6 columns gauge-max-lim">36</div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      }
                      {
                        !collegeData.hide_act_percent && 
                        <div className="box-div column small-12 medium-6" id="ACTScores-graph" style={{position: 'absolute', left: '464px', top: '1322px'}}>
                          <div className="">
                              <div className="radial-bdr margin10bottom min-h-300 ml5" style={{backgroundColor: '#168f3a'}}>
                                  <div className="text-right share-btn-height"></div>
                                  <div className="graph-image">
                                      <div className="vertical-graph-cover">
                                          <div className="vertical-graph" style={{height: !!collegeData.act_percent ? 100-collegeData.act_percent+'%' : '0%' }}></div>
                                      </div>
                                  </div>
                                  <div className="graph-image">
                                      <strong className="graph-per-fs text-white">{collegeData.act_percent ? collegeData.act_percent+'%' : 'N/A' }</strong>
                                      <br/>
                                      <strong className="sub-per-fs text-white">SUBMITING</strong>
                                      <br/>
                                      <strong className="sub-per-fs">ACT SCORES</strong>
                                  </div>
                              </div>
                          </div>
                        </div>
                      }

                      


                      <div className="box-div column small-12 medium-6" style={{position: 'absolute', left: '0px', top: '1604px'}}>
                          <div className="">
                              <div className="row2-bg-box-3 radial-bdr margin10bottom">
                                  <div className="box-3-content no-margin div-gray-background">
                                      <img src="/images/colleges/row-2-image-3.png" style={{width: '100%', borderRadius: '5px 5px 0 0'}} alt="" />
                                      <p className="text-center student-ratio-box-head div-gray-background">
                                          <strong>STUDENT </strong>
                                          <span>TO </span>
                                          <strong className="text-green">FACULTY</strong>
                                          <br/>
                                          <span className="font-22">RATIO</span>
                                      </p>
                                      <div className="student-ratio-div div-gray-background">
                                        {
                                          [...Array(!!collegeData.student_faculty_ratio ? collegeData.student_faculty_ratio : 0)].map((e, i) =>
                                            <img src="/images/colleges/student-ratio.png" alt=""/>
                                          )
                                        }
                                      </div>
                                      <div className="faculty-highlight div-gray-background"><img src="/images/colleges/teacher-ratio.png" alt=""/></div>
                                      <br/>
                                      <p className="green-ratio-title text-center">{!!collegeData.student_faculty_ratio ? collegeData.student_faculty_ratio : 'N/A' } : 1</p>
                                  </div>
                              </div>
                          </div>
                      </div>


                      <div className="box-div column small-12 medium-12 large-6" style={{position: 'absolute', left: '464px', top: '1657px'}}>
                          <div className="">
                              <div className="bg-pure-white radial-bdr text-black margin10bottom">
                                  <div className="graph-image padding20">
                                      <strong className="font-18">COLLEGE TOTAL</strong>
                                      <br/><br/>
                                      <strong className="font-26">ENDOWMENT</strong>
                                      <br/><br/>
                                      <strong className="font-30 text-green">{!!collegeData.totalEndowment ? '$'+collegeData.totalEndowment.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : 'N/A'}</strong>
                                  </div>
                                  <div><img src="/images/colleges/icon-hand.png" alt=""/></div>
                                  <br/>
                              </div>
                          </div>
                      </div>


                      <div className="box-div column small-12 medium-6" style={{position: 'absolute', left: '464px', top: '1951px'}}>
                          <div className="">
                              <div className="bg-box-3" style={{background: '#004358'}}>
                                  <div className="box-2-header div-gray-background">ACCREDITATION</div>
                                  <div className="box-3-content">
                                      <div className="bold-font">AGENCY</div>
                                      <div>{!!collegeData.accred_agency ? collegeData.accred_agency: 'N/A' }</div>
                                      <div className="bold-font">PERIODS OF ACCREDITATION</div>
                                      <div>{!!collegeData.accred_period ? collegeData.accred_period: 'N/A' }</div>
                                      <div className="bold-font">STATUS</div>
                                      <div>{!!collegeData.accred_status ? collegeData.accred_status: 'N/A' }</div>
                                  </div>
                              </div>
                          </div>
                      </div>


                      <div className="box-div column small-12 medium-6" style={{position: 'absolute', left: '0px', top: '2243px'}}>
                          <div className="">
                              <div className="bg-box-3" style={{background: '#05ced3'}}>
                                  <div className="box-2-header div-gray-background">AWARDS OFFERED</div>
                                  <div className="box-3-content">
                                      <div><span>Bachelor’s degree : {!!collegeData.bachelors_degree ? collegeData.bachelors_degree : 'N/A' }</span></div>
                                      <div><span>Master’s degree : {!!collegeData.masters_degree ? collegeData.masters_degree : 'N/A' }</span></div>
                                      <div><span>Post - Master’s certificate : {!!collegeData.post_masters_degree ? collegeData.post_masters_degree : 'N/A' }</span></div>
                                      <div><span>Doctor’s degree - Research/scholarship : {!!collegeData.doctors_degree_research ? collegeData.doctors_degree_research : 'N/A' }</span></div>
                                      <div><span>Doctor’s degree - Professional practice : {!!collegeData.doctors_degree_professional ? collegeData.doctors_degree_professional : 'N/A' }</span></div>
                                  </div>
                              </div>
                          </div>
                      </div>


                                  <div className="box-div column small-12 medium-6" style={{position: 'absolute', left: '464px', top: '2307px'}}>
                          <div className="">

                              <div className="bg-army-block radial-bdr text-black margin20bottom">
                                  <div className="box-2-header div-gray-background">ROTC</div>
                                  <div className="margin20top">
                                      <div className="large-12 columns no-padding margin20bottom">
                                          <div className="large-4 small-2 columns no-padding div-gray-background">
                                            {collegeData.rotc_army === 'Implied no' ? 
                                              <img src="/images/colleges/empty-big.png" alt=""/>
                                              :
                                              <img src="/images/colleges/correct-big.png" alt=""/>
                                            }
                                          </div>
                                          <div className="large-8 small-8 columns rotc-content-title div-gray-background">ARMY</div>
                                      </div>

                                      <div className="large-12 columns no-padding margin20bottom">
                                          <div className="large-4 small-2 columns no-padding div-gray-background">
                                            {collegeData.rotc_navy === 'Implied no' ? 
                                              <img src="/images/colleges/empty-big.png" alt=""/>
                                              :
                                              <img src="/images/colleges/correct-big.png" alt=""/>
                                            }
                                          </div>
                                          <div className="large-8 small-8 columns rotc-content-title div-gray-background">NAVY</div>
                                      </div>

                                      <div className="large-12 columns no-padding margin20bottom">
                                          <div className="large-4 small-2 columns no-padding div-gray-background">
                                            {collegeData.rotc_air === 'Implied no' ? 
                                              <img src="/images/colleges/empty-big.png" alt=""/>
                                              :
                                              <img src="/images/colleges/correct-big.png" alt=""/>
                                            }
                                          </div>
                                          <div className="large-8 small-8 columns rotc-content-title div-gray-background">AIR FORCE</div>
                                      </div>
                                  </div>
                              </div>

                          </div>
                      </div>
                  </Masonry>
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
    stats: state.search.stats,
    isFetching: state.search.isFetchingCollegeSubPage,
  }
}

export default connect(mapStateToProps, null)(withRouter(StatsPage));
