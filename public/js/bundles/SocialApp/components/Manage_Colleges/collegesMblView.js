import React, { Component } from 'react'
import axios from 'axios';
import CollegeDetils from './CollegeDetilsMbl'
import AddMoreColleges from './AddMoreColleges'
import MDSpinner from 'react-md-spinner';
import { Link } from 'react-router-dom'
class FavoriteColleges extends Component{
    constructor(props){
        super(props);
        this.state={
            collegDetails: [],
            collegeIds: [],
            addMore: false,
        }
        this.getCollegeData = this.getCollegeData.bind(this);
        this.handleAddMore = this.handleAddMore.bind(this);
    }
    getCollegeData(collegeId){
        let index = this.state.collegeIds.indexOf(collegeId);
        if(index == -1){
            const self = this;
            axios.get(`/ajax/recruiteme/portalcollegeinfo/data/${collegeId}`)
            .then(res => {
                let collegesArr = self.state.collegDetails;
                collegesArr.push(res.data);
                let idArr = self.state.collegeIds;
                idArr.push(collegeId);
                self.setState({
                    collegeIds: idArr,
                    collegDetails: collegesArr,
                })
            })
            .catch(error => {
            })
        }
    }
    handleAddMore() {
        this.setState({addMore: !this.state.addMore})
    }
    render(){
        let { colleges, selected, handleSelectCheck, loadSpinner } = this.props;
        return(
            <div className="favorite_colleges_mbl_view">
                <div className="title" onClick={this.handleAddMore}>Add more colleges</div>
                {
                    this.state.spinnerFlag &&
                    <div className="new-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>
                }
                {
                    !loadSpinner && colleges.length == 0 &&
                    <div className="no-record-found">No Record Found</div>
                }
                {
                    this.state.addMore &&
                    <AddMoreColleges handleAddMore={this.handleAddMore} />
                }
                {
                    colleges && colleges.map((college, index) => {
                        return <SingleColleg key={college.college_id+index} college={college} getCollegeData={this.getCollegeData} collegDetails={this.state.collegDetails} handleSelectCheck={handleSelectCheck} selected={selected}/>
                    })
                }
            </div>
        )
    }
}
class SingleColleg extends Component{
    constructor(props){
        super(props);
        this.state = {
            showDetail: false,
        }
        this.handleDetail = this.handleDetail.bind(this);
    }
    handleDetail(){
        this.setState({
            showDetail: !this.state.showDetail,
        })
    }
    render(){
        let { college, getCollegeData, collegDetails, handleSelectCheck, selected } = this.props;
        return(
            <div className="college_card">
                <div className="upper_banner">
                    <div className="checkbox_parent">
                        <input type="checkbox" checked={ selected && selected.includes(college.college_id)} onClick={()=>handleSelectCheck(college)}/>
                    </div>
                    <div className="name_parent">
                        <div className="clg_name">
                          <Link to={'/college/' +  college.school_name.replace(/ /g,'-')}>{college.school_name}</Link>
                        </div>
                        <div className="country_desc">
                            <div className={"flag flag-"+college.country_code}></div>
                            <span className="desc">{college.city},{college.state}</span>
                        </div>
                    </div>
                    <div className="rank">{ college.rank !== 'N/A' ? `#${college.rank}` : 'N/A' }</div>
                </div>
                <div className="_stats">
                    <div className="applied_status">
                        <div className="status">APPLIED?</div>
                        <div className="yes_no">{college.user_applied ? 'YES' : 'NO'}</div>
                    </div>
                    {
                    // <div className="applied_status">
                    //     <div className="status">HANDSHAKE</div>
                    //     <div className="img_parent">
                    //         {
                    //             college.hand_shake &&
                    //                 <img src="/social/images/Icons/handshake.svg" />
                    //         }
                    //     </div>
                    // </div>
                    }
                    <div className="applied_status">
                        <div className="status">MESSAGE</div>
                        <div className="img_parent">
                            <img src="/social/images/Icons/message.svg" />
                        </div>
                    </div>
                </div>
                <div className="bottom_banner">
                    <div className="quick_facts" onClick={()=>{getCollegeData(college.college_id); this.handleDetail()}}>
                        <span>QUICK FACTS</span>
                        {
                            this.state.showDetail ? <i className="fa fa-caret-up" aria-hidden="true"></i> : <i className="fa fa-caret-down" aria-hidden="true"></i>
                        }
                    </div>
                </div>
                {
                    this.state.showDetail &&
                        <CollegeDetils collegDetails={collegDetails} collegeId={college.college_id} handleDetail={this.handleDetail}/>
                }
            </div>
        )
    }
}
export default FavoriteColleges;
