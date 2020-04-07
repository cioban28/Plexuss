import React, { Component } from 'react'
import { connect } from 'react-redux'
import './styles.scss'
import axios from 'axios';
import DepartmentsList from './../Majors/majorsDepartmentList'
import WorldMap from './map'
import { getCollegesInitialData } from './../../../api/findColleges'
import Slider from "react-slick";
import BattleColleges from './battleColleges';
import InterestingCollegesList from './interestingCollegesList';
import ConferenceColleges from './conferenceColleges';
import CollegeDirectory from './collegeDirectory';
import {Collapse} from 'react-collapse';


 
class FindColleges extends Component {
  constructor(props){
    super(props)
    this.state ={
      reRender: false
    }
  }
  
  componentDidMount(){
    this.props.getCollegesInitialData()
  }

  setReRender = () => {
    this.setState((prevState) => ({
      reRender: !prevState.reRender,
      interestingCollapse: true
    }))
  }
  render(){

    let settings = {
      infinite: true,
      speed: 500,
      slidesToShow: 1,
      slidesToScroll: 1
    };


    const {initialCollegesData} = this.props
    let {interesting, conference} = !!initialCollegesData && initialCollegesData.lists
    let directories = !!initialCollegesData && initialCollegesData.dirAList
    
    let mainUrl = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/"
    return (
      <div>
        <div className="content-wrapper content-width-adjustment">
          <div className="row collapse fullWidth  college-home-c-wrapper">
            <div className="small-12 medium-10 large-10 columns find-clg-container-adjustment content-adjustment">

              <WorldMap />
              <DepartmentsList /> 
              {!!interesting &&
                <div className="row">
                  <div id="college-home-content" className="">
                    <div id="container-box" className="js-masonry row" style={{position: "relative", height: "901.188px"}}>
                      <div className="row">
                        <div className="small-12 medium-6 large-6 columns" style={{padding: 0}}>
                          <BattleColleges colleges={directories} />
                          <ConferenceColleges conference={conference} />
                        </div>
                      <div className="small-12 medium-6 large-6 columns">
                        <InterestingCollegesList interesting={interesting} setReRender={this.setReRender} ref={c => { this.container = c }}/>
                        {/* <CollegeDirectory /> */}
                      </div>
                    </div>
                      
                        
                    </div>
                  </div>
                </div>
              }
            </div>
          </div>
        </div>
      </div>
    );      
  }
}

const mapStateToProps = (state) => {
  return {
    initialCollegesData: state && state.findColleges && state.findColleges.initialCollegesData
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    getCollegesInitialData: () =>  getCollegesInitialData() 
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(FindColleges);