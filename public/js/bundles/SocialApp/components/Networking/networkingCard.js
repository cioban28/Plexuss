import React, { Component } from 'react'
import { connect } from 'react-redux'
import { sendSingleInvite } from './../../api/user'
import { sentInvitationAction } from './../../actions/setting'

class NetworkingCard extends Component{
    constructor(props){
        super(props);
        this.state={
            sendRequest: false,
        }
        this.singleInvite = this.singleInvite.bind(this);
    }
    componentDidMount(){
        let { contact } = this.props;
        if(contact.sent !== 0){
            this.setState({sendRequest: true})
        }
    }
    singleInvite(){
        let { contact } = this.props;
        sendSingleInvite(contact.invite_email)
        .then(()=>{
            this.props.sentInvitationAction(contact.invite_email);
            this.setState({sendRequest: true});
        })
    }
    render(){
        let { contact } = this.props;
        return(
            <li>
                <div className="row networking_card">
                    <div className="large-9 medium-9 small-9 columns">
                        <div className="userDiscription">
                        <div className="userName user_name_hover">{contact.invite_name}</div>
                        <div className="collegeName">{contact.invite_email}</div>
                        </div>
                    </div>
                    {
                        !this.state.sendRequest && contact && contact.sent &&    
                            <div className="large-3 medium-3 small-3 columns right_portion">                           
                                <div className="addUser">
                                    <img src="/social/images/Icons/add-user.png" className="addImage" />
                                </div>
                            </div> ||
                            !this.state.sendRequest && contact && !contact.sent &&
                            <div className="large-4 medium-4 small-4 columns" onClick={this.singleInvite}>
                                <div className="inviteUser">
                                    <img src="/social/images/Icons/add-user.png" className="addImage" />
                                    <span>INVITE</span>
                                </div>
                            </div>||
                            this.state.sendRequest &&
                                <div className="large-4 medium-4 small-4 columns">
                                    <div className="requst_sent">Invite Sent!</div>
                                </div>
                    }
                </div>
            </li>
        )
    }
}
const mapDispatchtoProps = (dispatch) => {
    return {
        sentInvitationAction: (data) => {dispatch(sentInvitationAction(data))},
    }
}
export default connect(null, mapDispatchtoProps)(NetworkingCard);