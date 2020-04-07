import React, { Component } from 'react'
import './styles.scss'
import TopNav from './TopNav';

class News extends Component {
  render(){
    const { children } = this.props;

    return (
      <div>
        <TopNav />
        { children }
      </div>
    );
  }
}



export default News;
