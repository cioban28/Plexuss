import React, { Component } from 'react'
import { addCollegeToMyCollegesList, addCollegeToMyApplicationList, removeCollegeFromMyApplicationsList } from '../../actions/Profile';
import { connect } from 'react-redux'
class SearchedCollegesListItem extends Component{
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
      <div className='select-college-list list' key={this.props.key}>
        <div className='school-item'>
          <div className='rank col col-1'>{this.props.college.rank ? this.props.college.rank : 'N/A' }</div>
          <div className='school-name col col-4'>
            <div className='name' style={{display: 'inline'}}>
              <img className='logo' src={this.props.college.logo_url} />
              <a href={"/college/" + this.props.college.slug} target="_blank">{this.props.college.school_name}</a>
            </div>
          </div>
          <div className='st col col-3'>
            <div id='_common_tooltip' className='state' style={{paddingLeft: '1%'}}>{this.props.college.city}, {this.props.college.state}</div>
          </div>
          <div className='st col col-2'>
            <div id='_common_tooltip' className='state' style={{borderBottom: "none", paddingLeft: '13%'}}>{`${this.props.college.application_fee ? '$' + this.props.college.application_fee : 'N/A'}`}</div>
          </div>
          <div className='st col col-2'>
            <div onClick={appliedColleges.length > 0 ?  this.handleRemoveClick : this.handleAddClick } className={`save bottom-bar-save-btn ${appliedColleges.length>0 ? 'grey-save-btn' : ''}`} style={{borderBottom: "none", marginTop: 0, textAlign: "center", cursor: "pointer"}}><div className="clg-add-button" style={{fontSize: '14px'}}>{  appliedColleges.length > 0 ? 'Added' : 'Add Application +'}</div></div>
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

export default connect(mapStateToProps, mapDispatchToProps)(SearchedCollegesListItem);