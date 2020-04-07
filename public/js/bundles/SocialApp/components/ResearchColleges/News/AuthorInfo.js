import React from 'react'
import TimeAgo from 'react-timeago'

function AuthorInfo(props){
    let { singleNews } = props;
    return(
        <div className="author_info_banner">
            <div className="image_banner">
                <img src={ singleNews.authors_img } alt=""/>
            </div>
            <div className="info">
                <div className="article_author_name">{singleNews.external_author}</div>
                <div className="external_form">Content writer @ {singleNews.external_name}</div>
                <div className="date"><TimeAgo date={singleNews.created_at} /></div>
            </div>
        </div>
    )
}
export default AuthorInfo;