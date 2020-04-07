import React, { Component } from 'react'
import { Link } from 'react-router-dom'
class LikedColleges extends Component{
    render(){
      const { likedColleges, visible } = this.props;
      let COLLEGES = '';
      if(likedColleges){
        COLLEGES = likedColleges.map((college, index)=>
          <Logo key={index} college={college}/>
        )
      }
      return(
          <div className="profile-widgets">
              <div className="widget-heading">
                <h2>Liked Colleges</h2>
              </div>
              <div className="widget-content">
              {!!visible ? 
                likedColleges.length > 0 ?
                  <ul className="liked-colleges">
                    {COLLEGES}
                  </ul>
                  :
                  <p>No Liked Colleges added yet</p>
                :
                <span className="private-section">This section is private</span>
              }
              </div>
          </div>
      )
    }
}
function Logo(props){
  let { college } = props;
  return(
    <li>
      <Link to={'/college/'+college.slug}><img src={"https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/"+ college.logo_url} /></Link>
    </li>
  )
}
export default LikedColleges;