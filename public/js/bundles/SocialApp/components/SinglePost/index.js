import React, { Component } from 'react';
import { connect } from 'react-redux'
import cloneDeep from 'lodash/cloneDeep';
import { withRouter } from 'react-router-dom';
import { homePage } from './../../actions/headerTab';
import { publishSinglePosts } from './../../api/post'
import Posts from './../common/post/index'
import { DeletedPost } from './helper'
import './styles.scss'
const _ = {
    cloneDeep: cloneDeep
}
class SinglePost extends Component{
    constructor(props){
        super(props);
        this.state={
            posts: [],
            flag: true,
            deletedPost: false
        }
        this.setPost = this.setPost.bind(this);
    }
    componentWillMount(){
        let id = this.props.match.params.id;
        publishSinglePosts(id)
        .then(()=>{
            this.setPost();
        })
    }
    componentDidMount(){
        // if(this.props.posts.length > 0){
            // let id = this.props.match.params.id;
            const { post } = this.props;
            // let index = posts.findIndex((post)=>post.id == id);
            let arr = [];
            if(post){
                arr.push(post)
                this.setState({
                    posts: _.cloneDeep(arr),
                    flag: false,
                })
            }else{
                this.setState({
                    deletedPost: true,
                })
            }
        // }
    }
    setPost(){
        // let id = this.props.match.params.id;
        const { post } = this.props;
        // let index = posts.findIndex((post)=>post.id == id);
        if(post){
            let arr = [];
            arr.push(post)
            this.setState({
                posts: _.cloneDeep(arr),
                deletedPost: false,
            })
        }else{
            this.setState({
                deletedPost: true,
            })
        }
    }
    componentWillReceiveProps(nextPops){
        if(nextPops.match.params.id != this.props.match.params.id){
            let id = nextPops.match.params.id;
            publishSinglePosts(id)
            .then(()=>{
                this.setPost();
            })
        }
        if(nextPops.post != this.props.post){
            // let id = this.props.match.params.id;
            const { post } = nextPops;
            // let index = posts.findIndex((post)=>post.id == id);
            if(post){
                let arr = [];
                arr.push(post)
                this.setState({
                    posts: _.cloneDeep(arr),
                    deletedPost: false,
                })
            }else{
                this.setState({
                    deletedPost: true,
                })
            }
        }
    }
    render(){
        const { user } = this.props;
        const { posts, deletedPost } = this.state;
        return(
            <div className="single-post-banner">
                <div className="single-post">
                    {
                        posts.length > 0 && user && !deletedPost &&
                        <Posts posts={posts} logInUser={user} commentsFlag={true}/> ||
                        deletedPost &&
                        <DeletedPost />
                    }
                </div>
            </div>
        )
    }
}
const mapStateToProps = (state) => {
    return{
        user: state.user.data,
        post: state.posts.singlePost,
    }
}
const mapDispatchtoProps = (dispatch) => {
    return {
      homePage: () => {dispatch(homePage())},
    }
}
export default connect(mapStateToProps, mapDispatchtoProps)(withRouter(SinglePost));
