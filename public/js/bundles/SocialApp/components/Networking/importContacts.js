import React, { Component } from 'react'
import './importContact.scss'
import { getGmailData } from './../../api/post'
class ImportContacts extends Component{
    constructor(props){
        super(props);
        this.state={
            provider: '',
        }
        this.handleProvider = this.handleProvider.bind(this);
    }
    handleProvider(provider){
        this.setState({
            provider: provider,
        })
    }
    render(){
        let { setSubComponent } = this.props;
        return(
            <div className="import_contacts">
                <div className="heading">Please select your provider</div>
                <div className="images_banner">
                    <a className={"image_parent " + (this.state.provider === 'gmail' ? 'select_provider' : '')} >
                        <div className="gmail image"></div>
                    </a>
                </div>
                <div className="description">
                    We'll import your address book to suggest connections and help you manage your contacts
                </div>
                <div className="buttons_banner">
                    <div className="cancel_button" onClick={()=>setSubComponent('importedContacts')}>
                        Cancel
                    </div>
                    <a href="/googleInviteForSocialApp" className="next_button" onClick={()=>this.handleProvider('gmail')}>
                        Next
                    </a>
                </div>
            </div>
        )
    }
}
export default ImportContacts;
