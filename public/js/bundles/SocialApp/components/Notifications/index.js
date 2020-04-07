import React, { Component } from 'react'
import { connect } from 'react-redux'
import './styles.scss'
import NotifyCard from './NotifyCard'
import { fetchNotification } from './../../api/notification'
import InfiniteScroll from 'react-infinite-scroller';
import { Link } from 'react-router-dom'
import { Helmet } from 'react-helmet';
class Notifications extends Component{
    constructor(props) {
        super(props)
        this.state = {
          notifications: [],
          read_notification: [],
          unread_notification: [],
          nextNotification: true,
        }
        this.filterList = this.filterList.bind(this);
        this.fetchNotifications = this.fetchNotifications.bind(this);
    }
    componentDidMount() {
        this.setState({notifications: this.props.notifications},()=>{
            this.helper();
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
    filterList(event){
        let { notifications } = this.props;
        if(notifications){
            var updatedList = notifications;
            updatedList = updatedList.filter(function(item){
                return item.name.toLowerCase().search( event.target.value.toLowerCase()) !== -1;
            });
            this.setState({notifications: updatedList},()=>{
                this.helper();
            });
        }
        event.target.value != "" ? this.state.nextNotification = false : this.state.nextNotification = true ;
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
    render(){
        let { notifications, read_notification, unread_notification } = this.state
        return(
            <div>
                <Helmet>
                    <title>Notifications | Plexuss.com</title>
                    <meta name="description" content="In this page, you will be able to see your Plexuss Notifications." />
                </Helmet>
                    <div className="all_notifications">
                        <div className="notification_banner">
                            <div className="top_header_banner">
                                <div className="heading">
                                      <div className="left_heading">Your Notifications</div>
                                  <Link to="/social/settings">
                                    <div className="right_heading plexuss-color">Notification Settings</div>
                                  </Link>
                                </div>
                                <form className="search_form">
                                    <input type="text" placeholder="Search Plexuss" className="input_contral" onChange={(e)=>this.filterList(e)}/>
                                    <a href="#" className="button postfix fa fa-search btn-search search_icon"></a>
                                </form>
                            </div>
                            {
                                notifications.length == 0 &&
                                <div className="no-data">No Record Found</div>
                            }
                            <ul>
                                <InfiniteScroll
                                        pageStart={0}
                                        loadMore={this.fetchNotifications}
                                        hasMore={this.state.nextNotification}
                                    >
                                    {
                                        unread_notification.length != 0 && unread_notification.map((notification, index) => {
                                            return <NotifyCard key={'unread'+index} notification={notification} />
                                        })
                                    }
                                    {
                                        read_notification.length != 0 && read_notification.map((notification, index) => {
                                            return <NotifyCard key={'read'+index} notification={notification} />
                                        })
                                    }
                                </InfiniteScroll>
                            </ul>
                        </div>
                    </div>
            </div>
        )
    }
}
function mapStateToProps(state){
    return{
        notifications: state.notification && state.notification.notifications,
        pageNumber: state.notification && state.notification.pageNumber,
        nextNotification: state.notification && state.notification.nextNotification,
    }
}
export default connect(mapStateToProps, null)(Notifications)
