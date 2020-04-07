import React, { Component } from 'react'
import { addCollegeToMyCollegesList, addCollegeToMyApplicationList, removeCollegeFromMyApplicationsList } from '../../actions/Profile';
import { connect } from 'react-redux'

class SearchedCollegesMobileList extends Component{
  constructor(props){
    super(props)
  }

  handleAddClick = () => {
    this.props.addCollegeToMyApplicationList(this.props.college)
  }

  handleRemoveClick = () => {
    this.props.removeCollegeFromMyApplicationsList(this.props.college)
  }
  render(){
  	let { _profile, route } = this.props
    let appliedColleges = this.props.myApplicationsList.filter(college => college.college_id === this.props.college.college_id)
  	return(
  		<div className='colleges-table'>
			<div className="main_container_div" key={this.props.key}>
				<div className="main_container_inner">
					<div className="left_div left_college_div">
						<div className="ranking_value">
							<span >{this.props.college.rank ? this.props.college.rank : 'N/A' }</span>
						</div>
						<div className="college_image">
							<img src={this.props.college.logo_url} />
						</div>
						<div className="college_link">
								<a href={"/college/" + this.props.college.slug}>{!!this.props.college && this.props.college.school_name}</a>
						</div>
						<div className="college_location">
							<span>{this.props.college.city}, {this.props.college.state}</span>
						</div>
					</div>
					<div className="right_div right_college_div">
						<div className="app_cost">
							<span style={{color: "black"}}>APP COST</span>
						</div>
						<div className="app_cost_value">
							<span>{`${this.props.college.application_fee ? '$' + this.props.college.application_fee : 'N/A'}`}</span>
						</div>
						<div className="add_college_btn" style={{color: "white"}} onClick={appliedColleges.length > 0 ?  this.handleRemoveClick : this.handleAddClick }>
							Add Application +
						</div>
					</div>
				</div>
			</div>
		</div>
  		)
  }
 }

 const mapStateToProps = (state) => {
  return{
    _profile: state._profile,
    myApplicationsList: state._profile.MyApplicationList
  }
}

const mapDispatchToProps = (dispatch) => {
  return{
    addCollegeToMyApplicationList: (college) => dispatch(addCollegeToMyApplicationList(college)),
    removeCollegeFromMyApplicationsList: (college) => dispatch(removeCollegeFromMyApplicationsList(college))
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(SearchedCollegesMobileList);
