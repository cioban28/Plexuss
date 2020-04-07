import React, { Component } from 'react';
import renderHTML from 'react-render-html';
import moment from 'moment'
import { Link } from 'react-router-dom'
class OutBubble extends Component{
    render(){
        let { message, time } = this.props;
        let viewTime = message.read_time && moment.utc(message.read_time).local().format('LT');
        return(
            message.msg &&
            isHTML(message.msg) ?
            <div className="out_bubble">
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
                {
                    message.read_time &&
                    <div className="view_time">{"Viewed at "+viewTime}</div>
                }
            </div>
            :
            <div className="out_bubble">
                <div className="message_content">
                    <div className="message_content_text">
                    {
                        !!message.msg && message.msg.split('\n').map((msg, index) => <div key={index}>{
                            renderHTML(msg)    
                        }</div>)
                    }
                    </div>
                </div>
                {
                    message.read_time &&
                    <div className="view_time">{"Viewed at "+viewTime}</div>
                }
            </div>
        )
    }
}
const isHTML = (str) => {
    const doc = new DOMParser().parseFromString(str, "text/html");
    return Array.from(doc.body.childNodes).some(node => node.nodeType === 1);
}
export default OutBubble;
