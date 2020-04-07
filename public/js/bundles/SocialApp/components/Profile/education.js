import React, { Component } from 'react'

class Education extends Component{
  constructor(props){
    super(props);
  }
  render(){
    let { education, user, visible } = this.props;
    return(
      <div className="profile-widgets">
          <div className="widget-heading">
            <h2>Education</h2>
          </div>
          <div className="widget-content">
            {!!visible ?
              education.length > 0 ?
                <div>
                  {education.map( (edu, i) => 
                      <EducationDetail key={i} education={edu} />
                    )
                  }
                </div>
                :
                <p>No Education added yet</p>
              :
              <span className="private-section">This section is private</span>
            }
          </div>
      </div>
    )
  }
}

function EducationDetail(props){
  let {education} = props;
  let logo = education.edu_level === 0 || (!!education.college && !!education.college.logo_url === false) ? 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png' : !!education.college && 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'+education.college.logo_url
  let schoolName = education.edu_level === 0 ? !!education.highschool && education.highschool.school_name : education.edu_level === 1 ?  !!education.college && education.college.school_name : 'N/A'
  return (
    <div className="row edu-detail-row">
      <div className="small-3 columns">
        <img className="edu-detail-logo" src={logo} />
      </div>
      <div className="small-9 columns">
        <div className="edu-detail-title">{schoolName + " '" + education.grad_year.substr(2,2)}</div>
        {education.edu_level === 1 && 
          <div className="edu-detail-subtitle">
            {!!education.degree && education.degree.display_name} in
            {!!education.majors && education.majors.map((maj,i) => <span>{(i === 0 ? ' ': ', ')+maj.major_name}</span>)}
          </div> 
        }
      </div>
    </div>
  )
}
export default Education;