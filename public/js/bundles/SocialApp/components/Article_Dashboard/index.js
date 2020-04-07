// /Article_Dashboard/index.js

import React from 'react'
import { connect } from 'react-redux'
import Tabs from './../common/Tabs'
import './styles.scss'
import Attributes from './attributes'
import Table from './table'
import { getArticles } from './../../api/article'
import MobileTable from './mobileTable'
import { Link } from 'react-router-dom'
import { Helmet } from 'react-helmet';

class Article_Dashboard extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      activeTab: 'published',
      showStats: true,
    }
    this.handleTab = this.handleTab.bind(this)
    this.handleShowStats = this.handleShowStats.bind(this)
  }
  componentDidMount(){
    getArticles();
  }

  componentDidUpdate() {
    window.scrollTo(0, 0)
  }

  handleTab(tab) {
    this.setState({
      activeTab: tab,
    })
  }

  handleShowStats() {
    this.setState({
      showStats: !this.state.showStats
    })
  }
  render(){
    return (
      <div>
        <Helmet>
          <title>Colleges Article Dashboard | College Recruiting Academic Network | Plexuss.com</title>
          <meta name="description" content="Colleges Articles Dashboard" />
          <meta name="keywords" content="Colleges Articles Dashboard" />
        </Helmet>
        <div id="content">
          <div className="stats-area-cover">
            <div className="dashboard-label">
                <span>
                  DASHBOARD
                </span>
            </div>
            <div className="write-article-mobile">
              <Link to="/social/article-editor" className="">
                <img src="/social/images/write-article.svg" />
                Write an article
              </Link>
            </div>
            {
              this.state.showStats &&
              <Attributes articles={this.props.articles}/>
            }
          </div>

          <div className="mobile-show-stats" onClick={this.handleShowStats}>
            {this.state.showStats ? "Hide stats" : "Show stats"}
          </div>

          <div className="article-list-area">
            <Tabs>
              <div label="PUBLISHED">
                <Table articles={this.props.articles} articleType={'Published'} type={1}/>
              </div>
              <div label="DRAFTS">
                <Table articles={this.props.articles} articleType={'Drafts'} type={0}/>
              </div>
            </Tabs>
          </div>

          <div className="mobile-article-list-area">
            <div className="row mobile-articles-menu">
              <div className="small-6 columns" onClick={() => this.handleTab("published")}>
                <span className={this.state.activeTab === "published" ? "article-menu-option article-active-tab" : "article-menu-option"}>PUBLISHED</span>
              </div>
              <div className="small-6 columns" onClick={() => this.handleTab("drafts")}>
                <span className={this.state.activeTab === "drafts" ? "article-menu-option article-active-tab" : "article-menu-option"}>DRAFTS</span>
              </div>
            </div>
            <div className="row mobile-table-heading">
              <div className="small-4 columns"><span className="articles-heading-label">Date</span></div>
              <div className="small-8 columns"><span className="articles-heading-label">Title</span></div>
            </div>
            <div className="article-table-body-container">
              <MobileTable articles={this.props.articles} type={this.state.activeTab === "published" ? 1 : 0}/>
            </div>
          </div>
        </div>
      </div>
    );
  }
}

const mapStateToProps = (state) =>{
  return{
    articles: state.articles && state.articles.userArticles ,
  }
}
export default  connect(mapStateToProps, null)(Article_Dashboard);
