import React from 'react'

export function UserHeader(props){
    let { post } = props;
    let imgStyles =  { backgroundImage: post && post.user && post.user.profile_img_loc ? "url('https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/"+post.user.profile_img_loc +"')"
    : 
    post.user && post.user.fname ? 'url(/social/images/Avatar_Letters/'+post.user.fname.charAt(0).toUpperCase()+'.svg)'
    :
    'url(/social/images/Avatar_Letters/P.svg)'}
    return(
        <div className="post_author_box">
            <div className="post-user user_images_parrent">
                <div className="post-user-img" style={imgStyles}/>
                {/* <img src={post && post.user && post.user.profile_img_loc ? "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/"+post.user.profile_img_loc : "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/user-avatar-r1.png"}/> */}
            </div>
            <div className="user_info_header">
                <div className="user_info_name">{post && post.user && post.user.fname} {post && post.user && post.user.lname}</div>
                <div className="user_info_title">{post && post.user && post.user.is_student === 1 ? 'Student' : ''}</div>
            </div>
        </div>
    )
}

export function Text(props){
    let { post } = props;
    return(
        <div className="post_content">{post && post.post_text}</div>
    )
}

export function SingleImage(props){
    let { image, widthIsGreaterThanHeight, percent, onMobile } = props;
    let styleToApply = onMobile ? ( widthIsGreaterThanHeight ? {height: `${percent}%`,marginTop: `${(100-percent)/2}%`} : {width: `${percent}%`, marginLeft: `${(100-percent)/2}%`}) : {}
    return(
        <div className="image_parent">
        {
            image.image_link && 
            <img  id="lightbox_img" src={image.image_link} alt=""/>
        }
        </div>
    )
}

export function SampleNextArrow(props){
    const { onClick } = props;
    return(
        <div className="next_arrow_banner">
            <div onClick={onClick} className="next_arrow">
                <i className="fa fa-chevron-right"></i>
            </div>
        </div>
    )
}
export function SamplePrevArrow(props){
    const { onClick } = props;
    return(
        <div className="pre_arrow_banner">
            <div onClick={onClick} className="pre_arrow">
                <i className="fa fa-chevron-left"></i>
            </div>
        </div>
    )
}