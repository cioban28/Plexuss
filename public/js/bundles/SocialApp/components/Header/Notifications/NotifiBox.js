import React, { Component } from 'react';
import { connect } from 'react-redux'
import TimeAgo from 'react-timeago'
import { Link } from 'react-router-dom'
import { readNotification } from './../../../actions/notificationAction'
import { readNotificationApi, readAllNotification } from './../../../api/notification'
import moment from 'moment';
class NotifiBox extends Component{
    constructor(props){
      super(props);
      this.state={
        read: false,
      }
      this.clickHandler = this.clickHandler.bind(this);
    }
    componentDidMount(){
      const { notification } = this.props;
      if(notification.is_read == "1"){
        this.setState({read: true})
      }
    }
    clickHandler(){
        const { notification, isMobile, user } = this.props;
        this.setState({read: true})
        let data = {
          id: notification.id,
          thread_room: 'post:room:'+user.user_id,
        }
        readNotificationApi(data);
        readAllNotification();
        if (isMobile) {
          this.props.handleNotifications()
        }
    }
    render(){
      const { notification } = this.props;
      const { read } = this.state;
      let userImg = {
        backgroundImage: !!notification.user_img_route ? 'url("'+(notification.user_img_route)+ '")' : 'url("/social/images/Avatar_Letters/'+(notification.name[0].toUpperCase())+ '.svg")'
      }
      const notificationCreatedAt = typeof(notification.created_at) === 'string' ? notification.created_at : (notification.created_at.date && notification.created_at.date.split('.')[0]);
      const localCreatedAtTime = moment.utc(notificationCreatedAt).local();
      return(
          <li onClick={this.clickHandler}>
            <Link to={notification.link} className={"notification_images__classes " + (read ? ' read' : 'unread')}>
              <div>{!!notification.created_at ? <TimeAgo date={localCreatedAtTime} /> : notification.date}</div>
              {!!notification.user_img_route || notification.icon_img_route === null ?
                <div className="user_image" style={userImg}/>
                :
                <img className="user_image" src={notification.icon_img_route} />
              }
              <span>{notification.name + ' ' + notification.msg}</span>
            </Link>
          </li>
        );
    }
}
const mapStateToProps = (state) =>{
  return{
    user: state.user.data,
  }
}
const mapDispatchToProps = (dispatch) => {
    return {
        readNotification: (id) => { dispatch(readNotification(id))}
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(NotifiBox);
