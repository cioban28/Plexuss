import cloneDeep from 'lodash/cloneDeep'
const _ = {
    cloneDeep: cloneDeep
}
const initialState ={
    userArticles: [],
    article: {}
}
const articles = (state = initialState, action) => {
    switch(action.type){
        case "SAVE_ARTICLE":
            let newUserArticles = _.cloneDeep(state.userArticles);
            let userIndex = newUserArticles.findIndex(article => article.id == action.payload.id);
            if(userIndex == -1){
                newUserArticles.push(action.payload)
            }
            return {
                ...state,
                userArticles: newUserArticles,
            }
        case "GET_ARTICLES":
            return { ...state, userArticles: [...action.payload], setDraftsTab: action.payload.setDraftsTab && action.payload.setDraftsTab }
        case "DELETE_ARTICLE":
            newUserArticles = _.cloneDeep(state.userArticles);
            userIndex = newUserArticles.findIndex(article => article.id == action.payload);
            if(index != -1){
                newUserArticles.splice(userIndex, 1);
            }
            return {
                ...state,
                userArticles: newUserArticles,
            }
        case "UPDATE_ARTICLE":
            return { ...state }
        case "GET_ARTICLE":
            return { ...state, article: {...action.payload}}
        case "SAVE_ARTICLE_COMMENT":
            return { ...state }
        case "ADD_ARTICLE_COMMENT":
            let newArticle = _.cloneDeep(state.article);
            newUserArticles = _.cloneDeep(state.userArticles);
            userIndex = newUserArticles.findIndex(article => article.id == action.payload[0].social_article_id);
            if(userIndex != -1){
                newUserArticles[userIndex].comments.push(action.payload[0]);
            }
            if(newArticle && newArticle[0] && newArticle[0].id === action.payload[0].social_article_id){
                newArticle[0].comments.push(action.payload[0]);
            }
            return {
                ...state,
                article: _.cloneDeep(newArticle),
                userArticles: _.cloneDeep(newUserArticles)
            };
        case "ADD_ARTICLE_LIKE":
            newArticle = _.cloneDeep(state.article);
            newUserArticles = _.cloneDeep(state.userArticles);
            userIndex = newUserArticles.findIndex(article => article.id == action.payload.social_article_id);
            if(userIndex != -1){
                if(action.payload.post_comment_id){
                    commentIndex = newUserArticles[userIndex].comments.findIndex(comment => comment.id == action.payload.post_comment_id);
                    if(commentIndex != -1){
                        newUserArticles[userIndex].comments[commentIndex].likes.push(action.payload);
                    }
                }else{
                    newUserArticles[userIndex].likes.push(action.payload);
                }
            }
            if(newArticle && newArticle[0] && newArticle[0].id === action.payload.social_article_id){
                if(action.payload.post_comment_id){
                    commentIndex = newArticle[0].comments.findIndex(comment => comment.id == action.payload.post_comment_id);
                    if(commentIndex != -1){
                        newArticle[0].comments[commentIndex].likes.push(action.payload);
                    }

                }else{
                    newArticle[0].likes.push(action.payload);
                }
            }
            return {
                ...state,
                article: _.cloneDeep(newArticle),
                userArticles: _.cloneDeep(newUserArticles)
            };
        case "UPDATE_ARTICLE_SHARE_COUNT":
            newArticle = _.cloneDeep(state);
            if(newArticle.article && newArticle.article[0] && newArticle.article[0].id == action.payload.id){
                newArticle.article[0].share_count = action.payload.share_count;
            }
            let index = newArticle.userArticles.findIndex(article => { return article.id == action.payload.id });
            if(index !== -1){
                newArticle.userArticles[index].share_count = action.payload.share_count;
            }
            return {
                ...state,
                article: _.cloneDeep(newArticle.article),
                userArticles: _.cloneDeep(newArticle.userArticles)
            }
        case "EDIT_ARTICLE_COMMENT":
            newArticle = _.cloneDeep(state.article);
            newUserArticles = _.cloneDeep(state.userArticles);
            userIndex = newUserArticles.findIndex(article => article.id == action.payload[0].social_article_id);
            let commentIndex = -1;
            if(userIndex != -1){
                commentIndex = newUserArticles[userIndex].comments.findIndex(comment => comment.id == action.payload[0].id);
                if(commentIndex != -1){
                    newUserArticles[userIndex].comments[commentIndex] = action.payload[0];
                }
            }
            if(newArticle && newArticle[0] && newArticle[0].id === action.payload[0].social_article_id){
                commentIndex = newArticle[0].comments.findIndex(comment => comment.id == action.payload[0].id);
                if(commentIndex != -1){
                    newArticle[0].comments[commentIndex] = action.payload[0];
                }
            }
            return {
                ...state,
                article: _.cloneDeep(newArticle),
                userArticles: _.cloneDeep(newUserArticles)
            };
        case "DELETE_ARTICLE_COMMENT":
            newArticle = _.cloneDeep(state.article);
            newUserArticles = _.cloneDeep(state.userArticles);
            userIndex = newUserArticles.findIndex(article => article.id == action.payload.social_article_id);
            commentIndex = -1;
            if(userIndex != -1){
                commentIndex = newUserArticles[userIndex].comments.findIndex(comment => comment.id == action.payload.comment_id);
                if(commentIndex != -1){
                    newUserArticles[userIndex].comments.splice(commentIndex, 1);
                }
            }
            if(newArticle && newArticle[0] && newArticle[0].id === action.payload.social_article_id){
                commentIndex = newArticle[0].comments.findIndex(comment => comment.id == action.payload.comment_id);
                if(commentIndex != -1){
                    newArticle[0].comments.splice(commentIndex,1)
                }
            }
            return {
                ...state,
                article: _.cloneDeep(newArticle),
                userArticles: _.cloneDeep(newUserArticles)
            };
        default:
            return { ...state }
    }
}
export default articles;
