import React, { Component } from 'react'
import Header from './content_header'
import { sendSingleInvite } from './../../api/user'
import { connect } from 'react-redux'
import InviteFromContacts from './invite_from_contacts'

class InviteFriends extends Component{
    constructor(props){
        super(props);
        this.state={
            friendEmail: '',
            singleInviteResponse: '',
            showContacts: false,
        }
        this.toggleShowContacts = this.toggleShowContacts.bind(this);
        this.onChangeEmail = this.onChangeEmail.bind(this);
        this.singleInvite = this.singleInvite.bind(this);
    }
    onChangeEmail(e){
        this.setState({ friendEmail: e.target.value});
    }
    singleInvite(){
        sendSingleInvite(this.state.friendEmail);
    }
    toggleShowContacts(){
        this.setState({
            showContacts: !this.state.showContacts,
        })
    }
    render(){
        return(
            this.state.showContacts && <InviteFromContacts toggleShowContacts={this.toggleShowContacts}/> ||
            <div className="large-9 medium-9 small-12 upper_container columns">
                <div className="setting_content_container invite_friends_container">
                    <div className="manage_imported_contacts" onClick={ this.toggleShowContacts }>MANAGE IMPORTED CONTACTS</div>
                    <Header imgSrc={'/social/images/settings/active_options/noun_invitation_58165_000000.png'} title={'INVITE FRIENDS'} imgClass={'invite_friends'} backClickHandler={this.props.backClickHandler}/>
                    <div className="content_container">
                        <div className="explanation">
                            Help your friends research colleges, find colleges and connect with similar minded students or alumni.
                        </div>
                        <div className="invite_friends_content">
                            <div className="heading">Please select your email provider</div>
                            <div className="mail_container">
                                <div className="img_container">
                                    <a href="/googleInvite">
                                        <img src="/social/images/settings/active_options/gmail.jpg" alt=""/>
                                    </a>
                                    <div className="caption">Gmail</div>
                                </div>
                                <div className="img_container">
                                    <a href="/microsoftInvite">
                                        <img src="/social/images/settings/active_options/gmail.jpg" alt=""/>
                                    </a>
                                    <div className="caption">Outlook</div>
                                </div>
                                <div className="img_container">
                                    <a href="/microsoftInvite">
                                        <img src="/social/images/settings/active_options/gmail.jpg" alt=""/>
                                    </a>
                                    <div className="caption">Hotmail</div>
                                </div>
                            </div>
                            <div className="explanation1">
                                We'll import your address book to suggest connections and help you manage your contracts.
                            </div>
                            <div className="heading">
                                Or invite a friend by email address.
                            </div>
                            <div className="invite_friends_form">
                                <input
                                    name="InviteFriends"
                                    placeholder="Add friends email addresses"
                                    type="text"
                                    onChange={this.onChangeEmail}
                                    className="input_invite_frnds"/>
                                <input type="button" value="SEND" className="submit_invite_frnds" onClick={() => this.singleInvite()}/>
                            </div>
                            {
                                this.props.singleInvite.response == "success" &&
                                <div className="single_invite_msg">{this.props.singleInvite.msg}</div>
                            }
                            <div className="preview_invite">PREVIEW INVITE</div>
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}
const mapStateToProps = (state) =>{
    return{
        singleInvite: state.setting.setting.singleInvite,
    }
}
export default connect(mapStateToProps, null)(InviteFriends)