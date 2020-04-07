import React, { Component } from 'react'
import ContactTable from './contact_table'
import Header from './content_header';
class InviteFromContacts extends Component{
    render(){
        let { toggleShowContacts } = this.props;
        return(
            <div className="large-9 medium-9 small-12 upper_container columns">
                <div className="setting_content_container invite_friends_container">
                    <Header imgSrc={'/social/images/settings/active_options/noun_invitation_58165_000000.png'} title={'INVITE FRIENDS'} imgClass={'invite_friends'}/>
                    <div onClick={() => toggleShowContacts()} className="back_contacts">‹ Invite from Contacts</div>
                    <div>Choose the contacts you would like to invite to plexuss</div>
                    <ContactTable />
                </div>
            </div>
        )
    }
}
export default InviteFromContacts;