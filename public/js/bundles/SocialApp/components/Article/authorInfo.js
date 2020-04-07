import React from 'react'

function AuthorInfo(props){
    let { article } = props;

    const formatDateCreate = () => {
        const dateSegments = article.created_at && article.created_at.split(' ')[0].split('-');
        return `${dateSegments[1]}/${dateSegments[2]}/${dateSegments[0]}`
    };    
    return(
        <div className="author_info_banner">
            <div className="image_banner">
                <img src={(article && article.user && article.user.profile_img_loc) ? "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/"+article.user.profile_img_loc : "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/user-avatar-r1.png" } alt=""/>
            </div>
            <div className="info">
                <div className="article_author_name">{article && article.user && article.user.fname} {article && article.user && article.user.lname}</div>
                <div className="date">{ !!article && formatDateCreate() }</div>
            </div>
        </div>
    )
}
export default AuthorInfo;
