import React, { Component } from 'react'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import '../styles.scss'

class OthersCollegeRanking extends Component {
     render(){
        const { other_colleges } = this.props;

    	   return (
                <div className="medium-6 columns ranking-boxes" style={{paddingBottom:15}}>
                    <div className="col-ranking-box" data-equalizer-watch>
                        <div className="plexuss-college-ranking-top-border">
                            Other Ranking Lists&nbsp;
                        </div>
                        <div className="row plr20">
                            {
                              other_colleges.map((other_college, i) => ( 
                                   i<3 ? (
                                           <div className="small-4  columns text-center" key={i}>
                                                <div className='ranking-lists-titles' >
                                                    {other_college.title}
                                                </div>
                                                <img className='ranking-lists-title-images' src={"https://s3-us-west-2.amazonaws.com/asset.plexuss.com/lists/images/" + other_college.image}  alt=""/>
                                            </div>
                                          ) : null

                                ))
                            }
                        </div>          
                        <div className="row">
                            <div className="small-10 columns small-centered" style={{textAlign:'center'}}>
                                <Link  to={'/ranking/categories'} className="ranking-button"> See all ranking lists </Link>
                            </div>
                        </div>                
                    </div>
                </div>
            )
    }
}

export default connect(null, null)(OthersCollegeRanking);
