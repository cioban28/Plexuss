import React , { Component } from 'react';
import renderHTML from 'react-render-html';
import { Link } from 'react-router-dom'
class CommingBubble extends Component{
    render(){
        let { message, img, time } = this.props;
        let avatarPic = !!message.full_name && '/social/images/Avatar_Letters/'+message.full_name.charAt(0).toUpperCase()+'.svg'
        let profPic = !!img ? !img.includes('default.png') ? img : avatarPic : avatarPic;
        return(
            message.msg &&
            isHTML(message.msg) ?
            <div className="comming_bubble">
                <div className="user_img">
                    <img src={profPic} />
                </div>
                {
                    message.post_id &&
                    <Link to={'/post/'+message.post_id} >
                        {renderHTML(message.msg)}
                    </Link> ||
                    message.share_article_id &&
                    <Link to={'/social/article/'+message.share_article_id} >
                        {renderHTML(message.msg)}
                    </Link> ||
                    <div className="message_content">
                        <div className="message_content_text">
                            {renderHTML(message.msg)}
                        </div>
                    </div>
                }
                <div className="message_time">{time}</div>
            </div>
            :
            <div className="comming_bubble">
                <div className="user_img">
                    <img src={profPic} />
                </div>
                <div className="message_content">
                    <div className="message_content_text">
                        {
                            !!message.msg && message.msg.split('\n').map((msg, index) => <div key={index}>{
                                renderHTML(msg)
                            }</div>)
                        }
                    </div>
                </div>
                <div className="message_time">{time}</div>
            </div>
        )
    }
}
const isHTML = (str) => {
    const doc = new DOMParser().parseFromString(str, "text/html");
    return Array.from(doc.body.childNodes).some(node => node.nodeType === 1);
}
export default CommingBubble;
