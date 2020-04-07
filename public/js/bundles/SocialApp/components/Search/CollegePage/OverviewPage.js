import React, { Component } from 'react';
import { connect } from 'react-redux';
import PhotoSlider from './PhotoSlider';
import { Link, withRouter } from 'react-router-dom';
import { getCollegeOverview } from '../../../api/search';


class OverviewPage extends Component {

  render() {
    const collegeData = this.props.overview;
    const collegeInfo = this.props.college;
    return (
      <div className='college-slider-cont'>
        {
          collegeInfo &&
          <div className="school-info-mobile">
            <div className="school-ranking">
              # {collegeData.plexuss_ranking || 'N/A'}
            </div>

            <div className="school-logo">
              {
                !!collegeData.logo_url && collegeData.id !== '1785' &&
                  <Link to={ `/college/${collegeData.slug}` }>
                    <img src={`https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/${collegeData.logo_url}`} alt="" />
                  </Link>
              }
            </div>

            <div className="school-title">
              <span className={`country-flag flag-icon flag-icon-${collegeInfo.country_code || ''}`}></span>
              {
                collegeInfo.school_name
              }
            </div>

            <div className="school-address">
              { !!collegeData.address && collegeData.address + ' - ' }
              { !!collegeData.zip && collegeData.zip + ' ' }
              { !!collegeData.city && collegeData.city + ' - ' }
              { !!collegeData.country_name && collegeData.country_name }
            </div>

            <div className="school-phone">
              { !!collegeData.general_phone && collegeData.general_phone }
            </div>

            { collegeInfo.is_online == 1 && <p className='online-school'>Online School</p> }
          </div>
        }

        {
          <PhotoSlider collegeMedia={collegeData.college_media} />
        }

        <div className="row">
          <div className="column small-12 no-padding">
            <div className="row overview-text-area">
              <div className="column no-padding">
                <h2>About</h2>
                <h3>{ collegeData.school_name || '' }</h3>
                <div dangerouslySetInnerHTML={{ __html: collegeData.overview_content }} />
                {
                  !!collegeData.overview_source && <div className="overviewSource">Source {collegeData.overview_source || ''}</div>
                }
              </div>
            </div>
          </div>
        </div>

        <div className="row" id="overview-bottom-3-boxes">
          <div className="column small-12 large-4 general-info-box no-padding-left">
            <div className="row">
              <div className="column small-12 text-left box-3-top-header">GENERAL INFORMATION</div>
            </div>
            <div className="row box-3-infobox">
              <div className="column small-12">Type:</div>
              <div className="column small-12 value">{collegeData.school_sector}</div>
              <div className="column small-12">Campus setting:</div>
              <div className="column small-12 value">{collegeData.institution_size}</div>
              <div className="column small-12">Campus housing:</div>
              <div className="column small-12 value">{collegeData.campus_housing}</div>
              <div className="column small-12">Religious Affiliation:</div>
              <div className="column small-12 value">{collegeData.religious_affiliation}</div>
              <div className="column small-12">Academic Calendar:</div>
              <div className="column small-12 value">{collegeData.calendar_system}</div>
            </div>
          </div>
          <div className="column small-12 large-4 general-links-box no-padding-middle">
            <div className="row">
              <div className="column small-12 text-left box-3-top-header">GENERAL LINKS</div>
            </div>
            <div className="column small-12 box-3-infobox">
              <div className="row">
                {
                  !!collegeData.school_url && collegeData.school_url.length > 1 &&
                  <div className="column small-12">
                    <a className="col-overview-link-hover" href={collegeData.school_url} target="_blank">Website</a>
                  </div>
                }
                {
                  !!collegeData.admission_url && collegeData.admission_url.length > 1 &&
                  <div className="column small-12">
                    <a className="col-overview-link-hover" href={collegeData.admission_url} target="_blank">Admissions</a>
                  </div>
                }
                {
                  !!collegeData.application_url && collegeData.application_url.length > 1 &&
                  <div className="column small-12">
                    <a className="col-overview-link-hover" href={collegeData.application_url} target="_blank">Apply Online</a>
                  </div>
                }
                {
                  !!collegeData.financial_aid_url && collegeData.financial_aid_url.length > 1 &&
                  <div className="column small-12">
                    <a className="col-overview-link-hover" href={collegeData.financial_aid_url} target="_blank">Financial Aid</a>
                  </div>
                }
                {
                  !!collegeData.calculator_url && collegeData.calculator_url.length > 1 &&
                  <div className="column small-12">
                    <a className="col-overview-link-hover" href={collegeData.calculator_url} target="_blank">Net Price Calculator</a>
                  </div>
                }
                {
                  !!collegeData.mission_url && collegeData.mission_url.length > 1 &&
                  <div className="column small-12">
                    <a className="col-overview-link-hover" href={collegeData.mission_url} target="_blank">Mission Statement</a>
                  </div>
                }
              </div>
            </div>
          </div>
          <div className="column small-12 large-4 find-out-more no-padding-right">
            <div className="row">
              <div className="column small-12 text-left box-3-top-header">FIND OUT MORE</div>
            </div>
            <div className="column small-12 box-3-infobox">
              <div className="row">
                <div className="column small-12">
                  <Link className="col-overview-link-hover" to={`/college/${collegeData.slug}/stats`}> Stats</Link>
                </div>
                <div className="column small-12">
                  <Link className="col-overview-link-hover" to={`/college/${collegeData.slug}/ranking`}> Ranking</Link>
                </div>
                <div className="column small-12">
                  <Link className="col-overview-link-hover" to={`/college/${collegeData.slug}/admissions`}> Admissions</Link>
                </div>
                <div className="column small-12">
                  <Link className="col-overview-link-hover" to={`/college/${collegeData.slug}/financial-aid`}>Financial Aid</Link>
                </div>
                <div className="column small-12">
                  <Link className="col-overview-link-hover" to={`/college/${collegeData.slug}/enrollment`}>Enrollment</Link>
                </div>
                <div className="column small-12">
                  <Link className="col-overview-link-hover" to={`/college/${collegeData.slug}/tuition`}>Tuition</Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

const mapStateToProps = (state) => {
  return {
    overview: state.search.overview,
    isFetching: state.search.isFetching,
    college: state.search.college,
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    getCollegeOverview: (slug) => { dispatch(getCollegeOverview(slug)) },
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(withRouter(OverviewPage));
