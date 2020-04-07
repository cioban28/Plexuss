import React, { Component } from 'react';
import renderHTML from 'react-render-html';
import { Link } from 'react-router-dom';

class ComparisonColumn extends Component {

  render() {
    const { selectedCollege, handleRemoveCollege, key } = this.props;

    const renderEmptyIfNotPresent = (value) => (value || '');
    const renderNAIfNotPresent = (value) => (value || 'N/A');

    const renderTuitionFees = (values) => {
      if (values.tuition_avg_in_state_ftug != '' && values.tuition_avg_in_state_ftug) {
        return values.tuition_avg_in_state_ftug.toFixed();
      }
    }

    const renderTuitionOutFees = (values) => {
      if (values.tuition_avg_out_state_ftug != '' && values.tuition_avg_out_state_ftug) {
        return values.tuition_avg_out_state_ftug.toFixed();
      }
    }

    const renderCollegeLogo = (values) => ((values.logo_url != '' && values.logo_url) ? 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/' + values.logo_url + '' : 'images/no_photo.jpg');

    const renderEndowment = (values) => {
      let endowment = 'N/A';

      if (values.public_endowment_end_fy_12 && values.public_endowment_end_fy_12 != 0 ) {
        endowment = '$' + (values.public_endowment_end_fy_12 * 0.000000001, 2).toFixed() + '<span className="fs10"> BILLION</span>';
      } else {
        endowment = '$'.round(values.private_endowment_end_fy_12 * 0.000000001, 2).toFixed() + '<span className="fs10"> BILLION</span>';
      }

      if (endowment == '$0<span className="fs10"> BILLION</span>') {
        endowment = 'N/A';
      }

      return endowment;
    }

    const renderExpense = (values) => {
      if (values.id && values.id != '') {
        return ((values.tuition_avg_in_state_ftug) + (values.books_supplies_1213) + (values.room_board_on_campus_1213) + (values.other_expenses_on_campus_1213)).toFixed();
      }
    }

    const renderAidRate = (values) => {
      if (values.undergrad_grant_pct != '' && values.undergrad_grant_pct) {
        return values.undergrad_grant_pct + '%'
      }
    }

    const renderAidGrant = (values) => {
      if (values.undergrad_grant_avg_amt != '' && values.undergrad_grant_avg_amt) {
        return '$' + values.undergrad_grant_avg_amt.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
      }
    }

    const renderAcceptanceRate = (values) => {
      let acceptanceRate = 0;

      if (values.id && values.id != '') {
        if (values.applicants_total > 0) {
          acceptanceRate = ((values.admissions_total) / (values.applicants_total) * 100).toFixed(1);
          if (101 > acceptanceRate && acceptanceRate > 0) {
            acceptanceRate = acceptanceRate + '%';
          }
        }
      }

      return acceptanceRate;
    }

    return (
        <div className='owl-item' style={{width: "100%", display: 'block'}}>
        <div className='item text-center pos-rel' style={{display: 'block'}}>
          <div className='comapreSchooltitleArea' style={{display: 'none'}}>
            <div className='row'>
              <div className='column small-12 small-text-right removeitem cursor' onClick={handleRemoveCollege}>×</div>
            </div>
            { renderEmptyIfNotPresent(selectedCollege.school_name) }
          </div>

          <div className='border-right-gray border-bottom-gray' data-fieldfor='college_logo'>
            <div className='show-for-small battle-icon'>
              <img src='/images/colleges/compare/battle-black.png' title='' alt=''/>
              </div>

            {
              selectedCollege.id ?
              <div className='pos-rel compareLogTopContainor' style={{paddingTop: '33px'}}>
                <div className='removeschool show-for-small'>
                  <img src='/images/colleges/compare/close.jpg' className='text-center removeitem mobile' style={{margin: 0}} alt='' onClick={handleRemoveCollege}/>
                </div>

                <div className="removeschool hide-for-small">
                  <img src='/images/colleges/compare/close.jpg' className='text-center removeitem' style={{marginTop: '-2px'}} alt='' onClick={handleRemoveCollege}/>
                </div>

                <Link to={`/college/${selectedCollege.slug}`}>
                  <img src={renderCollegeLogo(selectedCollege)} className='compare-school-logo' alt='' style={{margin: '0px auto'}}/>
                </Link>
                <br/>
                <div className='row'>
                  <div className='small-12 column'>
                    <Link to={`/college/${selectedCollege.slug}`} className='c79'>
                      <div className='college-name'>{ renderEmptyIfNotPresent(selectedCollege.school_name) }</div>
                    </Link>
                  </div>
                </div>
              </div> :
              <div id='addSchoolBoxCompareCol' className='addSchoolBox'>
                <a className='hide-for-small desktopClickToadd' href='#' data-reveal-id='selectSchoolPopup'>
                  <img id="clickToAddSchoolImg" src="/images/colleges/compare/addclick.jpg" style={{verticalAlign :'middle'}} alt=""/>
                </a>

                <a className='show-for-small' href="#" data-reveal-id="selectSchoolPopup">
                  <img src="/images/colleges/compare/mobile-add.png" className="compare-school-logo" style={{verticalAlign: 'middle'}} style={{padding: '0px'}} alt=""/>
                </a>
                <div className='college-name'></div>
              </div>
            }
          </div>
          {
            selectedCollege.id &&
            <div className='college-info'>
              <div className='odd-div title-text br-white text-center comparison-bottom-margin' style={{paddingTop: 15}}>
                <p className='m-title-heading show-for-small sos'>RANKING</p>
                <span className='raking-div-number'>{ renderNAIfNotPresent(selectedCollege.plexuss)}</span>
              </div>

              <div className='aid-section title-text text-center fs15 comparison-bottom-margin'>
                <p className='m-title-heading show-for-small sos' >GRANT OR SCHOLARSHIP AID</p>
              </div>
              <div className='title-text aid-sub-section text-center comparison-bottom-margin'>
                <div className='fs15'>{renderNAIfNotPresent(renderAidRate(selectedCollege))}</div>
                <div className='fs15'>{renderNAIfNotPresent(renderAidGrant(selectedCollege))}</div>
              </div>

              <div className='odd-div title-text br-white text-center fs15 comparison-bottom-margin'>
                <p className='m-title-heading show-for-small sos' >ACCEPTANCE RATE</p>
                <span className='fs15'>{ renderNAIfNotPresent(renderAcceptanceRate(selectedCollege)) }</span>
              </div>

              <div className='title-text br-white text-center fs15 comparison-bottom-margin'>
                <p className='m-title-heading show-for-small sos' >TUITION IN-STATE</p>
                <span className='fs15'>${ renderNAIfNotPresent(renderTuitionFees(selectedCollege)) }</span>
              </div>

              <div className='odd-div title-text br-white text-center fs15 comparison-bottom-margin'>
                <p className='m-title-heading show-for-small sos' >TUITION OUT-STATE</p>
                <span className='fs15'>${ renderNAIfNotPresent(renderTuitionOutFees(selectedCollege)) }</span>
              </div>

              <div className='title-text br-white text-center fs15 comparison-bottom-margin'>
                <p className='m-title-heading show-for-small sos' >TOTAL EXPENSE <br /> <span className='fs10'>(on campus)</span> </p>
                <span className='fs15'>${ renderNAIfNotPresent(renderExpense(selectedCollege)) }</span>
              </div>

              <div className='odd-div title-text br-white text-center fs15 comparison-bottom-margin'>
                <p className='m-title-heading show-for-small sos' >STUDENT BODY</p>
                <span className='fs15'>{ renderNAIfNotPresent(selectedCollege.student_body_total) }</span>
              </div>

              <div className='title-text br-white text-center fs15 comparison-bottom-margin'>
                <p className='m-title-heading show-for-small sos' >APPLICATION <br /> DEADLINE <br /> <span className='fs10'>(undergraduate)</span> </p>
                <span className='fs15'>{ renderNAIfNotPresent(selectedCollege.deadline) }</span>
              </div>

              <div className='odd-div title-text br-white text-center fs15 comparison-bottom-margin' style={{marginTop: 10}}>
                <p className='m-title-heading show-for-small sos' >APPLICATION <br /> FEE</p>
                <span className='fs15'>{ renderNAIfNotPresent(selectedCollege.application_fee_undergrad) }</span>
              </div>

              <div className='br-white text-center c79 fs12 pt5 comparison-bottom-margin'>
                <p className='m-title-heading show-for-small sos' >SECTOR OF <br /> INSTITUTION</p>
                <span className='fs15'>{ renderNAIfNotPresent(selectedCollege.sector_of_institution) }</span>
              </div>

              <div className='odd-div title-text br-white text-center fs15 comparison-bottom-margin'>
                <p className='m-title-heading show-for-small sos' >CALENDAR <br /> SYSTEM</p>
                <span className='fs15'>{ renderNAIfNotPresent(selectedCollege.calendar_system) }</span>
              </div>

              <div className='title-text br-white text-center fs15 comparison-bottom-margin'>
                <p className='m-title-heading show-for-small sos' >RELIGIOUS <br /> AFFILIATION</p>
                <span className='fs15'>{ renderNAIfNotPresent(selectedCollege.religous_affiliation) }</span>
              </div>

              <div className='odd-div title-text br-white text-center fs15 comparison-bottom-margin'>
                <p className='m-title-heading show-for-small sos' >CAMPUS SETTING</p>
                <span style={{fontSize: '15px !important'}}>{ renderNAIfNotPresent(selectedCollege.locale_type) }</span>
              </div>
            </div>
          }
        </div>
      </div>
    );
  }
}

export default ComparisonColumn;
