import React, { Component } from 'react'
import { connect } from 'react-redux'
import axios from 'axios'
import InfiniteScroll from 'react-infinite-scroller'
import './styles.scss'
import { fetchNotification, readNotificationApi } from './../../../api/notification'
import NotifiBox from './NotifiBox';
import { Link } from 'react-router-dom'
class Notifications extends Component{
  constructor(props){
    super(props);
    this.state={
      notifications: [],
      read_notification: [],
      unread_notification: [],
      nextNotification: true,
    }
    this.fetchNotifications = this.fetchNotifications.bind(this);
  }
  componentDidMount(){
    const { user } = this.props;
    this.setState({notifications: this.props.notifications}, ()=>{
      this.helper();
    })
    axios({
      method: 'get',
      url: '/ajax/notifications/setRead?type=notify',
    })
    .then(res => {
      let data = {
        id: -1,
        thread_room: 'post:room:'+user.user_id,
      }
      readNotificationApi(data);
    })
  }
  componentDidUpdate(prevProps){
    if(prevProps.notifications != this.props.notifications){
      this.setState({notifications: this.props.notifications},()=>{
        this.helper();
      })
    }
  }
  helper(){
    let r_notifications=[], ur_notifiations=[];
    this.state.notifications.map((notification)=>{
        if(notification.is_read == "1"){
            r_notifications.push(notification);
        }else{
            ur_notifiations.push(notification);
        }
    })
    this.setState({
        read_notification: r_notifications,
        unread_notification: ur_notifiations
    })
  }
  fetchNotifications(page){
    let pageNumber = parseInt(this.props.pageNumber);
    this.setState({nextNotification: false})
    fetchNotification(pageNumber)
    .then(()=>{
        if(this.props.nextNotification){
            this.setState({nextNotification: true})
        }
    })
  }
  render() {
    const { unread_notification, read_notification } = this.state;
    let { handleNotifications, isMobile } = this.props
    return (
      <span>
      {this.props.user.signed_in == 1 ? (
        <section className="rightbar notifications_parent">
          <ul className="notifications rightbar-list">
            <li className="row notification-head">
              Notifications
            </li>
            <InfiniteScroll
              pageStart={0}
              loadMore={this.fetchNotifications}
              hasMore={this.state.nextNotification}>
              {
                unread_notification.length != 0 && unread_notification.map((notification, index) =>{
                  return <NotifiBox isMobile={isMobile} handleNotifications={handleNotifications} notification={notification} key={'urnt'+index}/>
                })
              }
              {
                read_notification.length != 0 && read_notification.map((notification, index) =>{
                  return <NotifiBox isMobile={isMobile} handleNotifications={handleNotifications} notification={notification} key={'rnt'+index} />
                })
              }
              </InfiniteScroll>
              <Button handleNotifications={handleNotifications} />
          </ul>
        </section>
      ) : (
        <div className="rightbar notification-preview">
            <div className="right-circle"><img className="img-circle" src="/images/frontpage/notification-circle.png"/></div>
            <div className="right-text"><span className="desc-text">With Plexuss notifications stay on top of all your deadlines and engagements</span></div>
            <div className="right-login"><a href="/signup?utm_source=SEO&utm_medium=frontPage" className="btn-login">Login or Signup</a></div>
        </div>
      )}
      </span>
    );
  }
}
function Button({handleNotifications}){
  return(
      <li className="row view_all_btn_parent" onClick={handleNotifications}>
        <Link to={"/social/notifications"} className="view_all_btn">
          View All
        </Link>
      </li>
  )
}
function mapStateToProps(state){
  return{
      notifications: state.notification && state.notification.notifications,
      pageNumber: state.notification && state.notification.pageNumber,
      nextNotification: state.notification && state.notification.nextNotification,
      user: state.user && state.user.data,
  }
}
export default connect(mapStateToProps, null)(Notifications);
