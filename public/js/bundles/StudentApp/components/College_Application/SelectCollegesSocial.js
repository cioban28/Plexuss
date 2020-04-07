import React, { Component } from 'react'
import { connect } from 'react-redux'
import ReactDom from 'react-dom'
import axios from 'axios'
import CollegesSearchList from './CollegesSearchList';
import SearchedCollegesMobileList from './SearchedCollegesMobileList';
import Modal from 'react-modal';
import { removeCollegeFromMyCollegesList, removeCollegeFromMyApplicationsList, getProfileDataLists } from '../../actions/Profile';
import AddMoreColleges from '../../../SocialApp/components/Manage_Colleges/AddMoreColleges';

class SelectCollegesSocial extends Component{
	constructor(props){
		super(props)
		this.state={
			searchedColleges: [],
			addMore: false,
			showModal: true
		}
	}

	componentWillReceiveProps(np){
		let { dispatch, route, _profile } = this.props;
		if ( np._profile.save_success !== _profile.save_success && np._profile.save_success ) 
		{
			if ( window.location.pathname.includes('social')  )
			{
				this.props.history.push('/social/one-app/'+route.next + window.location.search);
			}
			else
			{
				browserHistory.push('/college-application/'+route.next + window.location.search);
			}
		}
	}

	handleSearchCollege = (event) => {
		let searchTerm = event.target.value;
		axios.get(`/getslugAutoCompleteData?type=colleges&urlslug=&term=${searchTerm}`)
		.then(response => {
		this.setState({ searchedColleges: response.data });
		})
		.catch(error => {
		})
	}

	handleAddMore = () => {
		this.setState({addMore: !this.state.addMore})
	}

	handleRemoveClick = (college) => {
		if (this.props._profile.MyApplicationList.length === 1)
		{
			if (this.props.showMouseEnterModal) {this.props.handleMouseEnterLeave();}
			this.props.removeCollegeFromMyApplicationsList(college)
		}
		else{
			this.props.removeCollegeFromMyApplicationsList(college)
		}
	}

	render(){
		let { _profile, route } = this.props
		return (
			this.state.addMore ? 
				<AddMoreColleges handleAddMore={this.handleAddMore}/> :
				<div id='select-colleges'>
					<div id="select-colleges-social">
						<div className='all-content'>
							<div className='heading-div'>
								<span className='heading-span'>
									<span className='heading-select-clgs'>Select Colleges</span>
									<span className='heading-content'>Add colleges to your list to start applying and keep track of your progress on each application</span>
								</span>
							</div>


							<button className="save bottom-bar-save-btn" onClick={() => this.setState((prevState) => ({addMore: !prevState.addMore}))} >Add more colleges</button>
							{
										this.props.showMouseEnterModal && 
										<div id="select_colleges_modal" style={{maxWidth: '62.5em', width: '20%'}}>
											<Modal style={{
												overlay: {
													backgroundColor: 'papayawhip',
															},
															content: {
																left: 0, top: '0 !important', right: 0, bottom: '11%', width: '37%', background: "transparent", border: 'none !important',
															}
														}}
												isOpen={this.props.showMouseEnterModal && _profile.MyApplicationList.length > 0}
												>
												<div>
													
													<div  style={{padding: "10% 2%", background: "white", borderRadius: '20px'}}  >
														<div className="modal-close" onClick= {this.props.handleMouseEnterLeave} style={{cursor: 'pointer'}}>
															x
														</div>
														<div className="scrollbar" id="style-2" style={{maxHeight: '280px', overflowY: 'auto'}}>
															{ _profile.MyApplicationList.map( college => 
																<div className="row" style={{marginBottom: '27px'}} >
																	<div className="large-8 left" style={{fontSize: '14px', width: 'auto'}}>
																		<img className='logo' style={{width: '50px'}} src={college.logo_url} />
																		&nbsp;{college.school_name}
																	</div>

																	<div onClick={() => {this.handleRemoveClick(college)}} style={{background: "transparent", paddingTop: '2%', paddingRight: '2%', float: 'right', width: 'auto', color: 'black', textDecoration: 'underline', fontSize: '11px', fontWeight: 'normal', cursor: 'pointer'}} className='large-4'>
																			<i className="fa fa-trash" style={{fontSize: '18px',
																							textAlign: 'center'}}></i>
																	</div>
																</div>
															)}
														</div>
														</div>
													</div>
												<span style={{
													content: " ",
													position: "absolute",
													left: "15%",
													borderWidth: "21px",
													borderStyle: "solid",
													borderColor: "white transparent transparent transparent"
												}}>
												</span>
											</Modal>
										</div>
									}
							

									<div className="hide-for-small-only">
									{!!this.props.myCollegesList && <CollegesSearchList colleges={this.props.myCollegesList} />}
									</div>
									<div className="show-for-small-only">
					        {this.props.myCollegesList && this.props.myCollegesList.map((college, index) =>  <SearchedCollegesMobileList key={index} college={college} /> )}
									</div>
						</div>
					</div>

				 </div>
		)
	}
}
const mapStateToProps = (state,  props) => {
	return {
		_profile: state._profile,
		myCollegesList: state._profile.MyCollegeList,
	}
}

const mapDispatchToProps = (dispatch) => {
	return{
		removeCollegeFromMyCollegesList: (college) => dispatch(removeCollegeFromMyCollegesList(college)),
		removeCollegeFromMyApplicationsList: (college) => dispatch(removeCollegeFromMyApplicationsList(college)),
		getMyCollegesList: () => dispatch(getProfileDataLists()),
	}
} 

export default connect(mapStateToProps, mapDispatchToProps)(SelectCollegesSocial);