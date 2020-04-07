
import React, { Component } from 'react'
import './styles.scss'
import SearchHeading from './searchHeading'
import SearchContent from './searchContent';
import axios from 'axios';
import { withRouter } from 'react-router-dom'
import { SpinningBubbles } from '../common/loader/loader'

class CollegeSearchComponent extends Component{
  constructor(props){
    super(props)
    this.state = {
      searchResults: {},
      currentPage: 1,
      nextPage: 2,
      prevPage: 1,
      flagImage: '',
      stateContent: '',
      readMore: false,
      recordCount: 0,
      metaData: {}
    }
  }

  componentDidMount(){
    axios.get(`/api/mainSearchMethod${this.props.location.search}${this.props.location.search.includes("page") ? '' : '&page='+this.state.currentPage}`)
      .then(res => {
        this.setState((prevState) => ({
          searchResults: res.data.searchData,
          nextPage: prevState.nextPage+1,
          prevPage: prevState.currentPage,
          flagImage: !!res.data.flag_image ? res.data.flag_image : '',
          stateContent: !!res.data.state_content ? res.data.state_content : '',
          recordCount: res.data.recordcount,
          stateName: res.data.state_name,
          metaData: res.data
        }))
      }).catch(error => {
      	console.log("not works", error);
      });
  }

  handleNextButtonClick =  () => {
    this.setState({recordCount: 0});
    axios.get(`/api/mainSearchMethod${this.props.location.search}&page=${this.state.nextPage}`)
      .then(res => {
        this.setState((prevState) => ({
          searchResults: res.data.searchData,
          currentPage: prevState.nextPage,
          nextPage: prevState.nextPage+1,
          prevPage: prevState.currentPage,
          flagImage: !!res.data.flag_image ? res.data.flag_image : '',
          stateContent: !!res.data.state_content ? res.data.state_content : '',
          recordCount: res.data.recordcount,
          stateName: res.data.state_name,
          metaData: res.data
        }))
      }).catch(error => {
      	console.log("not works", error);
      });
  }

  handlePrevButtonClick = () => {
    this.setState({recordCount: 0});
    axios.get(`/api/mainSearchMethod${this.props.location.search}&page=${this.state.prevPage}`)
      .then(res => {
        this.setState((prevState) => ({
          searchResults: res.data.searchData,
          nextPage: prevState.currentPage,
          currentPage: prevState.prevPage,
          prevPage: prevState.prevPage-1,
          flagImage: !!res.data.flag_image ? res.data.flag_image : '',
          stateContent: !!res.data.state_content ? res.data.state_content : '',
          recordCount: res.data.recordcount,
          stateName: res.data.state_name,
          metaData: res.data
        }))
      }).catch(error => {
      	console.log("not works", error);
      });
  }

  handleReadMoreClick = () => {
    this.setState((prevState) => ({
      readMore: !prevState.readMore
    }))
  }

  render(){
    return(
      <div>
        {
          this.state.recordCount <= 0 ? ( <SpinningBubbles /> ) : (
          <div className='college-search-content-wrapper'>
            <img src={this.state.metaData.background_image} className='results-background-image' />
            <div className="small-12 large-12 medium-12 columns">
              <div className="row">
                <div className="column small-12">
                  <div className="margin-adjustment row">

                    {this.state.flagImage.length > 0 &&
                      <div className="description-box-margin">
                        <h2 className="college-by-state-headline">Best Colleges in {this.state.stateName}</h2>
                        <div className='state-description-container'>
                          <div className={`description-content-container ${this.state.readMore ? 'read-more-height' : ''}`}>
                              <img src={this.state.flagImage} className="flag-image-college-list" alt=""/>

                              <div className="state-content" dangerouslySetInnerHTML={{__html: this.state.stateContent }}>
                              </div>
                          </div>

                          <div className="read-more-button" onClick={() => this.handleReadMoreClick()}>
                              <div className="read-more-text">{this.state.readMore ? 'Read Less' : 'Read More'}</div>
                          </div>
                      </div>
                      </div>
                    }

                    <SearchHeading
                      length={this.state.searchResults.length}
                      recordCount={this.state.recordCount}
                      stateName={!!this.state.metaData.country_name ? this.state.metaData.country_name : this.state.stateName}
                    />
                    <SearchContent
                      currentPage={this.state.currentPage}
                      searchData={this.state.searchResults}
                      handleNextButtonClick={this.handleNextButtonClick}
                      handlePrevButtonClick={this.handlePrevButtonClick}
                      currentPage={this.state.currentPage}
                      flagImage={this.state.flagImage}
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        )}
      </div>
    )
  }
}

export default withRouter(CollegeSearchComponent)


