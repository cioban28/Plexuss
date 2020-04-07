import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import { connect } from 'react-redux'
import './styles.scss'

class CalifoniaPolicy extends Component{
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
          <h3 className="policy-title">PLEXUSS’ CALIFORNIA PRIVACY POLICY</h3>
          <div className="policy-content">
            <h4 className="policy-subtitle">YOUR CALIFORNIA PRIVACY RIGHTS</h4>
            <div className="policy-subcontent">
              <p>California’s “Shine the Light” law permits customers in California to request certain details about how certain types of their information are shared with third parties and, in some cases, affiliates, for those third parties’ and affiliates’ own direct marketing purposes. Under the law, a business should either provide California customers certain information upon request or permit California customers to opt in to, or opt out of, this type of sharing.</p>
              <p>PLEXUSS provides California residents with the option to opt-out of sharing “personal information” as defined by California’s “Shine the Light” law with third parties for such third parties own direct marketing purposes. California residents may exercise that opt-out, and/or request information about PLEXUSS compliance with the “Shine the Light” law, by contacting PLEXUSS <a className="link">here</a>, or by sending a written request to:</p>
              <div className="inner-content">
                PLEXUSS, INC.<br/>Attention: Compliance Officer<br/>231 Market Place<br/>Suite 241<br/>San Ramon, California 94583
              </div>
              <p>Requests must include “California Privacy Rights Request” in the subject line of your request and include your name, street address, city, state, and ZIP code. Please note that PLEXUSS is not required to respond to requests made by means other than through the provided email address or mailing address.</p>
              <p>Any California residents under the age of eighteen (18) who have registered to use the Service, and who have posted content or information on the Service, can request removal by contacting us <a className="link">here</a>, or by sending a written request to:</p>
              <div className="inner-content">
                PLEXUSS, INC.<br/>Attention: Compliance Officer<br/>231 Market Place<br/>Suite 241<br/>San Ramon, California 94583
              </div>
              <p>Your written request should detail where the content or information is posted and attesting that you posted it. We will then make reasonable good faith efforts to remove the post from prospective public view or anonymize it so the minor cannot be individually identified to the extent required by applicable law. Please note that the removal process cannot ensure complete or comprehensive removal. For instance, third-parties may have republished or archived content by search engines and others that we do not control.</p>
              <p>The Service discloses its tracking practices (including across time and third-party services) <a className="link">here</a> and its practices regarding “Do not track” signals <a className="link">here</a>.</p>
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

export default connect(mapStateToProps, mapDispatchToProps)(CalifoniaPolicy);
