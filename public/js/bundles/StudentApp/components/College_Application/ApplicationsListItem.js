import React, { Component } from 'react'
import { addCollegeToMyCollegesList, removeCollegeFromMyApplicationsList } from '../../actions/Profile';
import { connect } from 'react-redux'

class ApplicationsListItem extends Component{
 constructor(props){
   super(props)
 }

 handleRemoveClick = () => {
   this.props.removeCollegeFromMyApplicationsList(this.props.college);
 }
 render(){
     return(
				<div className='select-college-list list'>
					<div className='school-item'>
							<div className='rank col col-1'>{this.props.college.rank}</div>
							<div className='school-name col col-4' style={{ width: '30%', fontSize: '12px'}}>
									<div className='name' style={{display: 'inline' }}>
											<img className='logo' src={this.props.college.logo_url} />
											<a href={"/college/" + this.props.college.slug} target="_blank">{!!this.props.college && this.props.college.school_name}</a>
									</div>
							</div>
							<div className='st col col-2' style={{ fontSize: '12px !important'}}>
									<div  className='state' style={{paddingLeft: '1%'}}>{`${this.props.college.city},${this.props.college.state}`}</div>
							</div>
							<div className='st col col-2'>
									<div  className='state' style={{borderBottom: "none", paddingLeft: '13%'}}>${this.props.college.application_fee}</div>
							</div>
							<div className='st col col-2'>
									<div className="apply-col apply-col-add apply" style={{borderBottom: "none", marginLeft: '19px', width: '50%'}}>
											<div className="apply-add-button" style={{fontSize: '12px'}}>
													<a target='_blank' href={this.props.college.app_url} style={{color: 'white'}}><i className="fa fa-chain" style={{color: 'white'}}></i> Apply</a>
											</div>
									</div>
							</div>
							<div onClick={this.handleRemoveClick} style={{cursor: 'pointer'}} className='st col col-1'>
									<i className="fa fa-trash" style={{fontSize: '20px',
													textAlign: 'center',
													paddingLeft: '40%'}}></i>
							</div>
					</div>

			</div>

		)
 	}

}

const mapStateToProps = (state) => {
 return{
   _profile: state._profile
 }
}

const mapDispatchToProps = (dispatch) => {
 return{
   removeCollegeFromMyApplicationsList: (college) => dispatch(removeCollegeFromMyApplicationsList(college)),
 }
}

export default connect(mapStateToProps, mapDispatchToProps)(ApplicationsListItem);
