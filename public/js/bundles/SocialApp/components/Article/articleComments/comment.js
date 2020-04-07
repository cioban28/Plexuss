import React, { Component } from 'react'
import Parser from 'html-react-parser';
import Functionality from './functionality'
class Comment extends Component{
    constructor(props){
        super(props);
        this.state={
            commentContent: '',
        }
    }
    componentDidMount(){
        let { comment } = this.props;
        let newText = '';
        let arr = [];
        arr = comment && comment.comment_text && comment.comment_text.split(' ');
        arr.map(word => {
            if(this.validURL(word)){
            var prefix = 'http';
            if(word.toLowerCase().substr(0, prefix.length) != prefix)
            {
                word = 'http://' + word;
            }
            newText =newText + ' ' + `<a href=${word} target='_blank'>${word}</a>`;
            }else{
                newText =newText + ' ' + word;
            }
        })
        this.setState({
            commentContent: newText,
        })
    }
    componentWillReceiveProps(nextProps){
        if(nextProps.comment != this.props.comment){
            let { comment } = nextProps;
            let newText = '';
            let arr = [];
            arr = comment && comment.comment_text && comment.comment_text.split(' ');
            arr.map(word => {
                if(this.validURL(word)){
                var prefix = 'http';
                if(word.toLowerCase().substr(0, prefix.length) != prefix)
                {
                    word = 'http://' + word;
                }
                newText =newText + ' ' + `<a href=${word} target='_blank'>${word}</a>`;
                }else{
                    newText =newText + ' ' + word;
                }
            })
            this.setState({
                commentContent: newText,
            })
        }
    }
    validURL(str) {
        var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
          '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
          '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
          '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
          '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
          '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
        return !!pattern.test(str);
    }
    render(){
        let { comment, articleId, user } = this.props;
        return(
            <div className="article_comment">
                <div className="image_banner">
                    <img src={comment && comment.user && comment.user.profile_img_loc ?  "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/"+comment.user.profile_img_loc : "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/user-avatar-r1.png"} alt=""/>
                </div>
                <div className="comment_content">
                    <div className="comment_body">
                        <strong className="comment_author_name">{comment && comment.user && comment.user.fname} {comment && comment.user && comment.user.lname}</strong>
                        <p>{Parser(this.state.commentContent)}</p>
                    </div>
                    <Functionality comment={comment} articleId={articleId} user={user}/>
                </div>
            </div>
        )
    }
}

export default Comment;