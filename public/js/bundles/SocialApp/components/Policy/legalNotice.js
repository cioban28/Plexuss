import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import { connect } from 'react-redux'
import './styles.scss'

class LegalNotice extends Component{
  constructor(props){
    super(props);

    this.state = {

    }
  }

  componentDidMount() {
    window.scrollTo(0, 0)
  }
  
  componentDidUpdate(prevProps) {
      if (this.props.location !== prevProps.location)
        window.scrollTo(0, 0)
  }

  render() {
    return (
      <div className="policy-background">
        <div className="policy-about"><img src="/images/policy/policy_about.svg"></img></div>
        <div className="policy-main-area">
          <h3 className="policy-title">PLEXUSS LEGAL NOTICES</h3>
          <div className="policy-content">
            <h4 className="policy-subtitle">IMPORTANT LEGAL NOTICES</h4>
            <div className="policy-subcontent">
              <p>Effective Date:</p>
              <p><b>Plexuss, Inc. (“PLEXUSS” or “We”) is a Delaware corporation which operates plexuss.com, the Plexuss Mobile Application, and other related digital properties (collectively referred to as the “Service”) in these notices. These notices apply to use of the Service including this site or application, whichever is applicable.</b></p>
              <p>By viewing, accessing, registering, creating a profile/account or otherwise using the Service, you agree to the terms and conditions contained within these notices. If you do not agree and consent, please discontinue use of the Service, and uninstall all Service downloads and applications</p>
              
              <h4 className="policy-subtitle-normal">Copyright and Trademarks</h4>
              <p>Copyright © 2011 - 2019 by Plexuss, Inc. (“PLEXUSS”). All the text, graphics, audio, video, design, software, and other works presented on and contained in the Service are the copyrighted works of PLEXUSS, All Rights Reserved. Any redistribution or reproduction of any materials herein is strictly prohibited.</p>
              <p>“PLEXUSS” and “PLEXUSS.COM” are ® Registered Trademarks of Plexuss, Inc. (“PLEXUSS”). PLEXUSS also claims the trademarks on “MYCOUNSELOR.”</p>
              
              <h4 className="policy-subtitle-normal">Disclaimer</h4>
              <p>THE INFORMATION FROM OR THROUGH THE SERVICE IS PROVIDED "AS-IS," "AS AVAILABLE," AND ALL WARRANTIES, EXPRESS OR IMPLIED, ARE DISCLAIMED (INCLUDING BUT NOT LIMITED TO THE DISCLAIMER OF ANY IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE). THE INFORMATION MAY CONTAIN ERRORS, PROBLEMS OR OTHER LIMITATIONS. PLEXUSS’ SOLE AND ENTIRE MAXIMUM LIABILITY FOR ANY INACCURATE INFORMATION, FOR ANY REASON, AND USER'S SOLE AND EXCLUSIVE REMEDY FOR ANY CAUSE WHATSOEVER, SHALL BE LIMITED TO CREDITING YOU THE AMOUNT PAID TO PLEXUSS IF ANY, TOWARD ANY FUTURE INFORMATION CHOSEN BY YOU WITHIN ONE YEAR. PLEXUSS IS NOT LIABLE FOR ANY INDIRECT, SPECIAL, INCIDENTAL, PUNITIVE, EXEMPLARY OR CONSEQUENTIAL DAMAGES (INCLUDING DAMAGES FOR LOSS OF BUSINESS, LOSS OF PROFITS, LITIGATION, OR THE LIKE). WHETHER BASED ON BREACH OF CONTRACT, BREACH OF WARRANTY, TORT, PRODUCT LIABILITY OR OTHERWISE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE. THE EXCLUSIONS AND LIMITATIONS SET FORTH ABOVE ARE FUNDAMENTAL ELEMENTS OF THE BASIS OF THE BARGAIN BETWEEN PLEXUSS AND YOU. PLEXUSS WOULD NOT PROVIDE THIS SERVICE AND INFORMATION WITHOUT SUCH LIMITATIONS. NO REPRESENTATIONS, WARRANTIES OR GUARANTEES WHATSOEVER ARE MADE AS TO THE ACCURACY, ADEQUACY, RELIABILITY, CURRENTNESS, COMPLETENESS, SUITABILITY OR APPLICABILITY OF THE INFORMATION TO A PARTICULAR SITUATION. THIS SERVICE CONTAINS LINKS TO OTHER INTERNET SITES. SUCH LINKS ARE NOT ENDORSEMENTS OF ANY PRODUCTS OR SERVICES IN SUCH SITES, AND NO INFORMATION IN SUCH SITE HAS BEEN ENDORSED OR APPROVED BY PLEXUSS.</p>
              
              <h4 className="policy-subtitle-normal">Users Reviews</h4>
              <p>PLEXUSS encourages its users to offer reviews of the resources they have used throughout the PLEXUSS platform as well as on colleges, universities, scholarship providers, and other education related entities that are featured throughout PLEXUSS. PLEXUSS, however, cannot guarantee the accuracy or validity of these reviews. If you post any reviews, comments, feedback or suggestions on the Service, they will become the royalty-free, perpetual, irrevocable, and fully sublicensable property of PLEXUSS.</p>
              
              <h4 className="policy-subtitle-normal">Privacy Policies</h4>
              <p>By viewing, accessing, registering, creating a profile/account or otherwise using the Service, you consent to our collection, use and disclosure practices, and other activities as described our privacy policies, and any additional privacy statements that may be posted on an applicable part of the Service. If you do not agree and consent, please discontinue use of the Service, and uninstall all Service downloads and applications.</p>
              <p>Please click <a className="link">here</a> for our Privacy Policy or <a className="link">here</a> for our California Privacy Policy.</p>
              
              <h4 className="policy-subtitle-normal">Terms of Use</h4>
              <p>By viewing, accessing, registering, creating a profile/account or otherwise using the Service, you agree to our <a className="link">Terms of Use</a>. If you do not agree and consent, please discontinue use of the Service, and uninstall all Service downloads and applications</p>
              
              <h4 className="policy-subtitle-normal">Applicable Law</h4>
              <p>This Service and all PLEXUSS digital properties are the property of PLEXUSS of San Ramon, California. As such, the laws of the State of California, U.S.A. shall exclusively govern these legal notices and any dispute arising, directly or indirectly, from your use of this Service or the PLEXUSS digital platform, without regard to conflicts of law principles. By using this Service or any related PLEXUSS digital property, you and PLEXUSS agree that the laws of the State of California, U.S.A. shall exclusively govern any claims and disputes between you and PLEXUSS.</p>

              <h4 className="policy-subtitle-normal">Choice of Forum for Dispute Resolution</h4>
              <p>You and PLEXUSS agree to not commence any action, litigation, or proceeding of any kind whatsoever against the other or any other party in any way arising from or relating to any claims of disputes that relate to your use of the Service, including, but not limited to, contract, equity, tort, fraud, and statutory claims, in any forum other than the United States District Court for the Northern District of California or, if such court does not have subject matter jurisdiction, the courts of the State of California sitting in Contra Costa County only and any appellate court from any thereof. You and PLEXUSS irrevocably and unconditionally submits to the exclusive jurisdiction of such courts and agrees to bring any such action, litigation, or proceeding only in the United States District Court for the Northern District of California or, if such court does not have subject matter jurisdiction, the courts of the State of California sitting in Contra Costa County only. You and PLEXUSS agree that a final judgment in any such action, litigation, or proceeding is conclusive and may be enforced in other jurisdictions by suit on the judgment or in any other manner provided by law.</p>

              <h4 className="policy-subtitle-normal">Changes to These Notices</h4>
              <p>We reserve the right to revise and reissue these notices at any time. Any changes will be effective immediately upon posting of the revised Legal Notices. Subject to applicable law, your continued use of our Service indicates your consent to the Legal Notices posted. Your continued use of the Service indicates your consent to the Legal Notices then posted. If you do not agree, please discontinue use of the Service, and uninstall Service downloads and applications.</p>
            </div>

          </div>
        </div>
      </div>
    );
  }
}

const mapStateToProps = (state) =>{
  return{
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(LegalNotice);
