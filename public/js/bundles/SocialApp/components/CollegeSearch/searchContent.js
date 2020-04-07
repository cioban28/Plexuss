
import React, { Component } from 'react';
import './styles.scss'
import axios from 'axios';
import SearchCollegeListItem from './searchCollegeListItem';
 
class SearchContent extends Component{
  constructor(props){
    super(props)
    this.state={
      prevHovered: false,
      nextHovered: false
    }
  }

  componentDidMount(){
  }

  handleNextMouseEnterLeave = () => {
    this.setState((prevState) => ({nextHovered: !prevState.nextHovered}))
  }

  handlePrevMouseEnterLeave = () => {
    this.setState((prevState) => ({prevHovered: !prevState.prevHovered}))
  }
  render(){
    let searchData  = this.props.searchData && (this.props.searchData.data ? this.props.searchData.data : this.props.searchData)
    let flagImage = this.props.searchData && this.props.searchData

    return(
      <div>
        <ul className="search-content-ul">
          {!!searchData.length && searchData.map( (datum, index) => 
            { return(<SearchCollegeListItem datum={datum} key={index} identity={index} />)}
          )}
          {
            !!searchData.length === 0 && <div class="row pt20 c79" style={{textAlign: "center", verticalAlign: "middle", paddingTop: "20px"}}>No {this.props.currentPage > 1 ? 'More' : ''} Records Found..</div>
          }
          <div className="row pt40">
            <div className="large-2 small-2 column no-padding"></div>
              <div className="large-10 small-10 column no-padding">
                <ul className="pagination">
                {this.props.currentPage <= 1 ? 
                  <li className={(this.props.currentPage > 1 ? "set-cursor temp" : "disabled")} disabled   onClick={this.props.handlePrevButtonClick}>
                  <a href="#" rel="prev">« Previous </a> </li>
                :  
                  <li className={(this.props.currentPage > 1 ? "set-cursor temp" : "disabled")}   onClick={this.props.handlePrevButtonClick}>
                  <a href="#" rel="prev">« Previous </a> </li>
                }
                  <li className="temp" ><a href="#" onClick={this.props.handleNextButtonClick} rel="next">Next »</a></li>
                </ul>
              </div>               
            </div>
        </ul>
      </div>
    )
  }
}


export default SearchContent



