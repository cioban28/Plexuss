import React, { Component } from 'react'
import Gauge from 'react-svg-gauge';
class CollegeDetils extends Component{
    constructor(props){
        super(props);
        this.state = {
            details: {},
        }
    }
    componentWillReceiveProps(nextProps){
        const { collegDetails, collegeId } = this.props;
        let index = collegDetails.findIndex(college => college.id == collegeId)
        this.setState({
            details: collegDetails[index],
        })
    }
    render(){
        let { details } = this.state;
        let { handleDetail } = this.props;
        return(
            <div className="college_details">
                <div className="college_details_row">
                    <div className="college_details_col">
                        <div className="college_title">Admission Deadline</div>
                        <div className="college_desc">{details.deadline}</div>
                    </div>
                    <div className="college_details_col">
                        <div className="college_title">In-state Tution</div>
                        <div className="college_desc">{details.inStateTution}</div>
                    </div>
                </div>
                <div className="college_details_row">
                    <div className="college_details_col">
                        <div className="college_title">Acceptance Rate</div>
                        <div className="college_desc">{details.percent_admitted}</div>
                    </div>
                    <div className="college_details_col">
                        <div className="college_title">Out-of-State Tution</div>
                        <div className="college_desc">{details.outStateTution}</div>
                    </div>
                </div>
                <div className="college_details_row">
                    <div className="college_details_col">
                        <div className="college_title">Student-Teacher Ratio</div>
                        <div className="college_desc">{details.student_faculty_ratio}</div>
                    </div>
                    <div className="college_details_col">
                        <div className="college_title">Student Body Size</div>
                        <div className="college_desc">{details.student_body_total}</div>
                    </div>
                </div>
                <div className="college_details_row">
                    <div className="college_details_col1">
                        <div className="college_title">SAT SCORE</div>
                        <div className="college_desc">{details.sat_total ? details.sat_total : 0}</div>
                        <Gauge value={details.sat_total ? details.sat_total : 0} width={70} height={70} label="" color="#2AC56C" min={0} max={2500} minMaxLabelStyle={{display: "none"}}/>
                    </div>
                    <div className="college_details_col1">
                        <div className="college_title">ACT SCORE</div>
                        <div className="college_desc">{details.act ? details.act : 0}</div>
                        <Gauge value={details.act ? details.act : 0} width={70} height={70} label="" color="#2AC56C" min={0} max={36} minMaxLabelStyle={{display: "none"}}/>
                    </div>
                    <div className="college_details_col1">
                        <div className="college_title">Athletics</div>
                        <div className="college_desc">{details.athletic}</div>
                    </div>
                </div>
                <div className="collapse" onClick={()=>handleDetail()}>COLLAPSE</div>
            </div>
        )
    }
}
export default CollegeDetils;
