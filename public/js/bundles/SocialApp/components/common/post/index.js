import React, { Component } from 'react'
import Post from './post'
class Posts extends Component{
    
    render(){
        const { posts, logInUser, commentsFlag } = this.props;
        return(
            <div className="posts">
              {
                posts.length != 0 && posts.map((post,index) => {
                  return <Post key={index} post={post} logInUser={logInUser} commentsFlag={commentsFlag}/>
                })
              }
            </div>
        )
    }
}
export default Posts;
