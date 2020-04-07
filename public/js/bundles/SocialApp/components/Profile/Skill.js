import React, { Component } from 'react';
import { connect } from 'react-redux';
import { saveEndorsements } from './../../api/profile';

class Skill extends Component{
    constructor(props){
        super(props);
        this.state={
            flag: false,
        }
        this.flagHandler = this.flagHandler.bind(this);
        this.handleEndorse = this.handleEndorse.bind(this);
    }
    flagHandler(){
        this.setState({
            flag: !this.state.flag,
        })
    }
    handleEndorse(skill){
        let data = {public_profile_skills_id: skill.id, endorser_user_id: this.props.user_id, profile_id: skill.user_id};
        saveEndorsements(data);
    }

    render(){
        const { skill } = this.props;
        const endorseCount = !!skill.endorsers && skill.endorsers.length;
        return(
            <span>
                <li>
                    <i className={"cursor_pointer fa " +(this.state.flag ? "fa-caret-down" : "fa-caret-right")} onClick={() => this.flagHandler()}></i>
                    <span className="cursor_pointer skill-name" onClick={() => this.flagHandler()}>{skill.name}</span>
                    <div className="skill-endorsers-pics">
                        {skill.endorsers.map((user, i) => (
                                <div key={i} className="skill-endorser-img" style={{backgroundImage: 'url("'+user.student_profile_photo+'")'}}/>
                        ))}
                    </div>
                    <div className="skills-count">{endorseCount}</div>
                    {this.props.user_id === skill.user_id ? <div/> :
                        skill.endorsers.some(i => i.user_id === this.props.user_id) ?
                            <div className="button add-button endorse-btn" onClick={() => this.handleEndorse(skill)}><i className="fa fa-check" style={{fontSize: '12px'}}/></div>
                            :
                            <div className="button add-button" onClick={() => this.handleEndorse(skill)}>+</div>
                    }
                </li>
                {
                    this.state.flag && 
                    <div className="single_skill_more_details_container">
                        <div>
                            <i className="fa fa-user-plus margin_right"></i>
                            <span className="margin_right">Group</span>
                            <span>{skill.group}</span>
                        </div>
                        <div>
                            <i className="fa fa-users margin_right"></i>
                            <span className="margin_right">Position</span>
                            <span>{skill.position}</span>
                        </div>
                        <div>
                            <i className="fa fa-archive margin_right"></i>
                            <span className="margin_right">Awards</span>
                            <span>{skill.awards}</span>
                        </div>
                    </div>
                }
            </span>
        )
    }
}

const mapStateToProps = (state) =>{
    return{
        user_id: state.user.data.user_id,
    }
}

export default connect(mapStateToProps, null)(Skill);