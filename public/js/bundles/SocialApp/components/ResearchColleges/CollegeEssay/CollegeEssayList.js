import React, {Component} from 'react'
import { connect } from 'react-redux';
import {Link } from 'react-router-dom';
import './styles.scss'
import InfiniteScroll from 'react-infinite-scroll-component';
import CollegeEssaysListItem from './CollegeEssayListItem';

class CollegeEssaysList extends Component {
  constructor(props){
    super(props)
  }

  render(){
    const { premiumArticles } = this.props
    let length = premiumArticles.length
    let firstHalf = premiumArticles.slice(0, (length/3)),
        secondHalf = premiumArticles.slice(length/3, (2*length/3)),
        thirdHalf = premiumArticles.slice(2*length/3, (3*length/3))

    return(
      <div className="content-wrapper">
        <div id="newshomecontent" className="row collapse" style={{height: "100%", maxWidth: "100%"}}>
          <div className="news-content-container">
            <div className="mt20"></div>
            
            <div className="news-cont-left-container">
              <div id="container-box" className="js-masonry row"  >
                <InfiniteScroll
                  dataLength={length}
                  next={ () => this.props.getPremiumArticles(parseInt(length/6)+1 )}
                  hasMore={true}
                  endMessage={
                    <p style={{ textAlign: "center" }}>
                      <b>Yay! You have seen it all</b>
                    </p>
                  }
                >
                <CollegeEssaysListItem premiumArticles={firstHalf} />
                <CollegeEssaysListItem premiumArticles={secondHalf} />
                <CollegeEssaysListItem premiumArticles={thirdHalf} />
                </InfiniteScroll>
              </div>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default CollegeEssaysList;