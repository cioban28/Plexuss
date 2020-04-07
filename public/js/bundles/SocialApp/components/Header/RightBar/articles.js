import React, { Component } from 'react'
import { connect } from 'react-redux'
import Article from './article'
import Tabs from './../../common/Tabs';
import { Link, withRouter } from 'react-router-dom'
class MyArticles extends Component {
    constructor(props){
        super(props);

        this._calculateCounts = this._calculateCounts.bind(this);
        this._separateArticles = this._separateArticles.bind(this);
    }
    componentDidMount(){
    }

    _calculateCounts(array){
        let pCount = 0;
        let dCount = 0;
        array.forEach((item) => {
            if(item.status === 1){ pCount += 1; }
            else{ dCount += 1; }
        });
        return [dCount, pCount];
    }
    _separateArticles(array) {
        let pArray = [];
        let dArray = [];
        array.forEach((item) => {
            if(item.status === 1) { pArray.push(item); }
            else { dArray.push(item); }
        });
        return {publishedArray: pArray, draftArray: dArray};
    }

    render(){
       let { articles, handleClick, userAccountSettings, closeCallback } = this.props;
       let articleCount = !!articles && this._calculateCounts(Object.values(articles));
       let articlesArray = !!articles && this._separateArticles(Object.values(articles));

       return (
            <section>
                <div className="articles">
                    <div className="articles-head">
                        <i className="fa fa-angle-left angle-left large-2 medium-2 small-3 columns" onClick={() => handleClick('back-to-home')}></i>
                        <span className="article_heading large-10 medium-10 small-9 columns cursor" onClick={() => handleClick('back-to-home')}>{'My Articles'}</span>
                    </div>
                    <div className="article_btn_parent">
                        {
                            !!userAccountSettings && !!userAccountSettings.is_incognito ?
                            <div className="incognito-article" onClick={() => this.props.history.push('/social/settings')}>Turn Off Incognito Mode to <br/> Write an Article</div>
                            : (userAccountSettings === null || !!userAccountSettings && !userAccountSettings.is_incognito) &&
                            <Link to={"/social/article-editor"} className="article_btn write_article" onClick={closeCallback}>Write an article</Link>
                        }
                    </div>
                    <Tabs>
                        <div label={"PUBLISHED ("+articleCount[1]+")"}>
                            <Article articles={articlesArray.publishedArray} type={1}/>
                        </div>
                        <div label={"DRAFTS ("+articleCount[0]+")"}>
                            <Article articles={articlesArray.draftArray} type={0}/>
                        </div>
                    </Tabs>
                    <div className="dashboard-btn_parent">
                    <Link to={"/social/article-dashboard"} className="article_btn dashboard-btn" onClick={closeCallback}>View Dashboard</Link>
                    </div>
                </div>
            </section>
       )
    }
}
const mapStateToProps = (state) =>{
    return{
      articles: state.articles && state.articles.userArticles,
      userAccountSettings: !!state.user.data && state.user.data.userAccountSettings,
    }
  }
export default connect(mapStateToProps, null)(withRouter(MyArticles));
