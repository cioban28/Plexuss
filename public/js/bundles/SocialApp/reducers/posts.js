import cloneDeep from 'lodash/cloneDeep';
import initialState from './initialState';
const _ = {
    cloneDeep: cloneDeep
}
const posts = (state = initialState.posts, action) => {
    switch(action.type){
        case "GET_SINGLE_POST":
            return { ...state, singlePost: _.cloneDeep(action.payload)};
        case "GET_HOME_POSTS":
            const { payload } = action;
            let newPosts = Object.assign([], state.posts);
            let startPoint = state.startPoint + 1;
            newPosts = [...newPosts,  ...payload ];
            if(payload.length == 0){
                state.isNextPost = false;
                startPoint = startPoint - 1;
            }
            return {...state, posts: _.cloneDeep(newPosts), startPoint: startPoint}
        case "GET_HOME_POSTS_FAILURE":
            return{
                ...state,
                isNextPost: false,
            }
        case "ADD_POST":
            newPosts =  _.cloneDeep(state.posts);
            let a_index = newPosts.findIndex( post => post.id == action.payload.id);
            if(a_index == -1){
                newPosts.unshift(action.payload);
                state.activePostId = action.payload.id;
                if(action.payload.hasOwnProperty('post_text')){
                    state.postType = 'post';
                }else{
                    state.postType = 'article';
                }
            }else{
                if(action.payload.post_status == 0){
                    newPosts.splice(a_index, 1);
                }else{
                    newPosts[a_index] = action.payload;
                }
            }
            return { ...state, posts: _.cloneDeep(newPosts), singlePost: _.cloneDeep(action.payload)};
        case "HIDE_POST":
            newPosts =  _.cloneDeep(state.posts);
            if(action.payload.type == 'post'){
                a_index = newPosts.findIndex( post => post.id == action.payload.id && post.hasOwnProperty('post_text'));
            }else{
                a_index = newPosts.findIndex( post => post.id == action.payload.id && post.hasOwnProperty('article_text'));
            }
            if(a_index != -1){
                newPosts.splice(a_index, 1);
            }
            return { ...state, posts: _.cloneDeep(newPosts)};
        case "DELETE_POST":
            newPosts =  _.cloneDeep(state.posts);
            let newSharedPosts =  _.cloneDeep(state.sharedPosts);
            let deletePost = action.payload;
            let postIndex = -1;
            let sharedPostIndex = -1;
            if(deletePost.type == 'post'){
                postIndex = newPosts.findIndex(o => o.id === deletePost.id && o.hasOwnProperty('post_text'));
                sharedPostIndex = newSharedPosts.findIndex(post => post.id == action.payload.id && post.hasOwnProperty('post_text'));
            }else{
                postIndex = newPosts.findIndex(o => o.id === deletePost.id && o.hasOwnProperty('article_text'));
                sharedPostIndex = newSharedPosts.findIndex(post => post.id == action.payload.id && post.hasOwnProperty('article_text'));
            }
            if(postIndex != -1){
                newPosts.splice(postIndex,1);
            }
            if(sharedPostIndex != -1){
                newSharedPosts.splice(sharedPostIndex,1);
            }
            return {
                ...state,
                posts: _.cloneDeep(newPosts),
                sharedPosts: _.cloneDeep(newSharedPosts)
            };
        case "ADD_COMMENT":
            let posts =  _.cloneDeep(state.posts);
            let singlePost = _.cloneDeep(state.singlePost);
            let comment = action.payload[0];
            postIndex = '';
            let postId = '';
            if(comment.social_article_id){
                let articleId = comment.social_article_id;
                postIndex = posts.findIndex(o => o.id === articleId && o.hasOwnProperty('article_text'));
            }else{
                postId = comment.post_id;
                postIndex = posts.findIndex(o => o.id === postId && o.hasOwnProperty('post_text'));
            }
            let comIndex = -1;
            if(postIndex != -1){
                comIndex = posts[postIndex].comments.findIndex(com => com.id == comment.id);
            }
            if(postIndex != -1 && comIndex == -1){
                posts[postIndex].comments.push(comment);
            }
            if (singlePost.id === comment.post_id) {
                singlePost.comments.push(comment);
            }
            return { ...state, posts: _.cloneDeep(posts), singlePost: _.cloneDeep(singlePost) };
        case "LIKE":
            posts =  _.cloneDeep(state.posts);
            let likeObj = action.payload;
            let comment_index = -1;
            postIndex = -1;
            if(likeObj.post_id){
                postIndex = posts.findIndex(post => post.id == likeObj.post_id && post.hasOwnProperty('post_text'));
                if(likeObj.post_comment_id){
                    if(postIndex !== -1){
                        comment_index = posts[postIndex].comments.findIndex(comment => comment.id == likeObj.post_comment_id);
                        if(comment_index !== -1){
                            posts[postIndex].comments[comment_index].likes.push(likeObj);
                        }
                    }
                }else{
                    if(postIndex !== -1){
                        posts[postIndex].likes.push(likeObj);
                    }
                }
            }else{
                postIndex = posts.findIndex(article => article.id == likeObj.social_article_id && article.hasOwnProperty('article_text'));
                if(likeObj.post_comment_id){
                    if(postIndex !== -1){
                        comment_index = posts[postIndex].comments.findIndex(comment => comment.id == likeObj.post_comment_id);
                        if(comment_index != -1){
                            posts[postIndex].comments[comment_index].likes.push(likeObj);
                        }
                    }
                }else{
                    if(postIndex !== -1){
                        posts[postIndex].likes.push(likeObj);
                    }
                }
            }
            return { ...state,  posts: _.cloneDeep(posts) };
        case "UNLIKE":
            posts = _.cloneDeep(state.posts);
            let unLikeObj = action.payload;
            postIndex = -1;
            comment_index = -1;
            let likeIndex = -1;
            if(unLikeObj.post_id){
                postIndex = posts.findIndex(post => post.id == unLikeObj.post_id && post.hasOwnProperty('post_text'));
                if(unLikeObj.post_comment_id){
                    if(postIndex !== -1){
                        comment_index = posts[postIndex].comments.findIndex(comment => comment.id == unLikeObj.post_comment_id);
                        if(comment_index !== -1){
                            likeIndex = posts[postIndex].comments[comment_index].likes.findIndex(like => like.user_id == unLikeObj.user_id);
                            if(likeIndex != -1){
                                posts[postIndex].comments[comment_index].likes.splice(likeIndex, 1);
                            }
                        }

                    }
                }else{
                    if(postIndex !== -1){
                        likeIndex = posts[postIndex].likes.findIndex(like => like.user_id == unLikeObj.user_id);
                        if(likeIndex != -1){
                            posts[postIndex].likes.splice(likeIndex, 1);
                        }
                    }
                }
            }else{
                postIndex = posts.findIndex(post => post.id == unLikeObj.social_article_id && post.hasOwnProperty('article_text'));
                if(unLikeObj.post_comment_id){
                    if(postIndex !== -1){
                        comment_index = posts[postIndex].comments.findIndex(comment => comment.id == unLikeObj.post_comment_id);
                        if(comment_index !== -1){
                            likeIndex = posts[postIndex].comments[comment_index].likes.findIndex(like => like.user_id == unLikeObj.user_id)
                            posts[postIndex].comments[comment_index].likes.splice(likeIndex, 1);
                        }

                    }
                }else{
                    if(postIndex !== -1){
                        likeIndex = posts[postIndex].likes.findIndex(like => like.user_id == unLikeObj.user_id);
                        if(likeIndex != -1){
                            posts[postIndex].likes.splice(likeIndex, 1);
                        }
                    }
                }
            }
            return { ...state, posts: _.cloneDeep(posts) };
        case "GET_PROFILE_POSTS_SUCCESS":
            if(action.payload.length < 20){
                state.isNextPost = false;
            }
            let profilePosts = Object.assign([], state.profilePosts);
            profilePosts = [...profilePosts,  ...action.payload];
            return {...state, profilePosts: [...profilePosts] };
        case "RESET_PROFILE_POSTS":
            return {...state, profilePosts: [], isNextPost: true}
        case "SET_SOCKET":
            return { ...state, socket: action.payload}
        case "UPDATE_SHARE_POST_COUNT":
            newPosts =  _.cloneDeep(state.posts);
            let index = -1;
            if(action.payload.type == 'article'){
                index = newPosts.findIndex(post => { return post.id == action.payload.id && post.hasOwnProperty('article_text')});
            }else if(action.payload.type == 'post'){
                index = newPosts.findIndex(post => { return post.id == action.payload.id && post.hasOwnProperty('post_text')});
            }
            if(index !== -1){
                newPosts[index].share_count = action.payload.share_count;
            }
            return { ...state, posts: [...newPosts]}
        case "SET_HEADER_STATE":
            let headerState = state.headerState;
            headerState = !headerState;
            return { ...state, headerState: headerState }
        case "DELETE_COMMENT":
            newPosts =  _.cloneDeep(state.posts);
            singlePost = _.cloneDeep(state.singlePost);
            let deleteComment = action.payload;
            if(deleteComment.post_id){
                postIndex = newPosts.findIndex(o => o.id === deleteComment.post_id && o.hasOwnProperty('post_text'));
            }else{
                postIndex = newPosts.findIndex(o => o.id === deleteComment.social_article_id && o.hasOwnProperty('article_text'));
            }
            let commentIndex = -1;
            if(postIndex != -1){
                commentIndex = newPosts[postIndex].comments.findIndex(comment => comment.id === deleteComment.comment_id);
            }
            if(commentIndex!= -1){
                newPosts[postIndex].comments.splice(commentIndex,1);
            }
            if (deleteComment.post_id === singlePost.id) {
                singlePost.comments.splice(commentIndex,1);
            }
            return {
                ...state,
                posts: _.cloneDeep(newPosts),
                singlePost: _.cloneDeep(singlePost)
            }
        case "EDIT_COMMENT":
            newPosts =  _.cloneDeep(state.posts);
            let commentData = action.payload[0];
            if(commentData.post_id){
                postIndex = newPosts.findIndex(o => o.id === commentData.post_id && o.hasOwnProperty('post_text'));
            }else{
                postIndex = newPosts.findIndex(o => o.id === commentData.social_article_id && o.hasOwnProperty('article_text'));
            }
            commentIndex = newPosts[postIndex].comments.findIndex(comment => comment.id === commentData.id);
            newPosts[postIndex].comments[commentIndex] = commentData;
            return {
                ...state,
                posts: _.cloneDeep(newPosts)
            }
        case "PUBLISH_POST_START":
            return {
                ...state,
                makePost: true,
            }
        case "PUBLISH_POST_COMPLETE":
            return {
                ...state,
                makePost: false,
            }
        case "ADD_IN_SHARED_POSTS":
            newSharedPosts =  _.cloneDeep(state.sharedPosts);
            if(action.payload.hasOwnProperty('post_text')){
                index = newSharedPosts.findIndex(post => post.id == action.payload.id && post.hasOwnProperty('post_text'));
            }else{
                index = newSharedPosts.findIndex(post => post.id == action.payload.id && post.hasOwnProperty('article_text'));
            }
            if(index == -1){
                newSharedPosts.push(action.payload);
            }
            return {
                ...state,
                sharedPosts: _.cloneDeep(newSharedPosts)
            }
        case "ADD_IN_FRNDSSTATEARR":
            let newFrndsStateArr = _.cloneDeep(state.frndsStateArr);
            let frndIndex = newFrndsStateArr.findIndex(frnd => frnd.user_one_id == action.payload.user_one_id && frnd.user_two_id == action.payload.user_two_id);
            if(frndIndex == -1){
                newFrndsStateArr.push(action.payload);
            }
            return{
                ...state,
                frndsStateArr: newFrndsStateArr,
            }
        default:
            return { ...state }
    }
}
export default posts;
