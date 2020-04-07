import React, { Component } from 'react'
import { connect } from 'react-redux'
import '../styles.scss'
import { Link } from 'react-router-dom'

class PlexussCollegeRanking extends Component {
  
  render() {
    const { plexuss_colleges } = this.props;

      return (

        <div className="medium-6 columns ranking-boxes" style={{}}>
          <div className="col-ranking-box row" data-equalizer-watch>
              <div className="plexuss-college-ranking-top-border">Plexuss College Rank&nbsp;</div>
                {
                  plexuss_colleges.map((plexuss_college, i) => (
                    i<3 ?
                    (
                      <div className="TopRankingData" key={i}>
                        <div className="row" style={{lineHeight:'20px',padding:'8px 0'}} key={i}>
                        <div className="small-2 columns" style={{paddingTop: '7px'}}><div className="rank-bg-panel-small"><div className="rank-numb-small" style={{fontSize:'10px'}}>#{plexuss_college.plexuss}</div></div></div>
                        <div className="small-10 columns" style={{color:'#000000',fontSize:'15px'}}><strong><a href="www.google.com" style={{color:'#000000'}}>{plexuss_college.College}</a></strong> <br />{plexuss_college.city} , {plexuss_college.state}</div>
                        </div>
                      </div>
                    ) : null
                  ))
                }
                    
                <div style={{textAlign:'center'}} className='column small-10 small-centered'>
                    <Link to="/college-search?school_name=&country=US&state=&city=&zipcode=&degree=&department=&imajor=&major_slug=&locale=&tuition_max_val=0&enrollment_min_val=0&enrollment_max_val=0&applicants_min_val=0&applicants_max_val=0&min_reading=&max_reading=&min_sat_math=&max_sat_math=&min_act_composite=&max_act_composite=&religious_affiliation=&type=college&term=&myMajors=&page=1" className="ranking-button">View full Plexuss ranking list</Link>
                </div>
            </div>
        </div>
      )
  }
}

export default connect(null, null)(PlexussCollegeRanking);
