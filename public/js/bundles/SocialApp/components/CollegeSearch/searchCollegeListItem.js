
import React, { Component } from 'react';
import { Link } from 'react-router-dom'
import './styles.scss'
 
class SearchCollegeListItem extends Component{
  constructor(props){
    super(props)
    this.state={
      showLinks: false
    }
  }

  handleQuickLinksClick = () => {
    this.setState((prevState) =>({
      showLinks: !prevState.showLinks
    }))   
  }

  render(){
    return(
      <li key={this.props.identity} >
        <div className="search-content-results-div">
          <div className="row pt20">
            <div className="large-2 small-3 column text-center">
              <img src={`https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/${this.props.datum.logo_url}`} className="college_logo" alt=""/>
            </div>

            <div className="large-10 small-9 column pr10">
              <span className="c-blue fs18 f-bold"><Link to={`/college/${this.props.datum.slug}`} className="c-blue">{this.props.datum.school_name}</Link></span>&nbsp;<span className={`flag flag-${this.props.datum.country_code}`}> </span>
              <span className="c79 fs12 d-block mt10 l-hght18">
                Acceptance rate: {this.props.datum.percent_admitted}%  |  In-state Tuition: ${this.props.datum.tuition_avg_in_state_ftug}  
                |  Total Enrolled Students: {this.props.datum.admissions_total}  |   Plexuss Rank {!!this.props.datum.plexuss ? this.props.datum.plexuss : 'N/A'} <br/> {this.props.datum.city},
                
              <span className="f-bold"></span>  |  <span className="c-blue fs12 quick-linker" style={{cursor: "pointer"}} onClick={() => this.handleQuickLinksClick()}>open quick links  <span className="expand-toggle-span run" id="quick-link-div-297863">&nbsp;</span> </span>
              </span>                      
        
              {this.state.showLinks && <div className="row d-none" id="quick-link-297863"> 
                <ul className="quick-link-ul">
                  <li className="large-4 small-12 medium-4" style={{float: "left"}} ><Link to={`/college/${this.props.datum.slug}/admissions`} className="c-blue">Admissions</Link></li>
                  <li className="large-4 small-12 medium-4" style={{float: "left"}}><Link to={`/college/${this.props.datum.slug}/ranking`} className="c-blue">Ranking</Link></li>                                
                  <li className="large-4 small-12 medium-4" style={{float: "left"}}><Link to={`/college/${this.props.datum.slug}/financial-aid`} className="c-blue">Financial Aid</Link></li>
                </ul>
              </div>}
            </div>
          </div>
        </div>
      </li>
    )
  }
}


export default SearchCollegeListItem



