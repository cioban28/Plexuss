import React, { Component } from 'react'
import ReactDOM from 'react-dom'
import { connect } from 'react-redux'
import axios from 'axios';
import Attributes from './attributes'
import Body from './body'
import CommentArea from './commentArea/index'
import Comments from './articleComments/index'
import { getSingleArticle } from './../../api/article'
import { SpinningBubbles } from './../common/loader/loader'
import { DeletedPost } from './helper'
import './styles.scss'
class Article extends Component{
    constructor(props){
        super(props);
        this.state={
            isChange: false,
            article: {},
            deletedArticle: false,
            deletedShareArticle: false,
        }
        this.loadData = this.loadData.bind(this);
        this.handleScrollToElement = this.handleScrollToElement.bind(this);
        this.isEmpty = this.isEmpty.bind(this);
    }
    componentDidMount(){
        this.loadData(this.props.match.params.id);
    }
    componentDidUpdate(prevProps) {
        if (this.props.location !== prevProps.location) {
            this.loadData(this.props.match.params.id);
        }
    }
    loadData(id) {
        this.setState({isChange: true})
        getSingleArticle(id)
        .then((res1) => {
            if(res1.data.length == 0){
                this.setState({deletedArticle: true})
            }
            this.setState({
                isChange: false,
            });
            const { article } = this.props;
            if(article && article.is_shared){
                axios({
                    method: 'get',
                    url: '/social/get-single-articles?article-id='+article.original_article_id,
                })
                .then(res => {
                    if(res.data.length == 0){
                        this.setState({deletedShareArticle: true})
                    }
                    this.setState({
                        article: res.data[0],
                    })
                })
                .catch(error => {
                })
            }else{
                this.setState({
                    article: article,
                })
            }
        });
    }
    handleScrollToElement(event) {
        const commentNode = ReactDOM.findDOMNode(this.refs.comment_ref)
        window.scrollTo(0, commentNode.offsetTop);
    }
    isEmpty(obj) {
        for(var key in obj) {
            if(obj.hasOwnProperty(key))
                return false;
        }
        return true;
    }
    render(){
        let id = this.props.match.params.id;
        const { deletedArticle, deletedShareArticle } = this.state;
        return(
            <div>
            {this.state.isChange ?( 
                <SpinningBubbles /> 
            ) : (
                this.props.user && this.props.article &&
                <div className="single_article">
                {
                    !deletedArticle && !this.isEmpty(this.props.article) &&
                    <span>
                        <Attributes article={this.props.article} user={this.props.user} handleScrollToElement={this.handleScrollToElement}/>
                        <Body article={this.state.article} deletedShareArticle={deletedShareArticle}/>
                        <CommentArea user={this.props.user} articleId={id}/>
                        <div ref="comment_ref"></div>
                        <Comments article={this.props.article} user={this.props.user}/>
                    </span>||
                    deletedArticle &&
                    <DeletedPost />
                }
                </div>
            )}
            </div>
        )
    }
}
function mapStateToProps(state){
    return{
        user: state.user.data,
        article: state.articles && state.articles.article && state.articles.article[0],
    }
}
export default connect(mapStateToProps, null)(Article);
