import React, { Component } from 'react'
import { connect } from 'react-redux'
import Education from './education'
import Claim from './claim'
import Objective from './objective'
import Occupation from './occupation'
import Skills from './Skills'
import Projects from './projects'
import LikedColleges from './likedColleges'

const nonStudents = ['alumni', 'parent', 'counselor', 'organization_rep'];

class UserInfo extends Component{
    constructor(props){
        super(props);

        this.state = {

        }
    }
    render(){
        return(
            <div className="small-12 medium-4 large-4 columns ">
                { nonStudents.includes(this.props.user_type) && <Education education={this.props.education} visible={!!this.props.education}/> }
                <Claim claimToFame={this.props.claimToFame} visible={!!this.props.claimToFame}/>
                { this.props.user_type === 'student' && <Objective objective={this.props.objective} visible={!!this.props.objective}/> }
                { nonStudents.includes(this.props.user_type) && <Occupation occupation={this.props.occupation} visible={!!this.props.occupation}/> }
                <Skills skillsAndEndorsement={this.props.skillsAndEndorsement} visible={!!this.props.skillsAndEndorsement}/>
                <Projects projectsAndPublications={this.props.projectsAndPublications} articles={this.props.articles} visible={!!this.props.projectsAndPublications}/>
                <LikedColleges likedColleges={this.props.likedColleges} visible={!!this.props.likedColleges}/>
            </div>
        )
    }
}
const mapStateToProps = (state) =>{
    return{
        user_type:  state.profile.user.user && state.profile.user.user.user_type,
        education: state.profile.user.education,
        claimToFame: state.profile.user.claimToFame,
        objective: state.profile.user.objective,
        occupation: state.profile.user.occupation,
        skillsAndEndorsement: state.profile.user.skillsAndEndorsement,
        projectsAndPublications: state.profile.user.projectsAndPublications,
        likedColleges: state.profile.user.likedColleges,
        articles: state.profile.user.articles,
    }
}
export default connect(mapStateToProps, null)(UserInfo);