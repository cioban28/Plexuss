import React from 'react'

function ArticleImage(props){
    let { article } = props;
    return(
        <div className="top_image">
            <img src={article && article.images && article.images[0] && article.images[0].image_link} alt=""/>
        </div>
    )
}
export default ArticleImage;