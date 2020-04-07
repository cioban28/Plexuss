import React, { Component } from 'react'
import { connect } from 'react-redux'
import PeopleViewed from './PeopleViewed'
import MyArticles from './articles.js'
import LiveNews from './live_news'
import HomeRightBar from './home_rightbar'
import './styles.scss';
import { getArticles } from './../../../api/article'

class RightBar extends Component{
  constructor(props){
    super(props);
    this.state = {render:'my-articles'}
    this._renderSubComp = this._renderSubComp.bind(this);
    this.handleClick =this.handleClick.bind(this);
    this.SIC_handler =this.SIC_handler.bind(this);
  }
  componentDidMount(){
      getArticles();
  }
  handleClick(compName){
    this.setState({render:compName});
  }
  SIC_handler () {
    switch(window.location.pathname){
      case '/social/profile': return <PeopleViewed />
      case '/social/article-dashboard' : return <MyArticles handleClick = {this.handleClick} />
      case '/social/article-editor' : return <MyArticles handleClick = {this.handleClick} />
      case '/home' : return <HomeRightBar handleClick = {this.handleClick} articles={this.props.articles}/>
      default : return <HomeRightBar handleClick = {this.handleClick} articles={this.props.articles}/>
    }
  }
  _renderSubComp(){
    let { closeCallback } = this.props;
    switch(this.state.render){
      case 'live-news': return <LiveNews handleClick = {this.handleClick} />
      case 'my-articles' : return <MyArticles handleClick = {this.handleClick} closeCallback={closeCallback} />
      case 'back-to-home' : return <HomeRightBar handleClick = {this.handleClick} articles={this.props.articles}/>
      case 'home' : return this.SIC_handler()
    }
  }
  render() {
    let { closeCallback } = this.props;
    return (
      <div className="rightbar">
        {this.props.user.signed_in == 1 ? (this._renderSubComp()) : (
          <div className="article-preview">
              <div className="right-circle"><img className="img-circle" src="/images/frontpage/articles-circle.png"/></div>
              <div className="right-text"><span className="desc-text">Write your own articles and share them with your peers</span></div>
              <div className="right-login"><a href="/signup?utm_source=SEO&utm_medium=frontPage" className="btn-login">Login or Signup</a></div>
          </div>
        )}
      </div>
    );
  }
}
const mapStateToProps = (state) =>{
  return{
    articles: state.articles && state.articles.userArticles ,
    user: state.user && state.user.data,
  }
}
export default connect(mapStateToProps, null)(RightBar);
