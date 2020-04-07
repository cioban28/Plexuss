import React, { Component } from 'react'
class Table extends Component{
    render(){
        let { articleType, articles, type } = this.props; 
        let ARTICLES = ''
        let keys = [];
        if(articles){
            for (let key in articles) {   
                if (articles.hasOwnProperty(key)) keys.push(articles[key]);
            }
            ARTICLES = keys.map((article, index) =>{
                if(article.status === type){
                    return <TableRow key={index} article={article} />
                }
            });
        }
        return(
            <table>
                <col width="20%" />
                <col width="48%" />
                <col width="8%" />
                <col width="8%" />
                <col width="8%" />
                <col width="8%" />
                <thead>
                    <tr>
                        <th width="200">{'Date ' + articleType}</th>
                        <th>Title</th>
                        <th width="150"><img src="/social/images/article/Views/Views.png" /></th>
                        <th width="150"><img src="/social/images/article/Likes/Likes.png" /></th>
                        <th width="150"><img src="/social/images/article/comments/comments.png" /></th>
                        <th width="150"><img src="/social/images/article/share/Share.png" /></th>
                    </tr>
                </thead>
                <tbody>
                    {ARTICLES}
                </tbody>
            </table>
        )
    }
}
class TableRow extends Component{
    render(){
        let { article } = this.props;
        return(
            <tr>
                <td>{article && article.created_at}</td>
                <td><a href={"/social/article/"+article.id}>{article && article.article_title}</a></td>
                <td>{article && article.views && article.views.length}</td>
                <td>{article && article.likes && article.likes.length}</td>
                <td>{article && article.comments && article.comments.length}</td>
                <td>{article && article.share_count && article.share_count}</td>
            </tr>
        )
    }
}

export default Table;