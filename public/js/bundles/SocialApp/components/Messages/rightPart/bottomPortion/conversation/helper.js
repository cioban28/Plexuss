import React from 'react'
import moment from 'moment'
import renderHTML from 'react-render-html';
import { Link } from 'react-router-dom'
export function InCommingWithImage(props){
    let message = props.message;
    let flag = props.flag;
    let date = props.date;
    let localtime = moment.utc(date).local().format('lll');
    let avatarPic = !!message.full_name && '/social/images/Avatar_Letters/'+message.full_name.charAt(0).toUpperCase()+'.svg'
    let profPic = !!message.img ? !message.img.includes('default.png') ? message.img : avatarPic : avatarPic;
    let viewTime = message.read_time && moment.utc(message.read_time).local().format('LT');
    return(
        <span>
            {
                flag &&
                <div className="date_line">
                    <span>{localtime}</span>
                </div>
            }
            {
                message.msg &&
                isHTML(message.msg)?
                <div className="in_comming_bubble_parent">
                    <div className="in_comming_bubble">
                        <div className="img_parent">
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
                            <div className="text">
                                {renderHTML(message.msg)}
                            </div>
                        } 
                    </div>
                </div>
                :
                <div className="in_comming_bubble_parent">
                    <div className="in_comming_bubble">
                        <div className="img_parent">
                            <img src={profPic} />
                        </div>
                        <div className="text">
                        {
                            !!message && !!message.msg && message.msg.split('\n').map((msg, index) => <div key={index}>{
                                renderHTML(msg)
                            }</div>)
                        }
                        </div>
                    </div>
                </div>
            }
        </span>
    )
}

export function OutGoingWithImage(props){
    let message = props.message;
    let flag = props.flag;
    let date = props.date;
    let localtime = moment.utc(date).local().format('lll');
    let avatarPic = !!message.Name && '/social/images/Avatar_Letters/'+message.Name.charAt(0).toUpperCase()+'.svg'
    let profPic = !!message.img ? !message.img.includes('default.png') ? message.img : avatarPic : avatarPic;
    let viewTime = message.read_time && moment.utc(message.read_time).local().format('LT');
    return(
        <span>
            {
                flag &&
                <div className="date_line">
                    <span>{localtime}</span>
                </div>
            }
            {
                message.msg &&
                isHTML(message.msg)?
                <div className="out_going_bubble_parent">
                    <div className="out_going_bubble">
                        {
                            message.post_id &&
                            <Link to={'/post/'+message.post_id} >
                                {renderHTML(message.msg)}
                            </Link> ||
                            message.share_article_id &&
                            <Link to={'/social/article/'+message.share_article_id} >
                                {renderHTML(message.msg)}
                            </Link> ||
                            <div className="text">
                                {renderHTML(message.msg)}
                            </div>
                        } 

                        <div className="img_parent">
                            <img src={profPic} />
                        </div>
                    </div>
                    {
                        message.read_time &&
                        <div className="msg_view_time">{"Viewed at "+viewTime}</div>   
                    }
                </div>
                :
                <div className="out_going_bubble_parent">
                    <div className="out_going_bubble">
                        <div className="text">
                        {
                            !!message && !!message.msg && message.msg.split('\n').map((msg, index) => <div key={index}>{
                                renderHTML(msg)
                            }</div>)
                        }
                        </div>
                        <div className="img_parent">
                            <img src={profPic} />
                        </div>
                    </div>
                    {
                        message.read_time &&
                        <div className="msg_view_time">{"Viewed at "+viewTime}</div>   
                    }
                </div>
            }
        </span>
    )
}

const isHTML = (str) => {
    const doc = new DOMParser().parseFromString(str, "text/html");
    return Array.from(doc.body.childNodes).some(node => node.nodeType === 1);
}
