import React, { Component } from 'react'
import axios from 'axios'
import Fade from 'react-reveal/Fade'
import {toastr} from 'react-redux-toastr';
import { connect } from 'react-redux'
import './AddMoreColleges.scss'
import { addCollegeToMyCollegesList } from '../../../StudentApp/actions/Profile';
import { getFavColleges } from '../../api/favColleges'

class AddMoreColleges extends Component {
  constructor(props) {
    super(props)
    this.state = {
      results: {},
      input: '',
      college_ids: [],
      colleges_saved: false,
      disableAddBtn: false,
    }
    this.searchValue = this.searchValue.bind(this)
    this.addMoreCollegs = this.addMoreCollegs.bind(this)
    this.setCollegeIds = this.setCollegeIds.bind(this)
  }

  getSearchResults = (input) => {
    if (this.state.input != '') {
      axios({
        method: 'get',
        url: '/social/manage-colleges/getAutoCompleteSearchForPortalAddColleges?type=college&term='+input,
      })
      .then(res => {
        this.setState({
          results: res.data,
        })
      })
      .catch(error => {
        console.log("error",error)
      })
    }
  }

  addMoreCollegs() {
    if (this.state.college_ids.length > 0) {
      this.setState({ disableAddBtn: true });
      let data_array = {}
      data_array.college_ids = this.state.college_ids
      axios({
        method: 'post',
        url: '/ajax/json/multiplerecruiteme',
        data: data_array,
      })
      .then(res => {
        this.setState({
          colleges_saved: res.data,
          disableAddBtn: false,
        })
        this.props.handleAddMore()
        toastr.success('Your selected colleges have been added');
        getFavColleges()
      })
      .catch(error => {
        this.setState({ disableAddBtn: false });
        console.log("error",error)
        toastr.error('Failed to add colleges');
      })
    }
  }

  setCollegeIds(college_id) {
    if(this.state.college_ids.find(val=>val === college_id)){
      let index = this.state.college_ids.indexOf(college_id)
      let sample = this.state.college_ids
      sample.splice(index, 1)
      this.setState({
        college_ids: sample
      })
    }
    else{
      let sample = this.state.college_ids
      sample.push(college_id)
      this.setState({
        college_ids: sample,
      })
    }
  }

  searchValue(event) {
    this.getSearchResults(event.target.value);
    this.setState({input: event.target.value})
  }

  render() {
    let {handleAddMore, onProfile, closeModal} = this.props;
    let {results, disableAddBtn} = this.state;
    results = Array.from(results);
    return(
      <Fade top>
        <div className="add-more-container">
          <div className="close-add-more-page" onClick={onProfile ? closeModal : handleAddMore}>&#10005;</div>
          <div className="page-title">
            Add Colleges to {!!this.props.title ? this.props.title : 'Your Favorites'}
          </div>
          <div className="page-sub-heading">
            Which college do you want to be recruited by?
          </div>
          <div className="college-search-form">
            <i className="fa fa-search search-icon" aria-hidden="true" ></i>
            <input type="text" placeholder="Start typing a college name" className={`college-social-search-bar college-search-bar ${onProfile && 'unset-profile-input-styles'}`} onKeyUp={this.searchValue} />
          </div>
          <ul className="college-list">
            {
              results.length != 0 && results.map((result, index) => {
                return !result.already_selected && <li key={`${index}'-'${result.id}`}><CollegeCard college={result} addCollegeToMyCollegesList={this.props.addCollegeToMyCollegesList} setCollegeIds={this.setCollegeIds}/></li>
              })
            }
          </ul>
          {
            this.state.college_ids.length > 0 &&
            <button disabled={disableAddBtn} className="add-college-button" onClick={this.addMoreCollegs}>
              Add {this.state.college_ids.length} College(s) to Favorites
            </button>
          }
        </div>
      </Fade>
    )
  }
}

class CollegeCard extends Component {
  constructor(props) {
    super(props)
    this.state = {
      selected: false,
    }

    this.handleCard = this.handleCard.bind(this)
    this.handleAction = this.handleAction.bind(this)
  }
  handleCard() {
    this.setState({
      selected: !this.state.selected,
    })
  }
  handleAction(college) {
    this.handleCard()
    this.props.setCollegeIds(this.props.college.id)
    this.props.addCollegeToMyCollegesList(college);
  }
  render() {
    let { college, setCollegeIds } = this.props;
    return(
      <div className="card-container">
        <div className="college-image">
          <img src={college.image} />
        </div>
        <div className="college-info">
          <div className="college-name">{college.label}</div>
          <div className="country-code">{college.desc}</div>
        </div>
        <div className="action-button" onClick={() => this.handleAction(college)}>
          <div className={this.state.selected ? "selected-college" : "add-college"} >
            <img src={this.state.selected ? "/social/images/Icons/accept.png" : "/social/images/Icons/add-user.png"} />
          </div>
        </div>
      </div>
    )
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    addCollegeToMyCollegesList: (college) => dispatch(addCollegeToMyCollegesList(college)),
  }
}

export default connect(null, mapDispatchToProps)(AddMoreColleges)
