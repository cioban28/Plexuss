export function saveArticles(payload){
    return{
        type: "SAVE_ARTICLE",
        payload,
    };
}
export function getArticlesAction(payload){
    return{
        type: "GET_ARTICLES",
        payload,
    };
}
export function getSingleArticleAction(payload){
    return{
        type: "GET_ARTICLE",
        payload,
    };
}
export function deleteArticlesAction(payload){
    return{
        type: "DELETE_ARTICLE",
        payload,
    };
}
export function updateArticleAction(payload){
    return{
        type: "UPDATE_ARTICLE",
        payload,
    };
}
export function saveArticleCommentAction(payload){
    return{
        type: "SAVE_ARTICLE_COMMENT",
        payload,
    };
}
export function addArticleComment(payload){
    return{
        type: "ADD_ARTICLE_COMMENT",
        payload,
    };
}
export function editArticleComment(payload){
    return{
        type: "EDIT_ARTICLE_COMMENT",
        payload,
    };
}
export function deleteArticleComment(payload){
    return{
        type: "DELETE_ARTICLE_COMMENT",
        payload,
    };
}
export function removeCommentLike(payload){
    return{
        type: "REMOVE_ARTICLE_COMMENT",
        payload,
    };
}
export function addArticleLike(payload){
    return{
        type: "ADD_ARTICLE_LIKE",
        payload,
    };
}
export function updateArticleCount(payload){
    return{
        type: "UPDATE_ARTICLE_SHARE_COUNT",
        payload,
    };
}