import React, { Component } from 'react'
import Comment from './comment'
import LoadMore from './loadMore'
import './styles.scss'
class Comments extends Component{
    constructor(props){
        super(props);
        this.state = { loadAllComment: false }
        this.handleLoadAllComments = this.handleLoadAllComments.bind(this);
    }
    handleLoadAllComments(){
        this.setState({ loadAllComment: !this.state.loadAllComment})
    }
    render(){
        let COMMENTS = ''
        let { article, user } = this.props;
        if(article && article.comments){
            COMMENTS = article.comments.map((comment, index) =>{
                if(this.state.loadAllComment){
                    return <Comment key={comment.id} comment={comment} articleId={article.id} user={user}/>
                }else{
                    if(index < 2){
                        return <Comment key={comment.id} comment={comment} articleId={article.id} user={user}/>
                    }
                }
            });
        }
        return(
            <div className="article_comments">
                {COMMENTS}
                {
                    article && article.comments && article.comments.length > 2 && 
                    <LoadMore handleLoadAllComments={this.handleLoadAllComments} loadAllComment={this.state.loadAllComment}/>                    
                }
            </div>
        )
    }
}

export default Comments;