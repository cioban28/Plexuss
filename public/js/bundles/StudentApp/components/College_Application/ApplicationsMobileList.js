import React, { Component } from 'react'

class ApplicationsMobileList extends Component{
	constructor(props){
		super(props)
	}
	handleRemoveClick = () => {
   		this.props.removeCollegeFromMyApplicationsList(this.props.college);
	}
	render(){
		return(
			<div className='colleges-table'>
				<div className="main_container_div">
					<div className="main_container_inner">
						<div className="left_div">
							<div className="ranking_value">
								<span >{this.props.college.rank}</span>
							</div>
							<div className="college_image">
								<img src={this.props.college.logo_url} />
							</div>
							<div className="college_link">
									<a href={"/college/" + this.props.college.slug}>{!!this.props.college && this.props.college.school_name}</a>
							</div>
							<div className="college_location">
								<span>{`${this.props.college.city},${this.props.college.state}`}</span>
							</div>
						</div>
						<div className="right_div">
							<div className="app_cost">
								<span style={{color: "black"}}>APP COST</span>
							</div>
							<div className="app_cost_value">
								<span>${this.props.college.application_fee}</span>
							</div>
							<div className="apply_btn">
								<a href={this.props.college.app_url} style={{color: "white"}}>
									<i className="fa fa-chain" style={{color: "white"}}></i> Apply
								</a>
							</div>
						</div>
					</div>
					<div onClick={this.handleRemoveClick} className="college_remove">
						<span className="college_remove_span">
						<i className="fa fa-trash" style={{fontSize: "20px"}}></i> &nbsp; &nbsp; Remove</span>
					</div>
				</div>
			</div>
		)
	}
}

export default ApplicationsMobileList
