import React, { Component } from 'react'
import { Link } from 'react-router-dom'

class NotifyCard extends Component{
    render(){
        let { notification } = this.props;
        let userImg = {
            backgroundImage: !!notification.user_img_route ? 'url("'+(notification.user_img_route)+ '")' : 'url("/social/images/Avatar_Letters/'+(notification.name[0].toUpperCase())+ '.svg")'
        }
        return(
            <li className= {` notification-spacing ${(notification.is_read === "1" ? ' read' : 'unread')}`} >
                <Link to={notification.link} className={"notification_images__classes " + (notification.is_read ? ' read' : 'unread')}>
                    <span className="card large-10 small-10 left">
                        <span className="_parent">
                            {!!notification.user_img_route || notification.icon_img_route === null ?
                            <div className="user_image" style={userImg}/>
                            :
                            <img className="imgage_classes" src={notification.icon_img_route} />
                          }
                        </span>
                        <span className="name1">{notification.name}<span className="desc">{" "+notification.msg}</span></span>
                      </span>
                    <span className="large-2 small-2">{notification.date}</span>
                </Link>
            </li>
        )
    }
}
export default NotifyCard;
