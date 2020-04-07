import React, { Component } from 'react'
import { UserHeader, Text } from './helper.js'
import PostFunctionality from './../post_functionality'
import DesktopComments from './../desktopComments'
class RightPostion extends Component{
    constructor(props){
        super(props);
        this.state={
            initialState: true,
        }
    }
    componentDidMount(){
        const { showDesktopComment, desktopComment } = this.props;
        this.setState({initialState: desktopComment})
        if(!desktopComment){
            showDesktopComment();
        }
    }
    componentWillUnmount(){
        const { showDesktopComment, desktopComment } = this.props;
        if(desktopComment != this.state.initialState){
            showDesktopComment();
        }
    }
    render(){
        let { post, handleMobileCommentd, logInUser, showDesktopComment, desktopComment } = this.props;
        return(
            <div className="post_box_parent">
                <div className="post_box">
                    <UserHeader post={post}/>
                    <Text post={post}/>
                </div>
                {
                    post &&
                        <div className="post-content">
                            <PostFunctionality post={post} handleMobileCommentd={handleMobileCommentd} logInUser={logInUser} showDesktopComment={showDesktopComment} desktopComment={desktopComment}/>
                        </div>
                }
                {
                    desktopComment &&
                        <DesktopComments post={post} logInUser={logInUser}/>
                }
            </div>
        )
    }
}
export default RightPostion;
