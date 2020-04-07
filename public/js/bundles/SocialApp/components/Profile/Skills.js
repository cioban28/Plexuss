import React, { Component } from 'react'
import { connect } from 'react-redux';
import Skill from './Skill'
class Skills extends Component{
    render(){
        let { skillsAndEndorsement, visible } = this.props;
        let SKILLS = ''
        if(skillsAndEndorsement){
            SKILLS = skillsAndEndorsement.map((skill, index) =>
                <Skill key={index} skill={skill}/> 
            );
        }
        return(
            <div className="profile-widgets">
                <div className="widget-heading">
                  <h2>Skills and Endorsements</h2>
                </div>
                <div className="widget-content">
                {!!visible ? 
                  skillsAndEndorsement && skillsAndEndorsement.length > 0 ?
                    <span>
                      {
                        skillsAndEndorsement && skillsAndEndorsement.length > 0 && this.props.user_id !== skillsAndEndorsement[0].user_id &&
                          <div className="endorse">Endorse</div>
                      }
                      <ul className="skills-list">
                        {SKILLS}
                      </ul>
                    </span>
                    :
                    <p>No Skills added yet</p>
                  :
                  <span className="private-section">This section is private</span>
                }
                </div>
            </div>
        )
    }
}
const mapStateToProps = (state) =>{
    return{
        user_id: state.user.data.user_id,
    }
}

export default connect(mapStateToProps, null)(Skills);